<?php
// app/controllers/producto/productoEliminarControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/producto/productoVerModelo.php';

class productoEliminarControlador
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
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        
        // Seguridad: Solo admins pueden eliminar
        if ($nivelUsuario != 1 && $nivelUsuario != 2) { 
            header("Location: " . BASE_URL . "productoVer"); 
            exit(); 
        }

        $id = $_GET['id'] ?? null;

        if ($id) { 
            $this->modelo->eliminarProductoLogicamente($id); 
        }
        
        header("Location: " . BASE_URL . "productoVer");
        exit();
    }
}