<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class EntradaSalidaCrearModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerRepuestosActivos()
    {
        try {
            $sql = "SELECT id_repuesto, codigo_referencia, nombre_repuesto FROM repuestos WHERE estado = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function insertarMovimiento($id_repuesto, $tipo_movimiento, $cantidad, $observacion, $id_usuario)
    {
        try {
            $sql = "INSERT INTO movimientos_inventario 
                    (id_repuesto, tipo_movimiento, cantidad, observacion, id_usuario_registra) 
                    VALUES (:id_repuesto, :tipo, :cantidad, :observacion, :usuario)";
            $stmt = $this->conn->prepare($sql);

            // Usamos bindValue que es más seguro cuando enviamos datos NULL
            $stmt->bindValue(':id_repuesto', $id_repuesto, $id_repuesto === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':tipo', $tipo_movimiento, PDO::PARAM_STR);
            $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindValue(':observacion', $observacion, PDO::PARAM_STR);
            $stmt->bindValue(':usuario', $id_usuario, PDO::PARAM_INT);

            $stmt->execute();
            return true; // Retornamos true si fue exitoso
        } catch (PDOException $e) {
            return $e->getMessage(); // Retornamos el texto exacto del error de MySQL
        }
    }
}