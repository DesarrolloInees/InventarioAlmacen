<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class RepuestoFormulaEditarModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerPadrePorId($id_padre) {
        try {
            $sql = "SELECT id_repuesto, nombre_repuesto, codigo_referencia FROM repuestos WHERE id_repuesto = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id_padre]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return false; }
    }

    public function obtenerDetallesFormula($id_padre) {
        try {
            $sql = "SELECT id_repuesto_hijo, cantidad_necesaria FROM repuesto_ensamblado_detalles WHERE id_repuesto_padre = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id_padre]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function obtenerTodosRepuestos() {
        try {
            $sql = "SELECT id_repuesto, nombre_repuesto, codigo_referencia FROM repuestos WHERE estado = 1 ORDER BY nombre_repuesto ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function actualizarFormula($id_padre, $componentes, $cantidades) {
        try {
            $this->conn->beginTransaction();

            // 1. Limpiamos la receta vieja
            $sqlDelete = "DELETE FROM repuesto_ensamblado_detalles WHERE id_repuesto_padre = :id_padre";
            $stmtDel = $this->conn->prepare($sqlDelete);
            $stmtDel->execute([':id_padre' => $id_padre]);

            // 2. Insertamos la receta reestructurada
            $sqlInsert = "INSERT INTO repuesto_ensamblado_detalles (id_repuesto_padre, id_repuesto_hijo, cantidad_necesaria) VALUES (:id_padre, :id_hijo, :cant)";
            $stmtIns = $this->conn->prepare($sqlInsert);

            foreach ($componentes as $index => $id_hijo) {
                $cant = intval($cantidades[$index] ?? 1);
                if ($id_padre == $id_hijo) {
                    throw new Exception("Un repuesto no puede ser componente de sí mismo.");
                }
                if ($cant > 0 && !empty($id_hijo)) {
                    $stmtIns->execute([
                        ':id_padre' => $id_padre,
                        ':id_hijo' => $id_hijo,
                        ':cant' => $cant
                    ]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return "Error al reconfigurar receta: " . $e->getMessage();
        }
    }
}