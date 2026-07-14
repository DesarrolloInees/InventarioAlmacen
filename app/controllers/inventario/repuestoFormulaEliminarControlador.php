<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';

class repuestoFormulaEliminarControlador
{
    public function index()
    {
        $nivel_acceso = $_SESSION['nivel_acceso'] ?? 0;
        if ($nivel_acceso != 1 && $nivel_acceso != 2) {
            die("Acceso restringido.");
        }

        $id_padre = $_GET['id'] ?? null;

        if ($id_padre) {
            try {
                $conexionObj = new Conexion();
                $db = $conexionObj->getConexion();
                
                // Borra todas las líneas de componentes asociadas a este repuesto final
                $sql = "DELETE FROM repuesto_ensamblado_detalles WHERE id_repuesto_padre = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute([':id' => $id_padre]);
            } catch (PDOException $e) {
                // Manejar error silenciosamente o registrar logs
            }
        }

        header('Location: ' . BASE_URL . 'repuestoFormulaVer');
        exit();
    }
}
