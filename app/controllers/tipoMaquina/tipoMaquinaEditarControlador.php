<?php
// app/controllers/tipoMaquina/tipoMaquinaEditarControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoMaquina/tipoMaquinaEditarModelo.php';

class tipoMaquinaEditarControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoMaquinaEditarModelo($this->db);
    }

    public function index()
    {
        // 1. Obtener ID de la URL o del POST
        $idTipoMaquina = $_GET['id'] ?? $_POST['idTipoMaquina'] ?? null;

        if (!$idTipoMaquina) {
            header("Location: " . BASE_URL . "tipoMaquinaVer");
            exit();
        }

        $errores = [];
        $datosMaquina = [];

        // ------------------------------------------------
        // A. PROCESAR GUARDADO (POST)
        // ------------------------------------------------
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datosMaquina = [
                'nombreTipoMaquina' => trim($_POST['nombreTipoMaquina'] ?? ''),
                'estado'            => $_POST['estado'] ?? 'activo'
            ];

            // Validaciones
            if (empty($datosMaquina['nombreTipoMaquina'])) {
                $errores[] = "El nombre del tipo de máquina es obligatorio.";
            }

            // Validar que no exista otra máquina con ese mismo nombre
            if (empty($errores) && $this->modelo->existeTipoMaquinaExcluyendoId($datosMaquina['nombreTipoMaquina'], $idTipoMaquina)) {
                $errores[] = "Ya existe OTRO tipo de máquina con ese nombre.";
            }

            if (empty($errores)) {
                if ($this->modelo->editarTipoMaquina($idTipoMaquina, $datosMaquina)) {
                    header("Location: " . BASE_URL . "tipoMaquinaVer");
                    exit();
                } else {
                    $errores[] = "Error al actualizar en la BD.";
                }
            }
        }

        // ------------------------------------------------
        // B. CARGAR DATOS SI NO ES POST (O SI HUBO ERROR)
        // ------------------------------------------------
        if (empty($datosMaquina) || !empty($errores)) {
            $maquinaActual = $this->modelo->obtenerTipoMaquinaPorId($idTipoMaquina);
            
            if (!$maquinaActual) {
                header("Location: " . BASE_URL . "tipoMaquinaVer");
                exit();
            }
            
            if (empty($datosMaquina)) {
                $datosMaquina = $maquinaActual;
            }
        }

        // ------------------------------------------------
        // C. MOSTRAR VISTA
        // ------------------------------------------------
        $titulo = "Editar Tipo de Máquina #" . $idTipoMaquina;
        $vistaContenido = "app/views/tipoMaquina/tipoMaquinaEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}