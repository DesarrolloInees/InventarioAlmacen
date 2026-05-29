<?php
// app/controllers/inventario/inventarioVerControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/inventarioVerModelo.php';

class inventarioVerControlador
{
    private $db;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new InventarioVerModelo($this->db);
    }

    public function index()
    {
        $inventario = $this->modelo->obtenerInventarioCompleto();

        // Calcular Totales para los KPIs Superiores
        $totalUnidades = 0;
        $totalDinero = 0;
        $itemsBajoStock = 0;

        foreach ($inventario as $item) {
            $totalUnidades += $item['cantidad_total'];
            
            // Determinar el valor de venta correcto
            $valorUnitario = !empty($item['id_repuesto']) ? $item['valor_repuesto'] : $item['valor_producto'];
            $totalDinero += ($item['cantidad_total'] * $valorUnitario);

            if ($item['cantidad_total'] <= 5) {
                $itemsBajoStock++;
            }
        }

        $data = [
            'titulo' => 'Inventario General Unificado',
            'inventario' => $inventario,
            'totales' => [
                'unidades' => $totalUnidades,
                'dinero' => $totalDinero,
                'bajo_stock' => $itemsBajoStock
            ]
        ];
        
        $vistaContenido = "app/views/inventario/inventarioVerVista.php";
        include "app/views/plantillaVista.php";
    }
}