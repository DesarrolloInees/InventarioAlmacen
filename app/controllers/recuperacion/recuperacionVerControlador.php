<?php
// app/controllers/recuperacion/recuperacionVerControlador.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/recuperacion/recuperacionVerModelo.php';

class recuperacionVerControlador
{
    private $db;
    private $modelo;
    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RecuperacionVerModelo($this->db);
    }
    public function index()
    {
        $data = ['titulo' => 'Historial de Recuperados', 'recuperados' => $this->modelo->obtenerHistorialRecuperados()];
        if (isset($_SESSION['error_eliminar'])) { $data['error'] = $_SESSION['error_eliminar']; unset($_SESSION['error_eliminar']); }
        if (isset($_SESSION['exito_eliminar'])) { $data['exito'] = $_SESSION['exito_eliminar']; unset($_SESSION['exito_eliminar']); }
        $vistaContenido = "app/views/recuperacion/recuperacionVerVista.php";
        include "app/views/plantillaVista.php";
    }
    public function eliminar()
    {
        $nivel = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivel != 1 && $nivel != 2) { header("Location: " . BASE_URL . "recuperacionVer"); exit(); }
        $id = intval($_GET['id'] ?? $_POST['id_movimiento'] ?? 0);
        if ($id <= 0) { header("Location: " . BASE_URL . "recuperacionVer"); exit(); }
        $res = $this->modelo->eliminarRecuperado($id);
        if ($res === true) $_SESSION['exito_eliminar'] = "Entrada recuperada anulada y stock recalculado.";
        elseif ($res === 'error_stock') $_SESSION['error_eliminar'] = "No se puede anular: el stock quedaria en negativo.";
        elseif ($res === 'no_permitido') $_SESSION['error_eliminar'] = "Ese movimiento no es un RECUPERADO.";
        else $_SESSION['error_eliminar'] = "Error interno al anular.";
        header("Location: " . BASE_URL . "recuperacionVer");
        exit();
    }
}
