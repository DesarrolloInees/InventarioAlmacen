<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/entradaSalida/entradaSalidaVerModelo.php';

class EntradaSalidaVerControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new EntradaSalidaVerModelo($this->db);
    }

    public function index()
    {
        $movimientos = $this->modelo->obtenerTodosLosMovimientos();

        $data = [
            'titulo' => 'Historial de Entradas y Salidas',
            'movimientos' => $movimientos
        ];

        $vistaContenido = "app/views/entradaSalida/entradaSalidaVerVista.php";
        include "app/views/plantillaVista.php";
    }
}