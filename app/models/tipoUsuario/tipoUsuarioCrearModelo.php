<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class TipoUsuarioCrearModelo
{
    private $conn;
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function crearTipoUsuario($datos)
    {
        try {
            $sql = "INSERT INTO tipousuario (nombreTipoUsuario) VALUES (:nombre)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombreTipoUsuario']);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function existeTipoUsuario($nombre)
    {
        try {
            $sql = "SELECT COUNT(*) FROM tipousuario WHERE nombreTipoUsuario = :nombre";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}