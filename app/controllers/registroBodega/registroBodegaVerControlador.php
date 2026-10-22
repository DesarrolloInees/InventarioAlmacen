<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/registroBodega/registroBodegaVerModelo.php';

use Spatie\Browsershot\Browsershot;

class registroBodegaVerControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RegistroBodegaVerModelo($this->db);
    }

    public function index()
    {
        $filtros = $this->obtenerFiltros();

        $registros = $this->modelo->obtenerRegistros($filtros);
        $kpis      = $this->modelo->obtenerKPIs($filtros);

        // Agrupación visual por remisión + tipo + fecha (una fila por artículo).
        list($registros, $grupos) = $this->agrupar($registros);

        $data = [
            'titulo'    => 'Registro de Entradas / Salidas de Bodega',
            'registros' => $registros,
            'kpis'      => $kpis,
            'grupos'    => $grupos,
            'bodegas'   => $this->modelo->obtenerBodegasActivas(),
            'filtros'   => $filtros,
            'mensaje'   => $this->textoGet('msg'),
        ];

        $vistaContenido = "app/views/registroBodega/registroBodegaVerVista.php";
        $titulo = 'Registro de Entradas / Salidas de Bodega';
        include "app/views/plantillaVista.php";
    }

    /**
     * Agrupa los registros por tipo + documento + fecha.
     * Devuelve [registros con grupo_key, resumen de grupos, filas por grupo].
     */
    private function agrupar(array $registros)
    {
        $grupos = [];
        $agrupados = [];

        foreach ($registros as $indice => $fila) {
            $documento = !empty($fila['remision'])
                ? $fila['remision']
                : 'RB-' . $fila['id_registro'];

            $clave = $fila['tipo_movimiento'] . '|' . $documento . '|' . date('Y-m-d', strtotime($fila['fecha_movimiento']));
            $registros[$indice]['grupo_key'] = $clave;

            if (!isset($grupos[$clave])) {
                $etiqueta = !empty($fila['remision'])
                    ? 'REMISIÓN N° ' . $fila['remision']
                    : 'DOCUMENTO INTERNO RB-' . str_pad((string) $fila['id_registro'], 6, '0', STR_PAD_LEFT);

                $etiqueta .= ' · ' . $fila['tipo_movimiento'];
                $etiqueta .= ' · ' . date('d/m/Y', strtotime($fila['fecha_movimiento']));

                if (!empty($fila['destino'])) {
                    $prefijo = ($fila['tipo_movimiento'] === 'SALIDA') ? 'Destino' : 'Procedencia';
                    $etiqueta .= ' · ' . $prefijo . ': ' . $fila['destino'];
                }

                $grupos[$clave] = [
                    'label'    => $etiqueta,
                    'items'    => 0,
                    'total'    => 0.0,
                    'anulados' => 0,
                ];
            }

            $grupos[$clave]['items']++;
            $grupos[$clave]['total'] += (float) $fila['cantidad'];
            if ($fila['estado'] === 'ANULADO') {
                $grupos[$clave]['anulados']++;
            }

            $agrupados[$clave][] = $registros[$indice];
        }

        return [$registros, $grupos, $agrupados];
    }

    private function describirFiltros(array $filtros)
    {
        $partes = [];
        if (!empty($filtros['tipo'])) $partes[] = 'Tipo: ' . $filtros['tipo'];
        if (!empty($filtros['fecha_desde'])) $partes[] = 'Desde: ' . date('d/m/Y', strtotime($filtros['fecha_desde']));
        if (!empty($filtros['fecha_hasta'])) $partes[] = 'Hasta: ' . date('d/m/Y', strtotime($filtros['fecha_hasta']));
        if (!empty($filtros['id_bodega'])) $partes[] = 'Bodega ID: ' . $filtros['id_bodega'];
        if (!empty($filtros['remision'])) $partes[] = 'Remisión: ' . $filtros['remision'];
        if (!empty($filtros['q'])) $partes[] = 'Búsqueda: ' . $filtros['q'];
        return $partes ? implode(' · ', $partes) : 'Todos los registros';
    }

    public function generarPdf()
    {
        $filtros = $this->obtenerFiltros();
        $registros = $this->modelo->obtenerRegistros($filtros);
        list($registros, $grupos, $agrupados) = $this->agrupar($registros);
        $kpis = $this->modelo->obtenerKPIs($filtros);

        $rutaLogo = __DIR__ . '/../../logos/logoInees.jpg';
        $logoBase64 = '';
        if (file_exists($rutaLogo)) {
            $logoBase64 = 'data:image/' . pathinfo($rutaLogo, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($rutaLogo));
        }

        $data = [
            'registros'     => $registros,
            'grupos'        => $grupos,
            'agrupados'     => $agrupados,
            'kpis'          => $kpis,
            'filtros'       => $filtros,
            'filtros_texto' => $this->describirFiltros($filtros),
            'logo'          => $logoBase64,
            'generado'      => date('d/m/Y H:i'),
            'usuario'       => $_SESSION['usuario_name'] ?? 'Sistema',
        ];

        while (ob_get_level()) {
            ob_end_clean();
        }

        ob_start();
        include __DIR__ . '/../../views/registroBodega/registroBodegaVerPdf.php';
        $html = ob_get_clean();

        $footerHtml = '<div style="width:100%;font-size:9px;padding:0 15px 10px 15px;font-family:sans-serif;color:#64748b;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">'
            . '<div style="width:33%;text-transform:uppercase;letter-spacing:1px;font-weight:bold;">Control de Inventario</div>'
            . '<div style="width:33%;text-align:center;font-weight:bold;">Generado: ' . date('d/m/Y H:i') . '</div>'
            . '<div style="width:33%;text-align:right;"><span style="background-color:#f8fafc;border:1px solid #cbd5e1;padding:2px 8px;border-radius:4px;font-weight:bold;color:#475569;">Página <span class="pageNumber"></span></span></div>'
            . '</div>';

        try {
            $browsershot = Browsershot::html($html)
                ->setNodeBinary('C:\\Program Files\\nodejs\\node.exe')
                ->setNpmBinary('C:\\Program Files\\nodejs\\npm.cmd')
                ->setOption('args', ['--no-sandbox'])
                ->format('A4')
                ->landscape()
                ->margins(8, 8, 14, 8)
                ->scale(0.8)
                ->timeout(120)
                ->showBrowserHeaderAndFooter()
                ->headerHtml('<div></div>')
                ->footerHtml($footerHtml);

            $chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
            if (file_exists($chromePath)) {
                $browsershot->setChromePath($chromePath);
            }

            $pdfContent = $browsershot->pdf();
            $nombreArchivo = 'RegistroBodega_' . date('d-m-Y_H-i') . '.pdf';

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $nombreArchivo . '"');
            header('Content-Length: ' . strlen($pdfContent));
            echo $pdfContent;
            exit;
        } catch (Throwable $e) {
            http_response_code(500);
            echo '<h1>Error generando PDF</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }

    private function obtenerFiltros()
    {
        $fechaDesde = $this->textoGet('fecha_desde');
        $fechaHasta = $this->textoGet('fecha_hasta');
        $idBodega = $this->textoGet('id_bodega');

        return [
            'tipo'        => strtoupper($this->textoGet('tipo')),
            'fecha_desde' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDesde) ? $fechaDesde : '',
            'fecha_hasta' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaHasta) ? $fechaHasta : '',
            'id_bodega'   => ctype_digit($idBodega) ? (int) $idBodega : '',
            'remision'    => $this->textoGet('remision'),
            'q'           => $this->textoGet('q'),
        ];
    }

    /**
     * Devuelve solo valores escalares y recortados del GET.
     */
    private function textoGet($clave)
    {
        return (isset($_GET[$clave]) && is_string($_GET[$clave])) ? trim($_GET[$clave]) : '';
    }
}
