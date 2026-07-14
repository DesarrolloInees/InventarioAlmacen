<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class BodegaEditarModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerBodegaPorId($id) {
        try {
            $sql = "SELECT * FROM bodegas WHERE id_bodega = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            return false; 
        }
    }

    public function actualizarBodega($id, $nombre, $ubicacion, $estado) {
        try {
            $sql = "UPDATE bodegas SET nombre_bodega = :nombre, ubicacion = :ubicacion, estado = :estado WHERE id_bodega = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':ubicacion', $ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) { 
            return false; 
        }
    }
}