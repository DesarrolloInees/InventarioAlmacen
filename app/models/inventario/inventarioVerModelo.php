<?php
// app/models/inventario/inventarioVerModelo.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

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
}