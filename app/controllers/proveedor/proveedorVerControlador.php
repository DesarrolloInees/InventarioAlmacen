<?php
// app/controllers/proveedor/proveedorVerControlador.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/proveedor/proveedorVerModelo.php';

class proveedorVerControlador {
    private $db, $modelo;
    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new ProveedorVerModelo($this->db);
    }
    public function index() {
        $data = [
            'titulo' => 'Lista de Proveedores',
            'proveedores' => $this->modelo->obtenerProveedores()
        ];
        $vistaContenido = "app/views/proveedor/proveedorVerVista.php";
        include "app/views/plantillaVista.php";
    }
}