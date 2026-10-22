<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/registroBodega/registroBodegaEliminarModelo.php';

class registroBodegaEliminarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RegistroBodegaEliminarModelo($this->db);
    }

    public function index()
    {
        // Solo administradores (1 = SuperAdmin, 2 = Supervisor) pueden anular.
        $nivel_acceso = (int) ($_SESSION['nivel_acceso'] ?? 0);
        if ($nivel_acceso !== 1 && $nivel_acceso !== 2) {
            die("No tienes permisos para realizar esta acción.");
        }

        $idGet = (isset($_GET['id']) && is_scalar($_GET['id'])) ? (int) $_GET['id'] : 0;
        $idPost = (isset($_POST['id_registro']) && is_scalar($_POST['id_registro'])) ? (int) $_POST['id_registro'] : 0;
        $id = $idGet > 0 ? $idGet : $idPost;

        if ($id <= 0) {
            header('Location: ' . BASE_URL . 'registroBodegaVer?msg=noencontrado');
            exit();
        }

        $registro = $this->modelo->obtenerRegistroPorId($id);
        if (!$registro) {
            header('Location: ' . BASE_URL . 'registroBodegaVer?msg=noencontrado');
            exit();
        }

        $error = null;

        // Confirmación enviada desde la vista de eliminar.
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
            if ($registro['estado'] === 'ANULADO') {
                header('Location: ' . BASE_URL . 'registroBodegaVer?msg=ya_anulado');
                exit();
            }

            if ($this->modelo->anularRegistro($id)) {
                header('Location: ' . BASE_URL . 'registroBodegaVer?msg=anulado');
                exit();
            }

            $error = "No fue posible anular el registro. Intenta nuevamente.";
        }

        $data = [
            'titulo'      => 'Anular Registro de Bodega',
            'error'       => $error,
            'registro'    => $registro,
            'id_registro' => $id,
        ];

        $vistaContenido = "app/views/registroBodega/registroBodegaEliminarVista.php";
        $titulo = 'Anular Registro de Bodega';
        include "app/views/plantillaVista.php";
    }
}
