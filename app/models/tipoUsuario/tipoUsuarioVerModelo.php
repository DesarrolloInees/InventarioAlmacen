<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class TipoUsuarioVerModelo
{
    private $conn;
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerTiposUsuario()
    {
        try {
            $sql = "SELECT idTipoUsuario, nombreTipoUsuario FROM tipousuario ORDER BY idTipoUsuario ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener tipos de usuario: " . $e->getMessage());
            return [];
        }
    }

    // Eliminación física (solo funcionará si no hay usuarios asociados a este rol)
    public function eliminarTipoUsuarioFisico($id)
    {
        try {
            $sql = "DELETE FROM tipousuario WHERE idTipoUsuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar rol (posible llave foránea): " . $e->getMessage());
            return false;
        }
    }
}