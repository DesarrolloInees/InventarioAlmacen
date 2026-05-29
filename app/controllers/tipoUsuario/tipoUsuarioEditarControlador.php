<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoUsuario/tipoUsuarioEditarModelo.php';

class tipoUsuarioEditarControlador {
    private $db, $modelo;
    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoUsuarioEditarModelo($this->db);
    }

    public function index() {
        $idTipoUsuario = $_GET['id'] ?? $_POST['idTipoUsuario'] ?? null;
        if (!$idTipoUsuario) { header("Location: " . BASE_URL . "tipoUsuarioVer"); exit(); }

        $errores = []; $datosRol = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosRol = ['nombreTipoUsuario' => trim($_POST['nombreTipoUsuario'] ?? '')];
            if (empty($datosRol['nombreTipoUsuario'])) $errores[] = "El nombre es obligatorio.";
            if (empty($errores) && $this->modelo->existeTipoUsuarioExcluyendoId($datosRol['nombreTipoUsuario'], $idTipoUsuario)) $errores[] = "Ya existe OTRO rol con ese nombre.";
            
            if (empty($errores)) {
                if ($this->modelo->editarTipoUsuario($idTipoUsuario, $datosRol)) {
                    header("Location: " . BASE_URL . "tipoUsuarioVer"); exit();
                } else $errores[] = "Error al actualizar BD.";
            }
        }

        if (empty($datosRol) || !empty($errores)) {
            $rolActual = $this->modelo->obtenerTipoUsuarioPorId($idTipoUsuario);
            if (!$rolActual) { header("Location: " . BASE_URL . "tipoUsuarioVer"); exit(); }
            if (empty($datosRol)) $datosRol = $rolActual;
        }

        $titulo = "Editar Rol #" . $idTipoUsuario;
        $vistaContenido = "app/views/tipoUsuario/tipoUsuarioEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}