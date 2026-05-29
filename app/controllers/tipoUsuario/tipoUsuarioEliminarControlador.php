<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoUsuario/tipoUsuarioVerModelo.php';

class tipoUsuarioEliminarControlador {
    private $db, $modelo;
    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoUsuarioVerModelo($this->db);
    }
    public function index() {
        session_start();
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivelUsuario != 1 && $nivelUsuario != 2) { header("Location: " . BASE_URL . "tipoUsuarioVer"); exit(); }
        $id = $_GET['id'] ?? null;
        if ($id) { $this->modelo->eliminarTipoUsuarioFisico($id); }
        header("Location: " . BASE_URL . "tipoUsuarioVer");
        exit();
    }
}