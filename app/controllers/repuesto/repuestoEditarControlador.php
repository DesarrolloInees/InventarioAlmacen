<?php
// app/controllers/repuesto/repuestoEditarControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/repuesto/repuestoEditarModelo.php';

class repuestoEditarControlador
{
    private $db, $modelo;

    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RepuestoEditarModelo($this->db);
    }

    public function index() {
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivelUsuario != 1 && $nivelUsuario != 2) { header("Location: " . BASE_URL . "repuestoVer"); exit(); }

        $idRepuesto = $_GET['id'] ?? $_POST['id_repuesto'] ?? null;
        if (!$idRepuesto) { header("Location: " . BASE_URL . "repuestoVer"); exit(); }

        $errores = [];
        $datosRepuesto = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datosRepuesto = [
                'nombre_repuesto'   => trim($_POST['nombre_repuesto'] ?? ''),
                'codigo_referencia' => trim($_POST['codigo_referencia'] ?? ''),
                'condicion'         => $_POST['condicion'] ?? 'nuevo',
                'valor_venta'       => floatval($_POST['valor_venta'] ?? 0),
                'estado'            => intval($_POST['estado'] ?? 1),
                'id_categoria'      => $_POST['id_categoria'] ?? null
            ];

            if (empty($datosRepuesto['nombre_repuesto'])) $errores[] = "El nombre del repuesto es obligatorio.";
            if (empty($datosRepuesto['id_categoria'])) $errores[] = "Debes asignar una categoría al repuesto.";

            if (!empty($datosRepuesto['codigo_referencia']) && $this->modelo->existeCodigoExcluyendoId($datosRepuesto['codigo_referencia'], $idRepuesto)) {
                $errores[] = "El código interno ya pertenece a otro repuesto.";
            }

            if (empty($errores)) {
                if ($this->modelo->editarRepuesto($idRepuesto, $datosRepuesto)) {
                    header("Location: " . BASE_URL . "repuestoVer");
                    exit();
                } else {
                    $errores[] = "Error interno al intentar actualizar.";
                }
            }
        }

        if (empty($datosRepuesto) || !empty($errores)) {
            $repuestoActual = $this->modelo->obtenerRepuestoPorId($idRepuesto);
            if (!$repuestoActual) { header("Location: " . BASE_URL . "repuestoVer"); exit(); }
            if (empty($datosRepuesto)) $datosRepuesto = $repuestoActual;
        }

        $titulo = "Editar Repuesto";
        $categoriasActivas = $this->modelo->obtenerCategoriasActivas();
        
        $vistaContenido = "app/views/repuesto/repuestoEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}