<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/entradaSalida/entradaSalidaModelo.php';

use Spatie\Browsershot\Browsershot;

class EntradaSalidaControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new EntradaSalidaModelo($this->db);
    }

    public function index()
    {
        // Fechas opcionales de GET
        $fechaDesde = isset($_GET['fecha_desde']) && !empty($_GET['fecha_desde']) ? $_GET['fecha_desde'] : null;
        $fechaHasta = isset($_GET['fecha_hasta']) && !empty($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : null;

        // Validar formato (evitar inyección)
        if ($fechaDesde && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDesde))
            $fechaDesde = null;
        if ($fechaHasta && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaHasta))
            $fechaHasta = null;

        // KPIs
        $kpis = $this->modelo->getKpisMovimientos($fechaDesde, $fechaHasta);
        $kpis['total_entradas'] = (int) ($kpis['total_entradas'] ?? 0);
        $kpis['total_salidas'] = (int) ($kpis['total_salidas'] ?? 0);
        $kpis['movimientos_entrada'] = (int) ($kpis['movimientos_entrada'] ?? 0);
        $kpis['movimientos_salida'] = (int) ($kpis['movimientos_salida'] ?? 0);
        $kpis['referencias_distintas'] = (int) ($kpis['referencias_distintas'] ?? 0);
        $kpis['balance_neto'] = $kpis['total_entradas'] - $kpis['total_salidas'];
        $kpis['movimientos_totales'] = $kpis['movimientos_entrada'] + $kpis['movimientos_salida'];

        // Movimientos crudos
        $movimientosRaw = $this->modelo->getMovimientos($fechaDesde, $fechaHasta);
        if (!is_array($movimientosRaw))
            $movimientosRaw = [];

        // Parseo de la observación (misma lógica que la vista con DataTables) y separación Entradas/Salidas
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
                $nombreArticulo = 'MANUAL';
            }

            $novedad = 'N/A';
            $destino = 'N/A';
            $remision = 'N/A';
            $cotizacion = 'N/A';

            $obs = $mov['observacion'] ?? '';

            if (strpos($obs, 'Repuesto Manual:') !== false) {
                $partesManual = explode(' | ', $obs, 2);
                $nombreArticulo = trim(str_replace('Repuesto Manual:', '', $partesManual[0]));
                $obs = count($partesManual) > 1 ? $partesManual[1] : '';
            }

            if (!empty($obs)) {
                $partes = explode(' | ', $obs);
                foreach ($partes as $parte) {
                    if (strpos($parte, 'Novedad:') !== false) {
                        $novedad = trim(str_replace('Novedad:', '', $parte));
                    } elseif (strpos($parte, 'Destino:') !== false) {
                        $destino = trim(str_replace('Destino:', '', $parte));
                    } elseif (strpos($parte, 'Remisión:') !== false) {
                        $remision = trim(str_replace('Remisión:', '', $parte));
                    } elseif (strpos($parte, 'Cotización:') !== false) {
                        $cotizacion = trim(str_replace('Cotización:', '', $parte));
                    }
                }
            }

            $fechaFormateada = date('d/m/Y', strtotime($mov['fecha_movimiento']));

            $filaProcesada = [
                'fecha' => $fechaFormateada,
                'articulo' => $nombreArticulo,
                'cantidad' => $mov['cantidad'],
                'novedad' => $novedad,
                'destino' => $destino,
                'remision' => $remision,
                'cotizacion' => $cotizacion,
                'usuario' => $mov['nombre_usuario'] ?? 'Sistema'
            ];

            if ($mov['tipo_movimiento'] === 'SALIDA') {
                $salidas[] = $filaProcesada;
                $salidasFechasUnicas[$fechaFormateada] = true;
            } elseif ($mov['tipo_movimiento'] === 'ENTRADA') {
                $entradas[] = $filaProcesada;
                $entradasFechasUnicas[$fechaFormateada] = true;
            }
        }

        // Si todo el rango cae en un único día, lo mostramos como subtítulo y ocultamos la columna Fecha
        $fechaUnicaSalidas = count($salidasFechasUnicas) === 1 ? array_key_first($salidasFechasUnicas) : null;
        $fechaUnicaEntradas = count($entradasFechasUnicas) === 1 ? array_key_first($entradasFechasUnicas) : null;

        // Encabezado del reporte
        if ($fechaDesde && $fechaHasta) {
            $fechaReporte = "Desde " . date('d/m/Y', strtotime($fechaDesde)) . " hasta " . date('d/m/Y', strtotime($fechaHasta));
        } else {
            $fechaReporte = date('d/m/Y H:i');
        }

        // Logo
        $rutaLogo = __DIR__ . '/../../logos/logoInees.jpg';
        $logoBase64 = "";
        if (file_exists($rutaLogo)) {
            $type = pathinfo($rutaLogo, PATHINFO_EXTENSION);
            $data = file_get_contents($rutaLogo);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // Renderizar HTML
        if (ob_get_length())
            ob_end_clean();
        ob_start();
        include __DIR__ . '/../../views/entradaSalida/entradaSalidaGenerar.php';
        $html = ob_get_clean();

        // Footer
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

        // Generar PDF con Browsershot
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
            echo "<h1>Error generando PDF de Entradas y Salidas</h1><p>" . $e->getMessage() . "</p>";
            die();
        }
    }
}