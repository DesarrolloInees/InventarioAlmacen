<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class SalidaCrearModelo
{
    private $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Establece conexión con la base de datos remota (inventario_almacen).
     * Si falla por red o credenciales, realiza un fallback a la base de datos local.
     */
    private function obtenerConexionRemota()
    {
        if (class_exists('Conexion')) {
            $conexionObj = new Conexion();
            return $conexionObj->getConexionRemota();
        }
        return $this->db;
    }

    /**
     * Obtiene el listado de productos y repuestos consolidados.
     */
    public function obtenerInventarioDisponible()
    {
        $conexionActiva = $this->obtenerConexionRemota();

        $sql = "SELECT 
                    r.id_repuesto AS inventario_id,
                    r.id_repuesto AS producto_id,
                    COALESCE(i.cantidad_total, 0) AS cantidad_disponible,
                    'Bodega Principal' AS ubicacion,
                    'disponible' AS estado_inventario,
                    NOW() AS fecha_actualizacion,
                    CAST(r.nombre_repuesto AS CHAR CHARACTER SET utf8mb4) AS nombre_producto,
                    'Repuesto' AS categoria,
                    'unidad' AS unidad_medida,
                    5 AS stock_minimo
                FROM repuestos r
                LEFT JOIN inventario_stock i ON r.id_repuesto = i.id_repuesto

                UNION ALL

                SELECT 
                    p.id_producto AS inventario_id,
                    p.id_producto AS producto_id,
                    COALESCE(i.cantidad_total, 0) AS cantidad_disponible,
                    'Bodega Principal' AS ubicacion,
                    'disponible' AS estado_inventario,
                    NOW() AS fecha_actualizacion,
                    CAST(p.nombre_producto AS CHAR CHARACTER SET utf8mb4) AS nombre_producto,
                    'Producto' AS categoria,
                    'unidad' AS unidad_medida,
                    5 AS stock_minimo
                FROM productos p
                LEFT JOIN inventario_stock i ON p.id_producto = i.id_producto

                ORDER BY nombre_producto ASC";

        try {
            $stmt = $conexionActiva->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();

            // Si retorna vacío, intentamos en la base local como respaldo secundario
            if (empty($data) && $conexionActiva !== $this->db) {
                $stmtLocal = $this->db->prepare($sql);
                $stmtLocal->execute();
                return $stmtLocal->fetchAll();
            }

            return $data;
        } catch (PDOException $e) {
            error_log("Error en SQL obtenerInventarioDisponible: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el detalle de un ítem según su ID único.
     */
    public function obtenerInventarioPorId($inventario_id)
    {
        $conexionActiva = $this->obtenerConexionRemota();

        $sql = "SELECT 
                    r.id_repuesto AS inventario_id,
                    r.id_repuesto AS producto_id,
                    COALESCE(i.cantidad_total, 0) AS cantidad_disponible,
                    'Bodega Principal' AS ubicacion,
                    'activo' AS estado,
                    CAST(r.nombre_repuesto AS CHAR CHARACTER SET utf8mb4) AS nombre_producto,
                    'Repuesto' AS categoria,
                    'unidad' AS unidad_medida
                FROM repuestos r
                LEFT JOIN inventario_stock i ON r.id_repuesto = i.id_repuesto
                WHERE r.id_repuesto = :id_r

                UNION ALL

                SELECT 
                    p.id_producto AS inventario_id,
                    p.id_producto AS producto_id,
                    COALESCE(i.cantidad_total, 0) AS cantidad_disponible,
                    'Bodega Principal' AS ubicacion,
                    'activo' AS estado,
                    CAST(p.nombre_producto AS CHAR CHARACTER SET utf8mb4) AS nombre_producto,
                    'Producto' AS categoria,
                    'unidad' AS unidad_medida
                FROM productos p
                LEFT JOIN inventario_stock i ON p.id_producto = i.id_producto
                WHERE p.id_producto = :id_p
                LIMIT 1";

        try {
            $stmt = $conexionActiva->prepare($sql);
            $stmt->bindParam(':id_r', $inventario_id, PDO::PARAM_INT);
            $stmt->bindParam(':id_p', $inventario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en obtenerInventarioPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza el registro de stock.
     */
    public function actualizarInventario($inventario_id, $cantidad, $ubicacion, $estado)
    {
        $conexionActiva = $this->obtenerConexionRemota();

        $sql = "UPDATE inventario_stock 
                SET cantidad_total = :cantidad
                WHERE id_repuesto = :id_r OR id_producto = :id_p";

        try {
            $stmt = $conexionActiva->prepare($sql);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':id_r', $inventario_id, PDO::PARAM_INT);
            $stmt->bindParam(':id_p', $inventario_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en actualizarInventario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Consulta unificada de personal técnico (Local + Remoto).
     */
    public function obtenerTecnicos()
    {
        $listaFinal = [];

        // 1. Técnicos Locales
        try {
            $sql = "SELECT id_tecnico_local, nombre_tecnico FROM tecnicos_locales WHERE estado = 1 ORDER BY nombre_tecnico ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $locales = $stmt->fetchAll();

            foreach ($locales as $loc) {
                $listaFinal[] = [
                    'usuario_id' => 'local_' . $loc['id_tecnico_local'],
                    'nombre'     => $loc['nombre_tecnico'],
                    'cargo'      => 'Taller / Interno'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al cargar técnicos locales: " . $e->getMessage());
        }

        // 2. Técnicos Motorizados (Conexión remota a inventario_almacen)
        try {
            $options = [PDO::ATTR_TIMEOUT => 3, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            $connMotos = new PDO("mysql:host=192.168.2.254;dbname=inventario-almacen;charset=utf8mb4", "root", "", $options);

            $sqlM = "SELECT id_tecnico, nombre_tecnico FROM tecnico WHERE estado = 1 ORDER BY nombre_tecnico ASC";
            $stmtM = $connMotos->prepare($sqlM);
            $stmtM->execute();
            $motos = $stmtM->fetchAll();

            foreach ($motos as $mot) {
                $listaFinal[] = [
                    'usuario_id' => 'moto_' . $mot['id_tecnico'],
                    'nombre'     => $mot['nombre_tecnico'],
                    'cargo'      => 'Técnico Motorizado (Ruta)'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error al cargar técnicos externos: " . $e->getMessage());
        }

        return $listaFinal;
    }

    /**
     * Usuarios Super Usuario (Local)
     */
    public function obtenerSuperusuarios()
    {
        $sql = "SELECT usuario_id, nombre, email 
                FROM usuarios 
                WHERE cargo = 'Super Usuario' AND estado = 'activo'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Registra notificaciones en el sistema local
     */
    public function crearNotificacionSistema($usuarioId, $mensaje, $tipo = 'alerta', $titulo = 'Notificación del sistema') 
    {
        $sql = "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, leida, fecha_creacion) 
                VALUES (:usuario_id, :tipo, :titulo, :mensaje, 0, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':tipo'       => $tipo,
            ':titulo'     => $titulo,
            ':mensaje'    => $mensaje
        ]);
    }

    /**
     * Consulta de notificaciones no leídas
     */
    public function obtenerNotificacionesNoLeidas($usuarioId) 
    {
        $sql = "SELECT * FROM notificaciones 
                WHERE usuario_id = :usuario_id 
                ORDER BY fecha_creacion DESC LIMIT 5";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchAll();
    }
}