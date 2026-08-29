<?php
// app/models/dashboard/dashboardModelo.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class DashboardModelo {
    private $conn;      // solicitudprosegur (usuarios, solicitudes...)
    private $connInv;   // inventario-almacen (productos, repuestos, inventario_stock, movimientos_inventario)

    // Umbral fijo de "stock bajo" (no existe columna stock_minimo en la BD real)
    private const UMBRAL_STOCK_BAJO = 5;

    public function __construct(PDO $db, PDO $dbInventario) {
        $this->conn = $db;
        $this->connInv = $dbInventario;
    }

    // 1. Métricas globales del inventario
    public function obtenerMetricasGlobales() {
        try {
            $umbral = self::UMBRAL_STOCK_BAJO;

            // Parte 1: viene de inventario-almacen
            $sqlInv = "SELECT 
                        (SELECT COALESCE(SUM(cantidad_total), 0) FROM inventario_stock) as total_unidades,

                        (SELECT COUNT(*) FROM inventario_stock 
                            WHERE cantidad_total <= $umbral) as items_bajo_stock,

                        (SELECT COUNT(*) FROM inventario_stock 
                            WHERE cantidad_total = 0) as items_agotados,

                        (
                            (SELECT COUNT(*) FROM productos WHERE estado = 1) +
                            (SELECT COUNT(*) FROM repuestos WHERE estado = 1)
                        ) as total_catalogo";

            $metricasInv = $this->connInv->query($sqlInv)->fetch(PDO::FETCH_ASSOC);

            // Parte 2: viene de solicitudprosegur
            $sqlSol = "SELECT COUNT(*) as solicitudes_pendientes 
                        FROM solicitudes 
                        WHERE estado = 'pendiente'";

            $metricasSol = $this->conn->query($sqlSol)->fetch(PDO::FETCH_ASSOC);

            // Combinamos ambos resultados en un solo arreglo
            return array_merge($metricasInv, $metricasSol);
        } catch (PDOException $e) { 
            error_log("Error obtenerMetricasGlobales: " . $e->getMessage());
            return []; 
        }
    }

    // 2. Datos para el gráfico (movimientos últimos 7 días)
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
            $stmt = $this->connInv->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Error obtenerMovimientosRecientes: " . $e->getMessage());
            return []; 
        }
    }

    // 3. Alertas de stock bajo (cantidad_total <= umbral fijo), combinando productos y repuestos
    public function obtenerAlertasStock() {
        try {
            $umbral = self::UMBRAL_STOCK_BAJO;

            $sql = "SELECT nombre_articulo, codigo, cantidad_total FROM (
                        SELECT 
                            CAST(p.nombre_producto AS CHAR CHARACTER SET utf8mb4) AS nombre_articulo,
                            CAST(COALESCE(p.codigo_interno, 'S/C') AS CHAR CHARACTER SET utf8mb4) AS codigo,
                            i.cantidad_total
                        FROM inventario_stock i
                        INNER JOIN productos p ON i.id_producto = p.id_producto
                        WHERE i.cantidad_total <= $umbral

                        UNION ALL

                        SELECT 
                            CAST(r.nombre_repuesto AS CHAR CHARACTER SET utf8mb4) AS nombre_articulo,
                            CAST(COALESCE(r.codigo_referencia, 'S/C') AS CHAR CHARACTER SET utf8mb4) AS codigo,
                            i.cantidad_total
                        FROM inventario_stock i
                        INNER JOIN repuestos r ON i.id_repuesto = r.id_repuesto
                        WHERE i.cantidad_total <= $umbral
                    ) AS alertas
                    ORDER BY cantidad_total ASC
                    LIMIT 5";

            return $this->connInv->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Error obtenerAlertasStock: " . $e->getMessage());
            return []; 
        }
    }

    // 4. Últimas solicitudes registradas
    public function obtenerSolicitudesRecientes() {
        try {
            $sql = "SELECT 
                        s.solicitud_id,
                        s.asunto,
                        s.prioridad,
                        s.estado,
                        s.fecha_solicitud,
                        u.nombre AS nombre_solicitante
                    FROM solicitudes s
                    INNER JOIN usuarios u ON s.usuario_id = u.usuario_id
                    ORDER BY s.fecha_solicitud DESC
                    LIMIT 5";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Error obtenerSolicitudesRecientes: " . $e->getMessage());
            return []; 
        }
    }
}