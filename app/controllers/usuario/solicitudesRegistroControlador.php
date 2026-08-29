<?php
// app/controllers/usuario/solicitudesRegistroControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/usuario/usuarioVerModelo.php';

class solicitudesRegistroControlador
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_solicitud'])) {
            $this->procesarAccion();
            return;
        }

        $this->cargarVista();
    }

    public function procesarAccion()
    {
        ob_clean();
        header('Content-Type: application/json');

        $accion = $_POST['accion_solicitud'] ?? '';
        $id = (int)($_POST['usuario_id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID de usuario no válido.']);
            exit;
        }

        if ($accion === 'aprobar') {
            if ($this->modelo->aprobarUsuario($id)) {
                echo json_encode([
                    'status'  => 'success',
                    'message' => '¡Credenciales del cliente aprobadas exitosamente! El cliente ahora tiene acceso al sistema.'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo aprobar el acceso del cliente.']);
            }
            exit;
        }

        if ($accion === 'denegar' || $accion === 'rechazar') {
            if ($this->modelo->rechazarUsuario($id)) {
                echo json_encode([
                    'status'  => 'success',
                    'message' => 'Solicitud de credenciales denegada. La cuenta ha sido desactivada.'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo denegar la solicitud.']);
            }
            exit;
        }

        echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
        exit;
    }

    public function cargarVista()
    {
        $listaPendientes = $this->modelo->obtenerSolicitudesPendientes();
        $listaAprobados  = $this->modelo->obtenerSolicitudesAprobadas();
        $listaDenegados  = $this->modelo->obtenerSolicitudesDenegadas();

        $data = [
            'titulo'     => 'Aprobación de Credenciales de Clientes',
            'pendientes' => $listaPendientes,
            'aprobados'  => $listaAprobados,
            'denegados'  => $listaDenegados
        ];

        $titulo = $data['titulo'];
        $vistaContenido = "app/views/usuario/solicitudesRegistroVista.php";

        include "app/views/plantillaVista.php";
    }
}
