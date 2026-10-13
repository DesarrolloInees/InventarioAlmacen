<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/herramientas/herramientaEditarModelo.php';

class herramientaEditarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new HerramientaEditarModelo($this->db);
    }

    public function index()
    {
        $nivel_acceso = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivel_acceso != 1 && $nivel_acceso != 2) {
            die("No tienes permisos para realizar esta acción.");
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . 'herramientaVer');
            exit();
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre_herramienta'] ?? '');
            $marca = !empty(trim($_POST['marca'] ?? '')) ? trim($_POST['marca']) : null;
            $modelo = !empty(trim($_POST['modelo'] ?? '')) ? trim($_POST['modelo']) : null;
            $codigo = !empty(trim($_POST['codigo_referencia'] ?? '')) ? trim($_POST['codigo_referencia']) : null;
            $descripcion = !empty(trim($_POST['descripcion'] ?? '')) ? trim($_POST['descripcion']) : null;
            $condicion = $_POST['condicion'] ?? 'usada';
            $stock = isset($_POST['stock']) && $_POST['stock'] !== '' ? (int)$_POST['stock'] : 0;
            $id_bodega = !empty($_POST['id_bodega']) ? $_POST['id_bodega'] : null;

            if (!empty($nombre)) {
                $resultado = $this->modelo->actualizarHerramienta($id, $nombre, $marca, $modelo, $codigo, $descripcion, $condicion, $stock, $id_bodega);

                if ($resultado === true) {
                    header('Location: ' . BASE_URL . 'herramientaVer');
                    exit();
                } else {
                    if ($resultado === 'DUPLICADO') {
                        $error = "El código de referencia '$codigo' ya está en uso por otra herramienta.";
                    } else {
                        $error = "Error al actualizar la herramienta.";
                    }
                }
            } else {
                $error = "El nombre de la herramienta es obligatorio.";
            }
        }

        $herramientaActual = $this->modelo->obtenerHerramientaPorId($id);
        if (!$herramientaActual) {
            header('Location: ' . BASE_URL . 'herramientaVer');
            exit();
        }

        $data = [
            'titulo' => 'Editar Herramienta',
            'herramienta' => $herramientaActual,
            'bodegas' => $this->modelo->obtenerBodegasActivas(),
            'error' => $error
        ];

        $vistaContenido = "app/views/herramientas/herramientaEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}
