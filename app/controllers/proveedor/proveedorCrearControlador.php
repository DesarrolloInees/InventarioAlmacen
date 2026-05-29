<?php
// app/controllers/proveedor/proveedorCrearControlador.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/proveedor/proveedorCrearModelo.php';

class proveedorCrearControlador {
    private $db, $modelo;
    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new ProveedorCrearModelo($this->db);
    }
    public function index() {
        $errores = []; $datosPrevios = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosPrevios = [
                'nit_documento'    => trim($_POST['nit_documento'] ?? ''),
                'nombre_proveedor' => trim($_POST['nombre_proveedor'] ?? ''),
                'telefono'         => trim($_POST['telefono'] ?? ''),
                'email'            => trim($_POST['email'] ?? ''),
                'estado'           => $_POST['estado'] ?? 'activo'
            ];

            if (empty($datosPrevios['nombre_proveedor'])) $errores[] = "El nombre del proveedor es obligatorio.";
            
            if (empty($errores) && !empty($datosPrevios['nit_documento']) && $this->modelo->existeNit($datosPrevios['nit_documento'])) {
                $errores[] = "Ya existe un proveedor registrado con ese NIT o Documento.";
            }

            if (empty($errores)) {
                if ($this->modelo->crearProveedor($datosPrevios)) {
                    header("Location: " . BASE_URL . "proveedorVer"); exit();
                } else $errores[] = "Error al guardar en BD.";
            }
        }
        $titulo = "Crear Proveedor";
        $vistaContenido = "app/views/proveedor/proveedorCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}