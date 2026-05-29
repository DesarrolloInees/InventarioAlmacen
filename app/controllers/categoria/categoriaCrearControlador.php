<?php
// app/controllers/categoria/categoriaCrearControlador.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/categoria/categoriaCrearModelo.php';

class categoriaCrearControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new CategoriaCrearModelo($this->db);
    }

    public function index()
    {
        $errores = [];
        $datosPrevios = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosPrevios = [
                'nombre_categoria' => trim($_POST['nombre_categoria'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? '')
            ];

            if (empty($datosPrevios['nombre_categoria'])) {
                $errores[] = "El nombre de la categoría es obligatorio.";
            }

            if (!empty($datosPrevios['nombre_categoria']) && $this->modelo->existeCategoria($datosPrevios['nombre_categoria'])) {
                $errores[] = "Ya existe una categoría registrada con ese nombre.";
            }

            if (empty($errores)) {
                if ($this->modelo->crearCategoria($datosPrevios)) {
                    header("Location: " . BASE_URL . "categoriaVer");
                    exit();
                } else {
                    $errores[] = "Error interno al intentar guardar la categoría.";
                }
            }
        }

        $titulo = "Crear Nueva Categoría";
        $vistaContenido = "app/views/categoria/categoriaCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}