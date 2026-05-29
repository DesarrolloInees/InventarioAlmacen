<?php
// app/controllers/tipoValidador/tipoValidadorEliminarControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoValidador/tipoValidadorVerModelo.php';

class tipoValidadorEliminarControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        // Usamos el mismo modelo de "Ver" porque ahí pusimos la función de eliminar
        $this->modelo = new TipoValidadorVerModelo($this->db);
    }

    public function index()
    {
        // Solo admins pueden eliminar
        session_start();
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivelUsuario != 1 && $nivelUsuario != 2) {
            header("Location: " . BASE_URL . "tipoValidadorVer");
            exit();
        }

        // Obtener el ID de la URL
        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->modelo->eliminarValidadorLogicamente($id);
        }

        // Volver a la vista principal
        header("Location: " . BASE_URL . "tipoValidadorVer");
        exit();
    }
}