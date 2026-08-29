<?php
// app/controllers/solicitudes/solicitudesControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../models/solicitudes/solicitudesModelo.php';
require_once __DIR__ . '/../../config/conexion.php';

class solicitudesControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new solicitudesModelo($this->db);

        // Captura las peticiones AJAX (Aprobar/Rechazar) que envían 'accion' por POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
            $this->procesarAccion();
        }
    }

    // Método que ejecuta el enrutador automáticamente para la acción 'crear'
    public function crear()
    {
        $this->cargarVistaCrear();
    }

    // Método que ejecuta el enrutador automáticamente para la acción 'guardar'
    public function guardar()
    {
        $this->guardarSolicitud();
    }

    // Método que ejecuta el enrutador automáticamente para la acción 'exportarPdf'
    public function exportarPdf()
    {
        require_once __DIR__ . '/../../../vendor/autoload.php';

        $solicitudes = $this->modelo->obtenerSolicitudes();

        $html = '<h2 style="text-align: center; color: #0054A6; font-family: sans-serif;">Reporte General de Solicitudes</h2>';
        $html .= '<p style="text-align: center; font-size: 12px; color: #666;">Generado el ' . date('d/m/Y H:i') . '</p>';
        $html .= '<table border="1" cellspacing="0" cellpadding="8" style="width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 11px;">';
        $html .= '<thead><tr style="background-color: #f1f5f9; color: #1e293b;">
                    <th>ID</th><th>Asunto</th><th>Solicitante</th><th>Área</th><th>Prioridad</th><th>Estado</th><th>Fecha</th>
                  </tr></thead><tbody>';

        if (!empty($solicitudes)) {
            foreach ($solicitudes as $s) {
                $html .= '<tr>
                            <td>#' . htmlspecialchars($s['solicitud_id'] ?? $s['id'] ?? '') . '</td>
                            <td>' . htmlspecialchars($s['asunto'] ?? '') . '</td>
                            <td>' . htmlspecialchars($s['nombre_solicitante'] ?? $s['nombre_usuario'] ?? 'N/A') . '</td>
                            <td>' . htmlspecialchars($s['nombre_area'] ?? 'Sin área') . '</td>
                            <td>' . strtoupper(htmlspecialchars($s['prioridad'] ?? '')) . '</td>
                            <td>' . strtoupper(htmlspecialchars(str_replace('_', ' ', $s['estado'] ?? ''))) . '</td>
                            <td>' . date('d/m/Y H:i', strtotime($s['fecha_solicitud'] ?? $s['fecha_creacion'] ?? 'now')) . '</td>
                          </tr>';
            }
        } else {
            $html .= '<tr><td colspan="7" style="text-align:center;">No hay registros disponibles.</td></tr>';
        }

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'tempDir' => sys_get_temp_dir()
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte-solicitudes.pdf', \Mpdf\Output\Destination::DOWNLOAD);
        exit;
    }

    // Método que ejecuta el enrutador automáticamente para la acción 'exportarExcel'
    public function exportarExcel()
    {
        require_once __DIR__ . '/../../../vendor/autoload.php';

        $solicitudes = $this->modelo->obtenerSolicitudes();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Solicitudes');

        $headers = ['ID', 'Asunto', 'Solicitante', 'Área', 'Prioridad', 'Estado', 'Fecha Solicitud'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        if (!empty($solicitudes)) {
            foreach ($solicitudes as $s) {
                $sheet->setCellValue('A' . $row, $s['solicitud_id'] ?? $s['id'] ?? '');
                $sheet->setCellValue('B' . $row, $s['asunto'] ?? '');
                $sheet->setCellValue('C' . $row, $s['nombre_solicitante'] ?? $s['nombre_usuario'] ?? 'N/A');
                $sheet->setCellValue('D' . $row, $s['nombre_area'] ?? 'Sin área');
                $sheet->setCellValue('E' . $row, ucfirst($s['prioridad'] ?? ''));
                $sheet->setCellValue('F' . $row, ucfirst(str_replace('_', ' ', $s['estado'] ?? '')));
                $sheet->setCellValue('G' . $row, date('d/m/Y H:i', strtotime($s['fecha_solicitud'] ?? $s['fecha_creacion'] ?? 'now')));
                $row++;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte-solicitudes.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // Método estándar que llama el index.php
    public function index()
    {
        $accionGet = $_GET['accion'] ?? '';

        if ($accionGet === 'crear') {
            $this->cargarVistaCrear();
        } elseif ($accionGet === 'guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->guardarSolicitud();
        } elseif ($accionGet === 'exportarPdf') {
            $this->exportarPdf();
        } elseif ($accionGet === 'exportarExcel') {
            $this->exportarExcel();
        } elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $this->cargarDetalle((int) $_GET['id']);
        } else {
            $this->cargarVista();
        }
    }

    public function cargarVista()
    {
        // 1. Lógica o llamadas al modelo
        $solicitudes = $this->modelo->obtenerSolicitudes();

        // 2. Datos para la vista
        $titulo = "Solicitudes";

        // 3. Definimos cuál es el pedazo de HTML interno
        $vistaContenido = "app/views/solicitudes/solicitudesVista.php";

        // 4. Cargamos la plantilla maestra
        include "app/views/plantillaVista.php";
    }

    public function cargarVistaCrear()
    {
        // 1. Cargar áreas para el selector dinámico
        $areas = method_exists($this->modelo, 'obtenerAreas') ? $this->modelo->obtenerAreas() : [];
        $inventario = method_exists($this->modelo, 'obtenerInventarioDisponible') ? $this->modelo->obtenerInventarioDisponible() : [];

        // 2. Datos para la vista
        $titulo = "Nueva Solicitud";

        // 3. Vista interna del formulario
        $vistaContenido = "app/views/solicitudes/solicitudCrearVista.php";

        // 4. Cargamos la plantilla maestra
        include "app/views/plantillaVista.php";
    }

    public function guardarSolicitud()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Recoger y limpiar datos del formulario
            $asunto      = trim($_POST['asunto'] ?? '');
            $area_id     = (int)($_POST['area_id'] ?? 1);
            if ($area_id <= 0) {
                $area_id = 1;
            }

            // La prioridad NO la define el solicitante bajo ninguna circunstancia.
            // Se ignora cualquier valor que llegue por POST y se asigna un estado neutro;
            // solo un admin/técnico podrá cambiarla después desde el detalle de la solicitud.
            $prioridad   = 'sin_asignar';

            $descripcion = trim($_POST['descripcion'] ?? '');
            
            // 2. Obtener el ID del usuario de la sesión
            $usuario_id = $_SESSION['usuario_id'] ?? 1;

            // 3. Procesar subida opcional de foto/imagen
            $nombreFoto = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fileTmp  = $_FILES['foto']['tmp_name'];
                $fileName = $_FILES['foto']['name'];
                $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($ext, $allowedExts)) {
                    $uploadDir = __DIR__ . '/../../../uploads/solicitudes/';
                    if (!file_exists($uploadDir)) {
                        @mkdir($uploadDir, 0777, true);
                    }

                    $nuevoNombre = 'solicitud_' . time() . '_' . uniqid() . '.' . $ext;
                    $destPath    = $uploadDir . $nuevoNombre;

                    if (move_uploaded_file($fileTmp, $destPath)) {
                        $nombreFoto = 'uploads/solicitudes/' . $nuevoNombre;
                    }
                }
            }

            // 4. Guardar la solicitud si la información requerida está completa
            if (!empty($asunto) && !empty($descripcion)) {
                $solicitud_id = $this->modelo->crearSolicitud($asunto, $descripcion, $prioridad, $area_id, $usuario_id, $nombreFoto);

                // 5. Guardar ítems del inventario solicitados si existen
                if ($solicitud_id && isset($_POST['items_json']) && !empty($_POST['items_json'])) {
                    $items = json_decode($_POST['items_json'], true);
                    if (is_array($items) && count($items) > 0) {
                        $this->modelo->guardarDetallesSolicitud($solicitud_id, $items);
                    }
                }
            }
        }

        // 6. Redirigir al listado de solicitudes
        header('Location: ' . BASE_URL . 'solicitudes');
        exit;
    }

    public function cargarDetalle($id)
    {
        // 1. Lógica del modelo
        $solicitud = $this->modelo->obtenerSolicitudPorId($id);

        if (!$solicitud) {
            // Si no existe la solicitud, regresamos al listado
            $this->cargarVista();
            return;
        }

        $detalles = method_exists($this->modelo, 'obtenerDetallesSolicitud') 
            ? $this->modelo->obtenerDetallesSolicitud($id) 
            : [];

        // 2. Datos para la vista
        $titulo = "Detalle de Solicitud #" . ($solicitud['solicitud_id'] ?? $solicitud['id'] ?? '');

        // 3. Definimos cuál es el pedazo de HTML interno
        $vistaContenido = "app/views/solicitudes/solicitudDetalleVista.php";

        // 4. Cargamos la plantilla maestra
        include "app/views/plantillaVista.php";
    }

    public function procesarAccion()
    {
        header('Content-Type: application/json');

        $accion = $_POST['accion'] ?? '';
        $id = $_POST['id'] ?? 0;
        $motivo = trim($_POST['motivo'] ?? '');

        if (!is_numeric($id) || (int) $id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
            exit;
        }

        $usuario_id = $_SESSION['usuario_id'] ?? null;

        if (!$usuario_id) {
            echo json_encode(['status' => 'error', 'message' => 'Sesión no válida']);
            exit;
        }

        // VALIDACIÓN RBAC: SuperAdministrador (ID 1) y Técnico (ID 2) pueden aprobar, rechazar, cambiar el flujo o asignar prioridad
        $rolId = $_SESSION['nivel_acceso'] ?? $_SESSION['idTipoUsuario'] ?? 0;
        $nombreRol = $_SESSION['nombre_rol'] ?? '';
        $puedeGestionar = ($rolId == 1 || $rolId == 2 || $nombreRol === 'superUsuario' || $nombreRol === 'tecnico');

        if (!$puedeGestionar) {
            echo json_encode(['status' => 'error', 'message' => 'Solo los administradores y técnicos pueden aprobar, rechazar o cambiar solicitudes.']);
            exit;
        }

        if ($accion === 'asignar_prioridad') {
            // Solo admin/técnico llegan hasta aquí (ya validado con $puedeGestionar arriba)
            $prioridadesValidas = ['baja', 'media', 'alta', 'urgente'];
            $nuevaPrioridad = trim($_POST['prioridad'] ?? '');

            if (!in_array($nuevaPrioridad, $prioridadesValidas)) {
                echo json_encode(['status' => 'error', 'message' => 'Prioridad no válida']);
                exit;
            }

            $ok = $this->modelo->actualizarPrioridadSolicitud((int) $id, $nuevaPrioridad, $usuario_id);
        } elseif ($accion === 'aprobar') {
            $ok = $this->modelo->cambiarEstadoSolicitud((int) $id, 'aprobada', $usuario_id, null);
        } elseif ($accion === 'pendiente') {
            $ok = $this->modelo->cambiarEstadoSolicitud((int) $id, 'pendiente', $usuario_id, null);
        } elseif ($accion === 'rechazar') {
            if ($motivo === '') {
                echo json_encode(['status' => 'error', 'message' => 'Debes indicar un motivo de rechazo']);
                exit;
            }
            $ok = $this->modelo->cambiarEstadoSolicitud((int) $id, 'rechazada', $usuario_id, $motivo);
        } elseif ($accion === 'iniciar_proceso') {
            $ok = $this->modelo->cambiarEstadoSolicitud((int) $id, 'en_proceso', $usuario_id, null);
        } elseif ($accion === 'finalizar') {
            $ok = $this->modelo->cambiarEstadoSolicitud((int) $id, 'atendida', $usuario_id, $motivo ?: null);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Acción no reconocida']);
            exit;
        }

        if ($ok) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar la solicitud']);
        }
        exit;
    }
}