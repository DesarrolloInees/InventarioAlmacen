<?php
// app/controllers/tipo_maquina/tipoMaquinaVerControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoMaquina/tipoMaquinaVerModelo.php';

class tipoMaquinaVerControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoMaquinaVerModelo($this->db);
    }

    public function index()
    {
        // 1. Obtener datos
        $listaMaquinas = $this->modelo->obtenerTiposMaquina();

        // 2. Empaquetar
        $data = [
            'titulo' => 'Tipos de Máquina',
            'maquinas' => $listaMaquinas
        ];

        // 3. Definir vista
        $vistaContenido = "app/views/tipoMaquina/tipoMaquinaVerVista.php";

        // 4. Cargar plantilla
        include "app/views/plantillaVista.php";
    }
}