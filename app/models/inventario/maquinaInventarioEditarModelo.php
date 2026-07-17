<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class MaquinaInventarioEditarModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerMaquinaPorId($id) {
        try {
            $sql = "SELECT * FROM inventario_maquinas WHERE id_maquina = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return false; }
    }

    public function obtenerTiposMaquina() {
        try {
            $sql = "SELECT idTipoMaquina, nombre_tipo FROM tipomaquina ORDER BY nombre_tipo ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function obtenerBodegasActivas() {
        try {
            $sql = "SELECT id_bodega, nombre_bodega FROM bodegas WHERE estado = 1 ORDER BY nombre_bodega ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function actualizarMaquina($id, $idTipoMaquina, $id_bodega, $numero_serie, $condicion, $estado_remision) {
        try {
            $sql = "UPDATE inventario_maquinas 
                    SET idTipoMaquina = :idTipo, 
                        id_bodega = :idBodega, 
                        numero_serie = :serial, 
                        condicion = :condicion, 
                        estado_remision = :remision 
                    WHERE id_maquina = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':idTipo', $idTipoMaquina, PDO::PARAM_INT);
            $stmt->bindValue(':idBodega', $id_bodega, $id_bodega === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':serial', $numero_serie, PDO::PARAM_STR);
            $stmt->bindParam(':condicion', $condicion, PDO::PARAM_STR);
            $stmt->bindValue(':remision', $estado_remision, $estado_remision === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) { 
            if ($e->getCode() == 23000) {
                return 'DUPLICADO';
            }
            return false; 
        }
    }
}