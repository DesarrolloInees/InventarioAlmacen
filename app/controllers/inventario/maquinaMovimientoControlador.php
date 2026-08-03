<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/maquinaMovimientoModelo.php';

class maquinaMovimientoControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new MaquinaMovimientoModelo($this->db);
    }

    public function index()
    {
        // 1. Extraer el ID manualmente de la URL (Ej: extrae el "1" de maquinaMovimiento/1)
        $url_actual = $_SERVER['REQUEST_URI'] ?? '';
        $partes_url = explode('/', rtrim($url_actual, '/'));
        $id_url = end($partes_url); // Toma el último número de la URL
        
        // Si viene por POST (al guardar), usamos el de POST. Si no, tratamos de usar el de la URL
        $id_maquina = $_POST['id_maquina'] ?? $id_url;

        // Validar si tenemos ID
        if (empty($id_maquina) || !is_numeric($id_maquina)) {
            // Descomenta la siguiente línea para depurar si sigue fallando:
            // die("No se recibió un ID válido. El ID detectado fue: " . htmlspecialchars($id_maquina));
            header('Location: ' . BASE_URL . 'maquinaInventarioVer');
            exit();
        }

        // Obtener datos específicos de esta máquina
        $maquina = $this->modelo->obtenerMaquinaPorId($id_maquina);
        if (!$maquina) {
            header('Location: ' . BASE_URL . 'maquinaInventarioVer');
            exit();
        }

        $bodegas = $this->modelo->obtenerBodegasActivas();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_movimiento = $_POST['tipo_movimiento'] ?? '';
            $id_bodega_destino = !empty($_POST['id_bodega_destino']) ? $_POST['id_bodega_destino'] : null;
            $observacion = trim($_POST['observacion'] ?? '');
            $id_usuario_registra = $_SESSION['usuario_id'] ?? 1;

            if (!empty($tipo_movimiento)) {
                if (($tipo_movimiento === 'INGRESO' || $tipo_movimiento === 'TRASLADO') && empty($id_bodega_destino)) {
                    $error = "Para un Ingreso o Traslado, debe seleccionar una Bodega de Destino.";
                } else {
                    $resultado = $this->modelo->registrarMovimiento($id_maquina, $tipo_movimiento, $id_bodega_destino, $observacion, $id_usuario_registra);
                    if ($resultado) {
                        header('Location: ' . BASE_URL . 'maquinaInventarioVer');
                        exit();
                    } else {
                        $error = "Error al registrar el movimiento.";
                    }
                }
            } else {
                $error = "El Tipo de Movimiento es obligatorio.";
            }
        }

        $data = [
            'titulo' => 'Registrar Movimiento',
            'maquina' => $maquina,
            'bodegas' => $bodegas,
            'error' => $error
        ];

        $vistaContenido = "app/views/inventario/maquinaMovimientoVista.php";
        include "app/views/plantillaVista.php";
    }
}