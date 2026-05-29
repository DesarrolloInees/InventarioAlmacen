<?php
// app/models/proveedor/proveedorCrearModelo.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class ProveedorCrearModelo {
    private $conn;
    public function __construct(PDO $db) { $this->conn = $db; }

    public function crearProveedor($datos) {
        try {
            $sql = "INSERT INTO proveedores (nit_documento, nombre_proveedor, telefono, email, estado) 
                    VALUES (:nit, :nombre, :tel, :email, :estado)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nit', $datos['nit_documento']);
            $stmt->bindParam(':nombre', $datos['nombre_proveedor']);
            $stmt->bindParam(':tel', $datos['telefono']);
            $stmt->bindParam(':email', $datos['email']);
            $stmt->bindParam(':estado', $datos['estado']);
            return $stmt->execute();
        } catch (PDOException $e) { return false; }
    }

    public function existeNit($nit) {
        if (empty($nit)) return false; // Permitimos nits vacíos si no es obligatorio
        try {
            $sql = "SELECT COUNT(*) FROM proveedores WHERE nit_documento = :nit AND nit_documento != ''";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nit', $nit);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) { return false; }
    }
}