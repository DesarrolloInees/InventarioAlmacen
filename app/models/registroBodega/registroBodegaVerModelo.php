<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

/**
 * Modelo exclusivo de la acción VER del registro manual de bodega.
 * Listado + KPIs + filtros. Solo lectura.
 */
class RegistroBodegaVerModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerRegistros(array $filtros = [])
    {
        try {
            $sql = "SELECT rb.*,
                           b.nombre_bodega,
                           u.nombre AS usuario_registra
                    FROM registro_bodega rb
                    LEFT JOIN bodegas b ON rb.id_bodega = b.id_bodega
                    LEFT JOIN usuarios u ON rb.id_usuario_registra = u.usuario_id";

            list($where, $params) = $this->construirFiltros($filtros);
            $sql .= $where . " ORDER BY rb.fecha_movimiento DESC, rb.id_registro DESC";

            $stmt = $this->conn->prepare($sql);
            foreach ($params as $nombre => $valor) {
                $stmt->bindValue($nombre, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar registro_bodega: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerKPIs(array $filtros = [])
    {
        $kpis = [
            'total_entradas'   => 0,
            'total_salidas'    => 0,
            'total_items'      => 0,
            'total_remisiones' => 0,
            'total_anulados'   => 0,
        ];

        try {
            $sql = "SELECT
                        COALESCE(SUM(CASE WHEN rb.tipo_movimiento = 'ENTRADA' AND rb.estado = 'ACTIVO' THEN rb.cantidad ELSE 0 END), 0) AS total_entradas,
                        COALESCE(SUM(CASE WHEN rb.tipo_movimiento = 'SALIDA'  AND rb.estado = 'ACTIVO' THEN rb.cantidad ELSE 0 END), 0) AS total_salidas,
                        COALESCE(SUM(CASE WHEN rb.estado = 'ACTIVO' THEN 1 ELSE 0 END), 0) AS total_items,
                        COALESCE(SUM(CASE WHEN rb.estado = 'ANULADO' THEN 1 ELSE 0 END), 0) AS total_anulados,
                        COUNT(DISTINCT CASE WHEN rb.estado = 'ACTIVO'
                              THEN COALESCE(NULLIF(TRIM(rb.remision), ''), CONCAT('RB-', rb.id_registro)) END) AS total_remisiones
                    FROM registro_bodega rb";

            list($where, $params) = $this->construirFiltros($filtros);
            $sql .= $where;

            $stmt = $this->conn->prepare($sql);
            foreach ($params as $nombre => $valor) {
                $stmt->bindValue($nombre, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fila) {
                $kpis['total_entradas']   = (float) $fila['total_entradas'];
                $kpis['total_salidas']    = (float) $fila['total_salidas'];
                $kpis['total_items']      = (int) $fila['total_items'];
                $kpis['total_remisiones'] = (int) $fila['total_remisiones'];
                $kpis['total_anulados']   = (int) $fila['total_anulados'];
            }
        } catch (PDOException $e) {
            error_log("Error al calcular KPIs registro_bodega: " . $e->getMessage());
        }

        return $kpis;
    }

    public function obtenerBodegasActivas()
    {
        try {
            $sql = "SELECT id_bodega, nombre_bodega
                    FROM bodegas
                    WHERE estado = 1
                    ORDER BY nombre_bodega ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener bodegas (registroBodegaVer): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Construye el WHERE compartido entre listado y KPIs.
     * Nota: se usan parámetros con nombres únicos porque PDO trabaja con
     * ATTR_EMULATE_PREPARES = false.
     */
    private function construirFiltros(array $filtros)
    {
        $condiciones = [];
        $params = [];

        $tipo = strtoupper(trim((string) ($filtros['tipo'] ?? '')));
        if (in_array($tipo, ['ENTRADA', 'SALIDA'], true)) {
            $condiciones[] = "rb.tipo_movimiento = :tipo";
            $params[':tipo'] = $tipo;
        }

        if (!empty($filtros['fecha_desde'])) {
            $condiciones[] = "rb.fecha_movimiento >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'] . ' 00:00:00';
        }

        if (!empty($filtros['fecha_hasta'])) {
            $condiciones[] = "rb.fecha_movimiento <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'] . ' 23:59:59';
        }

        if (!empty($filtros['id_bodega'])) {
            $condiciones[] = "rb.id_bodega = :id_bodega";
            $params[':id_bodega'] = (int) $filtros['id_bodega'];
        }

        if (!empty($filtros['remision'])) {
            $condiciones[] = "rb.remision LIKE :remision";
            $params[':remision'] = '%' . $filtros['remision'] . '%';
        }

        if (!empty($filtros['q'])) {
            $condiciones[] = "(rb.nombre_articulo LIKE :q1
                            OR rb.codigo_referencia LIKE :q2
                            OR rb.destino LIKE :q3
                            OR rb.remision LIKE :q4
                            OR rb.cotizacion LIKE :q5
                            OR rb.novedad LIKE :q6
                            OR rb.observacion LIKE :q7)";
            $texto = '%' . $filtros['q'] . '%';
            for ($i = 1; $i <= 7; $i++) {
                $params[':q' . $i] = $texto;
            }
        }

        $where = $condiciones ? ' WHERE ' . implode(' AND ', $condiciones) : '';
        return [$where, $params];
    }
}
