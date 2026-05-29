<?php
// app/models/producto/productoCrearModelo.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class ProductoCrearModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Guardar el nuevo producto consumible
    public function crearProducto($datos)
    {
        try {
            $sql = "INSERT INTO productos (codigo_interno, nombre_producto, valor_venta, estado) 
                    VALUES (:codigo, :nombre, :valor, 1)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':codigo', $datos['codigo_interno']);
            $stmt->bindParam(':nombre', $datos['nombre_producto']);
            $stmt->bindParam(':valor', $datos['valor_venta']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear producto: " . $e->getMessage());
            return false;
        }
    }

    // Validar si el código interno ya está registrado para evitar duplicados
    public function existeCodigoInterno($codigo)
    {
        if (empty($codigo)) return false;
        try {
            $sql = "SELECT COUNT(*) FROM productos WHERE codigo_interno = :codigo";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':codigo' => $codigo]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}