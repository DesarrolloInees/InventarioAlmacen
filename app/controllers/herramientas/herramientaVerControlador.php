<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/herramientas/herramientaVerModelo.php';

class herramientaVerControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new HerramientaVerModelo($this->db);
    }

    public function index()
    {
        $herramientas = $this->modelo->obtenerHerramientas();

        // KPIs del encabezado
        $totalItems = count($herramientas);
        $stockTotal = array_sum(array_column($herramientas, 'stock'));
        $itemsBajoStock = 0;
        foreach ($herramientas as $h) {
            if ($h['stock'] <= 5) {
                $itemsBajoStock++;
            }
        }

        $data = [
            'titulo' => 'Inventario de Herramientas',
            'herramientas' => $herramientas,
            'totales' => [
                'items' => $totalItems,
                'stock' => $stockTotal,
                'bajo_stock' => $itemsBajoStock
            ]
        ];

        $vistaContenido = "app/views/herramientas/herramientaVerVista.php";
        include "app/views/plantillaVista.php";
    }
}
