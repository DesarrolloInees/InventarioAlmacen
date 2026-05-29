<?php
defined('ENTRADA_PRINCIPAL') or die('Acceso denegado');

require_once __DIR__ . '/../repuestos/RepuestosModelo.php';

class MovimientosModelo
{
    private $conn;
    private $repuestosModelo;

    public function __construct(PDO $db)
    {
        $this->conn            = $db;
        $this->repuestosModelo = new RepuestosModelo($db);
    }

    /**
     * Registra un movimiento y ajusta el stock en una sola transacción.
     * $tipo = 'entrada' | 'salida'
     */
    public function registrar(int $repuesto_id, string $tipo, float $cantidad,
                                string $motivo, string $ref_doc, int $usuario_id): array
    {
        try {
            $this->conn->beginTransaction();

            // 1. Obtenemos el stock actual (con bloqueo de fila)
            $stmt = $this->conn->prepare(
                "SELECT stock_actual FROM repuestos WHERE repuesto_id = :id FOR UPDATE"
            );
            $stmt->execute([':id' => $repuesto_id]);
            $repuesto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$repuesto) {
                $this->conn->rollBack();
                return ['ok' => false, 'msg' => 'Repuesto no encontrado.'];
            }

            $stock_antes = (float)$repuesto['stock_actual'];
            $diferencia  = $tipo === 'entrada' ? $cantidad : -$cantidad;
            $stock_despues = $stock_antes + $diferencia;

            if ($stock_despues < 0) {
                $this->conn->rollBack();
                return ['ok' => false, 'msg' => "Stock insuficiente. Disponible: {$stock_antes}"];
            }

            // 2. Insertamos el movimiento
            $sql = "INSERT INTO movimientos_inventario
                        (repuesto_id, tipo, cantidad, stock_antes, stock_despues,
                            motivo, referencia_doc, usuario_id, fecha, hora)
                    VALUES
                        (:rep, :tipo, :cant, :antes, :despues,
                            :motivo, :ref, :usr, CURDATE(), CURTIME())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':rep'     => $repuesto_id,
                ':tipo'    => $tipo,
                ':cant'    => $cantidad,
                ':antes'   => $stock_antes,
                ':despues' => $stock_despues,
                ':motivo'  => $motivo,
                ':ref'     => $ref_doc,
                ':usr'     => $usuario_id,
            ]);

            // 3. Actualizamos el stock_actual
            $this->repuestosModelo->actualizarStock($repuesto_id, $diferencia);

            $this->conn->commit();
            return ['ok' => true, 'msg' => 'Movimiento registrado.', 'stock_nuevo' => $stock_despues];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error en movimiento: " . $e->getMessage());
            return ['ok' => false, 'msg' => 'Error interno al registrar el movimiento.'];
        }
    }

    public function obtenerPorRepuesto(int $repuesto_id, int $limite = 50): array
    {
        $sql = "SELECT m.*, u.nombre AS usuario_nombre
                FROM movimientos_inventario m
                INNER JOIN usuarios u ON m.usuario_id = u.usuario_id
                WHERE m.repuesto_id = :id
                ORDER BY m.creado_en DESC
                LIMIT :lim";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id',  $repuesto_id, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limite,       PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}