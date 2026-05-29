<?php
// app/controllers/salida/salidaEliminarControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/salida/salidaVerModelo.php';

class salidaEliminarControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new SalidaVerModelo($this->db);
    }

    public function index()
    {
        
        // Seguridad: Solo admins pueden anular salidas
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivelUsuario != 1 && $nivelUsuario != 2) {
            header("Location: " . BASE_URL . "salidaVer");
            exit();
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            $resultado = $this->modelo->anularSalida($id);
            
            if ($resultado === true) {
                $_SESSION['exito_eliminar'] = "Asignación anulada correctamente. Los repuestos han sido devueltos a tu almacén local.";
            } else {
                $_SESSION['error_eliminar'] = "Error interno al intentar anular la salida.";
            }
        }

        header("Location: " . BASE_URL . "salidaVer");
        exit();
    }
}