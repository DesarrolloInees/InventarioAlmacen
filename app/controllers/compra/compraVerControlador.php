<?php
// app/controllers/compra/compraVerControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/compra/compraVerModelo.php';

class compraVerControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new CompraVerModelo($this->db);
    }

    public function index()
    {
        
        $data = [
            'titulo' => 'Historial de Compras General',
            'compras' => $this->modelo->obtenerHistorialCompras()
        ];

        // Capturar respuestas de las operaciones de borrado/modificación
        if (isset($_SESSION['error_eliminar'])) {
            $data['error'] = $_SESSION['error_eliminar'];
            unset($_SESSION['error_eliminar']);
        }
        if (isset($_SESSION['exito_eliminar'])) {
            $data['exito'] = $_SESSION['exito_eliminar'];
            unset($_SESSION['exito_eliminar']);
        }

        $vistaContenido = "app/views/compra/compraVerVista.php";
        include "app/views/plantillaVista.php";
    }
}