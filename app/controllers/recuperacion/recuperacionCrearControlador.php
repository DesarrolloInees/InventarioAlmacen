<?php
// app/controllers/recuperacion/recuperacionCrearControlador.php
// Entrada de RECUPERADOS por tecnico. Separado de COMPRA.

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/recuperacion/recuperacionCrearModelo.php';

class recuperacionCrearControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RecuperacionCrearModelo($this->db);
    }

    public function index()
    {
        $errores = [];
        $exito = false;
        $datosPrevios = [];

        $idUsuario = $_SESSION['usuario_id'] ?? null;
        if (!$idUsuario) {
            header("Location: " . BASE_URL . "login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipoItem = $_POST['tipo_item'] ?? 'repuesto';
            if (!in_array($tipoItem, ['repuesto', 'producto', 'manual'])) $tipoItem = 'repuesto';

            $idRepuesto = ($tipoItem === 'repuesto') ? trim($_POST['id_repuesto'] ?? '') : '';
            $idProducto = ($tipoItem === 'producto') ? trim($_POST['id_producto'] ?? '') : '';
            $nombreManual = ($tipoItem === 'manual') ? trim($_POST['repuesto_manual'] ?? '') : '';
            $codigoManual = ($tipoItem === 'manual') ? strtoupper(trim($_POST['codigo_manual'] ?? '')) : '';
            $condicionManual = ($tipoItem === 'manual') ? ($_POST['condicion_manual'] ?? 'recuperado') : 'recuperado';
            if (!in_array($condicionManual, ['nuevo', 'recuperado', 'por revisar'])) $condicionManual = 'recuperado';
            $idTecnico = trim($_POST['id_tecnico_origen'] ?? '');
            $origenTec = null;
            $idTecnicoLocal = null;
            $nombreTecExterno = null;
            if ($idTecnico !== '') {
                $partes = explode('_', $idTecnico, 2);
                if (count($partes) === 2 && $partes[0] === 'local' && intval($partes[1]) > 0) {
                    $origenTec = 'local';
                    $idTecnicoLocal = intval($partes[1]);
                } elseif (count($partes) === 2 && $partes[0] === 'moto' && intval($partes[1]) > 0) {
                    $origenTec = 'moto';
                    try {
                        $connM = new PDO("mysql:host=127.0.0.1;dbname=inees_mantenimientos;charset=utf8mb4", "root", "");
                        $stM = $connM->prepare("SELECT nombre_tecnico FROM tecnico WHERE id_tecnico = :id AND estado = 1");
                        $stM->execute([':id' => intval($partes[1])]);
                        $nombreTecExterno = $stM->fetchColumn() ?: null;
                    } catch (Throwable $e) {
                        $nombreTecExterno = null;
                    }
                    if (!$nombreTecExterno) $errores[] = "El tecnico motorizado seleccionado no es valido.";
                } else {
                    $errores[] = "Seleccion de tecnico no valida.";
                }
            }
            $serial = trim($_POST['serial_recuperado'] ?? '');
            $cantidad = intval($_POST['cantidad'] ?? 0);
            $fecha = trim($_POST['fecha_movimiento'] ?? '');
            $novedad = trim($_POST['novedad'] ?? '');
            $observacion = trim($_POST['observacion'] ?? '');

            if ($fecha === '') $fecha = date('Y-m-d');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                $errores[] = "La fecha no es valida (usa formato AAAA-MM-DD).";
            }
            $fechaMov = $fecha . ' ' . date('H:i:s');

            $datosPrevios = [
                'tipo_item' => $tipoItem,
                'id_repuesto' => $idRepuesto,
                'id_producto' => $idProducto,
                'repuesto_manual' => $nombreManual,
                'codigo_manual' => $codigoManual,
                'condicion_manual' => $condicionManual,
                'id_tecnico_origen' => $idTecnico,
                'serial_recuperado' => $serial,
                'cantidad' => $cantidad,
                'fecha_movimiento' => $fecha,
                'novedad' => $novedad,
                'observacion' => $observacion,
            ];

            if ($tipoItem === 'repuesto' && $idRepuesto === '') $errores[] = "Debes seleccionar un repuesto del catalogo.";
            if ($tipoItem === 'producto' && $idProducto === '') $errores[] = "Debes seleccionar un producto del catalogo.";
            if ($tipoItem === 'manual' && $nombreManual === '') $errores[] = "Escribe el nombre del repuesto recuperado.";
            if ($idTecnico === '') $errores[] = "Debes seleccionar el tecnico que recupero el repuesto.";
            if ($cantidad <= 0) $errores[] = "La cantidad debe ser mayor a cero.";
            if ($serial !== '' && $cantidad !== 1) $errores[] = "Si registras un serial, la cantidad debe ser 1 (trazabilidad unitaria).";

            if (empty($errores)) {
                $datos = [
                    'id_repuesto' => $idRepuesto !== '' ? intval($idRepuesto) : null,
                    'id_producto' => $idProducto !== '' ? intval($idProducto) : null,
                    'repuesto_manual' => null,
                    'crear_repuesto' => $tipoItem === 'manual' ? ['nombre' => $nombreManual, 'codigo' => $codigoManual, 'condicion' => $condicionManual] : null,
                    'id_tecnico_origen' => $origenTec === 'local' ? $idTecnicoLocal : null,
                    'tecnico_origen_nombre' => $origenTec === 'moto' ? $nombreTecExterno : null,
                    'serial_recuperado' => $serial !== '' ? $serial : null,
                    'cantidad' => $cantidad,
                    'fecha_movimiento' => $fechaMov,
                    'novedad' => $novedad !== '' ? $novedad : null,
                    'observacion' => $observacion !== '' ? $observacion : null,
                    'id_usuario_registra' => $idUsuario,
                ];
                if ($this->modelo->registrarRecuperado($datos)) {
                    $exito = true;
                    $datosPrevios = [];
                } else {
                    $errores[] = "Ocurrio un error interno al guardar la entrada recuperada.";
                }
            }
        }

        $titulo = "Entrada de Recuperado (Tecnicos)";
        $repuestosActivos = $this->modelo->obtenerRepuestosActivos();
        $productosActivos = $this->modelo->obtenerProductosActivos();
        $tecnicos = $this->modelo->obtenerTecnicosOrigen();

        $vistaContenido = "app/views/recuperacion/recuperacionCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}
