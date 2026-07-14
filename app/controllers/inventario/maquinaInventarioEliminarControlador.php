<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
// Reciclamos el modelo principal de Ver
require_once __DIR__ . '/../../models/inventario/maquinaInventarioVerModelo.php';

class maquinaInventarioEliminarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new MaquinaInventarioVerModelo($this->db);
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
            $this->modelo->eliminarMaquina($id);
        }

        header('Location: ' . BASE_URL . 'maquinaInventarioVer');
        exit();
    }
}