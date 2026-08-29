<?php

// app/models/notificacion/notificacionInventarioModelo.php

if (!defined('ENTRADA_PRINCIPAL')) {
    die("Acceso denegado.");
}

class NotificacionInventarioModelo
{
    private $conn;      // solicitudprosegur (usuarios, notificaciones...)
    private $connInv;   // inventario-almacen (productos, repuestos, inventario_stock, movimientos_inventario)

    public function __construct(PDO $db, PDO $dbInventario)
    {
        $this->conn = $db;
        $this->connInv = $dbInventario;
    }


    /**
     * Obtener notificaciones no leídas del usuario actual.
     */
    public function obtenerNotificacionesNoLeidas($usuarioId)
    {
        try {

            $sql = "SELECT
                        notificacion_id,
                        usuario_id,
                        tipo,
                        titulo,
                        mensaje,
                        leida,
                        fecha_creacion,
                        fecha_lectura
                    FROM notificaciones
                    WHERE (usuario_id = :usuario_id OR usuario_id IS NULL OR usuario_id = 0)
                    AND leida = 0
                    ORDER BY fecha_creacion DESC";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':usuario_id' => (int)$usuarioId
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            error_log(
                "Error en obtenerNotificacionesNoLeidas: "
                . $e->getMessage()
            );

            return [];
        }
    }


    /**
     * Marcar una notificación como leída.
     *
     * IMPORTANTE:
     * La columna real de la BD es:
     * notificacion_id
     */
    public function marcarComoLeida($idNotificacion, $usuarioId = null)
    {
        try {
            $sql = "UPDATE notificaciones
                    SET leida = 1,
                        fecha_lectura = NOW()
                    WHERE notificacion_id = :notificacion_id";

            $params = [':notificacion_id' => (int)$idNotificacion];

            if ($usuarioId) {
                $sql .= " AND (usuario_id = :usuario_id OR usuario_id IS NULL OR usuario_id = 0)";
                $params[':usuario_id'] = (int)$usuarioId;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return true;

        } catch (PDOException $e) {
            error_log("Error en marcarComoLeida: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Obtener alertas de stock crítico.
     * Combina productos y repuestos con UNION ALL (compatible con MariaDB 10.4).
     * El "último responsable" se resuelve en 2 pasos porque movimientos_inventario
     * (inventario-almacen) y usuarios (solicitudprosegur) están en bases distintas.
     */
    public function obtenerStockCritico($umbral)
    {
        try {
            $umbral = (int)$umbral;

            // Paso 1: alertas de stock crítico, combinando productos y repuestos
            $sql = "SELECT 
                        i.id_stock,
                        i.id_producto,
                        NULL AS id_repuesto,
                        i.cantidad_total AS cantidad_disponible,
                        CAST(p.nombre_producto AS CHAR CHARACTER SET utf8mb4) AS nombre_articulo,
                        'Producto' AS categoria
                    FROM inventario_stock i
                    INNER JOIN productos p ON i.id_producto = p.id_producto
                    WHERE i.cantidad_total <= :umbral1

                    UNION ALL

                    SELECT 
                        i.id_stock,
                        NULL AS id_producto,
                        i.id_repuesto,
                        i.cantidad_total AS cantidad_disponible,
                        CAST(r.nombre_repuesto AS CHAR CHARACTER SET utf8mb4) AS nombre_articulo,
                        'Repuesto' AS categoria
                    FROM inventario_stock i
                    INNER JOIN repuestos r ON i.id_repuesto = r.id_repuesto
                    WHERE i.cantidad_total <= :umbral2

                    ORDER BY cantidad_disponible ASC";

            $stmt = $this->connInv->prepare($sql);
            $stmt->bindValue(':umbral1', $umbral, PDO::PARAM_INT);
            $stmt->bindValue(':umbral2', $umbral, PDO::PARAM_INT);
            $stmt->execute();
            $alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Paso 2: para cada alerta, buscar el último responsable de entrada
            foreach ($alertas as &$item) {
                $item['ultimo_responsable'] = 'Sin registro de entrada';

                $columna = $item['id_producto'] ? 'id_producto' : 'id_repuesto';
                $idItem = $item['id_producto'] ?: $item['id_repuesto'];

                $sqlMov = "SELECT id_usuario_registra
                            FROM movimientos_inventario
                            WHERE tipo_movimiento = 'ENTRADA'
                            AND $columna = :id
                            ORDER BY fecha_movimiento DESC
                            LIMIT 1";

                $stmtMov = $this->connInv->prepare($sqlMov);
                $stmtMov->bindValue(':id', $idItem, PDO::PARAM_INT);
                $stmtMov->execute();
                $idUsuario = $stmtMov->fetchColumn();

                if ($idUsuario) {
                    $sqlUser = "SELECT nombre FROM usuarios WHERE usuario_id = :id";
                    $stmtUser = $this->conn->prepare($sqlUser);
                    $stmtUser->bindValue(':id', $idUsuario, PDO::PARAM_INT);
                    $stmtUser->execute();
                    $nombre = $stmtUser->fetchColumn();
                    if ($nombre) {
                        $item['ultimo_responsable'] = $nombre;
                    }
                }
            }
            unset($item);

            return $alertas;

        } catch (PDOException $e) {

            error_log(
                "Error en obtenerStockCritico: "
                . $e->getMessage()
            );

            return [];
        }
    }
}