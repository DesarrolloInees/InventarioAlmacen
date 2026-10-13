<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class HerramientaVerModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerHerramientas() {
        try {
            $sql = "SELECT 
                        h.id_herramienta,
                        h.nombre_herramienta,
                        h.marca,
                        h.modelo,
                        h.codigo_referencia,
                        h.descripcion,
                        h.condicion,
                        h.stock,
                        h.estado,
                        h.fecha_registro,
                        b.nombre_bodega,
                        u.nombre AS usuario_registra
                    FROM herramientas h
                    LEFT JOIN bodegas b ON h.id_bodega = b.id_bodega
                    LEFT JOIN usuarios u ON h.id_usuario_registra = u.usuario_id
                    WHERE h.estado = 1
                    ORDER BY h.nombre_herramienta ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Error al obtener herramientas: " . $e->getMessage());
            return []; 
        }
    }

    // Borrado lógico (estado = 0), conserva el historial
    public function eliminarHerramienta($id) {
        try {
            $sql = "UPDATE herramientas SET estado = 0 WHERE id_herramienta = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) { 
            error_log("Error al eliminar herramienta: " . $e->getMessage());
            return false; 
        }
    }
}
