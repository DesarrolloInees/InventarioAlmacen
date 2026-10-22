<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

/**
 * Modelo exclusivo de la acción CREAR del registro manual de bodega.
 * No toca stock ni movimientos de inventario.
 */
class RegistroBodegaCrearModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Bodegas disponibles para el selector del formulario.
     */
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
            error_log("Error al obtener bodegas (registroBodegaCrear): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Nombres de artículos existentes para el autocompletado (datalist).
     * Solo lectura, no modifica ninguna tabla.
     */
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
                // Si una tabla no existe en esta instalación, se omite sin romper el formulario.
                continue;
            }
        }

        usort($sugerencias, function ($a, $b) {
            return strcasecmp($a['nombre'], $b['nombre']);
        });

        return array_values($sugerencias);
    }

    /**
     * Inserta un registro manual. Devuelve el ID creado o false.
     */
    public function insertarRegistro(array $datos)
    {
        try {
            $sql = "INSERT INTO registro_bodega
                        (tipo_movimiento, fecha_movimiento, nombre_articulo, codigo_referencia,
                         cantidad, id_bodega, destino, remision, cotizacion, novedad, observacion,
                         id_usuario_registra, estado)
                    VALUES
                        (:tipo_movimiento, :fecha_movimiento, :nombre_articulo, :codigo_referencia,
                         :cantidad, :id_bodega, :destino, :remision, :cotizacion, :novedad, :observacion,
                         :id_usuario_registra, 'ACTIVO')";

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
            $stmt->bindValue(':id_usuario_registra', (int) $datos['id_usuario_registra'], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return (int) $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al insertar registro_bodega: " . $e->getMessage());
            return false;
        }
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
