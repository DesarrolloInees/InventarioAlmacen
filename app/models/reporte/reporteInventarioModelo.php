<?php
// app/models/reportes/reporteInventarioModelo.php

if (!defined('ENTRADA_PRINCIPAL')) {
    die("Acceso denegado.");
}

class ReporteInventarioModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerInventarioPorCondicion($condicion = 'todos')
    {
        try {
            // Hacemos LEFT JOIN para que traiga los repuestos aunque no tengan entrada en inventario_stock (mostrando 0)
            $sql = "SELECT 
                        r.id_repuesto, 
                        r.nombre_repuesto, 
                        r.codigo_referencia, 
                        r.condicion,
                        COALESCE(i.cantidad_total, 0) AS cantidad_en_stock
                    FROM 
                        repuestos r
                    LEFT JOIN 
                        inventario_stock i ON r.id_repuesto = i.id_repuesto
                    WHERE 
                        r.estado = 1";

            // Filtramos si no se seleccionó 'todos'
            if ($condicion !== 'todos') {
                $sql .= " AND r.condicion = :condicion";
            }

            $sql .= " ORDER BY r.nombre_repuesto ASC";

            $stmt = $this->conn->prepare($sql);

            if ($condicion !== 'todos') {
                $stmt->bindParam(':condicion', $condicion, PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener inventario por condición: " . $e->getMessage());
            return [];
        }
    }
}