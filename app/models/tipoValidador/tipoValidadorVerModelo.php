<?php
// app/models/tipoValidador/tipoValidadorVerModelo.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class TipoValidadorVerModelo
{
    private $conn;
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerTiposValidador()
    {
        try {
            $sql = "SELECT idTipoValidador, nombreTipoValidador, estado FROM tipovalidador ORDER BY nombreTipoValidador ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener validadores: " . $e->getMessage());
            return [];
        }
    }

    // NUEVA FUNCIÓN PARA ELIMINADO LÓGICO
    public function eliminarValidadorLogicamente($id)
    {
        try {
            $sql = "UPDATE tipovalidador SET estado = 'inactivo' WHERE idTipoValidador = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar (inactivar) validador: " . $e->getMessage());
            return false;
        }
    }
}