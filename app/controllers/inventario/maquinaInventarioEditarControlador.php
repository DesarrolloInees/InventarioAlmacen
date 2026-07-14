<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/maquinaInventarioEditarModelo.php';

class maquinaInventarioEditarControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new MaquinaInventarioEditarModelo($this->db);
    }

    public function index()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . 'maquinaInventarioVer');
            exit();
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idTipoMaquina = $_POST['idTipoMaquina'] ?? '';
            // En una edición normal, el cambio de bodega se debería hacer vía módulo de movimientos, 
            // pero si permiten editarlo directo, lo tomamos de aquí.
            $id_bodega = !empty($_POST['id_bodega']) ? $_POST['id_bodega'] : null;
            $numero_serie = trim($_POST['numero_serie'] ?? '');
            $condicion = $_POST['condicion'] ?? 'nueva';
            $estado_remision = !empty(trim($_POST['estado_remision'])) ? trim($_POST['estado_remision']) : null;

            if (!empty($idTipoMaquina) && !empty($numero_serie)) {
                $resultado = $this->modelo->actualizarMaquina($id, $idTipoMaquina, $id_bodega, $numero_serie, $condicion, $estado_remision);
                
                if ($resultado === true) {
                    header('Location: ' . BASE_URL . 'maquinaInventarioVer');
                    exit();
                } else {
                    if ($resultado === 'DUPLICADO') {
                        $error = "El número de serie '$numero_serie' ya está en uso por otra máquina.";
                    } else {
                        $error = "Error al actualizar la máquina.";
                    }
                }
            } else {
                $error = "El Tipo de Máquina y el Número de Serie son obligatorios.";
            }
        }

        $maquinaActual = $this->modelo->obtenerMaquinaPorId($id);
        if (!$maquinaActual) {
            header('Location: ' . BASE_URL . 'maquinaInventarioVer');
            exit();
        }

        $data = [
            'titulo' => 'Editar Máquina',
            'maquina' => $maquinaActual,
            'tipos' => $this->modelo->obtenerTiposMaquina(),
            'bodegas' => $this->modelo->obtenerBodegasActivas(),
            'error' => $error
        ];

        $vistaContenido = "app/views/inventario/maquinaInventarioEditarVista.php";
        include "app/views/plantillaVista.php";
    }
}