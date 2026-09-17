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
    // Guarda ENTRADA RECUPERADO y suma stock. Sin factura/proveedor/precio.
    public function registrarRecuperado($datos)
    {
        try {
            $this->conn->beginTransaction();
            $conOrigen = $this->tieneSoporteRecuperado();
            if ($conOrigen) {
                $sqlMov = "INSERT INTO movimientos_inventario (id_repuesto, id_producto, repuesto_manual, tipo_movimiento, cantidad, origen_entrada, serial_recuperado, id_tecnico_origen, tecnico_origen_nombre, novedad, observacion, id_usuario_registra, fecha_movimiento) VALUES (:id_repuesto, :id_producto, :repuesto_manual, 'ENTRADA', :cantidad, 'RECUPERADO', :serial_recuperado, :id_tecnico_origen, :tecnico_origen_nombre, :novedad, :observacion, :id_usuario_registra, :fecha_movimiento)";
            } else {
                $sqlMov = "INSERT INTO movimientos_inventario (id_repuesto, id_producto, repuesto_manual, tipo_movimiento, cantidad, novedad, observacion, id_usuario_registra, fecha_movimiento) VALUES (:id_repuesto, :id_producto, :repuesto_manual, 'ENTRADA', :cantidad, :novedad, :observacion, :id_usuario_registra, :fecha_movimiento)";
            }
            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->bindValue(':id_repuesto', !empty($datos['id_repuesto']) ? $datos['id_repuesto'] : null, PDO::PARAM_INT);
            $stmtMov->bindValue(':id_producto', !empty($datos['id_producto']) ? $datos['id_producto'] : null, PDO::PARAM_INT);
            if (empty($datos['id_repuesto']) && empty($datos['id_producto'])) {
                $stmtMov->bindValue(':repuesto_manual', $datos['repuesto_manual']);
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
            if (!empty($datos['id_repuesto'])) {
                $targetCol = 'id_repuesto';
                $targetId = $datos['id_repuesto'];
            } elseif (!empty($datos['id_producto'])) {
                $targetCol = 'id_producto';
                $targetId = $datos['id_producto'];
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
