<?php
// app/models/tipoMaquina/tipoMaquinaCrearModelo.php

if (!defined('ENTRADA_PRINCIPAL')) {
    die("Acceso denegado.");
}

class TipoMaquinaCrearModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function crearTipoMaquina($datos)
    {
        try {
            $sql = "INSERT INTO tipomaquina (
                        nombreTipoMaquina, 
                        estado
                    ) VALUES (
                        :nombre, 
                        :estado
                    )";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':nombre', $datos['nombreTipoMaquina']);
            $estado = !empty($datos['estado']) ? $datos['estado'] : 'activo';
            $stmt->bindParam(':estado', $estado);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error Base de Datos (crearTipoMaquina): " . $e->getMessage());
            return false;
        }
    }

    public function existeTipoMaquina($nombre)
    {
        try {
            // Verificamos si el nombre ya existe para no romper el UNIQUE de la BD
            $sql = "SELECT COUNT(*) FROM tipomaquina WHERE nombreTipoMaquina = :nombre";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}