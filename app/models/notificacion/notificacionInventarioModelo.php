<?php
// app/models/notificacion/notificacionInventarioModelo.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class NotificacionInventarioModelo
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function obtenerStockCritico($umbral)
    {
        try {
            $sql = "SELECT 
                        i.id_stock,
                        i.cantidad_total,
                        CASE 
                            WHEN i.id_repuesto IS NOT NULL THEN 'REPUESTO MÁQUINA'
                            ELSE 'CONSUMIBLE'
                        END AS tipo_articulo,
                        COALESCE(r.nombre_repuesto, p.nombre_producto) AS nombre_articulo,
                        COALESCE(r.codigo_referencia, p.codigo_interno, 'S/C') AS codigo_referencia,
                        COALESCE(r.condicion, 'N/A') AS condicion,
                        
                        /* MAGIA: Buscamos el último proveedor que nos vendió este artículo */
                        COALESCE(
                            (SELECT prov.nombre_proveedor 
                                FROM movimientos_inventario mi 
                                INNER JOIN proveedores prov ON mi.id_proveedor = prov.id_proveedor
                                WHERE mi.id_repuesto = i.id_repuesto AND mi.tipo_movimiento = 'ENTRADA'
                                ORDER BY mi.fecha_movimiento DESC LIMIT 1),
                            
                            (SELECT prov.nombre_proveedor 
                                FROM movimientos_inventario mi 
                                INNER JOIN proveedores prov ON mi.id_proveedor = prov.id_proveedor
                                WHERE mi.id_producto = i.id_producto AND mi.tipo_movimiento = 'ENTRADA'
                                ORDER BY mi.fecha_movimiento DESC LIMIT 1),
                            
                            'Sin registro de compra'
                        ) AS proveedor
                        
                    FROM inventario_stock i
                    LEFT JOIN repuestos r ON i.id_repuesto = r.id_repuesto
                    LEFT JOIN productos p ON i.id_producto = p.id_producto
                    WHERE i.cantidad_total <= :umbral
                    ORDER BY i.cantidad_total ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':umbral', $umbral, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerStockCritico: " . $e->getMessage());
            return [];
        }
    }
}