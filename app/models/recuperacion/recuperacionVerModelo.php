<?php
// app/models/recuperacion/recuperacionVerModelo.php
// Historial SOLO RECUPERADO. No toca compras.

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class RecuperacionVerModelo
{
    private $conn;
    public function __construct(PDO $db) { $this->conn = $db; }

    private function tieneCol($col)
    {
        try {
            $st = $this->conn->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'movimientos_inventario' AND COLUMN_NAME = :c");
            $st->execute([':c' => $col]);
            return (bool)$st->fetchColumn();
        } catch (PDOException $e) { return false; }
    }

    public function obtenerHistorialRecuperados()
    {
        try {
            $conOrigen = $this->tieneCol('origen_entrada');
            $conSerial = $this->tieneCol('serial_recuperado');
            $conTec = $this->tieneCol('id_tecnico_origen');
            $selOrigen = $conOrigen ? "m.origen_entrada," : "NULL AS origen_entrada,";
            $selSerial = $conSerial ? "m.serial_recuperado," : "NULL AS serial_recuperado,";
            $selTec = $conTec ? "m.id_tecnico_origen," : "NULL AS id_tecnico_origen,";
            $joinTec = $conTec ? "LEFT JOIN tecnicos_locales t ON m.id_tecnico_origen = t.id_tecnico_local" : "";
            $selTecNom = $conTec ? "t.nombre_tecnico AS tecnico_origen," : "NULL AS tecnico_origen,";
            $where = $conOrigen
                ? "WHERE m.tipo_movimiento = 'ENTRADA' AND m.origen_entrada = 'RECUPERADO'"
                : "WHERE m.tipo_movimiento = 'ENTRADA' AND m.precio_compra IS NULL AND m.numero_factura IS NULL AND m.id_proveedor IS NULL";
            $conNom = $this->tieneCol('tecnico_origen_nombre');
            $selNom = $conNom ? "m.tecnico_origen_nombre," : "NULL AS tecnico_origen_nombre,";
            $sql = "SELECT m.id_movimiento, m.cantidad, m.repuesto_manual, m.novedad, m.observacion, m.fecha_movimiento, m.id_repuesto, m.id_producto, $selOrigen $selSerial $selTec $selNom r.nombre_repuesto, r.codigo_referencia, r.condicion, p.nombre_producto, p.codigo_interno, $selTecNom u.nombre as usuario_nombre FROM movimientos_inventario m LEFT JOIN repuestos r ON m.id_repuesto = r.id_repuesto LEFT JOIN productos p ON m.id_producto = p.id_producto $joinTec INNER JOIN usuarios u ON m.id_usuario_registra = u.usuario_id $where ORDER BY m.fecha_movimiento DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$r) {
                if (empty($r['tecnico_origen']) && !empty($r['tecnico_origen_nombre'])) $r['tecnico_origen'] = $r['tecnico_origen_nombre'] . ' (Motorizado)';
            }
            return $rows;
        } catch (PDOException $e) {
            error_log("Error historial recuperados: " . $e->getMessage());
            return [];
        }
    }

    public function eliminarRecuperado($idMovimiento)
    {
        try {
            $this->conn->beginTransaction();
            $conOrigen = $this->tieneCol('origen_entrada');
            $sqlInfo = "SELECT id_repuesto, id_producto, cantidad" . ($conOrigen ? ", origen_entrada" : "") . " FROM movimientos_inventario WHERE id_movimiento = :id AND tipo_movimiento = 'ENTRADA'";
            $stmtInfo = $this->conn->prepare($sqlInfo);
            $stmtInfo->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtInfo->execute();
            $mov = $stmtInfo->fetch(PDO::FETCH_ASSOC);
            if (!$mov) { $this->conn->rollBack(); return false; }
            if ($conOrigen && ($mov['origen_entrada'] ?? null) !== 'RECUPERADO') { $this->conn->rollBack(); return 'no_permitido'; }
            if (!empty($mov['id_repuesto'])) {
                $col = 'id_repuesto'; $tid = $mov['id_repuesto'];
            } elseif (!empty($mov['id_producto'])) {
                $col = 'id_producto'; $tid = $mov['id_producto'];
            } else { $col = null; }
            if ($col) {
                $st = $this->conn->prepare("SELECT cantidad_total FROM inventario_stock WHERE $col = :id");
                $st->execute([':id' => $tid]);
                $stock = $st->fetchColumn();
                if ($stock < $mov['cantidad']) { $this->conn->rollBack(); return 'error_stock'; }
                $rs = $this->conn->prepare("UPDATE inventario_stock SET cantidad_total = cantidad_total - :c WHERE $col = :id");
                $rs->execute([':c' => $mov['cantidad'], ':id' => $tid]);
            }
            $del = $this->conn->prepare("DELETE FROM movimientos_inventario WHERE id_movimiento = :id");
            $del->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $del->execute();
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            error_log("Error eliminar recuperado: " . $e->getMessage());
            return false;
        }
    }
}
