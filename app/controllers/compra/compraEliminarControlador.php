<?php
// app/controllers/compra/compraEliminarControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/compra/compraVerModelo.php';

class compraEliminarControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new CompraVerModelo($this->db);
    }

    public function index()
    {
        session_start();
        
        // Seguridad: Solo admins
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivelUsuario != 1 && $nivelUsuario != 2) {
            header("Location: " . BASE_URL . "compraVer");
            exit();
        }

        $id = $_GET['id'] ?? null;

        if ($id) {
            $resultado = $this->modelo->eliminarCompra($id);
            
            if ($resultado === 'error_stock') {
                $_SESSION['error_eliminar'] = "No se puede anular esta compra. Parte de estos repuestos ya fueron asignados a un técnico y el stock quedaría en negativo.";
            } elseif ($resultado === true) {
                $_SESSION['exito_eliminar'] = "Compra anulada correctamente. El stock ha sido restado.";
            } else {
                $_SESSION['error_eliminar'] = "Error interno al intentar eliminar la compra.";
            }
        }

        header("Location: " . BASE_URL . "compraVer");
        exit();
    }
}