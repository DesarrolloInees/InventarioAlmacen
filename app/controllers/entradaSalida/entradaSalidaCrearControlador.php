<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/entradaSalida/entradaSalidaCrearModelo.php';

class EntradaSalidaCrearControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new EntradaSalidaCrearModelo($this->db);
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $movimientos_json = $_POST['movimientos_json'] ?? '[]';
            $movimientos = json_decode($movimientos_json, true);

            // OJO ACÁ: Si en tu base de datos no existe un usuario con ID 1, MySQL rechazará 
            // la consulta por la restricción de llave foránea (fk_mov_usuario).
            // Tomamos el ID del usuario logueado, o ponemos un ID de un admin que SÍ exista por defecto
            $id_usuario = $_SESSION['usuario_id'] ?? 1; // Reemplaza el 1 por un ID real que veas en tu tabla
            if (is_array($movimientos) && count($movimientos) > 0) {
                $errores_count = 0;
                $detalles_error = [];

                foreach ($movimientos as $mov) {
                    $id_repuesto_bd = ($mov['id_repuesto'] === 'OTRO') ? null : $mov['id_repuesto'];
                    $texto_repuesto = ($id_repuesto_bd === null && !empty($mov['repuesto_manual'])) ? "Repuesto Manual: " . $mov['repuesto_manual'] . " | " : "";

                    $observacion = $texto_repuesto . "Novedad: " . $mov['novedad'] . " | Destino: " . $mov['destino'] . " | Remisión: " . $mov['n_remision'] . " | Cotización: " . $mov['n_cotizacion'];

                    $resultado = $this->modelo->insertarMovimiento(
                        $id_repuesto_bd,
                        $mov['tipo_movimiento'],
                        $mov['cantidad'],
                        $observacion,
                        $id_usuario
                    );

                    // Si no retorna 'true', es porque nos devolvió el string con el error
                    if ($resultado !== true) {
                        $errores_count++;
                        $detalles_error[] = "Error en '{$mov['nombreVisual']}': " . $resultado;
                    }
                }

                if ($errores_count === 0) {
                    header('Location: ' . BASE_URL . 'entradaSalidaVer');
                    exit();
                } else {
                    // Mostramos todos los errores técnicos recopilados
                    $error = "Hubo $errores_count error(es). Detalles: <br>" . implode("<br>", $detalles_error);
                }
            } else {
                $error = "La lista de movimientos estaba vacía.";
            }
        }

        $data = [
            'titulo' => 'Registrar Movimientos',
            'repuestos' => $this->modelo->obtenerRepuestosActivos(),
            'error' => $error ?? null
        ];

        $vistaContenido = "app/views/entradaSalida/entradaSalidaCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}