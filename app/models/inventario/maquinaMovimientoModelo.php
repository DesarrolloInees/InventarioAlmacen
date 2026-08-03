<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class MaquinaMovimientoModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    // Traemos solo la máquina específica por su ID
    public function obtenerMaquinaPorId($id_maquina) {
        try {
            $sql = "SELECT m.id_maquina, m.numero_serie, m.id_bodega, t.nombreTipoMaquina, b.nombre_bodega 
                    FROM inventario_maquinas m
                    INNER JOIN tipomaquina t ON m.idTipoMaquina = t.idTipoMaquina
                    LEFT JOIN bodegas b ON m.id_bodega = b.id_bodega
                    WHERE m.id_maquina = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id_maquina, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return false; }
    }

    public function obtenerBodegasActivas() {
        try {
            $sql = "SELECT id_bodega, nombre_bodega FROM bodegas WHERE estado = 1 ORDER BY nombre_bodega ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function registrarMovimiento($id_maquina, $tipo_movimiento, $id_bodega_destino, $observacion, $id_usuario_registra) {
        try {
            $this->conn->beginTransaction();

            // 1. Consultar origen
            $stmtOrigen = $this->conn->prepare("SELECT id_bodega FROM inventario_maquinas WHERE id_maquina = :idMaq");
            $stmtOrigen->bindParam(':idMaq', $id_maquina, PDO::PARAM_INT);
            $stmtOrigen->execute();
            $maquina = $stmtOrigen->fetch(PDO::FETCH_ASSOC);

            if (!$maquina) throw new Exception("Máquina no encontrada");
            
            $id_bodega_origen = $maquina['id_bodega'];
            $destino_final = ($tipo_movimiento === 'SALIDA_REMISION') ? null : $id_bodega_destino;

            // 2. Insertar movimiento
            $sqlMov = "INSERT INTO movimientos_maquinas (id_maquina, id_bodega_origen, id_bodega_destino, tipo_movimiento, id_usuario_registra, observacion) 
                       VALUES (:idMaq, :origen, :destino, :tipo, :idUser, :obs)";
            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->bindParam(':idMaq', $id_maquina, PDO::PARAM_INT);
            $stmtMov->bindValue(':origen', $id_bodega_origen, $id_bodega_origen === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtMov->bindValue(':destino', $destino_final, $destino_final === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtMov->bindParam(':tipo', $tipo_movimiento, PDO::PARAM_STR);
            $stmtMov->bindParam(':idUser', $id_usuario_registra, PDO::PARAM_INT);
            $stmtMov->bindValue(':obs', $observacion, empty($observacion) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmtMov->execute();

            // 3. Actualizar ubicación
            $sqlUpd = "UPDATE inventario_maquinas SET id_bodega = :nuevoDestino WHERE id_maquina = :idMaq";
            $stmtUpd = $this->conn->prepare($sqlUpd);
            $stmtUpd->bindValue(':nuevoDestino', $destino_final, $destino_final === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtUpd->bindParam(':idMaq', $id_maquina, PDO::PARAM_INT);
            $stmtUpd->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) { 
            $this->conn->rollBack();
            return false; 
        }
    }
}