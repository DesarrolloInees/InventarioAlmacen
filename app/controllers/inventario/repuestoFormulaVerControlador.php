<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/inventario/repuestoFormulaVerModelo.php';

class repuestoFormulaVerControlador
{
    private $db, $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new RepuestoFormulaVerModelo($this->db);
    }

    public function index()
    {
        $data = [
            'titulo' => 'Fórmulas de Ensamble',
            'formulas' => $this->modelo->obtenerFormulas()
        ];

        $vistaContenido = "app/views/inventario/repuestoFormulaVerVista.php";
        include "app/views/plantillaVista.php";
    }
}