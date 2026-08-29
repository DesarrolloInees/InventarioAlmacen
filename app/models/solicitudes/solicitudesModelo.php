<?php
// app/models/solicitudes/solicitudesModelo.php

class solicitudesModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerSolicitudes()
    {
        try {
            $sql = "SELECT 
                        s.solicitud_id,
                        s.asunto,
                        s.prioridad,
                        s.estado,
                        s.fecha_solicitud,
                        s.fecha_limite,
                        u.nombre AS nombre_solicitante,
                        a.nombre_area
                    FROM solicitudes s
                    INNER JOIN usuarios u ON s.usuario_id = u.usuario_id
                    LEFT JOIN areas a ON s.area_id = a.id_area
                    ORDER BY s.fecha_solicitud DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener solicitudes: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Obtener solicitudes cuyo tiempo ya venció.
     *
     * REGLA:
     * - Si fecha_limite ya pasó, la solicitud está vencida.
     * - Si está "Listo para Entrega", NO se genera alerta.
     * - Cualquier otro estado continúa siendo vigilado.
     */
    public function obtenerSolicitudesVencidas()
    {
        try {

            $sql = "SELECT
                        s.solicitud_id,
                        s.asunto,
                        s.descripcion,
                        s.prioridad,
                        s.estado,
                        s.usuario_id,
                        s.atendido_por,
                        s.fecha_solicitud,
                        s.fecha_actualizacion,
                        s.fecha_limite,
                        u.nombre AS nombre_solicitante,
                        a.nombre_area
                    FROM solicitudes s
                    INNER JOIN usuarios u
                        ON s.usuario_id = u.usuario_id
                    LEFT JOIN areas a
                        ON s.area_id = a.id_area
                    WHERE s.fecha_limite IS NOT NULL
                      AND s.fecha_limite < NOW()
                      AND LOWER(TRIM(s.estado)) NOT IN ('listo para entrega', 'atendida')
                    ORDER BY s.fecha_limite ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            error_log(
                "Error al obtener solicitudes vencidas: "
                . $e->getMessage()
            );

            return [];
        }
    }


    public function obtenerSolicitudPorId($solicitud_id)
    {
        try {
            $sql = "SELECT 
                        s.solicitud_id,
                        s.asunto,
                        s.descripcion,
                        s.prioridad,
                        s.estado,
                        s.observaciones,
                        s.foto,
                        s.fecha_solicitud,
                        s.fecha_actualizacion,
                        s.fecha_limite,
                        u.nombre AS nombre_solicitante,
                        a.nombre_area
                    FROM solicitudes s
                    INNER JOIN usuarios u ON s.usuario_id = u.usuario_id
                    LEFT JOIN areas a ON s.area_id = a.id_area
                    WHERE s.solicitud_id = :id
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $solicitud_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener solicitud por ID: " . $e->getMessage());
            return false;
        }
    }


    public function cambiarEstadoSolicitud(
        $solicitud_id,
        $nuevoEstado,
        $usuario_id,
        $observaciones = null
    )
    {
        try {
            $sql = "UPDATE solicitudes 
                    SET estado = :estado,
                        observaciones = :observaciones,
                        atendido_por = :atendido_por,
                        fecha_actualizacion = NOW(),
                        fecha_atencion = NOW()
                    WHERE solicitud_id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(
                ':estado',
                $nuevoEstado
            );

            $stmt->bindParam(
                ':observaciones',
                $observaciones
            );

            $stmt->bindParam(
                ':atendido_por',
                $usuario_id,
                PDO::PARAM_INT
            );

            $stmt->bindParam(
                ':id',
                $solicitud_id,
                PDO::PARAM_INT
            );

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log(
                "Error al cambiar estado de solicitud: "
                . $e->getMessage()
            );

            return false;
        }
    }


    // ============================================================
    // PRIORIDAD
    // ============================================================

    // Baja = 3 días (72h)
    // Media = 1 día y medio (36h)
    // Alta = 1 día (24h)
    // Urgente = 12h

    private function obtenerHorasPorPrioridad($prioridad)
    {
        $mapa = [
            'baja'    => 72,
            'media'   => 36,
            'alta'    => 24,
            'urgente' => 12
        ];

        $clave = strtolower(trim($prioridad));

        return $mapa[$clave] ?? 36;
    }


    // ============================================================
    // ACTUALIZAR PRIORIDAD
    // ============================================================

    public function actualizarPrioridadSolicitud(
        $solicitud_id,
        $prioridad,
        $usuario_id
    )
    {
        try {

            $horas = $this->obtenerHorasPorPrioridad($prioridad);

            $sql = "UPDATE solicitudes 
                    SET prioridad = :prioridad,
                        atendido_por = :atendido_por,
                        fecha_limite = DATE_ADD(
                            NOW(),
                            INTERVAL :horas HOUR
                        ),
                        fecha_actualizacion = NOW()
                    WHERE solicitud_id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(
                ':prioridad',
                $prioridad
            );

            $stmt->bindParam(
                ':atendido_por',
                $usuario_id,
                PDO::PARAM_INT
            );

            $stmt->bindParam(
                ':horas',
                $horas,
                PDO::PARAM_INT
            );

            $stmt->bindParam(
                ':id',
                $solicitud_id,
                PDO::PARAM_INT
            );

            return $stmt->execute();

        } catch (PDOException $e) {

            error_log(
                "Error al actualizar prioridad de solicitud: "
                . $e->getMessage()
            );

            return false;
        }
    }


    // ============================================================
    // CREAR SOLICITUD
    // ============================================================

    public function crearSolicitud(
        $asunto,
        $descripcion,
        $prioridad,
        $area_id,
        $usuario_id,
        $foto = null
    )
    {
        try {

            $horas = $this->obtenerHorasPorPrioridad($prioridad);

            $sql = "INSERT INTO solicitudes (
                        asunto,
                        descripcion,
                        prioridad,
                        area_id,
                        usuario_id,
                        foto,
                        estado,
                        fecha_solicitud,
                        fecha_limite
                    ) 
                    VALUES (
                        :asunto,
                        :descripcion,
                        :prioridad,
                        :area_id,
                        :usuario_id,
                        :foto,
                        'pendiente',
                        NOW(),
                        DATE_ADD(NOW(), INTERVAL :horas HOUR)
                    )";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(
                ':asunto',
                $asunto
            );

            $stmt->bindParam(
                ':descripcion',
                $descripcion
            );

            $stmt->bindParam(
                ':prioridad',
                $prioridad
            );

            $stmt->bindParam(
                ':area_id',
                $area_id,
                PDO::PARAM_INT
            );

            $stmt->bindParam(
                ':usuario_id',
                $usuario_id,
                PDO::PARAM_INT
            );

            $stmt->bindParam(
                ':foto',
                $foto
            );

            $stmt->bindParam(
                ':horas',
                $horas,
                PDO::PARAM_INT
            );

            if ($stmt->execute()) {
                return (int)$this->conn->lastInsertId();
            }

            return false;

        } catch (PDOException $e) {

            error_log(
                "Error al crear la solicitud: "
                . $e->getMessage()
            );

            return false;
        }
    }


    // ============================================================
    // DETALLES DE SOLICITUD
    // ============================================================

    public function guardarDetallesSolicitud(
        $solicitud_id,
        array $items
    )
    {
        try {

            $sql = "INSERT INTO detalle_solicitud (
                        solicitud_id,
                        producto_id,
                        cantidad_solicitada,
                        observaciones,
                        fecha_creacion
                    ) 
                    VALUES (
                        :solicitud_id,
                        :producto_id,
                        :cantidad,
                        :obs,
                        NOW()
                    )";

            $stmt = $this->conn->prepare($sql);

            foreach ($items as $item) {

                $producto_id = (int)(
                    $item['producto_id'] ?? 0
                );

                $cantidad = (int)(
                    $item['cantidad'] ?? 1
                );

                $obs = trim(
                    $item['observaciones'] ?? ''
                );

                if (
                    $producto_id > 0 &&
                    $cantidad > 0
                ) {

                    $stmt->execute([
                        ':solicitud_id' => $solicitud_id,
                        ':producto_id'  => $producto_id,
                        ':cantidad'     => $cantidad,
                        ':obs'          => $obs
                    ]);
                }
            }

            return true;

        } catch (PDOException $e) {

            error_log(
                "Error al guardar detalles de la solicitud: "
                . $e->getMessage()
            );

            return false;
        }
    }


    public function obtenerDetallesSolicitud($solicitud_id)
    {
        try {

            $sql = "SELECT 
                        d.detalle_id,
                        d.solicitud_id,
                        d.producto_id,
                        d.cantidad_solicitada,
                        d.cantidad_aprobada,
                        d.cantidad_entregada,
                        d.observaciones,
                        COALESCE(
                            p.nombre_producto,
                            CONCAT(
                                'Artículo #',
                                d.producto_id
                            )
                        ) AS nombre_producto,
                        COALESCE(
                            p.unidad_medida,
                            'unidad'
                        ) AS unidad_medida
                    FROM detalle_solicitud d
                    LEFT JOIN productos p
                        ON d.producto_id = p.producto_id
                    WHERE d.solicitud_id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(
                ':id',
                $solicitud_id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $detalles = $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );

            if (!empty($detalles)) {

                $inv = $this->obtenerInventarioDisponible();

                $mapa = [];

                foreach ($inv as $i) {

                    if (!empty($i['producto_id'])) {

                        $mapa[
                            $i['producto_id']
                        ] = $i['nombre_producto'];
                    }
                }

                foreach ($detalles as &$det) {

                    if (
                        isset(
                            $mapa[
                                $det['producto_id']
                            ]
                        )
                    ) {

                        $det['nombre_producto'] =
                            $mapa[
                                $det['producto_id']
                            ];
                    }
                }

                unset($det);
            }

            return $detalles;

        } catch (PDOException $e) {

            error_log(
                "Error al obtener detalles de la solicitud: "
                . $e->getMessage()
            );

            return [];
        }
    }


    // ============================================================
    // INVENTARIO
    // ============================================================

    public function obtenerInventarioDisponible()
    {
        require_once __DIR__ .
            '/../inventario/inventarioModelo.php';

        $invMod = new inventarioModelo(
            $this->conn
        );

        return $invMod->obtenerInventarioDisponible();
    }


    // ============================================================
    // ÁREAS
    // ============================================================

    public function obtenerAreas()
    {
        try {

            $sql = "SELECT
                        id_area,
                        nombre_area
                    FROM areas
                    ORDER BY nombre_area ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(
                PDO::FETCH_ASSOC
            );

        } catch (PDOException $e) {

            error_log(
                "Error al obtener las áreas: "
                . $e->getMessage()
            );

            return [];
        }
    }
}