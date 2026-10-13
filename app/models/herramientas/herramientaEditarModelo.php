<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class HerramientaEditarModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerHerramientaPorId($id) {
        try {
            $sql = "SELECT * FROM herramientas WHERE id_herramienta = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return false; }
    }

    public function obtenerBodegasActivas() {
        try {
            $sql = "SELECT id_bodega, nombre_bodega FROM bodegas WHERE estado = 1 ORDER BY nombre_bodega ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function actualizarHerramienta($id, $nombre, $marca, $modelo, $codigo, $descripcion, $condicion, $stock, $id_bodega) {
        try {
            $sql = "UPDATE herramientas 
                    SET nombre_herramienta = :nombre,
                        marca = :marca,
                        modelo = :modelo,
                        codigo_referencia = :codigo,
                        descripcion = :descripcion,
                        condicion = :condicion,
                        stock = :stock,
                        id_bodega = :bodega
                    WHERE id_herramienta = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':marca', $marca, $marca === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':modelo', $modelo, $modelo === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':codigo', $codigo, $codigo === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $descripcion, $descripcion === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':condicion', $condicion, PDO::PARAM_STR);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindValue(':bodega', $id_bodega, $id_bodega === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) { 
            if ($e->getCode() == 23000) {
                return 'DUPLICADO';
            }
            error_log("Error al actualizar herramienta: " . $e->getMessage());
            return false; 
        }
    }
}
