<?php
// app/controllers/categoria/categoriaEditarControlador.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/categoria/categoriaEditarModelo.php';

class categoriaEditarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new CategoriaEditarModelo($this->db);
    }

    public function index()
    {
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;

        // Seguridad: Solo administradores
        if ($nivelUsuario != 1 && $nivelUsuario != 2) {
            header("Location: " . BASE_URL . "categoriaVer");
            exit();
        }

        $idCategoria = $_GET['id'] ?? $_POST['id_categoria'] ?? null;

        if (!$idCategoria) {
            header("Location: " . BASE_URL . "categoriaVer");
            exit();
        }

        $errores = [];
        $datosCategoria = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosCategoria = [
                'nombre_categoria' => trim($_POST['nombre_categoria'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'estado' => intval($_POST['estado'] ?? 1)
            ];

            if (empty($datosCategoria['nombre_categoria'])) {
                $errores[] = "El nombre de la categoría es obligatorio.";
            }

            if (!empty($datosCategoria['nombre_categoria']) && $this->modelo->existeCategoriaExcluyendoId($datosCategoria['nombre_categoria'], $idCategoria)) {
                $errores[] = "Ya existe otra categoría registrada con ese nombre.";
            }

            if (empty($errores)) {
                if ($this->modelo->editarCategoria($idCategoria, $datosCategoria)) {
                    header("Location: " . BASE_URL . "categoriaVer");
                    exit();
                } else {
                    $errores[] = "Error interno al intentar actualizar la categoría.";
                }
            }
        }

        // Si es la primera vez o hubo error, cargamos la info actual
        if (empty($datosCategoria) || !empty($errores)) {
            $categoriaActual = $this->modelo->obtenerCategoriaPorId($idCategoria);
            if (!$categoriaActual) {
                header("Location: " . BASE_URL . "categoriaVer");
                exit();
            }
            if (empty($datosCategoria)) {
                $datosCategoria = $categoriaActual;
            }
        }

        $titulo = "Editar Categoría";
        $vistaContenido = "app/views/categoria/categoriaEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}