<?php
// app/models/tipo_maquina/tipoMaquinaVerModelo.php

if (!defined('ENTRADA_PRINCIPAL')) {
    die("Acceso denegado.");
}

class TipoMaquinaVerModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerTiposMaquina()
    {
        try {
            // Ajustado a la tabla tipomaquina con camelCase
            $sql = "SELECT 
                        idTipoMaquina, 
                        nombreTipoMaquina, 
                        estado 
                    FROM 
                        tipomaquina 
                    ORDER BY 
                        nombreTipoMaquina ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener tipos de máquina: " . $e->getMessage());
            return [];
        }
    }
}