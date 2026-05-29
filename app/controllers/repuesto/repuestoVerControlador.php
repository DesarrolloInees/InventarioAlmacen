<?php
// app/controllers/repuesto/repuestoVerControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/repuesto/repuestoVerModelo.php';

class repuestoVerControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RepuestoVerModelo($this->db);
    }

    public function index()
    {
        $this->cargarVista();
    }

    public function cargarVista()
    {
        // 1. Obtener datos
        $listaRepuestos = $this->modelo->obtenerRepuestos();

        // 2. Empaquetar
        $data = [
            'titulo' => 'Inventario de Repuestos',
            'repuestos' => $listaRepuestos
        ];

        // 3. Definir vista
        $vistaContenido = "app/views/repuesto/repuestoVerVista.php";

        // 4. Cargar plantilla
        include "app/views/plantillaVista.php";
    }
}