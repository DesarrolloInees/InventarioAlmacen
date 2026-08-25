<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/entradaSalida/entradaSalidaModelo.php';

use Spatie\Browsershot\Browsershot;

class entradaSalidaControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new EntradaSalidaModelo($this->db);
    }

    private function procesarDatos($fechaDesde, $fechaHasta)
    {
        if ($fechaDesde && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDesde)) $fechaDesde = null;
        if ($fechaHasta && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaHasta)) $fechaHasta = null;

        $kpis = $this->modelo->getKpisMovimientos($fechaDesde, $fechaHasta);
        $kpis['total_entradas'] = (int) ($kpis['total_entradas'] ?? 0);
        $kpis['total_salidas'] = (int) ($kpis['total_salidas'] ?? 0);
        $kpis['movimientos_entrada'] = (int) ($kpis['movimientos_entrada'] ?? 0);
        $kpis['movimientos_salida'] = (int) ($kpis['movimientos_salida'] ?? 0);
        $kpis['referencias_distintas'] = (int) ($kpis['referencias_distintas'] ?? 0);
        $kpis['balance_neto'] = $kpis['total_entradas'] - $kpis['total_salidas'];
        $kpis['movimientos_totales'] = $kpis['movimientos_entrada'] + $kpis['movimientos_salida'];

        $movimientosRaw = $this->modelo->getMovimientos($fechaDesde, $fechaHasta);
        if (!is_array($movimientosRaw)) $movimientosRaw = [];

        $salidas = [];
        $entradas = [];
        $salidasFechasUnicas = [];
        $entradasFechasUnicas = [];

        foreach ($movimientosRaw as $mov) {
            if (!empty($mov['nombre_repuesto'])) {
                $nombreArticulo = $mov['codigo_referencia'] . ' - ' . $mov['nombre_repuesto'];
            } elseif (!empty($mov['nombre_producto'])) {
                $nombreArticulo = $mov['codigo_interno'] . ' - ' . $mov['nombre_producto'];
            } else {
                $nombreArticulo = !empty($mov['repuesto_manual']) ? $mov['repuesto_manual'] : 'MANUAL';
            }

            $fechaFormateada = date('d/m/Y', strtotime($mov['fecha_movimiento']));

            $filaProcesada = [
                'id_movimiento' => $mov['id_movimiento'],
                'fecha' => $fechaFormateada,
                'articulo' => $nombreArticulo,
                'cantidad' => $mov['cantidad'],
                'novedad' => !empty($mov['novedad']) ? $mov['novedad'] : 'N/A',
                'destino' => !empty($mov['destino']) ? $mov['destino'] : 'N/A',
                'remision' => !empty($mov['numero_remision']) ? $mov['numero_remision'] : 'N/A',
                'cotizacion' => !empty($mov['numero_cotizacion']) ? $mov['numero_cotizacion'] : 'N/A',
                'usuario' => $mov['nombre_usuario'] ?? 'Sistema',
                'tipo' => $mov['tipo_movimiento']
            ];

            if ($mov['tipo_movimiento'] === 'SALIDA') {
                $salidas[] = $filaProcesada;
                $salidasFechasUnicas[$fechaFormateada] = true;
            } elseif ($mov['tipo_movimiento'] === 'ENTRADA') {
                $entradas[] = $filaProcesada;
                $entradasFechasUnicas[$fechaFormateada] = true;
            }
        }

        $fechaUnicaSalidas = count($salidasFechasUnicas) === 1 ? array_key_first($salidasFechasUnicas) : null;
        $fechaUnicaEntradas = count($entradasFechasUnicas) === 1 ? array_key_first($entradasFechasUnicas) : null;

        return [
            'kpis' => $kpis,
            'entradas' => $entradas,
            'salidas' => $salidas,
            'movimientosRaw' => $movimientosRaw,
            'fechaUnicaSalidas' => $fechaUnicaSalidas,
            'fechaUnicaEntradas' => $fechaUnicaEntradas,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta
        ];
    }

    // MÉTODO INDEX PRINCIPAL (SIEMPRE LLAMADO POR EL ROUTER)
    public function index()
    {
        // DETECCIÓN INTELIGENTE: Si viene la petición de PDF por cualquier vía GET
        if ((isset($_GET['accion']) && $_GET['accion'] === 'generarPdf') || (isset($_GET['export']) && $_GET['export'] === 'pdf')) {
            $this->generarPdf();
            return;
        }

        $fechaDesde = $_GET['fecha_desde'] ?? null;
        $fechaHasta = $_GET['fecha_hasta'] ?? null;

        $datos = $this->procesarDatos($fechaDesde, $fechaHasta);
        extract($datos);

        $vistaContenido = "app/views/entradaSalida/entradaSalidaVista.php";
        include "app/views/plantillaVista.php";
    }

    public function actualizarFecha()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idMovimiento = filter_input(INPUT_POST, 'id_movimiento', FILTER_VALIDATE_INT);
            $nuevaFecha = filter_input(INPUT_POST, 'nueva_fecha', FILTER_DEFAULT);

            if ($idMovimiento && $nuevaFecha && preg_match('/^\d{4}-\d{2}-\d{2}$/', $nuevaFecha)) {
                $fechaHoraCompleta = $nuevaFecha . ' ' . date('H:i:s');
                $this->modelo->actualizarFechaMovimiento($idMovimiento, $fechaHoraCompleta);
            }
        }

        header('Location: ' . BASE_URL . 'entradaSalida');
        exit;
    }

    public function generarPdf()
    {
        $fechaDesde = $_GET['fecha_desde'] ?? null;
        $fechaHasta = $_GET['fecha_hasta'] ?? null;

        $datos = $this->procesarDatos($fechaDesde, $fechaHasta);
        extract($datos);

        if ($fechaDesde && $fechaHasta) {
            $fechaReporte = "Desde " . date('d/m/Y', strtotime($fechaDesde)) . " hasta " . date('d/m/Y', strtotime($fechaHasta));
        } else {
            $fechaReporte = date('d/m/Y H:i');
        }

        $rutaLogo = __DIR__ . '/../../logos/logoInees.jpg';
        $logoBase64 = "";
        if (file_exists($rutaLogo)) {
            $type = pathinfo($rutaLogo, PATHINFO_EXTENSION);
            $data = file_get_contents($rutaLogo);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // Limpiar buffers de PHP para enviar solo el PDF
        while (ob_get_level()) {
            ob_end_clean();
        }

        ob_start();
        include __DIR__ . '/../../views/entradaSalida/entradaSalidaGenerar.php';
        $html = ob_get_clean();

        $footerHtml = '
        <div style="width: 100%; font-size: 9px; padding: 0 15px 10px 15px; font-family: sans-serif; color: #64748b; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="width: 33%; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;">
                Control de Inventario
            </div>
            <div style="width: 33%; text-align: center; font-weight: bold;">
                Generado: ' . $fechaReporte . '
            </div>
            <div style="width: 33%; text-align: right;">
                <span style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 2px 8px; border-radius: 4px; font-weight: bold; color: #475569;">
                    Página <span class="pageNumber"></span>
                </span>
            </div>
        </div>';

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
                ->scale(0.75)
                ->timeout(120)
                ->showBrowserHeaderAndFooter()
                ->headerHtml('<div></div>')
                ->footerHtml($footerHtml);

            if ($chromePath) {
                $browsershot->setChromePath($chromePath);
            }

            $pdfContent = $browsershot->pdf();
            $nombreArchivo = "Reporte_EntradasSalidas_" . date('d-m-Y_H-i') . ".pdf";

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $nombreArchivo . '"');
            header('Content-Length: ' . strlen($pdfContent));
            echo $pdfContent;
            exit;

        } catch (Exception $e) {
            echo "<h1>Error generando PDF</h1><p>" . $e->getMessage() . "</p>";
            die();
        }
    }
}