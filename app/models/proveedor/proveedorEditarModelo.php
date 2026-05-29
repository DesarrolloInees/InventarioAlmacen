<?php
// app/models/proveedor/proveedorEditarModelo.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class ProveedorEditarModelo {
    private $conn;
    public function __construct(PDO $db) { $this->conn = $db; }

    public function obtenerProveedorPorId($id) {
        try {
            $sql = "SELECT * FROM proveedores WHERE id_proveedor = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return false; }
    }

    public function editarProveedor($id, $datos) {
        try {
            $sql = "UPDATE proveedores SET 
                        nit_documento = :nit, nombre_proveedor = :nombre, telefono = :tel, email = :email, estado = :estado 
                    WHERE id_proveedor = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nit', $datos['nit_documento']);
            $stmt->bindParam(':nombre', $datos['nombre_proveedor']);
            $stmt->bindParam(':tel', $datos['telefono']);
            $stmt->bindParam(':email', $datos['email']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) { return false; }
    }

    public function existeNitExcluyendoId($nit, $idExcluido) {
        if (empty($nit)) return false;
        try {
            $sql = "SELECT COUNT(*) FROM proveedores WHERE nit_documento = :nit AND id_proveedor != :id AND nit_documento != ''";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nit', $nit);
            $stmt->bindParam(':id', $idExcluido, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) { return false; }
    }
}