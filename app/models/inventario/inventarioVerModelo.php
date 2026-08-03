<?php
// app/models/inventario/inventarioVerModelo.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class InventarioVerModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerInventarioCompleto()
    {
        try {
            $sql = "SELECT 
                        i.id_stock,
                        i.cantidad_total,
                        i.id_repuesto,
                        i.id_producto,
                        r.codigo_referencia,
                        r.nombre_repuesto,
                        r.condicion,
                        r.valor_venta AS valor_repuesto,
                        p.codigo_interno,
                        p.nombre_producto,
                        p.valor_venta AS valor_producto
                    FROM inventario_stock i
                    LEFT JOIN repuestos r ON i.id_repuesto = r.id_repuesto
                    LEFT JOIN productos p ON i.id_producto = p.id_producto
                    ORDER BY i.cantidad_total DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener inventario: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarStockManual($id_stock, $nueva_cantidad)
    {
        try {
            $sql = "UPDATE inventario_stock SET cantidad_total = :cantidad WHERE id_stock = :id_stock";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':cantidad', $nueva_cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':id_stock', $id_stock, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar stock manual: " . $e->getMessage());
            return false;
        }
    }

    // Elimina un registro puntual de inventario_stock (no toca repuestos/productos ni el historial
    // de movimientos_inventario, así que no debería chocar con llaves foráneas). Sirve para "limpiar"
    // registros cargados por error y poder volver a ingresarlos bien después.
    public function eliminarStock($id_stock)
    {
        try {
            $sql = "DELETE FROM inventario_stock WHERE id_stock = :id_stock";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_stock', $id_stock, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar stock: " . $e->getMessage());
            return false;
        }
    }
}