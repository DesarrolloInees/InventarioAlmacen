<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/herramientas/herramientaCrearModelo.php';

class herramientaCrearControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new HerramientaCrearModelo($this->db);
    }

    public function index()
    {
        // Solo administradores y supervisores pueden crear
        $nivel_acceso = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivel_acceso != 1 && $nivel_acceso != 2) {
            die("No tienes permisos para realizar esta acción.");
        }

        $bodegas = $this->modelo->obtenerBodegasActivas();
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
            $id_usuario_registra = $_SESSION['usuario_id'] ?? null;

            if (!empty($nombre)) {
                $resultado = $this->modelo->insertarHerramienta($nombre, $marca, $modelo, $codigo, $descripcion, $condicion, $stock, $id_bodega, $id_usuario_registra);

                if ($resultado === true) {
                    header('Location: ' . BASE_URL . 'herramientaVer');
                    exit();
                } else {
                    if ($resultado === 'DUPLICADO') {
                        $error = "El código de referencia '$codigo' ya está en uso por otra herramienta.";
                    } else {
                        $error = "Error al guardar la herramienta en la base de datos.";
                    }
                }
            } else {
                $error = "El nombre de la herramienta es obligatorio.";
            }
        }

        $data = [
            'titulo' => 'Registrar Herramienta',
            'bodegas' => $bodegas,
            'error' => $error
        ];

        $vistaContenido = "app/views/herramientas/herramientaCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}
