<?php
// app/controllers/compra/compraCrearControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/compra/compraCrearModelo.php';

class compraCrearControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new CompraCrearModelo($this->db);
    }

    public function index()
    {
        $errores = [];
        $exito = false;
        $datosPrevios = [];

        $idUsuario = $_SESSION['usuario_id'] ?? null;
        if (!$idUsuario) {
            header("Location: " . BASE_URL . "login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipoItem = $_POST['tipo_item'] ?? 'repuesto';
            
            $datosPrevios = [
                'tipo_item'           => $tipoItem,
                'id_repuesto'         => ($tipoItem === 'repuesto') ? ($_POST['id_repuesto'] ?? '') : '',
                'id_producto'         => ($tipoItem === 'producto') ? ($_POST['id_producto'] ?? '') : '',
                'cantidad'            => intval($_POST['cantidad'] ?? 0),
                'precio_compra'       => floatval($_POST['precio_compra'] ?? 0),
                'numero_factura'      => trim($_POST['numero_factura'] ?? ''),
                'id_proveedor'        => $_POST['id_proveedor'] ?? '',
                'observacion'         => trim($_POST['observacion'] ?? ''),
                'id_usuario_registra' => $idUsuario
            ];

            // Validaciones inteligentes
            if ($tipoItem === 'repuesto' && empty($datosPrevios['id_repuesto'])) {
                $errores[] = "Debes seleccionar un repuesto de máquina.";
            }
            if ($tipoItem === 'producto' && empty($datosPrevios['id_producto'])) {
                $errores[] = "Debes seleccionar un producto consumible.";
            }
            if ($datosPrevios['cantidad'] <= 0) {
                $errores[] = "La cantidad comprada debe ser mayor a cero.";
            }
            if ($datosPrevios['precio_compra'] < 0) {
                $errores[] = "El precio de compra no puede ser negativo.";
            }
            if (empty($datosPrevios['numero_factura'])) {
                $errores[] = "El número de factura o recibo de compra es obligatorio.";
            }
            if (empty($datosPrevios['id_proveedor'])) {
                $errores[] = "Debes seleccionar el proveedor.";
            }

            // Procesar guardado
            if (empty($errores)) {
                if ($this->modelo->registrarCompra($datosPrevios)) {
                    $exito = true;
                    $datosPrevios = []; // Limpiamos formulario
                } else {
                    $errores[] = "Ocurrió un error interno al guardar el inventario.";
                }
            }
        }

        // Carga de catálogos para la vista
        $titulo = "Ingresar Compra (Almacén Central)";
        $repuestosActivos = $this->modelo->obtenerRepuestosActivos();
        $productosActivos = $this->modelo->obtenerProductosActivos();
        $proveedoresActivos = $this->modelo->obtenerProveedoresActivos();
        
        $vistaContenido = "app/views/compra/compraCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}