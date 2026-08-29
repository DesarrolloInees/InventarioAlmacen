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

    public function actualizarStockAjax()
    {
        $rolUsuario = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
        $esAdmin = ($rolUsuario == 1);

        if (!$esAdmin) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_stock = $_POST['id_stock'] ?? null;
            $nueva_cantidad = $_POST['nueva_cantidad'] ?? null;

            if ($id_stock !== null && $nueva_cantidad !== null) {
                $resultado = $this->modelo->actualizarStockManual($id_stock, $nueva_cantidad);

                if ($resultado) {
                    echo json_encode(['success' => true, 'message' => 'Stock actualizado correctamente.']);
                    exit;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar en la base de datos.']);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Faltan datos para procesar la solicitud.']);
                exit;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        exit;
    }

    // Elimina un registro de inventario_stock (para poder corregir cargas erróneas y re-ingresarlas después)
    public function eliminarStockAjax()
    {
        $rolUsuario = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
        $esAdmin = ($rolUsuario == 1);

        if (!$esAdmin) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_stock = $_POST['id_stock'] ?? null;

            if ($id_stock !== null) {
                $resultado = $this->modelo->eliminarStock($id_stock);

                if ($resultado) {
                    echo json_encode(['success' => true, 'message' => 'Ítem eliminado del inventario correctamente.']);
                    exit;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al eliminar el registro en la base de datos.']);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Falta el id_stock para procesar la solicitud.']);
                exit;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        exit;
    }
}