<?php
// app/models/tipoMaquina/tipoMaquinaEditarModelo.php

if (!defined('ENTRADA_PRINCIPAL')) {
    die("Acceso denegado.");
}

class TipoMaquinaEditarModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Obtener un solo tipo de máquina para llenar el formulario
    public function obtenerTipoMaquinaPorId($id)
    {
        try {
            $sql = "SELECT * FROM tipomaquina WHERE idTipoMaquina = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo tipo de máquina por ID: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar datos
    public function editarTipoMaquina($id, $datos)
    {
        try {
            $sql = "UPDATE tipomaquina SET 
                        nombreTipoMaquina = :nombre, 
                        estado = :estado
                    WHERE 
                        idTipoMaquina = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':nombre', $datos['nombreTipoMaquina']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizando tipo de máquina: " . $e->getMessage());
            return false;
        }
    }

    // Verificar que el nuevo nombre no lo tenga OTRA máquina distinta
    public function existeTipoMaquinaExcluyendoId($nombre, $idExcluido)
    {
        try {
            $sql = "SELECT COUNT(*) FROM tipomaquina WHERE nombreTipoMaquina = :nombre AND idTipoMaquina != :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id', $idExcluido, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}