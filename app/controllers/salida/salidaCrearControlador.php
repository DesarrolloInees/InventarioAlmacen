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
            $idTecnico = $_POST['id_tecnico'];
            $tipoAsignacion = $_POST['tipo_asignacion'];
            $items = json_decode($_POST['items'], true);

            // 1. PROCESAR LA SALIDA DE INVENTARIO
            $res = $this->modelo->procesarSalidaMultiple($idTecnico, $items, $idAdmin, $tipoAsignacion);
            
            // --- LOG DE SEGUIMIENTO 1 ---
            error_log("[GATILLO SEGUIMIENTO] 1. Salida procesada. Resultado del modelo: " . json_encode($res));
            
            // Evaluamos de forma flexible si el resultado es exitoso (por si viene como true, 1 o estatus)
            $esSalidaExitosa = false;
            if ($res === true || (is_array($res) && isset($res['exito']) && ($res['exito'] === true || $res['exito'] == 1 || $res['exito'] === 'true'))); {
                $esSalidaExitosa = true;
            }

            // ======================================================================
            // 2. GATILLO CRÚDO (SIN BLINDAJE PARA PRUEBAS)
            // ======================================================================
            if ($esSalidaExitosa) {
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
                    // Si el correo o la alerta falla, te mandará un SweetAlert con el error exacto
                    echo json_encode(['exito' => false, 'msg' => "🚨 ERROR EN ALERTAS: " . $t->getMessage() . " | Línea: " . $t->getLine()]);
                    exit(); 
                }
            }
            // ======================================================================

            // 3. RESPONDER INSTANTÁNEAMENTE A LA VISTA
            echo json_encode($res);
            exit();
        }

        $data = [
            'titulo' => 'Despacho y Asignaciones de Almacén',
            'tecnicos' => $this->modelo->obtenerTecnicos(),
            'inventario' => $this->modelo->obtenerInventarioDisponible()
        ];

        $vistaContenido = "app/views/salida/salidaCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}