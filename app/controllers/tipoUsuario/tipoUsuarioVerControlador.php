<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/tipoUsuario/tipoUsuarioVerModelo.php';

class tipoUsuarioVerControlador {
    private $db, $modelo;

    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new TipoUsuarioVerModelo($this->db);
    }

    public function index() {
        $data = [
            'titulo' => 'Roles de Usuario',
            'tiposUsuario' => $this->modelo->obtenerTiposUsuario()
        ];
        $vistaContenido = "app/views/tipoUsuario/tipoUsuarioVerVista.php";
        include "app/views/plantillaVista.php";
    }
}