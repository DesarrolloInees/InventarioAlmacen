<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class RepuestoFormulaVerModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerFormulas() {
        try {
            // Agrupamos por repuesto padre y concatenamos los nombres de los hijos para mostrarlos en la tabla
            $sql = "SELECT 
                        r.id_repuesto AS id_padre,
                        r.nombre_repuesto AS nombre_padre,
                        r.codigo_referencia AS codigo_padre,
                        COUNT(red.id_repuesto_hijo) AS total_componentes,
                        GROUP_CONCAT(CONCAT(rh.nombre_repuesto, ' (x', red.cantidad_necesaria, ')') SEPARATOR ', ') AS resumen_componentes
                    FROM repuesto_ensamblado_detalles red
                    INNER JOIN repuestos r ON red.id_repuesto_padre = r.id_repuesto
                    INNER JOIN repuestos rh ON red.id_repuesto_hijo = rh.id_repuesto
                    WHERE r.estado = 1
                    GROUP BY r.id_repuesto
                    ORDER BY r.nombre_repuesto ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            return []; 
        }
    }
}