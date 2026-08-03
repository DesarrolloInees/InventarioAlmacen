<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class MaquinaMovimientoVerModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerHistorialMovimientos() {
        try {
            $sql = "SELECT 
                        mm.id_mov_maquina,
                        mm.tipo_movimiento,
                        mm.fecha_movimiento,
                        mm.observacion,
                        im.numero_serie,
                        tm.nombreTipoMaquina,
                        bo.nombre_bodega AS bodega_origen,
                        bd.nombre_bodega AS bodega_destino,
                        u.nombre AS usuario_registra
                    FROM movimientos_maquinas mm
                    INNER JOIN inventario_maquinas im ON mm.id_maquina = im.id_maquina
                    INNER JOIN tipomaquina tm ON im.idTipoMaquina = tm.idTipoMaquina
                    LEFT JOIN bodegas bo ON mm.id_bodega_origen = bo.id_bodega
                    LEFT JOIN bodegas bd ON mm.id_bodega_destino = bd.id_bodega
                    LEFT JOIN usuarios u ON mm.id_usuario_registra = u.usuario_id
                    ORDER BY mm.fecha_movimiento DESC";
            
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            return []; 
        }
    }
}