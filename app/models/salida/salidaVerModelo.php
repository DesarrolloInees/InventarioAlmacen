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
                        m.repuesto_manual,
                        m.destino,
                        m.numero_remision,
                        m.numero_cotizacion,
                        m.novedad,
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

    // Anular salida y DEVOLVER al stock local + Descontar BD externa de motorizado si aplica
    public function anularSalida($idMovimiento)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Obtener la información de la salida antes de borrarla
            $sqlInfo = "SELECT id_repuesto, id_producto, repuesto_manual, id_tecnico_destino, cantidad, observacion 
                        FROM movimientos_inventario 
                        WHERE id_movimiento = :id AND tipo_movimiento = 'SALIDA'";
            $stmtInfo = $this->conn->prepare($sqlInfo);
            $stmtInfo->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtInfo->execute();
            $movimiento = $stmtInfo->fetch(PDO::FETCH_ASSOC);

            if (!$movimiento) {
                $this->conn->rollBack();
                return false;
            }

            $cantidadDevuelta = $movimiento['cantidad'];
            $esManual = !empty($movimiento['repuesto_manual']);
            $idRepuesto = $movimiento['id_repuesto'];
            $idProducto = $movimiento['id_producto'];
            $idTecnico = $movimiento['id_tecnico_destino'];

            // 2. Si NO es un ítem manual, devolvemos (SUMAMOS) la cantidad al stock local
            if (!$esManual) {
                $esRepuesto = !empty($idRepuesto);
                $columnaFiltro = $esRepuesto ? 'id_repuesto' : 'id_producto';
                $targetId = $esRepuesto ? $idRepuesto : $idProducto;

                $sqlSumar = "UPDATE inventario_stock SET cantidad_total = cantidad_total + :cantidad WHERE $columnaFiltro = :id";
                $stmtSumar = $this->conn->prepare($sqlSumar);
                $stmtSumar->bindParam(':cantidad', $cantidadDevuelta, PDO::PARAM_INT);
                $stmtSumar->bindParam(':id', $targetId, PDO::PARAM_INT);
                $stmtSumar->execute();
            }

            // 3. Si era una asignación a motorizado (con repuesto de catálogo), descontamos de su App externa
            $esMotorizado = (strpos(strtolower($movimiento['observacion'] ?? ''), 'motorizado') !== false);
            $connMotos = null;

            if ($esMotorizado && $idTecnico && !empty($idRepuesto) && !$esManual) {
                try {
                    // Cédula técnico y código repuesto
                    $stmtCed = $this->conn->prepare("SELECT cedula FROM usuarios WHERE usuario_id = :id");
                    $stmtCed->execute([':id' => $idTecnico]);
                    $cedula = $stmtCed->fetchColumn();

                    $stmtCod = $this->conn->prepare("SELECT codigo_referencia FROM repuestos WHERE id_repuesto = :id");
                    $stmtCod->execute([':id' => $idRepuesto]);
                    $codigo = $stmtCod->fetchColumn();

                    if ($cedula && $codigo) {
                        $connMotos = new PDO("mysql:host=127.0.0.1;dbname=inees_mantenimientos;charset=utf8mb4", "root", "");
                        $connMotos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $connMotos->beginTransaction();

                        $stmtTecM = $connMotos->prepare("SELECT id_tecnico FROM tecnico WHERE cedula = :cedula");
                        $stmtTecM->execute([':cedula' => $cedula]);
                        $idTecMoto = $stmtTecM->fetchColumn();

                        $stmtRepM = $connMotos->prepare("SELECT id_repuesto FROM repuesto WHERE codigo_referencia = :codigo");
                        $stmtRepM->execute([':codigo' => $codigo]);
                        $idRepMoto = $stmtRepM->fetchColumn();

                        if ($idTecMoto && $idRepMoto) {
                            // Le RESTAMOS al técnico motorizado la cantidad anulada
                            $sqlRestarM = "UPDATE inventario_tecnico 
                                            SET cantidad_actual = GREATEST(0, cantidad_actual - :cant) 
                                            WHERE id_tecnico = :idt AND id_repuesto = :idr";
                            $stmtRestarM = $connMotos->prepare($sqlRestarM);
                            $stmtRestarM->execute([':cant' => $cantidadDevuelta, ':idt' => $idTecMoto, ':idr' => $idRepMoto]);
                        }
                    }
                } catch (Exception $e) {
                    error_log("Error al revertir inventario externo en anulación: " . $e->getMessage());
                }
            }

            // 4. Eliminar el registro del historial
            $sqlDelete = "DELETE FROM movimientos_inventario WHERE id_movimiento = :id";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtDelete->execute();

            $this->conn->commit();
            if ($connMotos !== null && $connMotos->inTransaction()) {
                $connMotos->commit();
            }

            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            if (isset($connMotos) && $connMotos !== null && $connMotos->inTransaction()) {
                $connMotos->rollBack();
            }
            error_log("Error al anular salida unificada: " . $e->getMessage());
            return false;
        }
    }
}