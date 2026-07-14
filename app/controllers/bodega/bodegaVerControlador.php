<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/bodega/bodegaVerModelo.php';

class bodegaVerControlador
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
        $data = [
            'titulo' => 'Bodegas del Sistema',
            'bodegas' => $this->modelo->obtenerBodegas()
        ];

        $vistaContenido = "app/views/bodega/bodegaVerVista.php";
        include "app/views/plantillaVista.php";
    }
}