<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class TipoValidadorEditarModelo
{
    private $conn;
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerTipoValidadorPorId($id)
    {
        try {
            $sql = "SELECT * FROM tipovalidador WHERE idTipoValidador = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    public function editarTipoValidador($id, $datos)
    {
        try {
            $sql = "UPDATE tipovalidador SET nombreTipoValidador = :nombre, estado = :estado WHERE idTipoValidador = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombreTipoValidador']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function existeTipoValidadorExcluyendoId($nombre, $idExcluido)
    {
        try {
            $sql = "SELECT COUNT(*) FROM tipovalidador WHERE nombreTipoValidador = :nombre AND idTipoValidador != :id";
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