<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class HerramientaEliminarModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    // Borrado lógico: solo marca estado=0 y no rompe historiales ni FK
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
