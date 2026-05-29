<?php
// app/controllers/proveedor/proveedorEditarControlador.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/proveedor/proveedorEditarModelo.php';

class proveedorEditarControlador {
    private $db, $modelo;
    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new ProveedorEditarModelo($this->db);
    }
    public function index() {
        $idProv = $_GET['id'] ?? $_POST['id_proveedor'] ?? null;
        if (!$idProv) { header("Location: " . BASE_URL . "proveedorVer"); exit(); }

        $errores = []; $datosProv = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosProv = [
                'nit_documento'    => trim($_POST['nit_documento'] ?? ''),
                'nombre_proveedor' => trim($_POST['nombre_proveedor'] ?? ''),
                'telefono'         => trim($_POST['telefono'] ?? ''),
                'email'            => trim($_POST['email'] ?? ''),
                'estado'           => $_POST['estado'] ?? 'activo'
            ];

            if (empty($datosProv['nombre_proveedor'])) $errores[] = "El nombre es obligatorio.";
            if (empty($errores) && !empty($datosProv['nit_documento']) && $this->modelo->existeNitExcluyendoId($datosProv['nit_documento'], $idProv)) {
                $errores[] = "Ya existe OTRO proveedor con ese NIT.";
            }

            if (empty($errores)) {
                if ($this->modelo->editarProveedor($idProv, $datosProv)) {
                    header("Location: " . BASE_URL . "proveedorVer"); exit();
                } else $errores[] = "Error al actualizar BD.";
            }
        }

        if (empty($datosProv) || !empty($errores)) {
            $provActual = $this->modelo->obtenerProveedorPorId($idProv);
            if (!$provActual) { header("Location: " . BASE_URL . "proveedorVer"); exit(); }
            if (empty($datosProv)) $datosProv = $provActual;
        }

        $titulo = "Editar Proveedor";
        $vistaContenido = "app/views/proveedor/proveedorEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}