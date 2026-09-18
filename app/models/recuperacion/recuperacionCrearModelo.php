<?php
// app/models/recuperacion/recuperacionCrearModelo.php
// Entradas RECUPERADO (tecnicos) -> movimientos_inventario + inventario_stock.
// NO toca el flujo de COMPRA: precio/factura/proveedor quedan NULL.

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class RecuperacionCrearModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function tieneSoporteRecuperado()
    {
        try {
            $st = $this->conn->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'movimientos_inventario' AND COLUMN_NAME = 'origen_entrada'");
            $st->execute();
            return (bool)$st->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerRepuestosActivos()
    {
        try {
            $sql = "SELECT id_repuesto, codigo_referencia, nombre_repuesto, condicion FROM repuestos WHERE estado = 1 ORDER BY nombre_repuesto ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerProductosActivos()
    {
        try {
            $sql = "SELECT id_producto, codigo_interno, nombre_producto FROM productos WHERE estado = 1 ORDER BY nombre_producto ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerTecnicosLocales()
    {
        try {
            $sql = "SELECT id_tecnico_local, nombre_tecnico FROM tecnicos_locales WHERE estado = 1 ORDER BY nombre_tecnico ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    public function obtenerTecnicosOrigen()
    {
        $listaFinal = [];
        try {
            $sql = "SELECT id_tecnico_local, nombre_tecnico FROM tecnicos_locales WHERE estado = 1 ORDER BY nombre_tecnico ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $loc) {
                $listaFinal[] = [
                    'value' => 'local_' . $loc['id_tecnico_local'],
                    'nombre' => $loc['nombre_tecnico'],
                    'cargo' => 'Taller / Interno'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error cargando tecnicos locales origen: " . $e->getMessage());
        }
        try {
            $connMotos = new PDO("mysql:host=127.0.0.1;dbname=inees_mantenimientos;charset=utf8mb4", "root", "");
            $connMotos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sqlM = "SELECT id_tecnico, nombre_tecnico FROM tecnico WHERE estado = 1 ORDER BY nombre_tecnico ASC";
            $stmtM = $connMotos->prepare($sqlM);
            $stmtM->execute();
            foreach ($stmtM->fetchAll(PDO::FETCH_ASSOC) as $mot) {
                $listaFinal[] = [
                    'value' => 'moto_' . $mot['id_tecnico'],
                    'nombre' => $mot['nombre_tecnico'],
                    'cargo' => 'Tecnico Motorizado (Ruta)'
                ];
            }
        } catch (Throwable $e) {
            error_log("Error cargando tecnicos externos origen: " . $e->getMessage());
        }
        return $listaFinal;
    }
    // Crea un repuesto en el catalogo cuando la entrada es manual.
    // Debe llamarse DENTRO de la transaccion de registrarRecuperado.
    private function insertarRepuestoManual($cfg)
    {
        $nombre = trim($cfg['nombre'] ?? '');
        if ($nombre === '') return 0;
        $condicion = in_array($cfg['condicion'] ?? '', ['nuevo', 'recuperado', 'por revisar']) ? $cfg['condicion'] : 'recuperado';
        $codigo = strtoupper(trim($cfg['codigo'] ?? ''));
        $stCod = $this->conn->prepare("SELECT COUNT(*) FROM repuestos WHERE codigo_referencia = :c");
        if ($codigo !== '') {
            $stCod->execute([':c' => $codigo]);
            if ((int)$stCod->fetchColumn() > 0) {
                throw new PDOException("El codigo '$codigo' ya existe en el catalogo.");
            }
        } else {
            do {
                $codigo = 'REC-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
                $stCod->execute([':c' => $codigo]);
            } while ((int)$stCod->fetchColumn() > 0);
        }
        $ins = $this->conn->prepare("INSERT INTO repuestos (id_categoria, codigo_referencia, condicion, nombre_repuesto, valor_venta, estado) VALUES (NULL, :cod, :cond, :nom, 0.00, 1)");
        $ins->execute([':cod' => $codigo, ':cond' => $condicion, ':nom' => $nombre]);
        return (int)$this->conn->lastInsertId();
    }

    // Guarda ENTRADA RECUPERADO y suma stock. Sin factura/proveedor/precio.
    // Si viene crear_repuesto (entrada manual), primero crea el repuesto en catalogo.
    public function registrarRecuperado($datos)
    {
        try {
            $this->conn->beginTransaction();
            $idRepuesto = !empty($datos['id_repuesto']) ? intval($datos['id_repuesto']) : null;
            $idProducto = !empty($datos['id_producto']) ? intval($datos['id_producto']) : null;
            $manualNombre = $datos['repuesto_manual'] ?? null;
            if (empty($idRepuesto) && empty($idProducto) && !empty($datos['crear_repuesto']['nombre'])) {
                $idRepuesto = $this->insertarRepuestoManual($datos['crear_repuesto']);
                if (!$idRepuesto) throw new PDOException("No se pudo crear el repuesto desde la entrada manual.");
                $manualNombre = null;
            }
            $conOrigen = $this->tieneSoporteRecuperado();
            if ($conOrigen) {
                $sqlMov = "INSERT INTO movimientos_inventario (id_repuesto, id_producto, repuesto_manual, tipo_movimiento, cantidad, origen_entrada, serial_recuperado, id_tecnico_origen, tecnico_origen_nombre, novedad, observacion, id_usuario_registra, fecha_movimiento) VALUES (:id_repuesto, :id_producto, :repuesto_manual, 'ENTRADA', :cantidad, 'RECUPERADO', :serial_recuperado, :id_tecnico_origen, :tecnico_origen_nombre, :novedad, :observacion, :id_usuario_registra, :fecha_movimiento)";
            } else {
                $sqlMov = "INSERT INTO movimientos_inventario (id_repuesto, id_producto, repuesto_manual, tipo_movimiento, cantidad, novedad, observacion, id_usuario_registra, fecha_movimiento) VALUES (:id_repuesto, :id_producto, :repuesto_manual, 'ENTRADA', :cantidad, :novedad, :observacion, :id_usuario_registra, :fecha_movimiento)";
            }
            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->bindValue(':id_repuesto', $idRepuesto, PDO::PARAM_INT);
            $stmtMov->bindValue(':id_producto', $idProducto, PDO::PARAM_INT);
            if (empty($idRepuesto) && empty($idProducto)) {
                $stmtMov->bindValue(':repuesto_manual', $manualNombre);
            } else {
                $stmtMov->bindValue(':repuesto_manual', null, PDO::PARAM_NULL);
            }
            $stmtMov->bindValue(':cantidad', $datos['cantidad'], PDO::PARAM_INT);
            $stmtMov->bindValue(':novedad', $datos['novedad']);
            $stmtMov->bindValue(':observacion', $datos['observacion']);
            $stmtMov->bindValue(':id_usuario_registra', $datos['id_usuario_registra'], PDO::PARAM_INT);
            $stmtMov->bindValue(':fecha_movimiento', $datos['fecha_movimiento']);
            if ($conOrigen) {
                $stmtMov->bindValue(':serial_recuperado', $datos['serial_recuperado']);
                $stmtMov->bindValue(':id_tecnico_origen', $datos['id_tecnico_origen']);
                $stmtMov->bindValue(':tecnico_origen_nombre', $datos['tecnico_origen_nombre']);
            }
            $stmtMov->execute();
            $targetCol = null;
            $targetId = null;
            if (!empty($idRepuesto)) {
                $targetCol = 'id_repuesto';
                $targetId = $idRepuesto;
            } elseif (!empty($idProducto)) {
                $targetCol = 'id_producto';
                $targetId = $idProducto;
            }
            if ($targetCol) {
                $stmtCheck = $this->conn->prepare("SELECT id_stock FROM inventario_stock WHERE $targetCol = :id LIMIT 1");
                $stmtCheck->execute([':id' => $targetId]);
                if ($stmtCheck->rowCount() > 0) {
                    $stmtUpd = $this->conn->prepare("UPDATE inventario_stock SET cantidad_total = cantidad_total + :cantidad WHERE $targetCol = :id");
                    $stmtUpd->execute([':cantidad' => $datos['cantidad'], ':id' => $targetId]);
                } else {
                    $stmtIns = $this->conn->prepare("INSERT INTO inventario_stock ($targetCol, cantidad_total) VALUES (:id, :cantidad)");
                    $stmtIns->execute([':id' => $targetId, ':cantidad' => $datos['cantidad']]);
                }
            }
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            error_log("Error en transaccion de recuperado: " . $e->getMessage());
            return false;
        }
    }

}
