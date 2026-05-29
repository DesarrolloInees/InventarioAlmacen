<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoUsuario/tipoUsuarioCrearModelo.php';

class tipoUsuarioCrearControlador {
    private $db, $modelo;
    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoUsuarioCrearModelo($this->db);
    }

    public function index() {
        $errores = []; $datosPrevios = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosPrevios = ['nombreTipoUsuario' => trim($_POST['nombreTipoUsuario'] ?? '')];
            if (empty($datosPrevios['nombreTipoUsuario'])) $errores[] = "El nombre del rol es obligatorio.";
            if (empty($errores) && $this->modelo->existeTipoUsuario($datosPrevios['nombreTipoUsuario'])) $errores[] = "Ya existe un rol con ese nombre.";
            
            if (empty($errores)) {
                if ($this->modelo->crearTipoUsuario($datosPrevios)) {
                    header("Location: " . BASE_URL . "tipoUsuarioVer"); exit();
                } else $errores[] = "Error al guardar en BD.";
            }
        }
        $titulo = "Crear Rol";
        $vistaContenido = "app/views/tipoUsuario/tipoUsuarioCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}