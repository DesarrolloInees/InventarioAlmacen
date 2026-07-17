<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/bodega/bodegaEditarModelo.php';

class bodegaEditarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new BodegaEditarModelo($this->db);
    }

    public function index()
    {
        // Tu Router manda el ID por GET['id']
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: ' . BASE_URL . 'bodegaVer');
            exit();
        }

        // Procesar POST para actualizar
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_bodega = trim($_POST['nombre_bodega'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $estado = $_POST['estado'] ?? 1;

            if (!empty($nombre_bodega)) {
                $resultado = $this->modelo->actualizarBodega($id, $nombre_bodega, $ubicacion, $estado);
                if ($resultado) {
                    header('Location: ' . BASE_URL . 'bodegaVer');
                    exit();
                } else {
                    $error = "Error al actualizar la bodega.";
                }
            } else {
                $error = "El nombre de la bodega no puede estar vacío.";
            }
        }

        // Cargar datos actuales
        $bodegaActual = $this->modelo->obtenerBodegaPorId($id);
        
        if (!$bodegaActual) {
            header('Location: ' . BASE_URL . 'bodegaVer');
            exit();
        }

        $data = [
            'titulo' => 'Editar Bodega',
            'bodega' => $bodegaActual,
            'error' => $error ?? null
        ];

        $vistaContenido = "app/views/bodega/bodegaEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}