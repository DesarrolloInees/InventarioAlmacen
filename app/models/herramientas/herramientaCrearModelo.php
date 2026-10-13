<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class HerramientaCrearModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerBodegasActivas() {
        try {
            $sql = "SELECT id_bodega, nombre_bodega FROM bodegas WHERE estado = 1 ORDER BY nombre_bodega ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function insertarHerramienta($nombre, $marca, $modelo, $codigo, $descripcion, $condicion, $stock, $id_bodega, $id_usuario_registra) {
        try {
            $sql = "INSERT INTO herramientas (nombre_herramienta, marca, modelo, codigo_referencia, descripcion, condicion, stock, id_bodega, id_usuario_registra)
                    VALUES (:nombre, :marca, :modelo, :codigo, :descripcion, :condicion, :stock, :bodega, :usuario)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':marca', $marca, $marca === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':modelo', $modelo, $modelo === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':codigo', $codigo, $codigo === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $descripcion, $descripcion === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':condicion', $condicion, PDO::PARAM_STR);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindValue(':bodega', $id_bodega, $id_bodega === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':usuario', $id_usuario_registra, $id_usuario_registra === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) { 
            // Código 23000 = violación UNIQUE (código de referencia duplicado)
            if ($e->getCode() == 23000) {
                return 'DUPLICADO';
            }
            error_log("Error al insertar herramienta: " . $e->getMessage());
            return false; 
        }
    }
}
