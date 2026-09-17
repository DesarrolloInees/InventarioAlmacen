<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class EntradaSalidaModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // KPIs generales del rango de fechas
    public function getKpisMovimientos($fechaDesde = null, $fechaHasta = null)
    {
        $sql = "SELECT
                COALESCE(SUM(CASE WHEN tipo_movimiento = 'ENTRADA' THEN cantidad ELSE 0 END), 0) as total_entradas,
                COALESCE(SUM(CASE WHEN tipo_movimiento = 'SALIDA' THEN cantidad ELSE 0 END), 0) as total_salidas,
                COALESCE(SUM(CASE WHEN tipo_movimiento = 'ENTRADA' THEN 1 ELSE 0 END), 0) as movimientos_entrada,
                COALESCE(SUM(CASE WHEN tipo_movimiento = 'SALIDA' THEN 1 ELSE 0 END), 0) as movimientos_salida,
                COALESCE(COUNT(DISTINCT COALESCE(id_repuesto, id_producto)), 0) as referencias_distintas
            FROM movimientos_inventario
            WHERE 1=1";

        if ($fechaDesde) {
            $sql .= " AND fecha_movimiento >= :fecha_desde";
        }
        if ($fechaHasta) {
            $sql .= " AND fecha_movimiento <= :fecha_hasta_fin";
        }

        $stmt = $this->conn->prepare($sql);
        if ($fechaDesde) {
            $fd = $fechaDesde . ' 00:00:00';
            $stmt->bindParam(':fecha_desde', $fd);
        }
        if ($fechaHasta) {
            $fh = $fechaHasta . ' 23:59:59';
            $stmt->bindParam(':fecha_hasta_fin', $fh);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listado completo de movimientos (con joins), filtrado por rango de fechas
    // Incluye tecnico origen de RECUPERADOS (local via JOIN + motorizado via texto).
    public function getMovimientos($fechaDesde = null, $fechaHasta = null)
    {
        $tieneOrigen = $this->tieneColumna('origen_entrada');
        $tieneSerial = $this->tieneColumna('serial_recuperado');
        $tieneTec = $this->tieneColumna('id_tecnico_origen');
        $tieneTecNom = $this->tieneColumna('tecnico_origen_nombre');

        $selOrigen = $tieneOrigen ? "m.origen_entrada," : "NULL AS origen_entrada,";
        $selSerial = $tieneSerial ? "m.serial_recuperado," : "NULL AS serial_recuperado,";
        $selTecId = $tieneTec ? "m.id_tecnico_origen," : "NULL AS id_tecnico_origen,";
        $selTecNom = $tieneTecNom ? "m.tecnico_origen_nombre," : "NULL AS tecnico_origen_nombre,";
        $joinTec = $tieneTec ? "LEFT JOIN tecnicos_locales t ON m.id_tecnico_origen = t.id_tecnico_local" : "";
        $selTecLocal = $tieneTec ? "t.nombre_tecnico AS tecnico_local," : "NULL AS tecnico_local,";

        $sql = "SELECT m.id_movimiento, m.*,
                    $selOrigen $selSerial $selTecId $selTecNom $selTecLocal
                    r.nombre_repuesto, r.codigo_referencia,
                    p.nombre_producto, p.codigo_interno,
                    u.nombre as nombre_usuario
            FROM movimientos_inventario m
            LEFT JOIN repuestos r ON m.id_repuesto = r.id_repuesto
            LEFT JOIN productos p ON m.id_producto = p.id_producto
            LEFT JOIN usuarios u ON m.id_usuario_registra = u.usuario_id
            $joinTec
            WHERE 1=1";

        if ($fechaDesde) {
            $sql .= " AND m.fecha_movimiento >= :fecha_desde";
        }
        if ($fechaHasta) {
            $sql .= " AND m.fecha_movimiento <= :fecha_hasta_fin";
        }

        $sql .= " ORDER BY m.fecha_movimiento DESC";

        $stmt = $this->conn->prepare($sql);
        if ($fechaDesde) {
            $fd = $fechaDesde . ' 00:00:00';
            $stmt->bindParam(':fecha_desde', $fd);
        }
        if ($fechaHasta) {
            $fh = $fechaHasta . ' 23:59:59';
            $stmt->bindParam(':fecha_hasta_fin', $fh);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function tieneColumna($col)
    {
        try {
            $st = $this->conn->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'movimientos_inventario' AND COLUMN_NAME = :c");
            $st->execute([':c' => $col]);
            return (bool)$st->fetchColumn();
        } catch (PDOException $e) { return false; }
    }

    public function actualizarFechaMovimiento($idMovimiento, $nuevaFecha)
{
    $sql = "UPDATE movimientos_inventario 
            SET fecha_movimiento = :fecha 
            WHERE id_movimiento = :id";
            
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':fecha', $nuevaFecha);
    $stmt->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
    return $stmt->execute();
}
}