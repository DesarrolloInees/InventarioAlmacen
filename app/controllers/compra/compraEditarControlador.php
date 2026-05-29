<?php
// app/controllers/compra/compraEditarControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/compra/compraEditarModelo.php';

class compraEditarControlador
{
    private $db, $modelo;

    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new CompraEditarModelo($this->db);
    }

    public function index() {
        
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivelUsuario != 1 && $nivelUsuario != 2) { 
            header("Location: " . BASE_URL . "compraVer"); 
            exit(); 
        }

        $idMovimiento = $_GET['id'] ?? $_POST['id_movimiento'] ?? null;
        if (!$idMovimiento) { 
            header("Location: " . BASE_URL . "compraVer"); 
            exit(); 
        }

        $errores = []; 
        $datosCompra = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosNuevos = [
                'cantidad'       => intval($_POST['cantidad'] ?? 0),
                'precio_compra'  => floatval($_POST['precio_compra'] ?? 0),
                'numero_factura' => trim($_POST['numero_factura'] ?? ''),
                'id_proveedor'   => $_POST['id_proveedor'] ?? '',
                'observacion'    => trim($_POST['observacion'] ?? '')
            ];

            if ($datosNuevos['cantidad'] <= 0) $errores[] = "La cantidad debe ser mayor a cero.";
            if ($datosNuevos['precio_compra'] < 0) $errores[] = "El precio de compra no puede ser negativo.";
            if (empty($datosNuevos['numero_factura'])) $errores[] = "El número de factura es obligatorio.";
            if (empty($datosNuevos['id_proveedor'])) $errores[] = "Debes seleccionar un proveedor.";

            if (empty($errores)) {
                $resultado = $this->modelo->editarCompra($idMovimiento, $datosNuevos);
                if ($resultado === 'error_stock') {
                    $errores[] = "No puedes bajar la cantidad porque el stock local quedaría en negativo (algunos artículos ya fueron asignados).";
                } elseif ($resultado === true) {
                    $_SESSION['exito_eliminar'] = "Compra actualizada correctamente y el stock fue recalculado.";
                    header("Location: " . BASE_URL . "compraVer"); 
                    exit();
                } else {
                    $errores[] = "Error interno al actualizar.";
                }
            }
            $datosCompra = $datosNuevos; 
        }

        // Cargar data actual para mostrar
        $compraActual = $this->modelo->obtenerCompraPorId($idMovimiento);
        if (!$compraActual) { 
            header("Location: " . BASE_URL . "compraVer"); 
            exit(); 
        }

        if (empty($datosCompra)) {
            $datosCompra = $compraActual;
        } else {
            // Mantenemos la información de lectura (no editable) en caso de que la validación falle
            $datosCompra['id_repuesto'] = $compraActual['id_repuesto'];
            $datosCompra['nombre_repuesto'] = $compraActual['nombre_repuesto'];
            $datosCompra['codigo_referencia'] = $compraActual['codigo_referencia'];
            $datosCompra['condicion'] = $compraActual['condicion'];
            
            $datosCompra['id_producto'] = $compraActual['id_producto'];
            $datosCompra['nombre_producto'] = $compraActual['nombre_producto'];
            $datosCompra['codigo_interno'] = $compraActual['codigo_interno'];
        }

        $titulo = "Editar Compra #" . $idMovimiento;
        $proveedoresActivos = $this->modelo->obtenerProveedoresActivos(); 
        $vistaContenido = "app/views/compra/compraEditarVista.php";
        
        include "app/views/plantillaVista.php";
    }
}