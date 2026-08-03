<?php
// app/controllers/salida/salidaEditarControlador.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/salida/salidaEditarModelo.php';

class salidaEditarControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new SalidaEditarModelo($this->db);
    }

    public function index()
    {
        $nivelUsuario = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivelUsuario != 1 && $nivelUsuario != 2) {
            header("Location: " . BASE_URL . "salidaVer");
            exit();
        }

        $idMovimiento = $_GET['id'] ?? $_POST['id_movimiento'] ?? null;

        if (!$idMovimiento) {
            header("Location: " . BASE_URL . "salidaVer");
            exit();
        }

        $errores = [];
        $datosSalida = [];

        // 1. PROCESAR GUARDADO (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cantidadNueva = intval($_POST['cantidad'] ?? 0);
            $observacionNueva = trim($_POST['observacion'] ?? '');
            
            // Capturamos los nuevos campos
            $destinoNuevo = trim($_POST['destino'] ?? '');
            $remisionNueva = trim($_POST['numero_remision'] ?? '');
            $cotizacionNueva = trim($_POST['numero_cotizacion'] ?? '');
            $novedadNueva = trim($_POST['novedad'] ?? '');

            if ($cantidadNueva <= 0) {
                $errores[] = "La cantidad asignada debe ser mayor a cero.";
            }

            if (empty($errores)) {
                $resultado = $this->modelo->editarSalida(
                    $idMovimiento, $cantidadNueva, $observacionNueva, 
                    $destinoNuevo, $remisionNueva, $cotizacionNueva, $novedadNueva
                );

                if ($resultado === 'error_stock_local') {
                    $errores[] = "No hay suficiente stock en el almacén principal para aumentar esta asignación.";
                } elseif ($resultado === 'error_stock_tecnico') {
                    $errores[] = "No puedes bajar la cantidad. El técnico ya no tiene suficientes repuestos de este tipo en su inventario externo.";
                } elseif ($resultado === true) {
                    $_SESSION['exito_eliminar'] = "La asignación fue actualizada correctamente.";
                    header("Location: " . BASE_URL . "salidaVer");
                    exit();
                } else {
                    $errores[] = "Error interno al actualizar la base de datos.";
                }
            }

            $datosSalida['cantidad'] = $cantidadNueva;
            $datosSalida['observacion'] = $observacionNueva;
            $datosSalida['destino'] = $destinoNuevo;
            $datosSalida['numero_remision'] = $remisionNueva;
            $datosSalida['numero_cotizacion'] = $cotizacionNueva;
            $datosSalida['novedad'] = $novedadNueva;
        }

        // 2. CARGAR DATOS
        $salidaActual = $this->modelo->obtenerSalidaPorId($idMovimiento);
        if (!$salidaActual) {
            header("Location: " . BASE_URL . "salidaVer");
            exit();
        }

        if (empty($datosSalida)) {
            $datosSalida = $salidaActual;
        } else {
            // Mantenemos la data de lectura si hubo error en POST
            $datosSalida['nombre_repuesto'] = $salidaActual['nombre_repuesto'] ?? null;
            $datosSalida['codigo_referencia'] = $salidaActual['codigo_referencia'] ?? null;
            $datosSalida['nombre_producto'] = $salidaActual['nombre_producto'] ?? null;
            $datosSalida['codigo_interno'] = $salidaActual['codigo_interno'] ?? null;
            $datosSalida['repuesto_manual'] = $salidaActual['repuesto_manual'] ?? null;
            $datosSalida['tecnico_nombre'] = $salidaActual['tecnico_nombre'] ?? null;
        }

        $titulo = "Editar Asignación #" . $idMovimiento;
        $vistaContenido = "app/views/salida/salidaEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}