<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class TipoUsuarioEditarModelo
{
    private $conn;
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerTipoUsuarioPorId($id)
    {
        try {
            $sql = "SELECT *, nombre_rol AS nombreTipoUsuario FROM tipousuario WHERE idTipoUsuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    public function editarTipoUsuario($id, $datos)
    {
        try {
            $sql = "UPDATE tipousuario SET nombre_rol = :nombre WHERE idTipoUsuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombreTipoUsuario']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function existeTipoUsuarioExcluyendoId($nombre, $idExcluido)
    {
        try {
            $sql = "SELECT COUNT(*) FROM tipousuario WHERE nombre_rol = :nombre AND idTipoUsuario != :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id', $idExcluido, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}