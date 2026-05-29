<?php
// app/controllers/producto/productoCrearControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/producto/productoCrearModelo.php';

class productoCrearControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new ProductoCrearModelo($this->db);
    }

    public function index()
    {
        
        $errores = [];
        $datosPrevios = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosPrevios = [
                'nombre_producto' => trim($_POST['nombre_producto'] ?? ''),
                'codigo_interno'  => trim($_POST['codigo_interno'] ?? ''),
                'valor_venta'     => floatval($_POST['valor_venta'] ?? 0)
            ];

            // Validaciones básicas
            if (empty($datosPrevios['nombre_producto'])) {
                $errores[] = "El nombre del producto es obligatorio.";
            }

            if (!empty($datosPrevios['codigo_interno']) && $this->modelo->existeCodigoInterno($datosPrevios['codigo_interno'])) {
                $errores[] = "El código interno '{$datosPrevios['codigo_interno']}' ya está asignado a otro producto.";
            }

            // Si no hay errores, procedemos a guardar
            if (empty($errores)) {
                if ($this->modelo->crearProducto($datosPrevios)) {
                    // Redirigimos a la vista de "Ver Productos" (que haremos luego)
                    header("Location: " . BASE_URL . "productoVer");
                    exit();
                } else {
                    $errores[] = "Error interno al intentar guardar el producto en la base de datos.";
                }
            }
        }

        $titulo = "Registrar Nuevo Consumible";
        $vistaContenido = "app/views/producto/productoCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}