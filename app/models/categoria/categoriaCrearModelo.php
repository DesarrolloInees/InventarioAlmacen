<?php
// app/models/categoria/categoriaCrearModelo.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class CategoriaCrearModelo
{
    private $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function crearCategoria($datos) {
        try {
            $sql = "INSERT INTO categorias (nombre_categoria, descripcion, estado) VALUES (:nombre, :descripcion, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre_categoria']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear categoría: " . $e->getMessage());
            return false;
        }
    }

    public function existeCategoria($nombre) {
        if (empty($nombre)) return false;
        try {
            $sql = "SELECT COUNT(*) FROM categorias WHERE nombre_categoria = :nombre";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':nombre' => $nombre]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}