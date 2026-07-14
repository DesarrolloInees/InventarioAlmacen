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

    // Configuración de cabeceras CORS (Indispensable para que Angular pueda consultar a PHP)
    private function setHeaders() {
        header("Access-Control-Allow-Origin: *"); // En producción, limita esto al puerto de tu Angular (ej: http://localhost:4200)
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    public function index()
    {
        $this->setHeaders();

        // 1. PETICIÓN GET: Devolver la lista de repuestos
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $repuestos = $this->modelo->obtenerTodosRepuestos();
            echo json_encode($repuestos);
            exit();
        }

        // 2. PETICIÓN POST: Guardar la nueva fórmula
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Capturar el cuerpo JSON enviado por Angular
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $id_padre = $data['id_repuesto_padre'] ?? '';
            $insumos = $data['insumos'] ?? [];

            if (!empty($id_padre) && !empty($insumos)) {
                $resultado = $this->modelo->guardarFormula($id_padre, $insumos);
                
                if ($resultado === true) {
                    http_response_code(201); // Created
                    echo json_encode(["status" => "success", "message" => "Fórmula registrada con éxito"]);
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(["status" => "error", "message" => $resultado]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Datos incompletos. Debe seleccionar el repuesto padre y al menos un componente."]);
            }
            exit();
        }
    }
}