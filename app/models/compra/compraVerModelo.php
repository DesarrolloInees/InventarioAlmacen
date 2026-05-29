<?php
// app/models/compra/compraVerModelo.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class CompraVerModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Obtener el historial completo fusionando Repuestos y Productos + Factura
    public function obtenerHistorialCompras()
    {
        try {
            $sql = "SELECT 
                        m.id_movimiento, 
                        m.cantidad, 
                        m.precio_compra, 
                        m.numero_factura,
                        m.observacion, 
                        m.fecha_movimiento,
                        m.id_repuesto,
                        m.id_producto,
                        r.nombre_repuesto,
                        r.codigo_referencia,
                        r.condicion,
                        p.nombre_producto,
                        p.codigo_interno,
                        prov.nombre_proveedor,
                        u.nombre as usuario_nombre
                    FROM movimientos_inventario m
                    LEFT JOIN repuestos r ON m.id_repuesto = r.id_repuesto
                    LEFT JOIN productos p ON m.id_producto = p.id_producto
                    INNER JOIN usuarios u ON m.id_usuario_registra = u.usuario_id
                    LEFT JOIN proveedores prov ON m.id_proveedor = prov.id_proveedor
                    WHERE m.tipo_movimiento = 'ENTRADA'
                    ORDER BY m.fecha_movimiento DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener historial unificado de compras: " . $e->getMessage());
            return [];
        }
    }

    // Eliminar compra y restar stock del casillero correcto (TRANSACCIÓN INTELIGENTE)
    public function eliminarCompra($idMovimiento)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Identificar qué se compró y qué cantidad
            $sqlInfo = "SELECT id_repuesto, id_producto, cantidad FROM movimientos_inventario WHERE id_movimiento = :id AND tipo_movimiento = 'ENTRADA'";
            $stmtInfo = $this->conn->prepare($sqlInfo);
            $stmtInfo->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtInfo->execute();
            $movimiento = $stmtInfo->fetch(PDO::FETCH_ASSOC);

            if (!$movimiento) {
                $this->conn->rollBack();
                return false;
            }

            $cantidadComprada = $movimiento['cantidad'];
            
            // 2. Determinar dinámicamente qué campo de stock evaluar
            if (!empty($movimiento['id_repuesto'])) {
                $sqlStock = "SELECT cantidad_total FROM inventario_stock WHERE id_repuesto = :id";
                $sqlRestar = "UPDATE inventario_stock SET cantidad_total = cantidad_total - :cantidad WHERE id_repuesto = :id";
                $targetId = $movimiento['id_repuesto'];
            } else {
                $sqlStock = "SELECT cantidad_total FROM inventario_stock WHERE id_producto = :id";
                $sqlRestar = "UPDATE inventario_stock SET cantidad_total = cantidad_total - :cantidad WHERE id_producto = :id";
                $targetId = $movimiento['id_producto'];
            }

            // 3. Validar que el stock actual soporte la resta
            $stmtStock = $this->conn->prepare($sqlStock);
            $stmtStock->execute([':id' => $targetId]);
            $stockActual = $stmtStock->fetchColumn();

            if ($stockActual < $cantidadComprada) {
                // Bloqueo de seguridad si ya se consumió/asignó el producto
                $this->conn->rollBack();
                return 'error_stock'; 
            }

            // 4. Restar la cantidad del stock correspondiente
            $stmtRestar = $this->conn->prepare($sqlRestar);
            $stmtRestar->bindParam(':cantidad', $cantidadComprada, PDO::PARAM_INT);
            $stmtRestar->bindParam(':id', $targetId, PDO::PARAM_INT);
            $stmtRestar->execute();

            // 5. Borrar el registro físico del movimiento
            $sqlDelete = "DELETE FROM movimientos_inventario WHERE id_movimiento = :id";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtDelete->execute();

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar compra unificada: " . $e->getMessage());
            return false;
        }
    }
}