<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class BodegaCrearModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function insertarBodega($nombre_bodega, $ubicacion, $estado) {
        try {
            $sql = "INSERT INTO bodegas (nombre_bodega, ubicacion, estado) VALUES (:nombre, :ubicacion, :estado)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre_bodega, PDO::PARAM_STR);
            $stmt->bindParam(':ubicacion', $ubicacion, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) { 
            return false; 
        }
    }
}