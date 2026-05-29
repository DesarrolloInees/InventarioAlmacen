<?php
// app/models/repuesto/repuestoEditarModelo.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class RepuestoEditarModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerCategoriasActivas()
    {
        try {
            $sql = "SELECT id_categoria, nombre_categoria FROM categorias WHERE estado = 1 ORDER BY nombre_categoria ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerRepuestoPorId($id)
    {
        try {
            $sql = "SELECT * FROM repuestos WHERE id_repuesto = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function editarRepuesto($id, $datos)
    {
        try {
            $sql = "UPDATE repuestos SET 
                        codigo_referencia = :codigo, 
                        condicion = :condicion, 
                        nombre_repuesto = :nombre, 
                        valor_venta = :valor,
                        estado = :estado,
                        id_categoria = :id_categoria
                    WHERE id_repuesto = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':codigo', $datos['codigo_referencia']);
            $stmt->bindParam(':condicion', $datos['condicion']);
            $stmt->bindParam(':nombre', $datos['nombre_repuesto']);
            $stmt->bindParam(':valor', $datos['valor_venta']);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindValue(':id_categoria', !empty($datos['id_categoria']) ? $datos['id_categoria'] : null, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function existeCodigoExcluyendoId($codigo, $idExcluido)
    {
        if (empty($codigo))
            return false;
        try {
            $sql = "SELECT COUNT(*) FROM repuestos WHERE codigo_referencia = :codigo AND id_repuesto != :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':codigo' => $codigo, ':id' => $idExcluido]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}