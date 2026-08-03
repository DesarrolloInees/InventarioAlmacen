<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/maquinaMovimientoVerModelo.php';

class maquinaMovimientoVerControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new MaquinaMovimientoVerModelo($this->db);
    }

    public function index()
    {
        $movimientos = $this->modelo->obtenerHistorialMovimientos();

        $data = [
            'titulo' => 'Historial de Movimientos',
            'movimientos' => $movimientos
        ];

        $vistaContenido = "app/views/inventario/maquinaMovimientoVerVista.php";
        include "app/views/plantillaVista.php";
    }
}