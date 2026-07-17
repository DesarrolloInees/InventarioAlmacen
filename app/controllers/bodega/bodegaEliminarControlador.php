<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
// Reutilizamos el modelo de Ver que ya tiene el método para eliminar (desactivar)
require_once __DIR__ . '/../../models/bodega/bodegaVerModelo.php';

class bodegaEliminarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new BodegaVerModelo($this->db);
    }

    public function index()
    {
        // Protegemos la ruta para que solo Admin(1) o Supervisor(2) puedan desactivar
        $nivel_acceso = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivel_acceso != 1 && $nivel_acceso != 2) {
            die("No tienes permisos para realizar esta acción.");
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            // Ejecutamos la función que pasará el estado a 0
            $this->modelo->eliminarBodegaLogicamente($id);
        }

        // Ya sea que falle o tenga éxito, lo devolvemos a la tabla
        header('Location: ' . BASE_URL . 'bodegaVer');
        exit();
    }
}