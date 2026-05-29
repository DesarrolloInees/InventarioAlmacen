<?php
// app/models/categoria/categoriaVerModelo.php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class CategoriaVerModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerCategorias() {
        try {
            // Cambiamos la tabla y la columna de orden
            $sql = "SELECT * FROM categorias ORDER BY nombre_categoria ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            return []; 
        }
    }

    public function eliminarCategoriaLogicamente($id) {
        try {
            // Actualizamos la tabla, la columna ID, y el estado numérico (0)
            $sql = "UPDATE categorias SET estado = 0 WHERE id_categoria = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) { 
            return false; 
        }
    }
}