<?php
// app/controllers/producto/productoVerControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/producto/productoVerModelo.php';

class productoVerControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new ProductoVerModelo($this->db);
    }

    public function index()
    {
        $data = [
            'titulo' => 'Catálogo de Productos Consumibles',
            'productos' => $this->modelo->obtenerProductos()
        ];
        
        $vistaContenido = "app/views/producto/productoVerVista.php";
        include "app/views/plantillaVista.php";
    }
}