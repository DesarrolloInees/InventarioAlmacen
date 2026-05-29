<?php
// app/controllers/categoria/categoriaVerControlador.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/categoria/categoriaVerModelo.php';

class categoriaVerControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new CategoriaVerModelo($this->db);
    }

    public function index()
    {
        $data = [
            'titulo' => 'Categorías del Sistema', // Título más general
            'categorias' => $this->modelo->obtenerCategorias()
        ];

        $vistaContenido = "app/views/categoria/categoriaVerVista.php";
        include "app/views/plantillaVista.php";
    }
}