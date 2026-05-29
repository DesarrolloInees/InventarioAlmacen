<?php
// app/models/salida/salidaEditarModelo.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class SalidaEditarModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Obtener los datos actuales de la asignación
    public function obtenerSalidaPorId($idMovimiento)
    {
        try {
            $sql = "SELECT 
                        m.id_movimiento, 
                        m.id_repuesto,
                        m.id_tecnico_destino,
                        m.cantidad, 
                        m.observacion,
                        r.nombre_repuesto,
                        r.codigo_referencia,
                        u.nombre AS tecnico_nombre
                    FROM movimientos_inventario m
                    INNER JOIN repuestos r ON m.id_repuesto = r.id_repuesto
                    LEFT JOIN usuarios u ON m.id_tecnico_destino = u.usuario_id
                    WHERE m.id_movimiento = :id AND m.tipo_movimiento = 'SALIDA'";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Editar salida y recalcular stock en AMBAS bases de datos si aplica
    public function editarSalida($idMovimiento, $cantidadNueva, $observacionNueva)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Obtener datos viejos para comparar
            $sqlOld = "SELECT id_repuesto, id_tecnico_destino, cantidad, observacion FROM movimientos_inventario WHERE id_movimiento = :id";
            $stmtOld = $this->conn->prepare($sqlOld);
            $stmtOld->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
            $stmtOld->execute();
            $datosViejos = $stmtOld->fetch(PDO::FETCH_ASSOC);

            if (!$datosViejos) {
                $this->conn->rollBack();
                return false;
            }

            $idRepuesto = $datosViejos['id_repuesto'];
            $idTecnico = $datosViejos['id_tecnico_destino'];
            $cantidadVieja = $datosViejos['cantidad'];

            // Si la cantidad es la misma, solo actualizamos la observación y terminamos
            if ($cantidadNueva == $cantidadVieja) {
                $sqlUpdObs = "UPDATE movimientos_inventario SET observacion = :obs WHERE id_movimiento = :id";
                $stmtUpdObs = $this->conn->prepare($sqlUpdObs);
                $stmtUpdObs->execute([':obs' => $observacionNueva, ':id' => $idMovimiento]);
                $this->conn->commit();
                return true;
            }

            // Calculamos la diferencia (Ej: Nueva 8 - Vieja 5 = +3) (Ej: Nueva 2 - Vieja 5 = -3)
            $diferencia = $cantidadNueva - $cantidadVieja;

            // 2. Si la diferencia es positiva (le damos más), verificamos stock local
            if ($diferencia > 0) {
                $sqlStock = "SELECT cantidad_total FROM inventario_stock WHERE id_repuesto = :id FOR UPDATE";
                $stmtStock = $this->conn->prepare($sqlStock);
                $stmtStock->execute([':id' => $idRepuesto]);
                $stockLocal = $stmtStock->fetchColumn();

                if ($stockLocal < $diferencia) {
                    $this->conn->rollBack();
                    return 'error_stock_local';
                }
            }

            // 3. Detectar si era una asignación a motorizado (por la observación original)
            $esMotorizado = (strpos(strtolower($datosViejos['observacion']), 'motorizado') !== false);
            $connMotos = null;

            if ($esMotorizado) {
                // Obtenemos Cédula del Técnico y Código del Repuesto para la BD externa
                $stmtCedula = $this->conn->prepare("SELECT cedula FROM usuarios WHERE usuario_id = :id");
                $stmtCedula->execute([':id' => $idTecnico]);
                $cedulaTecnico = $stmtCedula->fetchColumn();

                $stmtCod = $this->conn->prepare("SELECT codigo_referencia FROM repuestos WHERE id_repuesto = :id");
                $stmtCod->execute([':id' => $idRepuesto]);
                $codigoReferencia = $stmtCod->fetchColumn();

                try {
                    $connMotos = new PDO("mysql:host=127.0.0.1;dbname=base_datos_motos;charset=utf8mb4", "root", "");
                    $connMotos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $connMotos->beginTransaction();

                    $stmtTecMoto = $connMotos->prepare("SELECT id_tecnico FROM tecnico WHERE cedula = :cedula");
                    $stmtTecMoto->execute([':cedula' => $cedulaTecnico]);
                    $idTecnicoMoto = $stmtTecMoto->fetchColumn();

                    $stmtRepMoto = $connMotos->prepare("SELECT id_repuesto FROM repuesto WHERE codigo_referencia = :codigo");
                    $stmtRepMoto->execute([':codigo' => $codigoReferencia]);
                    $idRepuestoMoto = $stmtRepMoto->fetchColumn();

                    if ($idTecnicoMoto && $idRepuestoMoto) {
                        // Si la diferencia es negativa (le quitamos), verificamos si el técnico aún tiene eso en su inventario externo
                        if ($diferencia < 0) {
                            $stmtExtStock = $connMotos->prepare("SELECT cantidad_actual FROM inventario_tecnico WHERE id_tecnico = :idt AND id_repuesto = :idr");
                            $stmtExtStock->execute([':idt' => $idTecnicoMoto, ':idr' => $idRepuestoMoto]);
                            $stockTecnico = $stmtExtStock->fetchColumn();

                            if ($stockTecnico < abs($diferencia)) {
                                $this->conn->rollBack();
                                $connMotos->rollBack();
                                return 'error_stock_tecnico'; // El técnico ya gastó los repuestos, no se los podemos quitar
                            }
                        }

                        // Actualizamos el inventario externo (Sumamos la diferencia, que puede ser positiva o negativa)
                        $sqlMoto = "UPDATE inventario_tecnico SET cantidad_actual = cantidad_actual + :dif WHERE id_tecnico = :idt AND id_repuesto = :idr";
                        $stmtMoto = $connMotos->prepare($sqlMoto);
                        $stmtMoto->execute([':dif' => $diferencia, ':idt' => $idTecnicoMoto, ':idr' => $idRepuestoMoto]);
                    }
                } catch (Exception $e) {
                    error_log("Error BD externa al editar salida: " . $e->getMessage());
                    // Si falla la conexión externa, continuamos pero registramos el error
                }
            }

            // 4. Actualizamos el stock local (Restamos la diferencia)
            $sqlUpdateStock = "UPDATE inventario_stock SET cantidad_total = cantidad_total - :diferencia WHERE id_repuesto = :id";
            $stmtUpdateStock = $this->conn->prepare($sqlUpdateStock);
            $stmtUpdateStock->execute([':diferencia' => $diferencia, ':id' => $idRepuesto]);

            // 5. Actualizamos el movimiento
            $sqlUpdateMov = "UPDATE movimientos_inventario SET cantidad = :cantidad, observacion = :observacion WHERE id_movimiento = :id";
            $stmtUpdateMov = $this->conn->prepare($sqlUpdateMov);
            $stmtUpdateMov->execute([':cantidad' => $cantidadNueva, ':observacion' => $observacionNueva, ':id' => $idMovimiento]);

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
            error_log("Error al editar salida: " . $e->getMessage());
            return false;
        }
    }
}