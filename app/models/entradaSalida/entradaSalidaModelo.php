<?php
if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class EntradaSalidaModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // KPIs generales del rango de fechas
    public function getKpisMovimientos($fechaDesde = null, $fechaHasta = null)
    {
        $sql = "SELECT
                COALESCE(SUM(CASE WHEN tipo_movimiento = 'ENTRADA' THEN cantidad ELSE 0 END), 0) as total_entradas,
                COALESCE(SUM(CASE WHEN tipo_movimiento = 'SALIDA' THEN cantidad ELSE 0 END), 0) as total_salidas,
                COALESCE(SUM(CASE WHEN tipo_movimiento = 'ENTRADA' THEN 1 ELSE 0 END), 0) as movimientos_entrada,
                COALESCE(SUM(CASE WHEN tipo_movimiento = 'SALIDA' THEN 1 ELSE 0 END), 0) as movimientos_salida,
                COALESCE(COUNT(DISTINCT COALESCE(id_repuesto, id_producto)), 0) as referencias_distintas
            FROM movimientos_inventario
            WHERE 1=1";

        if ($fechaDesde) {
            $sql .= " AND fecha_movimiento >= :fecha_desde";
        }
        if ($fechaHasta) {
            $sql .= " AND fecha_movimiento <= :fecha_hasta_fin";
        }

        $stmt = $this->conn->prepare($sql);
        if ($fechaDesde) {
            $fd = $fechaDesde . ' 00:00:00';
            $stmt->bindParam(':fecha_desde', $fd);
        }
        if ($fechaHasta) {
            $fh = $fechaHasta . ' 23:59:59';
            $stmt->bindParam(':fecha_hasta_fin', $fh);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listado completo de movimientos (con joins), filtrado por rango de fechas
    // Incluye tecnico origen de RECUPERADOS (local via JOIN + motorizado via texto).
    public function getMovimientos($fechaDesde = null, $fechaHasta = null)
    {
        $tieneOrigen = $this->tieneColumna('origen_entrada');
        $tieneSerial = $this->tieneColumna('serial_recuperado');
        $tieneTec = $this->tieneColumna('id_tecnico_origen');
        $tieneTecNom = $this->tieneColumna('tecnico_origen_nombre');

        $selOrigen = $tieneOrigen ? "m.origen_entrada," : "NULL AS origen_entrada,";
        $selSerial = $tieneSerial ? "m.serial_recuperado," : "NULL AS serial_recuperado,";
        $selTecId = $tieneTec ? "m.id_tecnico_origen," : "NULL AS id_tecnico_origen,";
        $selTecNom = $tieneTecNom ? "m.tecnico_origen_nombre," : "NULL AS tecnico_origen_nombre,";
        $joinTec = $tieneTec ? "LEFT JOIN tecnicos_locales t ON m.id_tecnico_origen = t.id_tecnico_local" : "";
        $selTecLocal = $tieneTec ? "t.nombre_tecnico AS tecnico_local," : "NULL AS tecnico_local,";

        $sql = "SELECT m.id_movimiento, m.*,
                    $selOrigen $selSerial $selTecId $selTecNom $selTecLocal
                    r.nombre_repuesto, r.codigo_referencia,
                    p.nombre_producto, p.codigo_interno,
                    u.nombre as nombre_usuario
            FROM movimientos_inventario m
            LEFT JOIN repuestos r ON m.id_repuesto = r.id_repuesto
            LEFT JOIN productos p ON m.id_producto = p.id_producto
            LEFT JOIN usuarios u ON m.id_usuario_registra = u.usuario_id
            $joinTec
            WHERE 1=1";

        if ($fechaDesde) {
            $sql .= " AND m.fecha_movimiento >= :fecha_desde";
        }
        if ($fechaHasta) {
            $sql .= " AND m.fecha_movimiento <= :fecha_hasta_fin";
        }

        $sql .= " ORDER BY m.fecha_movimiento DESC";

        $stmt = $this->conn->prepare($sql);
        if ($fechaDesde) {
            $fd = $fechaDesde . ' 00:00:00';
            $stmt->bindParam(':fecha_desde', $fd);
        }
        if ($fechaHasta) {
            $fh = $fechaHasta . ' 23:59:59';
            $stmt->bindParam(':fecha_hasta_fin', $fh);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================================
    // MÁQUINAS (tabla movimientos_maquinas): sincronización con taller.
    // INGRESO -> ENTRADA | SALIDA_REMISION -> SALIDA | TRASLADO -> aparte
    // Tolerante a fallos: si la tabla aún no existe en producción, no rompe nada.
    // =====================================================================
    public function getKpisMaquinas($fechaDesde = null, $fechaHasta = null)
    {
        $default = [
            'maquinas_entradas' => 0,
            'maquinas_salidas' => 0,
            'maquinas_traslados' => 0,
            'maquinas_distintas' => 0
        ];

        try {
            if (!$this->existeTabla('movimientos_maquinas')) {
                return $default;
            }

            $sql = "SELECT
                    COALESCE(SUM(CASE WHEN tipo_movimiento = 'INGRESO' THEN 1 ELSE 0 END), 0) AS maquinas_entradas,
                    COALESCE(SUM(CASE WHEN tipo_movimiento = 'SALIDA_REMISION' THEN 1 ELSE 0 END), 0) AS maquinas_salidas,
                    COALESCE(SUM(CASE WHEN tipo_movimiento = 'TRASLADO' THEN 1 ELSE 0 END), 0) AS maquinas_traslados,
                    COALESCE(COUNT(DISTINCT id_maquina), 0) AS maquinas_distintas
                FROM movimientos_maquinas
                WHERE 1=1";

            if ($fechaDesde) $sql .= " AND fecha_movimiento >= :fecha_desde";
            if ($fechaHasta) $sql .= " AND fecha_movimiento <= :fecha_hasta_fin";

            $stmt = $this->conn->prepare($sql);
            if ($fechaDesde) { $fd = $fechaDesde . ' 00:00:00'; $stmt->bindParam(':fecha_desde', $fd); }
            if ($fechaHasta) { $fh = $fechaHasta . ' 23:59:59'; $stmt->bindParam(':fecha_hasta_fin', $fh); }
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res ?: $default;
        } catch (Throwable $e) {
            error_log("Aviso getKpisMaquinas (no bloqueante): " . $e->getMessage());
            return $default;
        }
    }

    public function getMovimientosMaquinas($fechaDesde = null, $fechaHasta = null)
    {
        try {
            if (!$this->existeTabla('movimientos_maquinas') || !$this->existeTabla('inventario_maquinas')) {
                return [];
            }

            $sql = "SELECT mm.id_mov_maquina, mm.tipo_movimiento, mm.fecha_movimiento, mm.observacion,
                           im.numero_serie, tm.nombreTipoMaquina,
                           bo.nombre_bodega AS bodega_origen, bd.nombre_bodega AS bodega_destino,
                           u.nombre AS nombre_usuario
                    FROM movimientos_maquinas mm
                    INNER JOIN inventario_maquinas im ON mm.id_maquina = im.id_maquina
                    INNER JOIN tipomaquina tm ON im.idTipoMaquina = tm.idTipoMaquina
                    LEFT JOIN bodegas bo ON mm.id_bodega_origen = bo.id_bodega
                    LEFT JOIN bodegas bd ON mm.id_bodega_destino = bd.id_bodega
                    LEFT JOIN usuarios u ON mm.id_usuario_registra = u.usuario_id
                    WHERE 1=1";

            if ($fechaDesde) $sql .= " AND mm.fecha_movimiento >= :fecha_desde";
            if ($fechaHasta) $sql .= " AND mm.fecha_movimiento <= :fecha_hasta_fin";
            $sql .= " ORDER BY mm.fecha_movimiento DESC";

            $stmt = $this->conn->prepare($sql);
            if ($fechaDesde) { $fd = $fechaDesde . ' 00:00:00'; $stmt->bindParam(':fecha_desde', $fd); }
            if ($fechaHasta) { $fh = $fechaHasta . ' 23:59:59'; $stmt->bindParam(':fecha_hasta_fin', $fh); }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Aviso getMovimientosMaquinas (no bloqueante): " . $e->getMessage());
            return [];
        }
    }

    private function existeTabla($tabla)
    {
        try {
            $st = $this->conn->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t");
            $st->execute([':t' => $tabla]);
            return (bool)$st->fetchColumn();
        } catch (Throwable $e) {
            return false;
        }
    }

    private function tieneColumna($col)
    {
        try {
            $st = $this->conn->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'movimientos_inventario' AND COLUMN_NAME = :c");
            $st->execute([':c' => $col]);
            return (bool)$st->fetchColumn();
        } catch (PDOException $e) { return false; }
    }

    public function actualizarFechaMovimiento($idMovimiento, $nuevaFecha)
{
    $sql = "UPDATE movimientos_inventario 
            SET fecha_movimiento = :fecha 
            WHERE id_movimiento = :id";
            
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':fecha', $nuevaFecha);
    $stmt->bindParam(':id', $idMovimiento, PDO::PARAM_INT);
    return $stmt->execute();
}
}