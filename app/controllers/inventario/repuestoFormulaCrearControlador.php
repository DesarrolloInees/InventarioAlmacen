<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/repuestoFormulaCrearModelo.php';

class repuestoFormulaCrearControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RepuestoFormulaCrearModelo($this->db);
    }

    public function index()
    {
        // 1. Obtenemos los repuestos para llenar los <select> de la vista
        $repuestos = $this->modelo->obtenerTodosRepuestos();
        $error = null;

        // 2. Si el usuario envía el formulario (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $id_padre = $_POST['id_repuesto_padre'] ?? '';
            
            // Recibimos los arreglos que vienen de los inputs con name="componentes[]" y "cantidades[]"
            $componentes_post = $_POST['componentes'] ?? [];
            $cantidades_post = $_POST['cantidades'] ?? [];
            
            $insumos = [];

            // Transformamos esos arreglos al formato que espera tu modelo
            if (!empty($componentes_post)) {
                foreach ($componentes_post as $index => $id_hijo) {
                    if (!empty($id_hijo)) {
                        $insumos[] = [
                            'id_repuesto_hijo' => $id_hijo,
                            'cantidad' => $cantidades_post[$index] ?? 1
                        ];
                    }
                }
            }

            // Validamos y guardamos
            if (!empty($id_padre) && !empty($insumos)) {
                $resultado = $this->modelo->guardarFormula($id_padre, $insumos);
                
                if ($resultado === true) {
                    // Si guarda bien, redireccionamos a la tabla de ver fórmulas
                    header('Location: ' . BASE_URL . 'repuestoFormulaVer');
                    exit();
                } else {
                    $error = $resultado; // Mensaje de error (ej: fórmula duplicada)
                }
            } else {
                $error = "Debe seleccionar el repuesto padre y al menos un componente válido.";
            }
        }

        // 3. Preparamos la data y cargamos la vista HTML (Ya no devolvemos JSON)
        $data = [
            'titulo' => 'Estructurar Nueva Fórmula',
            'repuestos' => $repuestos,
            'error' => $error
        ];

        $vistaContenido = "app/views/inventario/repuestoFormulaCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}