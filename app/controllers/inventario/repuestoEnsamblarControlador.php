<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/repuestoEnsamblarModelo.php';

class repuestoEnsamblarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RepuestoEnsamblarModelo($this->db);
    }

    public function index()
    {
        $error = null;
        $exito = null;
        $id_usuario = $_SESSION['usuario_id'] ?? 1;

        // Si es una petición AJAX para ver los componentes de una receta
        if (isset($_GET['obtener_receta'])) {
            header('Content-Type: application/json');
            $id_padre = intval($_GET['obtener_receta']);
            echo json_encode($this->modelo->obtenerComponentesFormula($id_padre));
            exit();
        }

        // Si procesamos el formulario de Ensamble
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_repuesto_padre = $_POST['id_repuesto_padre'] ?? '';
            $cantidad_a_armar = intval($_POST['cantidad_a_armar'] ?? 0);

            if (!empty($id_repuesto_padre) && $cantidad_a_armar > 0) {
                $resultado = $this->modelo->ejecutarEnsamble($id_repuesto_padre, $cantidad_a_armar, $id_usuario);
                if ($resultado === true) {
                    $exito = "¡Excelente! Se han ensamblado {$cantidad_a_armar} unidades correctamente y el stock fue actualizado.";
                } else {
                    $error = $resultado; // Devuelve el mensaje detallado de falta de stock o error sql
                }
            } else {
                $error = "Debe seleccionar un repuesto válido y una cantidad mayor a cero.";
            }
        }

        $data = [
            'titulo' => 'Ensamblar Repuestos',
            'repuestos_formula' => $this->modelo->obtenerRepuestosConFormula(),
            'error' => $error,
            'exito' => $exito
        ];

        $vistaContenido = "app/views/inventario/repuestoEnsamblarVista.php";
        include "app/views/plantillaVista.php";
    }
}