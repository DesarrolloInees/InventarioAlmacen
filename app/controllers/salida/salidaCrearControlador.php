<?php
// app/controllers/salida/salidaCrearControlador.php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

// --- MODO DEBUG ACTIVADO (Borrar esto antes de subir al servidor final) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -------------------------------------------------------------------------

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/salida/salidaCrearModelo.php';

class salidaCrearControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new SalidaCrearModelo($this->db);
    }

    public function index()
    {
        $idAdmin = $_SESSION['usuario_id'] ?? null;
        if (!$idAdmin) {
            header("Location: " . BASE_URL . "login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'])) {
            $idTecnico = $_POST['id_tecnico'] ?? null;
            $tipoAsignacion = $_POST['tipo_asignacion'] ?? 'interno';
            $items = json_decode($_POST['items'], true);
            
            // Recibimos los datos globales
            $datosGlobales = [
                'destino' => $_POST['destino'] ?? null,
                'remision' => $_POST['remision'] ?? null,
                'cotizacion' => $_POST['cotizacion'] ?? null,
                'novedad' => $_POST['novedad'] ?? null,
            ];

            if (!is_array($items) || count($items) === 0) {
                echo json_encode(['exito' => false, 'msg' => 'No se recibieron ítems para procesar.']);
                exit();
            }

            // Pasamos los globales al modelo
            $res = $this->modelo->procesarMovimientoMultiple($idTecnico, $items, $idAdmin, $tipoAsignacion, $datosGlobales);

            // --- LOG DE SEGUIMIENTO 1 ---
            error_log("[GATILLO SEGUIMIENTO] 1. Movimiento procesado. Resultado del modelo: " . json_encode($res));

            $esExitoso = is_array($res) && isset($res['exito']) && $res['exito'] === true;

            // ======================================================================
            // 2. GATILLO DE ALERTAS DE STOCK CRÍTICO
            // ======================================================================
            if ($esExitoso) {
                try {
                    require_once __DIR__ . '/../../models/notificacion/notificacionInventarioModelo.php';
                    $alertasMod = new NotificacionInventarioModelo($this->db);
                    $hayCriticos = $alertasMod->obtenerStockCritico(ALERTAS_UMBRAL_STOCK_CRITICO);

                    if (!empty($hayCriticos)) {
                        require_once __DIR__ . '/../notificacion/notificacionInventarioControlador.php';
                        $notificador = new NotificacionInventarioControlador();
                        $notificador->procesarNotificacionesStock(true);
                    }
                } catch (\Throwable $t) {
                    echo json_encode(['exito' => false, 'msg' => "🚨 ERROR EN ALERTAS: " . $t->getMessage() . " | Línea: " . $t->getLine()]);
                    exit();
                }
            }
            // ======================================================================

            // 3. RESPONDER A LA VISTA
            echo json_encode($res);
            exit();
        }

        $data = [
            'titulo' => 'Movimientos de Almacén',
            'tecnicos' => $this->modelo->obtenerTecnicos(),
            'inventario' => $this->modelo->obtenerInventarioDisponible(), // para SALIDA (con stock)
            'catalogo' => $this->modelo->obtenerCatalogoCompleto()        // para ENTRADA (todo el catálogo)
        ];

        $vistaContenido = "app/views/salida/salidaCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}