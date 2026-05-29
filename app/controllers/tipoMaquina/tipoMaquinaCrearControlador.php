<?php
// app/controllers/tipoMaquina/tipoMaquinaCrearControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoMaquina/tipoMaquinaCrearModelo.php';

class tipoMaquinaCrearControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoMaquinaCrearModelo($this->db);
    }

    public function index()
    {
        // Seguridad: Si quieres que solo admins creen, descomenta esto
        /*
        session_start();
        if ($_SESSION['nivel_acceso'] != 1 && $_SESSION['nivel_acceso'] != 2) {
            header("Location: " . BASE_URL . "tipoMaquinaVer");
            exit();
        }
        */

        $errores = [];
        $datosPrevios = [];

        // 1. DETECTAR SI SE ENVIÓ EL FORMULARIO (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Recolectar datos
            $datosPrevios = [
                'nombreTipoMaquina' => trim($_POST['nombreTipoMaquina'] ?? ''),
                'estado'            => $_POST['estado'] ?? 'activo'
            ];

            // Validaciones básicas
            if (empty($datosPrevios['nombreTipoMaquina'])) {
                $errores[] = "El nombre del tipo de máquina es obligatorio.";
            }

            // Validar que no exista ya en BD
            if (empty($errores) && $this->modelo->existeTipoMaquina($datosPrevios['nombreTipoMaquina'])) {
                $errores[] = "Ya existe un tipo de máquina con ese nombre.";
            }

            // Guardar
            if (empty($errores)) {
                if ($this->modelo->crearTipoMaquina($datosPrevios)) {
                    header("Location: " . BASE_URL . "tipoMaquinaVer");
                    exit();
                } else {
                    $errores[] = "Error al guardar en la base de datos.";
                }
            }
        }

        // 2. PREPARAR LA VISTA
        $titulo = "Crear Tipo de Máquina";
        $vistaContenido = "app/views/tipoMaquina/tipoMaquinaCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}