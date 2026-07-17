<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/repuestoFormulaEditarModelo.php';

class repuestoFormulaEditarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RepuestoFormulaEditarModelo($this->db);
    }

    public function index()
    {
        $id_padre = $_GET['id'] ?? null;
        if (!$id_padre) {
            header('Location: ' . BASE_URL . 'repuestoFormulaVer');
            exit();
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $componentes = $_POST['componentes'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];

            if (!empty($componentes)) {
                $resultado = $this->modelo->actualizarFormula($id_padre, $componentes, $cantidades);
                if ($resultado === true) {
                    header('Location: ' . BASE_URL . 'repuestoFormulaVer');
                    exit();
                } else {
                    $error = $resultado;
                }
            } else {
                $error = "Debe registrar al menos un componente.";
            }
        }

        $data = [
            'titulo' => 'Modificar Fórmula',
            'padre' => $this->modelo->obtenerPadrePorId($id_padre),
            'componentes_actuales' => $this->modelo->obtenerDetallesFormula($id_padre),
            'repuestos' => $this->modelo->obtenerTodosRepuestos(),
            'error' => $error
        ];

        $vistaContenido = "app/views/inventario/repuestoFormulaEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}