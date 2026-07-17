<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/maquinaInventarioCrearModelo.php';

class maquinaInventarioCrearControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new MaquinaInventarioCrearModelo($this->db);
    }

    public function index()
    {
        // Traemos los datos para los selects
        $tiposMaquina = $this->modelo->obtenerTiposMaquina();
        $bodegas = $this->modelo->obtenerBodegasActivas();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idTipoMaquina = $_POST['idTipoMaquina'] ?? '';
            $id_bodega = !empty($_POST['id_bodega']) ? $_POST['id_bodega'] : null;
            $numero_serie = trim($_POST['numero_serie'] ?? '');
            $condicion = $_POST['condicion'] ?? 'nueva';
            $estado_remision = !empty(trim($_POST['estado_remision'])) ? trim($_POST['estado_remision']) : null;
            $id_usuario_registra = $_SESSION['usuario_id'] ?? 1;

            if (!empty($idTipoMaquina) && !empty($numero_serie)) {
                $resultado = $this->modelo->insertarMaquina($idTipoMaquina, $id_bodega, $numero_serie, $condicion, $estado_remision, $id_usuario_registra);
                
                if ($resultado === true) {
                    header('Location: ' . BASE_URL . 'maquinaInventarioVer');
                    exit();
                } else {
                    // Si el error es por el UNIQUE del serial
                    if ($resultado === 'DUPLICADO') {
                        $error = "El número de serie '$numero_serie' ya existe en el inventario.";
                    } else {
                        $error = "Error al guardar la máquina en la base de datos.";
                    }
                }
            } else {
                $error = "El Tipo de Máquina y el Número de Serie son obligatorios.";
            }
        }

        $data = [
            'titulo' => 'Registrar Máquina',
            'tipos' => $tiposMaquina,
            'bodegas' => $bodegas,
            'error' => $error
        ];

        $vistaContenido = "app/views/inventario/maquinaInventarioCrearVista.php";
        include "app/views/plantillaVista.php";
    }
}