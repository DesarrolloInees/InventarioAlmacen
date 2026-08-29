<?php
// app/controllers/dashboard/dashboardControlador.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/dashboard/dashboardModelo.php';

class dashboardControlador
{
    private $db, $dbInventario, $modelo;

    public function __construct()
    {
        // Conexión principal: usuarios, solicitudes, sesiones, etc.
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();

        // Nueva conexión: base real de inventario (productos, repuestos, stock)
        $this->dbInventario = $conexionObj->getConexionRemota();

        $this->modelo = new DashboardModelo($this->db, $this->dbInventario);
    }

    /**
     * Mostrar dashboard
     */
    public function index()
    {
        // Recolectamos toda la data para la vista
        $data = [
            'titulo' => 'Panel de Control Almacén',
            'metricas' => $this->modelo->obtenerMetricasGlobales(),
            'grafico' => $this->modelo->obtenerMovimientosRecientes(),
            'alertas' => $this->modelo->obtenerAlertasStock(),
            'solicitudes_recientes' => $this->modelo->obtenerSolicitudesRecientes()
        ];

        // Instanciar el modelo de notificaciones
        require_once __DIR__ . '/../../models/notificacion/notificacionInventarioModelo.php';

        $notificacionesModelo = new NotificacionInventarioModelo($this->db, $this->dbInventario);

        // Consultar si hay algo por agotarse
        $alertasCriticasTotales = $notificacionesModelo->obtenerStockCritico(10);

        // Compartimos la cantidad a la sesión
        $_SESSION['alerta_flash_stock'] = count($alertasCriticasTotales);

        // Vista principal del dashboard
        $vistaContenido = "app/views/dashboard/dashboardVista.php";

        include "app/views/plantillaVista.php";
    }


    /**
     * Exportar dashboard a PDF con mPDF
     */
    public function exportarPDF()
    {
        // Cargamos el autoload de Composer
        require_once __DIR__ . '/../../../vendor/autoload.php';

        // Recolectamos datos para el reporte
        $data = [
            'titulo' => 'Panel de Control Almacén',
            'metricas' => $this->modelo->obtenerMetricasGlobales(),
            'grafico' => $this->modelo->obtenerMovimientosRecientes(),
            'alertas' => $this->modelo->obtenerAlertasStock()
        ];

        // Capturamos el HTML de la vista PDF
        ob_start();
        include __DIR__ . '/../../views/dashboard/dashboardPDF.php';
        $html = ob_get_clean();

        // Instanciamos mPDF (remplazando Dompdf)
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'tempDir' => sys_get_temp_dir()
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('dashboard-inventario.pdf', \Mpdf\Output\Destination::DOWNLOAD);
        exit;
    }
}