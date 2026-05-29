<?php
// app/models/producto/productoVerModelo.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class ProductoVerModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerProductos()
    {
        try {
            $sql = "SELECT * FROM productos ORDER BY nombre_producto ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function eliminarProductoLogicamente($id)
    {
        try {
            // Pasamos a 0 (Inactivo) según la estructura de tu tabla
            $sql = "UPDATE productos SET estado = 0 WHERE id_producto = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}