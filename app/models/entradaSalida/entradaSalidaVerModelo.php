<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class EntradaSalidaVerModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerTodosLosMovimientos() {
        try {
            // Agregamos el LEFT JOIN para la tabla productos
            $sql = "SELECT m.*, 
                            r.nombre_repuesto, r.codigo_referencia, 
                            p.nombre_producto, p.codigo_interno,
                            u.nombre as nombre_usuario
                    FROM movimientos_inventario m
                    LEFT JOIN repuestos r ON m.id_repuesto = r.id_repuesto
                    LEFT JOIN productos p ON m.id_producto = p.id_producto
                    LEFT JOIN usuarios u ON m.id_usuario_registra = u.usuario_id
                    ORDER BY m.fecha_movimiento DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}