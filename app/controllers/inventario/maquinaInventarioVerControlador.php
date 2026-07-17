<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/maquinaInventarioVerModelo.php';

class maquinaInventarioVerControlador
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
        $data = [
            'titulo' => 'Inventario de Máquinas',
            'maquinas' => $this->modelo->obtenerInventario()
        ];

        $vistaContenido = "app/views/inventario/maquinaInventarioVerVista.php";
        include "app/views/plantillaVista.php";
    }
}