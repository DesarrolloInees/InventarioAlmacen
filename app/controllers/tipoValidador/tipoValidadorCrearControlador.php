<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoValidador/tipoValidadorCrearModelo.php';

class tipoValidadorCrearControlador {
    private $db, $modelo;

    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoValidadorCrearModelo($this->db);
    }

    public function index() {
        $errores = []; $datosPrevios = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosPrevios = [
                'nombreTipoValidador' => trim($_POST['nombreTipoValidador'] ?? ''),
                'estado' => $_POST['estado'] ?? 'activo'
            ];
            if (empty($datosPrevios['nombreTipoValidador'])) $errores[] = "El nombre del validador es obligatorio.";
            if (empty($errores) && $this->modelo->existeTipoValidador($datosPrevios['nombreTipoValidador'])) $errores[] = "Ya existe un validador con ese nombre.";
            
            if (empty($errores)) {
                if ($this->modelo->crearTipoValidador($datosPrevios)) {
                    header("Location: " . BASE_URL . "tipoValidadorVer"); exit();
                } else $errores[] = "Error al guardar en BD.";
            }
        }
        $titulo = "Crear Tipo de Validador";
        $vistaContenido = "app/views/tipoValidador/tipoValidadorCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}