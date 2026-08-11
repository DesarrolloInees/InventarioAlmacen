<?php
// app/controllers/reportes/reporteInventarioControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/reporte/reporteInventarioModelo.php';

use Spatie\Browsershot\Browsershot;

class reporteInventarioControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new ReporteInventarioModelo($this->db);
    }

    public function index()
    {
        $condicion = isset($_GET['condicion']) ? $_GET['condicion'] : 'recuperado';
        $this->cargarVista($condicion);
    }

    public function cargarVista($condicion)
    {
        $listaInventario = $this->modelo->obtenerInventarioPorCondicion($condicion);

        $data = [
            'titulo' => 'Reporte de Inventario',
            'inventario' => $listaInventario,
            'condicion_actual' => $condicion
        ];

        $vistaContenido = "app/views/reporte/reporteInventarioVista.php";
        include "app/views/plantillaVista.php";
    }

    // index.php ejecutará este método cuando reciba ?accion=generarPdf
    public function generarPdf()
    {
        $condicion = isset($_GET['condicion']) ? $_GET['condicion'] : 'todos';
        $inventario = $this->modelo->obtenerInventarioPorCondicion($condicion);

        // 1. KPIs
        $kpis = [
            'total_referencias' => count($inventario),
            'stock_total' => array_sum(array_column($inventario, 'cantidad_en_stock'))
        ];

        // 2. Variables del encabezado
        $etiquetaCondicion = strtoupper($condicion);
        $fechaReporte = date('d/m/Y H:i');

        // 3. Logo
        $rutaLogo = __DIR__ . '/../../logos/logoInees.jpg';
        $logoBase64 = "";
        if (file_exists($rutaLogo)) {
            $type = pathinfo($rutaLogo, PATHINFO_EXTENSION);
            $data = file_get_contents($rutaLogo);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // 4. Renderizar HTML
        if (ob_get_length()) ob_end_clean();
        ob_start();
        include __DIR__ . '/../../views/reporte/reporteInventarioGenerar.php';
        $html = ob_get_clean();

        // 5. Footer HTML
        $footerHtml = '
        <div style="width: 100%; font-size: 9px; padding: 0 15px 10px 15px; font-family: sans-serif; color: #64748b; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="width: 33%; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;">
                Inventario - Condición: ' . $etiquetaCondicion . '
            </div>
            <div style="width: 33%; text-align: center; font-weight: bold;">
                Generado: ' . $fechaReporte . '
            </div>
            <div style="width: 33%; text-align: right;">
                <span style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 2px 8px; border-radius: 4px; font-weight: bold; color: #475569;">
                    Página <span class="pageNumber"></span> de <span class="totalPages"></span>
                </span>
            </div>
        </div>';

        // 6. Generación con Browsershot
        try {
            $nodePath = 'C:\\Program Files\\nodejs\\node.exe';
            $npmPath = 'C:\\Program Files\\nodejs\\npm.cmd';

            $posiblesRutasChrome = [
                'C:\\Users\\User\\.cache\\puppeteer\\chrome\\win64-144.0.7559.96\\chrome-win64\\chrome.exe',
                'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
                'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe'
            ];
            
            $chromePath = null;
            foreach ($posiblesRutasChrome as $ruta) {
                if (file_exists($ruta)) {
                    $chromePath = $ruta;
                    break;
                }
            }

            $browsershot = Browsershot::html($html)
                ->setNodeBinary($nodePath)
                ->setNpmBinary($npmPath)
                ->setOption('args', ['--no-sandbox'])
                ->format('A4')
                ->landscape()
                ->margins(10, 10, 15, 10)
                ->scale(0.8)
                ->timeout(120)
                ->showBrowserHeaderAndFooter()
                ->headerHtml('<div></div>')
                ->footerHtml($footerHtml);

            if ($chromePath) {
                $browsershot->setChromePath($chromePath);
            }

            $pdfContent = $browsershot->pdf();
            $nombreArchivo = "Inventario_" . ucfirst($condicion) . "_" . date('d-m-Y') . ".pdf";

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $nombreArchivo . '"');
            header('Content-Length: ' . strlen($pdfContent));
            echo $pdfContent;
            exit;

        } catch (Exception $e) {
            echo "<h1>Error generando PDF de Inventario</h1><p>" . $e->getMessage() . "</p>";
            die();
        }
    }
}