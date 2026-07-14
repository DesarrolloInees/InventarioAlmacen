<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class BodegaVerModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerBodegas() {
        try {
            $sql = "SELECT * FROM bodegas ORDER BY nombre_bodega ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            return []; 
        }
    }

    public function eliminarBodegaLogicamente($id) {
        try {
            $sql = "UPDATE bodegas SET estado = 0 WHERE id_bodega = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) { 
            return false; 
        }
    }
}