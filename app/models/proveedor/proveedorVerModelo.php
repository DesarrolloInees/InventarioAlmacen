<?php
// app/models/proveedor/proveedorVerModelo.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class ProveedorVerModelo {
    private $conn;
    public function __construct(PDO $db) { $this->conn = $db; }

    public function obtenerProveedores() {
        try {
            $sql = "SELECT * FROM proveedores ORDER BY nombre_proveedor ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function eliminarProveedorLogicamente($id) {
        try {
            // Eliminación lógica: lo pasamos a inactivo
            $sql = "UPDATE proveedores SET estado = 'inactivo' WHERE id_proveedor = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) { return false; }
    }
}