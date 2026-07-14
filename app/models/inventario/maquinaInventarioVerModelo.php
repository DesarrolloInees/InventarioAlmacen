<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class MaquinaInventarioVerModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerInventario() {
        try {
            $sql = "SELECT 
                        im.id_maquina,
                        im.numero_serie,
                        im.condicion,
                        im.estado_remision,
                        im.fecha_registro,
                        tm.nombre_tipo AS tipo_maquina, /* Cambia 'nombre_tipo' por la columna real de tu tabla tipomaquina */
                        b.nombre_bodega
                    FROM inventario_maquinas im
                    INNER JOIN tipomaquina tm ON im.idTipoMaquina = tm.idTipoMaquina
                    LEFT JOIN bodegas b ON im.id_bodega = b.id_bodega
                    ORDER BY im.id_maquina DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            return []; 
        }
    }

    // Nota: Como inventario_maquinas no tiene columna "estado" (1/0), 
    // la eliminación debe ser física (DELETE) o manejada a través de un cambio de ubicación.
    public function eliminarMaquina($id) {
        try {
            $sql = "DELETE FROM inventario_maquinas WHERE id_maquina = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) { 
            return false; 
        }
    }
}