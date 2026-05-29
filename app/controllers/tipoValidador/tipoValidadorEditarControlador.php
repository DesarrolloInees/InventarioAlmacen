<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoValidador/tipoValidadorEditarModelo.php';

class tipoValidadorEditarControlador {
    private $db, $modelo;

    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoValidadorEditarModelo($this->db);
    }

    public function index() {
        $idTipoValidador = $_GET['id'] ?? $_POST['idTipoValidador'] ?? null;
        if (!$idTipoValidador) { header("Location: " . BASE_URL . "tipoValidadorVer"); exit(); }

        $errores = []; $datosValidador = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosValidador = [
                'nombreTipoValidador' => trim($_POST['nombreTipoValidador'] ?? ''),
                'estado' => $_POST['estado'] ?? 'activo'
            ];
            if (empty($datosValidador['nombreTipoValidador'])) $errores[] = "El nombre es obligatorio.";
            if (empty($errores) && $this->modelo->existeTipoValidadorExcluyendoId($datosValidador['nombreTipoValidador'], $idTipoValidador)) $errores[] = "Ya existe OTRO validador con ese nombre.";
            
            if (empty($errores)) {
                if ($this->modelo->editarTipoValidador($idTipoValidador, $datosValidador)) {
                    header("Location: " . BASE_URL . "tipoValidadorVer"); exit();
                } else $errores[] = "Error al actualizar BD.";
            }
        }

        if (empty($datosValidador) || !empty($errores)) {
            $validadorActual = $this->modelo->obtenerTipoValidadorPorId($idTipoValidador);
            if (!$validadorActual) { header("Location: " . BASE_URL . "tipoValidadorVer"); exit(); }
            if (empty($datosValidador)) $datosValidador = $validadorActual;
        }

        $titulo = "Editar Validador #" . $idTipoValidador;
        $vistaContenido = "app/views/tipoValidador/tipoValidadorEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}