<?php
// app/models/compra/compraCrearModelo.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class CompraCrearModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Traer repuestos activos indicando si son nuevos o recuperados
    public function obtenerRepuestosActivos()
    {
        try {
            $sql = "SELECT id_repuesto, codigo_referencia, nombre_repuesto, condicion 
                    FROM repuestos 
                    WHERE estado = 1 
                    ORDER BY nombre_repuesto ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Traer los productos consumibles activos (Nueva tabla)
    public function obtenerProductosActivos()
    {
        try {
            $sql = "SELECT id_producto, codigo_interno, nombre_producto 
                    FROM productos 
                    WHERE estado = 1 
                    ORDER BY nombre_producto ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Traer proveedores activos para el select
    public function obtenerProveedoresActivos()
    {
        try {
            $sql = "SELECT id_proveedor, nombre_proveedor 
                    FROM proveedores 
                    WHERE estado = 'activo' 
                    ORDER BY nombre_proveedor ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Guardar compra y actualizar el stock correspondiente (TRANSACCIÓN INTELIGENTE)
    public function registrarCompra($datos)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar movimiento en el historial (Soporta id_repuesto o id_producto + factura)
            $sqlMov = "INSERT INTO movimientos_inventario 
                        (id_repuesto, id_producto, tipo_movimiento, cantidad, precio_compra, numero_factura, id_proveedor, id_usuario_registra, observacion) 
                       VALUES 
                        (:id_repuesto, :id_producto, 'ENTRADA', :cantidad, :precio_compra, :numero_factura, :id_proveedor, :id_usuario_registra, :observacion)";
            
            $stmtMov = $this->conn->prepare($sqlMov);
            
            // Evaluamos cuál ID viene lleno y cuál se va como NULL
            $stmtMov->bindValue(':id_repuesto', !empty($datos['id_repuesto']) ? $datos['id_repuesto'] : null, PDO::PARAM_INT);
            $stmtMov->bindValue(':id_producto', !empty($datos['id_producto']) ? $datos['id_producto'] : null, PDO::PARAM_INT);
            $stmtMov->bindParam(':cantidad', $datos['cantidad'], PDO::PARAM_INT);
            $stmtMov->bindParam(':precio_compra', $datos['precio_compra']);
            $stmtMov->bindParam(':numero_factura', $datos['numero_factura']);
            $stmtMov->bindParam(':id_proveedor', $datos['id_proveedor'], PDO::PARAM_INT);
            $stmtMov->bindParam(':id_usuario_registra', $datos['id_usuario_registra'], PDO::PARAM_INT);
            $stmtMov->bindParam(':observacion', $datos['observacion']);
            $stmtMov->execute();

            // 2. Definir dinámicamente las consultas de Stock según el tipo de artículo
            if (!empty($datos['id_repuesto'])) {
                $sqlCheck = "SELECT id_stock FROM inventario_stock WHERE id_repuesto = :id LIMIT 1";
                $sqlUpdate = "UPDATE inventario_stock SET cantidad_total = cantidad_total + :cantidad WHERE id_repuesto = :id";
                $sqlInsert = "INSERT INTO inventario_stock (id_repuesto, cantidad_total) VALUES (:id, :cantidad)";
                $targetId = $datos['id_repuesto'];
            } else {
                $sqlCheck = "SELECT id_stock FROM inventario_stock WHERE id_producto = :id LIMIT 1";
                $sqlUpdate = "UPDATE inventario_stock SET cantidad_total = cantidad_total + :cantidad WHERE id_producto = :id";
                $sqlInsert = "INSERT INTO inventario_stock (id_producto, cantidad_total) VALUES (:id, :cantidad)";
                $targetId = $datos['id_producto'];
            }

            // 3. Ejecutar la actualización/inserción de stock
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([':id' => $targetId]);
            
            if ($stmtCheck->rowCount() > 0) {
                $stmtUpdate = $this->conn->prepare($sqlUpdate);
                $stmtUpdate->execute([':cantidad' => $datos['cantidad'], ':id' => $targetId]);
            } else {
                $stmtInsert = $this->conn->prepare($sqlInsert);
                $stmtInsert->execute([':id' => $targetId, ':cantidad' => $datos['cantidad']]);
            }

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error en la transacción de compra inteligente: " . $e->getMessage());
            return false;
        }
    }
}