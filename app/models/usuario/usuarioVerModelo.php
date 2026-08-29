<?php
// modelos/usuario/usuarioVerModelo.php

if (!defined('ENTRADA_PRINCIPAL')) {
    die("Acceso denegado.");
}

class UsuarioVerModelo
{

    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Obtiene una lista de todos los usuarios activos.
     * Incluye el nombre del tipo de usuario (rol) en lugar de solo el ID.
     */
    public function obtenerUsuarios()
    {
        try { 
            $sql = "SELECT 
                        u.usuario_id, 
                        u.nombre, 
                        u.cedula, 
                        u.cargo, 
                        u.empresa,
                        u.email, 
                        u.celular,
                        u.usuario,
                        u.estado,
                        tu.nombre_rol AS rol
                    FROM 
                        usuarios u
                    INNER JOIN 
                        tipousuario tu ON COALESCE(u.idTipoUsuario, u.nivel_acceso) = tu.idTipoUsuario
                    WHERE 
                        u.estado = 'activo' 
                    ORDER BY 
                        u.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener los usuarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene una lista de todas las solicitudes de registro pendientes de aprobación.
     */
    public function obtenerSolicitudesPendientes()
    {
        try {
            $sql = "SELECT 
                        u.usuario_id, 
                        u.nombre, 
                        u.cedula, 
                        u.cargo, 
                        u.empresa,
                        u.email, 
                        u.celular,
                        u.usuario,
                        u.estado,
                        tu.nombre_rol AS rol
                    FROM 
                        usuarios u
                    INNER JOIN 
                        tipousuario tu ON COALESCE(u.idTipoUsuario, u.nivel_acceso) = tu.idTipoUsuario
                    WHERE 
                        u.estado = 'pendiente' 
                    ORDER BY 
                        u.usuario_id DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener solicitudes pendientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las solicitudes de clientes aprobadas históricamente.
     */
    public function obtenerSolicitudesAprobadas()
    {
        try {
            $sql = "SELECT 
                        u.usuario_id, 
                        u.nombre, 
                        u.cedula, 
                        u.cargo, 
                        u.empresa,
                        u.email, 
                        u.celular,
                        u.usuario,
                        u.estado,
                        tu.nombre_rol AS rol
                    FROM 
                        usuarios u
                    INNER JOIN 
                        tipousuario tu ON COALESCE(u.idTipoUsuario, u.nivel_acceso) = tu.idTipoUsuario
                    WHERE 
                        u.estado = 'activo'
                        AND COALESCE(u.idTipoUsuario, u.nivel_acceso) = 3
                    ORDER BY 
                        u.usuario_id DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener solicitudes aprobadas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las solicitudes de clientes rechazadas/denegadas.
     */
    public function obtenerSolicitudesDenegadas()
    {
        try {
            $sql = "SELECT 
                        u.usuario_id, 
                        u.nombre, 
                        u.cedula, 
                        u.cargo, 
                        u.empresa,
                        u.email, 
                        u.celular,
                        u.usuario,
                        u.estado,
                        tu.nombre_rol AS rol
                    FROM 
                        usuarios u
                    INNER JOIN 
                        tipousuario tu ON COALESCE(u.idTipoUsuario, u.nivel_acceso) = tu.idTipoUsuario
                    WHERE 
                        u.estado = 'inactivo'
                        AND COALESCE(u.idTipoUsuario, u.nivel_acceso) = 3
                    ORDER BY 
                        u.usuario_id DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener solicitudes denegadas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Aprueba la solicitud de un usuario cambiando su estado a 'activo'.
     */
    public function aprobarUsuario($id)
    {
        $sql = "UPDATE usuarios SET estado = 'activo' WHERE usuario_id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al aprobar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Rechaza la solicitud de un usuario cambiando su estado a 'inactivo'.
     */
    public function rechazarUsuario($id)
    {
        $sql = "UPDATE usuarios SET estado = 'inactivo' WHERE usuario_id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al rechazar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marca un usuario como inactivo (borrado lógico).
     */
    public function eliminarUsuarioLogicamente($id)
    {
        $sql = "UPDATE usuarios SET estado = 'inactivo' WHERE usuario_id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar el usuario lógicamente: " . $e->getMessage());
            return false;
        }
    }
}
