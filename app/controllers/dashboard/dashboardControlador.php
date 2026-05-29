<?php
// app/controllers/dashboard/dashboardControlador.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/dashboard/dashboardModelo.php';

class dashboardControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new DashboardModelo($this->db);
    }

    public function index()
    {
        // Recolectamos toda la data para la vista
        $data = [
            'titulo' => 'Panel de Control Almacén',
            'metricas' => $this->modelo->obtenerMetricasGlobales(),
            'grafico' => $this->modelo->obtenerMovimientosRecientes(),
            'alertas' => $this->modelo->obtenerAlertasStock()
        ];

        // app/controllers/dashboard/dashboardControlador.php -> Dentro del método index()

        // Instanciar el modelo de notificaciones de forma pasiva
        require_once __DIR__ . '/../../models/notificacion/notificacionInventarioModelo.php';
        $notificacionesModelo = new NotificacionInventarioModelo($this->db);

        // Consultar si hay algo por agotarse (usando el umbral de 10)
        $alertasCriticasTotales = $notificacionesModelo->obtenerStockCritico(10);

        // Compartimos la cantidad a la sesión para pintar un banner flotante global si hay peligro
        $_SESSION['alerta_flash_stock'] = count($alertasCriticasTotales);

        $vistaContenido = "app/views/dashboard/dashboardVista.php";
        include "app/views/plantillaVista.php";
    }
}