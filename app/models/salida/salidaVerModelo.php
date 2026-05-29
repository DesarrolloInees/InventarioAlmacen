<?php
// app/models/salida/salidaVerModelo.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class SalidaVerModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Obtener el historial unificado de salidas (Repuestos + Consumibles)
    public function obtenerHistorialSalidas()
    {
        try {
            $sql = "SELECT 
                        m.id_movimiento, 
                        m.cantidad, 
                        m.observacion, 
                        m.fecha_movimiento,
                        m.id_repuesto,
                        m.id_producto,
                        r.nombre_repuesto,
                        r.codigo_referencia,
                        r.condicion,
                        p.nombre_producto,
                        p.codigo_interno,
                        u_admin.nombre AS admin_nombre,
                        u_tec.nombre AS tecnico_nombre
                    FROM movimientos_inventario m
                    LEFT JOIN repuestos r ON m.id_repuesto = r.id_repuesto
                    LEFT JOIN productos p ON m.id_producto = p.id_producto
                    INNER JOIN usuarios u_admin ON m.id_usuario_registra = u_admin.usuario_id
                    LEFT JOIN usuarios u_tec ON m.id_tecnico_destino = u_tec.usuario_id
                    WHERE m.tipo_movimiento = 'SALIDA'
                    ORDER BY m.fecha_movimiento DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener salidas unificadas: " . $e->getMessage());
            return [];
        }
    }

    // Anular salida y DEVOLVER al stock local de forma inteligente (TRANSACCIÓN)
    public function anularSalida($idMovimiento)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Obtener la información de la salida antes de borrarla
            $sqlInfo = "SELECT id_repuesto, id_producto, cantidad FROM movimientos_inventario WHERE id_movimiento = :id AND tipo_movimiento = 'SALIDA'";
            $stmtInfo = $this->conn->prepare($sqlInfo);
            $stmtInfo->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtInfo->execute();
            $movimiento = $stmtInfo->fetch(PDO::FETCH_ASSOC);

            if (!$movimiento) {
                $this->conn->rollBack();
                return false;
            }

            $cantidadDevuelta = $movimiento['cantidad'];

            // Determinar si devolvemos stock a un repuesto o a un producto
            $esRepuesto = !empty($movimiento['id_repuesto']);
            $columnaFiltro = $esRepuesto ? 'id_repuesto' : 'id_producto';
            $targetId = $esRepuesto ? $movimiento['id_repuesto'] : $movimiento['id_producto'];

            // 2. Devolver (SUMAR) la cantidad al stock local correspondiente
            $sqlSumar = "UPDATE inventario_stock SET cantidad_total = cantidad_total + :cantidad WHERE $columnaFiltro = :id";
            $stmtSumar = $this->conn->prepare($sqlSumar);
            $stmtSumar->bindParam(':cantidad', $cantidadDevuelta, PDO::PARAM_INT);
            $stmtSumar->bindParam(':id', $targetId, PDO::PARAM_INT);
            $stmtSumar->execute();

            // 3. Eliminar el registro del historial
            $sqlDelete = "DELETE FROM movimientos_inventario WHERE id_movimiento = :id";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtDelete->execute();

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al anular salida unificada: " . $e->getMessage());
            return false;
        }
    }
}