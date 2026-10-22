<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/registroBodega/registroBodegaCrearModelo.php';

class registroBodegaCrearControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RegistroBodegaCrearModelo($this->db);
    }

    public function index()
    {
        // Solo administradores (1 = SuperAdmin, 2 = Supervisor) pueden crear.
        $nivel_acceso = (int) ($_SESSION['nivel_acceso'] ?? 0);
        if ($nivel_acceso !== 1 && $nivel_acceso !== 2) {
            die("No tienes permisos para realizar esta acción.");
        }

        $error = null;
        $registro = $this->valoresIniciales();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $registro = $this->normalizarDatos($_POST);
            $error = $this->validar($registro);

            if ($error === null) {
                if ($this->modelo->insertarRegistro($registro)) {
                    header('Location: ' . BASE_URL . 'registroBodegaVer?msg=creado');
                    exit();
                }
                $error = "No fue posible guardar el registro. Revisa la conexión e intenta nuevamente.";
            }
        }

        $data = [
            'titulo'      => 'Registrar Entrada / Salida de Bodega',
            'error'       => $error,
            'registro'    => $registro,
            'bodegas'     => $this->modelo->obtenerBodegasActivas(),
            'sugerencias' => $this->modelo->sugerenciasArticulos(),
        ];

        $vistaContenido = "app/views/registroBodega/registroBodegaCrearVista.php";
        $titulo = 'Registrar Entrada / Salida de Bodega';
        include "app/views/plantillaVista.php";
    }

    private function valoresIniciales()
    {
        return [
            'tipo_movimiento'     => 'ENTRADA',
            'fecha_movimiento'    => date('Y-m-d H:i:s'),
            'nombre_articulo'     => '',
            'codigo_referencia'   => '',
            'cantidad'            => '1',
            'id_bodega'           => '',
            'destino'             => '',
            'remision'            => '',
            'cotizacion'          => '',
            'novedad'             => '',
            'observacion'         => '',
            'id_usuario_registra' => (int) ($_SESSION['usuario_id'] ?? 0),
        ];
    }

    private function normalizarDatos(array $post)
    {
        $fecha = $this->textoPost($post, 'fecha_movimiento');
        if ($fecha !== '' && strtotime($fecha) !== false) {
            $fecha = date('Y-m-d H:i:s', strtotime($fecha));
        }

        $idBodega = $this->textoPost($post, 'id_bodega');
        $idBodega = ($idBodega === '' || !ctype_digit($idBodega) || (int) $idBodega <= 0) ? '' : (int) $idBodega;

        return [
            'tipo_movimiento'     => strtoupper($this->textoPost($post, 'tipo_movimiento')),
            'fecha_movimiento'    => $fecha,
            'nombre_articulo'     => $this->textoPost($post, 'nombre_articulo'),
            'codigo_referencia'   => $this->textoPost($post, 'codigo_referencia'),
            'cantidad'            => $this->textoPost($post, 'cantidad'),
            'id_bodega'           => $idBodega,
            'destino'             => $this->textoPost($post, 'destino'),
            'remision'            => $this->textoPost($post, 'remision'),
            'cotizacion'          => $this->textoPost($post, 'cotizacion'),
            'novedad'             => $this->textoPost($post, 'novedad'),
            'observacion'         => $this->textoPost($post, 'observacion'),
            'id_usuario_registra' => (int) ($_SESSION['usuario_id'] ?? 0),
        ];
    }

    /**
     * Devuelve solo valores escalares y recortados del POST.
     */
    private function textoPost(array $post, $clave)
    {
        return (isset($post[$clave]) && is_string($post[$clave])) ? trim($post[$clave]) : '';
    }

    private function validar(array $datos)
    {
        if (!in_array($datos['tipo_movimiento'], ['ENTRADA', 'SALIDA'], true)) {
            return "Debes seleccionar el tipo de movimiento (Entrada o Salida).";
        }

        if ($datos['fecha_movimiento'] === '' || strtotime($datos['fecha_movimiento']) === false) {
            return "La fecha del movimiento es obligatoria y debe ser válida.";
        }

        if ($datos['nombre_articulo'] === '') {
            return "El nombre del artículo es obligatorio.";
        }

        if (mb_strlen($datos['nombre_articulo'], 'UTF-8') > 200) {
            return "El nombre del artículo no puede superar los 200 caracteres.";
        }

        if ($datos['cantidad'] === '' || !is_numeric($datos['cantidad']) || (float) $datos['cantidad'] <= 0) {
            return "La cantidad debe ser un número mayor que cero.";
        }

        if ($datos['id_usuario_registra'] <= 0) {
            return "No se pudo identificar al usuario que registra. Vuelve a iniciar sesión.";
        }

        return null;
    }
}
