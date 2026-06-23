<?php
// app/controllers/producto/productoEditarControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/producto/productoEditarModelo.php';

class productoEditarControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new ProductoEditarModelo($this->db);
    }

    public function index()
    {
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        
        // Seguridad: Solo admins pueden editar
        if ($nivelUsuario != 1 && $nivelUsuario != 2) { 
            header("Location: " . BASE_URL . "productoVer"); 
            exit(); 
        }

        $idProducto = $_GET['id'] ?? $_POST['id_producto'] ?? null;
        
        if (!$idProducto) {
            header("Location: " . BASE_URL . "productoVer");
            exit();
        }

        $errores = [];
        $datosProducto = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosProducto = [
                'nombre_producto' => trim($_POST['nombre_producto'] ?? ''),
                'codigo_interno'  => trim($_POST['codigo_interno'] ?? ''),
                'valor_venta'     => floatval($_POST['valor_venta'] ?? 0),
                'estado'          => intval($_POST['estado'] ?? 1)
            ];

            // Validaciones
            if (empty($datosProducto['nombre_producto'])) {
                $errores[] = "El nombre del producto es obligatorio.";
            }

            if (!empty($datosProducto['codigo_interno']) && $this->modelo->existeCodigoInternoExcluyendoId($datosProducto['codigo_interno'], $idProducto)) {
                $errores[] = "El código interno '{$datosProducto['codigo_interno']}' ya está asignado a otro producto.";
            }

            // Procesar guardado
            if (empty($errores)) {
                if ($this->modelo->editarProducto($idProducto, $datosProducto)) {
                    header("Location: " . BASE_URL . "productoVer");
                    exit();
                } else {
                    $errores[] = "Error interno al intentar actualizar el producto.";
                }
            }
        }

        // Si es la primera vez que entra a la página (o si hubo error), cargar datos actuales
        if (empty($datosProducto) || !empty($errores)) {
            $productoActual = $this->modelo->obtenerProductoPorId($idProducto);
            if (!$productoActual) {
                header("Location: " . BASE_URL . "productoVer");
                exit();
            }
            if (empty($datosProducto)) {
                $datosProducto = $productoActual;
            }
        }

        $titulo = "Editar Producto Consumible";
        $vistaContenido = "app/views/producto/productoEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}