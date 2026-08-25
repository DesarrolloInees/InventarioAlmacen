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
    public function getMovimientos($fechaDesde = null, $fechaHasta = null)
    {
        $sql = "SELECT m.id_movimiento, m.*, 
                    r.nombre_repuesto, r.codigo_referencia,
                    p.nombre_producto, p.codigo_interno,
                    u.nombre as nombre_usuario
            FROM movimientos_inventario m
            LEFT JOIN repuestos r ON m.id_repuesto = r.id_repuesto
            LEFT JOIN productos p ON m.id_producto = p.id_producto
            LEFT JOIN usuarios u ON m.id_usuario_registra = u.usuario_id
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