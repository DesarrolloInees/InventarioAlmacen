<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../models/inventario/inventarioModelo.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../helpers/correoHelper.php';

class inventarioControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexionRemota();
        $this->modelo = new inventarioModelo($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
            if ($_POST['accion'] === 'actualizar_inventario') {
                $this->procesarActualizacion();
            } elseif ($_POST['accion'] === 'solicitud_actualizacion_almacen') {
                $this->procesarSolicitudActualizacion();
            }
        }
    }

    public function procesarSolicitudActualizacion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json; charset=UTF-8');

        $asunto  = trim($_POST['asunto'] ?? '');
        $detalle = trim($_POST['detalle'] ?? '');
        $destino = 'almacen@inees.co';

        if (empty($asunto) || empty($detalle)) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Por favor completa el asunto y el detalle de la solicitud.'
            ]);
            exit;
        }

        $usuarioNombre = $_SESSION['usuario_name'] ?? $_SESSION['nombre'] ?? 'Usuario del Sistema';
        $usuarioId     = $_SESSION['usuario_id'] ?? 1;

        // 1. Enviar correo a almacen@inees.com
        $enviado = enviarSolicitudActualizacionAlmacen($asunto, $detalle, $usuarioNombre, '', $destino);

        // 2. Registrar notificación interna en el sistema
        try {
            $conexionObj = new Conexion();
            $dbLocal = $conexionObj->getConexion();
            $stmt = $dbLocal->prepare("INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, leida, fecha_creacion) VALUES (:usuario_id, 'solicitud_almacen', :titulo, :mensaje, 0, NOW())");
            $stmt->execute([
                ':usuario_id' => $usuarioId,
                ':titulo'     => "Solicitud a Almacén: {$asunto}",
                ':mensaje'    => "Enviada a {$destino} por {$usuarioNombre}. Detalle: " . mb_substr($detalle, 0, 100)
            ]);
        } catch (Exception $e) {
            error_log("Error creando notificación solicitud almacén: " . $e->getMessage());
        }

        echo json_encode([
            'ok' => true,
            'correo' => $destino,
            'mensaje' => "¡Solicitud enviada exitosamente al correo predeterminado {$destino}!"
        ]);
        exit;
    }

    public function index()
    {
        $rolUsuario = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;

        if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
            if ($rolUsuario != 1) {
                header('Location: ' . BASE_URL . 'inventario');
                exit;
            }
            $this->cargarFormularioEditar((int) $_GET['editar']);
        } else {
            $inventario = $this->modelo->obtenerInventarioDisponible();
            $titulo = "Inventario Disponible";
            $vistaContenido = "app/views/inventario/inventarioVista.php";

            include "app/views/plantillaVista.php";
        }
    }

    public function cargarFormularioEditar($inventario_id)
    {
        $rolUsuario = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
        if ($rolUsuario != 1) {
            header('Location: ' . BASE_URL . 'inventario');
            exit;
        }

        $item = $this->modelo->obtenerInventarioPorId($inventario_id);

        if (!$item) {
            $this->index();
            return;
        }

        $titulo = "Editar Inventario";
        $vistaContenido = "app/views/inventario/inventarioEditarVista.php";

        include "app/views/plantillaVista.php";
    }

    public function procesarActualizacion()
    {
        $rolUsuario = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
        if ($rolUsuario != 1) {
            header('Location: ' . BASE_URL . 'inventario');
            exit;
        }

        $inventario_id = $_POST['inventario_id'] ?? 0;
        $cantidad = $_POST['cantidad_disponible'] ?? 0;
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $estado = $_POST['estado'] ?? 'disponible';

        $estadosValidos = ['disponible', 'stock_bajo', 'agotado', 'inactivo'];

        if (!is_numeric($inventario_id) || (int) $inventario_id <= 0) {
            header('Location: ' . BASE_URL . 'inventario');
            exit;
        }

        if (!in_array($estado, $estadosValidos)) {
            $estado = 'disponible';
        }

        $this->modelo->actualizarInventario((int) $inventario_id, (int) $cantidad, $ubicacion, $estado);

        // Verificamos si quedó en stock bajo para notificar
        $item = $this->modelo->obtenerInventarioPorId((int) $inventario_id);
        if ($item) {
            $this->verificarYNotificarStockBajo($item['nombre_producto'], (int) $cantidad);
        }

        header('Location: ' . BASE_URL . 'inventario');
        exit;
    }

    /**
     * Evalúa automáticamente si un producto tiene menos de 5 unidades.
     * Si se cumple la condición, dispara la alerta a la BD y al correo.
     */
    public function verificarYNotificarStockBajo($nombreProducto, $cantidadRestante)
    {
        // Regla automática: Menos de 5 unidades en stock
        if ($cantidadRestante < 5) {
            
            // 1. Consulta los superusuarios activos ('Super Usuario')
            $superusuarios = $this->modelo->obtenerSuperusuarios();

            if (!empty($superusuarios)) {
                
                // 2. Envía la alerta por correo vía PHPMailer
                enviarNotificacionStockBajo($superusuarios, $nombreProducto, $cantidadRestante);

                // 3. Registra la alerta en la tabla 'notificaciones' de la web
                $mensajeAlerta = "⚠️ Stock crítico: El producto '{$nombreProducto}' tiene solo {$cantidadRestante} unidades disponibles.";
                
                foreach ($superusuarios as $admin) {
                    $this->modelo->crearNotificacionSistema($admin['usuario_id'], $mensajeAlerta, 'stock_bajo', "Stock bajo: {$nombreProducto}");
                }
            }
        }
    }
}