<?php
// app/models/producto/productoEditarModelo.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class ProductoEditarModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Obtener los datos actuales del producto
    public function obtenerProductoPorId($id)
    {
        try {
            $sql = "SELECT * FROM productos WHERE id_producto = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Actualizar la información en la base de datos
    public function editarProducto($id, $datos)
    {
        try {
            $sql = "UPDATE productos SET 
                        codigo_interno = :codigo, 
                        nombre_producto = :nombre, 
                        valor_venta = :valor,
                        estado = :estado 
                    WHERE id_producto = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':codigo', $datos['codigo_interno']);
            $stmt->bindParam(':nombre', $datos['nombre_producto']);
            $stmt->bindParam(':valor', $datos['valor_venta']);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al editar producto: " . $e->getMessage());
            return false;
        }
    }

    // Validar que el nuevo código interno no le pertenezca a OTRO producto
    public function existeCodigoInternoExcluyendoId($codigo, $idExcluido)
    {
        if (empty($codigo)) return false;
        try {
            $sql = "SELECT COUNT(*) FROM productos WHERE codigo_interno = :codigo AND id_producto != :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':codigo' => $codigo, ':id' => $idExcluido]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}