<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

/**
 * Modelo exclusivo de la acción ELIMINAR (anulación lógica) del registro
 * manual de bodega. El registro nunca se borra físicamente.
 */
class RegistroBodegaEliminarModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerRegistroPorId($id)
    {
        try {
            $sql = "SELECT rb.*,
                           b.nombre_bodega,
                           u.nombre AS usuario_registra
                    FROM registro_bodega rb
                    LEFT JOIN bodegas b ON rb.id_bodega = b.id_bodega
                    LEFT JOIN usuarios u ON rb.id_usuario_registra = u.usuario_id
                    WHERE rb.id_registro = :id
                    LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener registro_bodega (eliminar): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Anula lógicamente el registro (estado = 'ANULADO').
     */
    public function anularRegistro($id)
    {
        try {
            $sql = "UPDATE registro_bodega
                    SET estado = 'ANULADO', updated_at = NOW()
                    WHERE id_registro = :id AND estado = 'ACTIVO'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al anular registro_bodega: " . $e->getMessage());
            return false;
        }
    }
}
