<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/usuario/usuarioVerModelo.php';

class usuarioVerControlador
{

    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new UsuarioVerModelo($this->db);
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_usuario'])) {
            $this->procesarAccion();
            return;
        }

        $this->cargarVista();
    }

    public function procesarAccion()
    {
        ob_clean();
        header('Content-Type: application/json');

        $accion = $_POST['accion_usuario'] ?? '';
        $id = (int)($_POST['usuario_id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID de usuario no válido.']);
            exit;
        }

        if ($accion === 'aprobar') {
            if ($this->modelo->aprobarUsuario($id)) {
                echo json_encode(['status' => 'success', 'message' => '¡Usuario aprobado exitosamente!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al aprobar el usuario.']);
            }
            exit;
        }

        if ($accion === 'rechazar') {
            if ($this->modelo->rechazarUsuario($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Solicitud de usuario rechazada.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al rechazar la solicitud.']);
            }
            exit;
        }

        if ($accion === 'eliminar') {
            if ($this->modelo->eliminarUsuarioLogicamente($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Usuario eliminado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el usuario.']);
            }
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
        exit;
    }

    public function cargarVista()
    {
        // 1. Obtener datos del modelo
        $listaUsuarios   = $this->modelo->obtenerUsuarios();
        $listaPendientes = $this->modelo->obtenerSolicitudesPendientes();

        // 2. EMPAQUETAR LOS DATOS PARA LA VISTA
        $data = [
            'titulo'     => 'Gestión de Usuarios',
            'usuarios'   => $listaUsuarios,
            'pendientes' => $listaPendientes
        ];

        // 3. Variables sueltas
        $titulo = $data['titulo'];

        // 4. Definir y cargar vistas
        $vistaContenido = "app/views/usuario/usuarioVerVista.php";

        include "app/views/plantillaVista.php";
    }
}
