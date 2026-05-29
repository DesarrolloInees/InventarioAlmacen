<?php
// app/controllers/repuesto/repuestoCrearControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/repuesto/repuestoCrearModelo.php';

class repuestoCrearControlador
{
    private $db, $modelo;

    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RepuestoCrearModelo($this->db);
    }

    public function index() {
        $errores = [];
        $datosPrevios = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosPrevios = [
                'nombre_repuesto'   => trim($_POST['nombre_repuesto'] ?? ''),
                'codigo_referencia' => trim($_POST['codigo_referencia'] ?? ''),
                'condicion'         => $_POST['condicion'] ?? 'nuevo',
                'valor_venta'       => floatval($_POST['valor_venta'] ?? 0),
                'id_categoria'      => $_POST['id_categoria'] ?? null
            ];

            if (empty($datosPrevios['nombre_repuesto'])) $errores[] = "El nombre del repuesto es obligatorio.";
            if (empty($datosPrevios['id_categoria'])) $errores[] = "Debes asignar una categoría al repuesto.";

            if (!empty($datosPrevios['codigo_referencia']) && $this->modelo->existeCodigoReferencia($datosPrevios['codigo_referencia'])) {
                $errores[] = "El código '{$datosPrevios['codigo_referencia']}' ya está registrado.";
            }

            if (empty($errores)) {
                if ($this->modelo->crearRepuesto($datosPrevios)) {
                    header("Location: " . BASE_URL . "repuestoVer");
                    exit();
                } else {
                    $errores[] = "Error interno al intentar guardar.";
                }
            }
        }

        $titulo = "Registrar Nuevo Repuesto";
        $categoriasActivas = $this->modelo->obtenerCategoriasActivas();
        
        $vistaContenido = "app/views/repuesto/repuestoCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}