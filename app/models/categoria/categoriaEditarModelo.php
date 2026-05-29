<?php
// app/models/categoria/categoriaEditarModelo.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class CategoriaEditarModelo
{
    private $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function obtenerCategoriaPorId($id) {
        try {
            $sql = "SELECT * FROM categorias WHERE id_categoria = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function editarCategoria($id, $datos) {
        try {
            $sql = "UPDATE categorias SET 
                        nombre_categoria = :nombre, 
                        descripcion = :descripcion, 
                        estado = :estado 
                    WHERE id_categoria = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre_categoria']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al editar categoría: " . $e->getMessage());
            return false;
        }
    }

    public function existeCategoriaExcluyendoId($nombre, $idExcluido) {
        if (empty($nombre)) return false;
        try {
            $sql = "SELECT COUNT(*) FROM categorias WHERE nombre_categoria = :nombre AND id_categoria != :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':nombre' => $nombre, ':id' => $idExcluido]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}