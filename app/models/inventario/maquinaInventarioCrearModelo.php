<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class MaquinaInventarioCrearModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerTiposMaquina() {
        try {
            // Asumo que tienes idTipoMaquina y nombre_tipo en tu tabla
            $sql = "SELECT idTipoMaquina, nombre_tipo FROM tipomaquina ORDER BY nombre_tipo ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function obtenerBodegasActivas() {
        try {
            $sql = "SELECT id_bodega, nombre_bodega FROM bodegas WHERE estado = 1 ORDER BY nombre_bodega ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function insertarMaquina($idTipoMaquina, $id_bodega, $numero_serie, $condicion, $estado_remision, $id_usuario_registra) {
        try {
            // Iniciamos transacción para insertar la máquina y su movimiento de ingreso
            $this->conn->beginTransaction();

            $sql = "INSERT INTO inventario_maquinas (idTipoMaquina, id_bodega, numero_serie, condicion, estado_remision) 
                    VALUES (:idTipo, :idBodega, :serial, :condicion, :remision)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':idTipo', $idTipoMaquina, PDO::PARAM_INT);
            $stmt->bindValue(':idBodega', $id_bodega, $id_bodega === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':serial', $numero_serie, PDO::PARAM_STR);
            $stmt->bindParam(':condicion', $condicion, PDO::PARAM_STR);
            $stmt->bindValue(':remision', $estado_remision, $estado_remision === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();

            $id_maquina = $this->conn->lastInsertId();

            // Si se asignó a una bodega, registramos el movimiento de INGRESO
            if ($id_bodega !== null) {
                $sqlMov = "INSERT INTO movimientos_maquinas (id_maquina, id_bodega_destino, tipo_movimiento, id_usuario_registra, observacion) 
                           VALUES (:idMaq, :idBodegaDest, 'INGRESO', :idUser, 'Ingreso inicial al sistema')";
                $stmtMov = $this->conn->prepare($sqlMov);
                $stmtMov->bindParam(':idMaq', $id_maquina, PDO::PARAM_INT);
                $stmtMov->bindParam(':idBodegaDest', $id_bodega, PDO::PARAM_INT);
                $stmtMov->bindParam(':idUser', $id_usuario_registra, PDO::PARAM_INT);
                $stmtMov->execute();
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) { 
            $this->conn->rollBack();
            // Código 23000 es violación de restricción UNIQUE (Duplicate entry)
            if ($e->getCode() == 23000) {
                return 'DUPLICADO';
            }
            return false; 
        }
    }
}