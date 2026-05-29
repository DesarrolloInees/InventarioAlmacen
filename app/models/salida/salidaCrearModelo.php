<?php
// app/models/salida/salidaCrearModelo.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class SalidaCrearModelo
{
    private $conn;
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerTecnicos()
    {
        $listaFinal = [];

        // 1. CARGAMOS USUARIOS LOCALES (Taller / Sistemas)
        try {
            $sql = "SELECT usuario_id, nombre, cargo FROM usuarios WHERE estado = 'activo' ORDER BY nombre ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $locales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($locales as $loc) {
                $listaFinal[] = [
                    // Le ponemos el prefijo 'local_' para que el sistema sepa de dónde viene
                    'usuario_id' => 'local_' . $loc['usuario_id'],
                    'nombre' => $loc['nombre'],
                    'cargo' => 'Taller / ' . $loc['cargo']
                ];
            }
        } catch (PDOException $e) {
            error_log("Error cargando técnicos locales: " . $e->getMessage());
        }

        // 2. CARGAMOS TÉCNICOS EXTERNOS (Motorizados)
        try {
            // ⚠️ OJO: Cambia credenciales aquí cuando pases a producción
            $connMotos = new PDO("mysql:host=127.0.0.1;dbname=inees_mantenimientos;charset=utf8mb4", "root", "");
            $connMotos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlM = "SELECT id_tecnico, nombre_tecnico FROM tecnico WHERE estado = 1 ORDER BY nombre_tecnico ASC";
            $stmtM = $connMotos->prepare($sqlM);
            $stmtM->execute();
            $motos = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            foreach ($motos as $mot) {
                $listaFinal[] = [
                    // Le ponemos el prefijo 'moto_'
                    'usuario_id' => 'moto_' . $mot['id_tecnico'],
                    'nombre' => $mot['nombre_tecnico'],
                    'cargo' => 'Técnico Motorizado (Ruta)'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error cargando técnicos externos: " . $e->getMessage());
        }

        return $listaFinal;
    }

    // Fusión de catálogos: SOLO MUESTRA ARTÍCULOS CON STOCK MAYOR A 0 (Collation corregido)
    public function obtenerInventarioDisponible()
    {
        try {
            $sql = "SELECT 
                        'repuesto' AS tipo, 
                        r.id_repuesto AS id_interno, 
                        CAST(r.nombre_repuesto AS CHAR CHARACTER SET utf8mb4) AS nombre, 
                        CAST(r.codigo_referencia AS CHAR CHARACTER SET utf8mb4) AS codigo, 
                        i.cantidad_total AS stock, 
                        CAST(r.condicion AS CHAR CHARACTER SET utf8mb4) AS condicion
                    FROM repuestos r
                    INNER JOIN inventario_stock i ON r.id_repuesto = i.id_repuesto
                    WHERE r.estado = 1 AND i.cantidad_total > 0
                    
                    UNION ALL
                    
                    SELECT 
                        'producto' AS tipo, 
                        p.id_producto AS id_interno, 
                        CAST(p.nombre_producto AS CHAR CHARACTER SET utf8mb4) AS nombre, 
                        CAST(p.codigo_interno AS CHAR CHARACTER SET utf8mb4) AS codigo, 
                        i.cantidad_total AS stock, 
                        'N/A' AS condicion
                    FROM productos p
                    INNER JOIN inventario_stock i ON p.id_producto = i.id_producto
                    WHERE p.estado = 1 AND i.cantidad_total > 0
                    
                    ORDER BY nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al cargar inventario para salida: " . $e->getMessage());
            return [];
        }
    }

    public function procesarSalidaMultiple($idTecnicoDropdown, $items, $idAdmin, $tipoAsignacion)
    {
        // 1. Desempaquetamos el ID secreto (Ej: "moto_5" -> se divide en "moto" y "5")
        $partesId = explode('_', $idTecnicoDropdown);
        $origen = $partesId[0]; // Sabremos si es 'local' o 'moto'
        $idTecnicoReal = intval($partesId[1] ?? 0); // El número real en la base de datos

        if ($idTecnicoReal === 0) {
            return ['exito' => false, 'msg' => 'ID de técnico inválido.'];
        }

        try {
            $this->conn->beginTransaction();
            $connMotos = null;

            // 2. Conexión a BD externa SOLO si el destino es motorizado
            // (Si la chica se equivoca en el 2do dropdown, el sistema lo corrige gracias al '$origen')
            if ($tipoAsignacion === 'motorizado' || $origen === 'moto') {
                try {
                    $connMotos = new PDO("mysql:host=127.0.0.1;dbname=inees_mantenimientos;charset=utf8mb4", "root", "");
                    $connMotos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $connMotos->beginTransaction();

                    // Validamos directo con el ID sin usar cédulas
                    $stmtTecMoto = $connMotos->prepare("SELECT id_tecnico FROM tecnico WHERE id_tecnico = :id");
                    $stmtTecMoto->execute([':id' => $idTecnicoReal]);

                    if (!$stmtTecMoto->fetchColumn()) {
                        throw new Exception("El motorizado seleccionado no existe en la base de datos externa.");
                    }
                } catch (PDOException $e) {
                    throw new Exception("Error de conexión a BD Motorizados: " . $e->getMessage());
                }
            }

            foreach ($items as $item) {
                $esRepuesto = ($item['tipo'] === 'repuesto');
                $idItem = $item['id'];
                $cantidadSalida = $item['cantidad'];
                $codigoRef = $item['codigo'] ?? '';
                $columnaFiltro = $esRepuesto ? 'id_repuesto' : 'id_producto';

                $sqlStock = "SELECT cantidad_total FROM inventario_stock WHERE $columnaFiltro = :id FOR UPDATE";
                $stmtS = $this->conn->prepare($sqlStock);
                $stmtS->execute([':id' => $idItem]);
                $stockActual = $stmtS->fetchColumn();

                if ($stockActual === false || $stockActual < $cantidadSalida) {
                    throw new Exception("Stock insuficiente para: " . $item['nombre']);
                }

                $observacionFinal = "Asignación " . ($origen === 'moto' ? 'motorizado' : 'interno');

                $sqlMov = "INSERT INTO movimientos_inventario 
                            (id_repuesto, id_producto, tipo_movimiento, cantidad, id_tecnico_destino, id_usuario_registra, observacion, fecha_movimiento) 
                            VALUES 
                            (:id_repuesto, :id_producto, 'SALIDA', :cant, :idt, :ida, :obs, NOW())";

                $stmtM = $this->conn->prepare($sqlMov);
                $stmtM->bindValue(':id_repuesto', $esRepuesto ? $idItem : null, PDO::PARAM_INT);
                $stmtM->bindValue(':id_producto', !$esRepuesto ? $idItem : null, PDO::PARAM_INT);
                $stmtM->bindParam(':cant', $cantidadSalida, PDO::PARAM_INT);
                $stmtM->bindParam(':idt', $idTecnicoReal, PDO::PARAM_INT); // Se guarda el ID limpio
                $stmtM->bindParam(':ida', $idAdmin, PDO::PARAM_INT);
                $stmtM->bindParam(':obs', $observacionFinal);
                $stmtM->execute();

                $sqlUpd = "UPDATE inventario_stock SET cantidad_total = cantidad_total - :cant WHERE $columnaFiltro = :id";
                $stmtU = $this->conn->prepare($sqlUpd);
                $stmtU->execute([':cant' => $cantidadSalida, ':id' => $idItem]);

                // 3. Sincronización a la maleta del Motorizado
                if (($tipoAsignacion === 'motorizado' || $origen === 'moto') && $connMotos !== null && $esRepuesto) {
                    if (empty($codigoRef) || $codigoRef === 'S/C') {
                        throw new Exception("El repuesto [{$item['nombre']}] no tiene un código de referencia válido para enviar a la App Externa.");
                    }

                    $stmtRepMoto = $connMotos->prepare("SELECT id_repuesto FROM repuesto WHERE codigo_referencia = :codigo");
                    $stmtRepMoto->execute([':codigo' => $codigoRef]);
                    $idRepuestoMoto = $stmtRepMoto->fetchColumn();

                    if (!$idRepuestoMoto) {
                        throw new Exception("El repuesto [$codigoRef] no existe en el catálogo de Motorizados.");
                    }

                    $sqlMoto = "INSERT INTO inventario_tecnico (id_tecnico, id_repuesto, cantidad_actual) 
                                VALUES (:idt, :idr, :cant) 
                                ON DUPLICATE KEY UPDATE cantidad_actual = cantidad_actual + :cant";
                    $stmtMoto = $connMotos->prepare($sqlMoto);
                    $stmtMoto->execute([':idt' => $idTecnicoReal, ':idr' => $idRepuestoMoto, ':cant' => $cantidadSalida]);
                }
            }

            $this->conn->commit();
            if ($connMotos !== null) {
                $connMotos->commit();
            }
            return ['exito' => true, 'msg' => 'Despacho registrado y sincronizado exitosamente.'];

        } catch (\Throwable $e) {
            $this->conn->rollBack();
            if (isset($connMotos) && $connMotos !== null && $connMotos->inTransaction()) {
                $connMotos->rollBack();
            }
            $errorCrudo = "💥 ERROR FATAL: " . $e->getMessage() . " | Línea: " . $e->getLine();
            error_log($errorCrudo);
            return ['exito' => false, 'msg' => $errorCrudo];
        }
    }
}