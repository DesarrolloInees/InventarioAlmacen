<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

/**
 * Modelo exclusivo de la acción EDITAR del registro manual de bodega.
 */
class RegistroBodegaEditarModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerRegistroPorId($id)
    {
        try {
            $sql = "SELECT rb.*,
                           b.nombre_bodega,
                           u.nombre AS usuario_registra
                    FROM registro_bodega rb
                    LEFT JOIN bodegas b ON rb.id_bodega = b.id_bodega
                    LEFT JOIN usuarios u ON rb.id_usuario_registra = u.usuario_id
                    WHERE rb.id_registro = :id
                    LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener registro_bodega: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarRegistro($id, array $datos)
    {
        try {
            $sql = "UPDATE registro_bodega SET
                        tipo_movimiento   = :tipo_movimiento,
                        fecha_movimiento  = :fecha_movimiento,
                        nombre_articulo   = :nombre_articulo,
                        codigo_referencia = :codigo_referencia,
                        cantidad          = :cantidad,
                        id_bodega         = :id_bodega,
                        destino           = :destino,
                        remision          = :remision,
                        cotizacion        = :cotizacion,
                        novedad           = :novedad,
                        observacion       = :observacion,
                        updated_at        = NOW()
                    WHERE id_registro = :id AND estado = 'ACTIVO'";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':tipo_movimiento', $datos['tipo_movimiento'], PDO::PARAM_STR);
            $stmt->bindValue(':fecha_movimiento', $datos['fecha_movimiento'], PDO::PARAM_STR);
            $stmt->bindValue(':nombre_articulo', $datos['nombre_articulo'], PDO::PARAM_STR);
            $this->bindNullable($stmt, ':codigo_referencia', $datos['codigo_referencia']);
            $stmt->bindValue(':cantidad', $datos['cantidad']);
            $this->bindNullable($stmt, ':id_bodega', $datos['id_bodega'], PDO::PARAM_INT);
            $this->bindNullable($stmt, ':destino', $datos['destino']);
            $this->bindNullable($stmt, ':remision', $datos['remision']);
            $this->bindNullable($stmt, ':cotizacion', $datos['cotizacion']);
            $this->bindNullable($stmt, ':novedad', $datos['novedad']);
            $this->bindNullable($stmt, ':observacion', $datos['observacion']);
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar registro_bodega: " . $e->getMessage());
            return false;
        }
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
            error_log("Error al obtener bodegas (registroBodegaEditar): " . $e->getMessage());
            return [];
        }
    }

    public function sugerenciasArticulos()
    {
        $consultas = [
            "SELECT nombre_repuesto AS nombre, codigo_referencia AS codigo
               FROM repuestos WHERE estado = 1",
            "SELECT nombre_producto AS nombre, codigo_interno AS codigo
               FROM productos WHERE estado = 1",
            "SELECT nombre_herramienta AS nombre, codigo_referencia AS codigo
               FROM herramientas WHERE estado = 1",
            "SELECT CONCAT(tm.nombreTipoMaquina, ' - ', im.numero_serie) AS nombre,
                    im.numero_serie AS codigo
               FROM inventario_maquinas im
               INNER JOIN tipomaquina tm ON im.idTipoMaquina = tm.idTipoMaquina"
        ];

        $sugerencias = [];
        foreach ($consultas as $sql) {
            try {
                $stmt = $this->conn->query($sql);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
                    $nombre = trim((string) ($fila['nombre'] ?? ''));
                    if ($nombre === '') continue;
                    $codigo = trim((string) ($fila['codigo'] ?? ''));
                    $clave = mb_strtolower($nombre . '|' . $codigo, 'UTF-8');
                    if (!isset($sugerencias[$clave])) {
                        $sugerencias[$clave] = ['nombre' => $nombre, 'codigo' => $codigo];
                    }
                }
            } catch (PDOException $e) {
                continue;
            }
        }

        usort($sugerencias, function ($a, $b) {
            return strcasecmp($a['nombre'], $b['nombre']);
        });

        return array_values($sugerencias);
    }

    private function bindNullable(PDOStatement $stmt, $parametro, $valor, $tipo = PDO::PARAM_STR)
    {
        if ($valor === null || $valor === '') {
            $stmt->bindValue($parametro, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue($parametro, $valor, $tipo);
        }
    }
}
