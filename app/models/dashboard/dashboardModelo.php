<?php
// app/models/dashboard/dashboardModelo.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class DashboardModelo {
    private $conn;
    public function __construct(PDO $db) { $this->conn = $db; }

    // 1. Estadísticas Globales Híbridas (Suma Repuestos + Productos)
    public function obtenerMetricasGlobales() {
        try {
            $sql = "SELECT 
                        (SELECT SUM(cantidad_total) FROM inventario_stock) as total_unidades,
                        
                        (SELECT SUM(i.cantidad_total * COALESCE(r.valor_venta, p.valor_venta, 0)) 
                            FROM inventario_stock i 
                            LEFT JOIN repuestos r ON i.id_repuesto = r.id_repuesto 
                            LEFT JOIN productos p ON i.id_producto = p.id_producto) as valor_inventario,

                        (SELECT COUNT(*) FROM inventario_stock WHERE cantidad_total <= 5) as items_bajo_stock,
                        
                        ((SELECT COUNT(*) FROM repuestos WHERE estado = 1) + 
                            (SELECT COUNT(*) FROM productos WHERE estado = 1)) as total_catalogo";
                        
            return $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // 2. Datos para el Gráfico (Movimientos últimos 7 días)
    public function obtenerMovimientosRecientes() {
        try {
            $sql = "SELECT 
                        DATE(fecha_movimiento) as fecha,
                        SUM(CASE WHEN tipo_movimiento = 'ENTRADA' THEN cantidad ELSE 0 END) as entradas,
                        SUM(CASE WHEN tipo_movimiento = 'SALIDA' THEN cantidad ELSE 0 END) as salidas
                    FROM movimientos_inventario 
                    WHERE fecha_movimiento >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    GROUP BY DATE(fecha_movimiento)
                    ORDER BY fecha ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // 3. Alertas de Stock Bajo Unificadas (Repuestos o Consumibles)
    public function obtenerAlertasStock() {
        try {
            $sql = "SELECT 
                        COALESCE(r.nombre_repuesto, p.nombre_producto) as nombre_articulo, 
                        i.cantidad_total, 
                        COALESCE(r.codigo_referencia, p.codigo_interno, 'S/C') as codigo
                    FROM inventario_stock i
                    LEFT JOIN repuestos r ON i.id_repuesto = r.id_repuesto
                    LEFT JOIN productos p ON i.id_producto = p.id_producto
                    WHERE i.cantidad_total <= 5
                    ORDER BY i.cantidad_total ASC
                    LIMIT 5";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }
}