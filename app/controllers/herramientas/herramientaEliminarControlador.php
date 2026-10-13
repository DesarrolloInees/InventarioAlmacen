<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/herramientas/herramientaEliminarModelo.php';

class herramientaEliminarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new HerramientaEliminarModelo($this->db);
    }

    public function index()
    {
        // Solo administradores y supervisores
        $nivel_acceso = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivel_acceso != 1 && $nivel_acceso != 2) {
            die("No tienes permisos para realizar esta acción.");
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->modelo->eliminarHerramienta($id);
        }

        header('Location: ' . BASE_URL . 'herramientaVer');
        exit();
    }
}
