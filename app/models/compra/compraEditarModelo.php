<?php
// app/models/compra/compraEditarModelo.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class CompraEditarModelo
{
    private $conn;

    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    public function obtenerProveedoresActivos() {
        try {
            $sql = "SELECT id_proveedor, nombre_proveedor FROM proveedores WHERE estado = 'activo' ORDER BY nombre_proveedor ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function obtenerCompraPorId($idMovimiento) {
        try {
            $sql = "SELECT 
                        m.id_movimiento, 
                        m.id_repuesto,
                        m.id_producto,
                        m.cantidad, 
                        m.precio_compra,
                        m.numero_factura, 
                        m.id_proveedor, 
                        m.observacion,
                        r.nombre_repuesto,
                        r.codigo_referencia,
                        r.condicion,
                        p.nombre_producto,
                        p.codigo_interno
                    FROM movimientos_inventario m
                    LEFT JOIN repuestos r ON m.id_repuesto = r.id_repuesto
                    LEFT JOIN productos p ON m.id_producto = p.id_producto
                    WHERE m.id_movimiento = :id AND m.tipo_movimiento = 'ENTRADA'";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return false; }
    }

    public function editarCompra($idMovimiento, $datosNuevos) {
        try {
            $this->conn->beginTransaction();
            
            // 1. Obtener datos viejos para comparar
            $sqlOld = "SELECT id_repuesto, id_producto, cantidad FROM movimientos_inventario WHERE id_movimiento = :id";
            $stmtOld = $this->conn->prepare($sqlOld);
            $stmtOld->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtOld->execute();
            $datosViejos = $stmtOld->fetch(PDO::FETCH_ASSOC);

            if (!$datosViejos) { 
                $this->conn->rollBack(); 
                return false; 
            }

            // Identificar dinámicamente si es repuesto o producto
            $esRepuesto = !empty($datosViejos['id_repuesto']);
            $columnaFiltro = $esRepuesto ? 'id_repuesto' : 'id_producto';
            $targetId = $esRepuesto ? $datosViejos['id_repuesto'] : $datosViejos['id_producto'];

            $cantidadVieja = $datosViejos['cantidad'];
            $cantidadNueva = $datosNuevos['cantidad'];
            $diferencia = $cantidadNueva - $cantidadVieja;

            // 2. Revisar stock negativo si baja la cantidad (solo si la diferencia es negativa)
            if ($diferencia < 0) {
                $sqlStock = "SELECT cantidad_total FROM inventario_stock WHERE $columnaFiltro = :id";
                $stmtStock = $this->conn->prepare($sqlStock);
                $stmtStock->bindParam(':id', $targetId, PDO::PARAM_INT);
                $stmtStock->execute();
                $stockActual = $stmtStock->fetchColumn();

                if (($stockActual + $diferencia) < 0) { 
                    $this->conn->rollBack(); 
                    return 'error_stock'; 
                }
            }

            // 3. Actualizar stock correspondiente
            if ($diferencia != 0) {
                $sqlUpdateStock = "UPDATE inventario_stock SET cantidad_total = cantidad_total + :diferencia WHERE $columnaFiltro = :id";
                $stmtUpdateStock = $this->conn->prepare($sqlUpdateStock);
                $stmtUpdateStock->bindParam(':diferencia', $diferencia, PDO::PARAM_INT);
                $stmtUpdateStock->bindParam(':id', $targetId, PDO::PARAM_INT);
                $stmtUpdateStock->execute();
            }

            // 4. Actualizar movimiento (ahora incluye la factura)
            $sqlUpdateMov = "UPDATE movimientos_inventario SET 
                                cantidad = :cantidad, 
                                precio_compra = :precio_compra, 
                                numero_factura = :numero_factura,
                                id_proveedor = :id_proveedor, 
                                observacion = :observacion 
                             WHERE id_movimiento = :id";
            
            $stmtUpdateMov = $this->conn->prepare($sqlUpdateMov);
            $stmtUpdateMov->bindParam(':cantidad', $cantidadNueva, PDO::PARAM_INT);
            $stmtUpdateMov->bindParam(':precio_compra', $datosNuevos['precio_compra']);
            $stmtUpdateMov->bindParam(':numero_factura', $datosNuevos['numero_factura']);
            $stmtUpdateMov->bindParam(':id_proveedor', $datosNuevos['id_proveedor'], PDO::PARAM_INT);
            $stmtUpdateMov->bindParam(':observacion', $datosNuevos['observacion']);
            $stmtUpdateMov->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtUpdateMov->execute();

            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) { 
            $this->conn->rollBack(); 
            error_log("Error Editando Compra: " . $e->getMessage());
            return false; 
        }
    }
}