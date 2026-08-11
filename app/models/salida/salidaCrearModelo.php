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

        // 1. CARGAR TÉCNICOS LOCALES (Desde la nueva tabla)
        try {
            $sql = "SELECT id_tecnico_local, nombre_tecnico FROM tecnicos_locales WHERE estado = 1 ORDER BY nombre_tecnico ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $locales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($locales as $loc) {
                $listaFinal[] = [
                    // Mantenemos el prefijo 'local_' para que la lógica de guardado siga funcionando perfecto
                    'usuario_id' => 'local_' . $loc['id_tecnico_local'],
                    'nombre' => $loc['nombre_tecnico'],
                    'cargo' => 'Taller / Interno'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error cargando técnicos locales: " . $e->getMessage());
        }

        // 2. CARGAR TÉCNICOS EXTERNOS / MOTORIZADOS (Se queda exactamente igual)
        try {
            $connMotos = new PDO("mysql:host=127.0.0.1;dbname=inees_mantenimientos;charset=utf8mb4", "root", "");
            $connMotos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlM = "SELECT id_tecnico, nombre_tecnico FROM tecnico WHERE estado = 1 ORDER BY nombre_tecnico ASC";
            $stmtM = $connMotos->prepare($sqlM);
            $stmtM->execute();
            $motos = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            foreach ($motos as $mot) {
                $listaFinal[] = [
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

    public function obtenerInventarioDisponible()
    {
        try {
            $sql = "SELECT 
                        'repuesto' AS tipo, r.id_repuesto AS id_interno,
                        CAST(r.nombre_repuesto AS CHAR CHARACTER SET utf8mb4) AS nombre,
                        CAST(r.codigo_referencia AS CHAR CHARACTER SET utf8mb4) AS codigo,
                        i.cantidad_total AS stock,
                        CAST(r.condicion AS CHAR CHARACTER SET utf8mb4) AS condicion
                    FROM repuestos r
                    INNER JOIN inventario_stock i ON r.id_repuesto = i.id_repuesto
                    WHERE r.estado = 1 AND i.cantidad_total > 0
                    UNION ALL
                    SELECT 
                        'producto' AS tipo, p.id_producto AS id_interno,
                        CAST(p.nombre_producto AS CHAR CHARACTER SET utf8mb4) AS nombre,
                        CAST(p.codigo_interno AS CHAR CHARACTER SET utf8mb4) AS codigo,
                        i.cantidad_total AS stock, 'N/A' AS condicion
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

    public function obtenerCatalogoCompleto()
    {
        try {
            $sql = "SELECT 
                        'repuesto' AS tipo, r.id_repuesto AS id_interno,
                        CAST(r.nombre_repuesto AS CHAR CHARACTER SET utf8mb4) AS nombre,
                        CAST(r.codigo_referencia AS CHAR CHARACTER SET utf8mb4) AS codigo,
                        CAST(r.condicion AS CHAR CHARACTER SET utf8mb4) AS condicion
                    FROM repuestos r
                    WHERE r.estado = 1
                    UNION ALL
                    SELECT 
                        'producto' AS tipo, p.id_producto AS id_interno,
                        CAST(p.nombre_producto AS CHAR CHARACTER SET utf8mb4) AS nombre,
                        CAST(p.codigo_interno AS CHAR CHARACTER SET utf8mb4) AS codigo,
                        'N/A' AS condicion
                    FROM productos p
                    WHERE p.estado = 1
                    ORDER BY nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al cargar catálogo completo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Procesa un carrito mixto ENTRADA/SALIDA. Cada tipo va a su propia tabla:
     * ENTRADA -> movimientos_entrada | SALIDA -> movimientos_salida
     */
    public function procesarMovimientoMultiple($idTecnicoDropdown, $items, $idAdmin, $tipoAsignacion, $datosGlobales)
    {
        $origen = null;
        $idTecnicoReal = null;

        // Parseamos el técnico solo si enviaron uno
        if (!empty($idTecnicoDropdown)) {
            $partesId = explode('_', $idTecnicoDropdown);
            $origen = $partesId[0]; // 'local' o 'moto'
            $idTecnicoReal = intval($partesId[1] ?? 0);
            if ($idTecnicoReal === 0)
                $idTecnicoReal = null;
        }

        // Si eligieron sync con App Motorizados pero no seleccionaron a nadie, ahí sí bloqueamos
        if (($tipoAsignacion === 'motorizado' || $origen === 'moto') && !$idTecnicoReal) {
            return ['exito' => false, 'msg' => 'Debes seleccionar un motorizado válido para sincronizar con la App Externa.'];
        }

        $destino = $datosGlobales['destino'];
        $remision = $datosGlobales['remision'];
        $cotizacion = $datosGlobales['cotizacion'];
        $novedad = $datosGlobales['novedad'];

        try {
            $this->conn->beginTransaction();
            $connMotos = null;

            // Conexión externa (solo si hay un técnico motorizado real)
            if ($idTecnicoReal && ($tipoAsignacion === 'motorizado' || $origen === 'moto')) {
                try {
                    $connMotos = new PDO("mysql:host=127.0.0.1;dbname=inees_mantenimientos;charset=utf8mb4", "root", "");
                    $connMotos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $connMotos->beginTransaction();

                    $stmtTecMoto = $connMotos->prepare("SELECT id_tecnico FROM tecnico WHERE id_tecnico = :id");
                    $stmtTecMoto->execute([':id' => $idTecnicoReal]);

                    if (!$stmtTecMoto->fetchColumn()) {
                        throw new Exception("El motorizado seleccionado no existe en la BD externa.");
                    }
                } catch (PDOException $e) {
                    throw new Exception("Error conexión BD Motorizados: " . $e->getMessage());
                }
            }

            foreach ($items as $item) {
                $esManual = empty($item['id']) || $item['id'] === 'OTRO';
                $esRepuesto = ($item['tipo'] === 'repuesto');
                $idItem = $esManual ? null : intval($item['id']);
                $cantidad = intval($item['cantidad']);
                $codigoRef = $item['codigo'] ?? '';
                $columnaFiltro = $esRepuesto ? 'id_repuesto' : 'id_producto';

                if ($cantidad <= 0)
                    throw new Exception("Cantidad inválida para ítem.");

                // Descontar Stock
                if (!$esManual) {
                    $sqlStock = "SELECT cantidad_total FROM inventario_stock WHERE $columnaFiltro = :id FOR UPDATE";
                    $stmtS = $this->conn->prepare($sqlStock);
                    $stmtS->execute([':id' => $idItem]);
                    $stockActual = $stmtS->fetchColumn();

                    if ($stockActual === false || $stockActual < $cantidad) {
                        throw new Exception("Stock insuficiente para: " . ($item['nombre'] ?? ''));
                    }

                    $sqlUpd = "UPDATE inventario_stock SET cantidad_total = cantidad_total - :cant WHERE $columnaFiltro = :id";
                    $stmtU = $this->conn->prepare($sqlUpd);
                    $stmtU->execute([':cant' => $cantidad, ':id' => $idItem]);
                }

                $observacionFinal = "Salida - Origen: " . ($origen === 'moto' ? 'motorizado' : 'interno');
                $novedadItem = !empty($item['novedad']) ? trim($item['novedad']) : $novedad;

                // INSERT CORREGIDO APUNTANDO A movimientos_inventario
                $sqlMov = "INSERT INTO movimientos_inventario
                            (id_repuesto, id_producto, repuesto_manual, tipo_movimiento, cantidad, id_tecnico_destino,
                                destino, numero_remision, numero_cotizacion, novedad,
                                observacion, id_usuario_registra, fecha_movimiento)
                            VALUES
                            (:id_repuesto, :id_producto, :repuesto_manual, 'SALIDA', :cant, :idt,
                                :destino, :remision, :cotizacion, :novedad,
                                :obs, :ida, NOW())";

                $stmtM = $this->conn->prepare($sqlMov);
                $stmtM->bindValue(':id_repuesto', ($esRepuesto && !$esManual) ? $idItem : null, PDO::PARAM_INT);
                $stmtM->bindValue(':id_producto', (!$esRepuesto && !$esManual) ? $idItem : null, PDO::PARAM_INT);
                $stmtM->bindValue(':repuesto_manual', $esManual ? ($item['repuesto_manual'] ?? $item['nombre'] ?? null) : null);
                $stmtM->bindValue(':cant', $cantidad, PDO::PARAM_INT);
                $stmtM->bindValue(':idt', $idTecnicoReal, $idTecnicoReal ? PDO::PARAM_INT : PDO::PARAM_NULL);
                $stmtM->bindValue(':destino', $destino);
                $stmtM->bindValue(':remision', $remision);
                $stmtM->bindValue(':cotizacion', $cotizacion);
                $stmtM->bindValue(':novedad', $novedadItem);
                $stmtM->bindValue(':obs', $observacionFinal);
                $stmtM->bindValue(':ida', $idAdmin, PDO::PARAM_INT);
                $stmtM->execute();

                // Sincronización Motorizados
                if (!$esManual && $esRepuesto && $connMotos !== null && ($tipoAsignacion === 'motorizado' || $origen === 'moto')) {
                    if (empty($codigoRef) || $codigoRef === 'S/C')
                        throw new Exception("Repuesto sin código válido para App Externa.");

                    $stmtRepMoto = $connMotos->prepare("SELECT id_repuesto FROM repuesto WHERE codigo_referencia = :codigo");
                    $stmtRepMoto->execute([':codigo' => $codigoRef]);
                    $idRepuestoMoto = $stmtRepMoto->fetchColumn();

                    // --- NUEVA LÓGICA: Si no existe, lo creamos automáticamente ---
                    if (!$idRepuestoMoto) {
                        $sqlInsertMoto = "INSERT INTO repuesto (nombre_repuesto, codigo_referencia) VALUES (:nombre, :codigo)";
                        $stmtInsertMoto = $connMotos->prepare($sqlInsertMoto);
                        // Usamos el nombre que viene del carrito. Los demás campos tomarán su DEFAULT (ej. valor_venta = 0.00)
                        $stmtInsertMoto->execute([
                            ':nombre' => $item['nombre'] ?? 'Repuesto Sin Nombre',
                            ':codigo' => $codigoRef
                        ]);
                        // Capturamos el ID del repuesto recién creado en la otra base de datos
                        $idRepuestoMoto = $connMotos->lastInsertId();
                    }
                    // -------------------------------------------------------------

                    $sqlMoto = "INSERT INTO inventario_tecnico (id_tecnico, id_repuesto, cantidad_actual) 
                                VALUES (:idt, :idr, :cant) 
                                ON DUPLICATE KEY UPDATE cantidad_actual = cantidad_actual + :cant";
                    $stmtMoto = $connMotos->prepare($sqlMoto);
                    $stmtMoto->execute([':idt' => $idTecnicoReal, ':idr' => $idRepuestoMoto, ':cant' => $cantidad]);
                }
            }

            $this->conn->commit();
            if ($connMotos !== null)
                $connMotos->commit();

            return ['exito' => true, 'msg' => 'Salida procesada con éxito.'];

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