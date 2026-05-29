<?php
// app/controllers/salida/salidaVerControlador.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/salida/salidaVerModelo.php';

class salidaVerControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new SalidaVerModelo($this->db);
    }

    public function index()
    {
    
        $data = [
            'titulo' => 'Historial de Salidas / Asignaciones',
            'salidas' => $this->modelo->obtenerHistorialSalidas()
        ];

        // Manejar mensajes de sesión devueltos desde salidaEliminarControlador
        if (isset($_SESSION['error_eliminar'])) {
            $data['error'] = $_SESSION['error_eliminar'];
            unset($_SESSION['error_eliminar']);
        }
        if (isset($_SESSION['exito_eliminar'])) {
            $data['exito'] = $_SESSION['exito_eliminar'];
            unset($_SESSION['exito_eliminar']);
        }

        $vistaContenido = "app/views/salida/salidaVerVista.php";
        include "app/views/plantillaVista.php";
    }
}