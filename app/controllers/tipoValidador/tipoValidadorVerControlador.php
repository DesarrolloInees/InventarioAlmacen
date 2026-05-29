<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoValidador/tipoValidadorVerModelo.php';

class tipoValidadorVerControlador {
    private $db;
    private $modelo;

    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoValidadorVerModelo($this->db);
    }

    public function index() {
        $data = [
            'titulo' => 'Tipos de Validador',
            'validadores' => $this->modelo->obtenerTiposValidador()
        ];
        $vistaContenido = "app/views/tipoValidador/tipoValidadorVerVista.php";
        include "app/views/plantillaVista.php";
    }
}