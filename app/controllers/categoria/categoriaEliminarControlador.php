<?php
// app/controllers/categoria/categoriaEliminarControlador.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/categoria/categoriaVerModelo.php';

class categoriaEliminarControlador
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
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;

        // Seguridad: Solo administradores pueden eliminar
        if ($nivelUsuario != 1 && $nivelUsuario != 2) {
            header("Location: " . BASE_URL . "categoriaVer");
            exit();
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->modelo->eliminarCategoriaLogicamente($id);
        }

        header("Location: " . BASE_URL . "categoriaVer");
        exit();
    }
}