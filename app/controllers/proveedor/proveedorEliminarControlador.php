<?php
// app/controllers/proveedor/proveedorEliminarControlador.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/proveedor/proveedorVerModelo.php';

class proveedorEliminarControlador
{
    private $db, $modelo;
    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new ProveedorVerModelo($this->db);
    }
    public function index()
    {
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivelUsuario != 1 && $nivelUsuario != 2) {
            header("Location: " . BASE_URL . "proveedorVer");
            exit();
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->modelo->eliminarProveedorLogicamente($id);
        }
        header("Location: " . BASE_URL . "proveedorVer");
        exit();
    }
}