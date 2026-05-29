<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class TipoValidadorCrearModelo
{
    private $conn;
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function crearTipoValidador($datos)
    {
        try {
            $sql = "INSERT INTO tipovalidador (nombreTipoValidador, estado) VALUES (:nombre, :estado)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombreTipoValidador']);
            $estado = !empty($datos['estado']) ? $datos['estado'] : 'activo';
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error BD: " . $e->getMessage());
            return false;
        }
    }

    public function existeTipoValidador($nombre)
    {
        try {
            $sql = "SELECT COUNT(*) FROM tipovalidador WHERE nombreTipoValidador = :nombre";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}