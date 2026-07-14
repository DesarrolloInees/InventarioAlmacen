<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/bodega/bodegaCrearModelo.php';

class bodegaCrearControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new BodegaCrearModelo($this->db);
    }

    public function index()
    {
        // Si se envía el formulario por POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre_bodega = trim($_POST['nombre_bodega'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $estado = $_POST['estado'] ?? 1;

            if (!empty($nombre_bodega)) {
                $resultado = $this->modelo->insertarBodega($nombre_bodega, $ubicacion, $estado);
                if ($resultado) {
                    header('Location: ' . BASE_URL . 'bodegaVer');
                    exit();
                } else {
                    $error = "Error al guardar la bodega en la base de datos.";
                }
            } else {
                $error = "El nombre de la bodega es obligatorio.";
            }
        }

        $data = [
            'titulo' => 'Nueva Bodega',
            'error' => $error ?? null
        ];

        $vistaContenido = "app/views/bodega/bodegaCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}