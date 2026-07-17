<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class RepuestoFormulaCrearModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerTodosRepuestos() {
        try {
            $sql = "SELECT id_repuesto, nombre_repuesto, codigo_referencia FROM repuestos WHERE estado = 1 ORDER BY nombre_repuesto ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // Adaptamos el método para recibir el formato JSON mapeado
    public function guardarFormula($id_padre, $insumos) {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO repuesto_ensamblado_detalles (id_repuesto_padre, id_repuesto_hijo, cantidad_necesaria) VALUES (:id_padre, :id_hijo, :cant)";
            $stmt = $this->conn->prepare($sql);

            foreach ($insumos as $insumo) {
                $id_hijo = $insumo['id_repuesto_hijo'] ?? '';
                $cant = intval($insumo['cantidad'] ?? 1);

                if ($id_padre == $id_hijo) {
                    throw new Exception("Un repuesto no puede ser componente de sí mismo.");
                }

                if ($cant > 0 && !empty($id_hijo)) {
                    $stmt->execute([
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
            if ($e->getCode() == '23000') {
                return "Este repuesto padre ya posee elementos configurados en su receta (Fórmula duplicada).";
            }
            return "Error al almacenar: " . $e->getMessage();
        }
    }
}