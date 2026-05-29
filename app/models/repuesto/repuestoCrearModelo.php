<?php
// app/models/repuesto/repuestoCrearModelo.php

if (!defined('ENTRADA_PRINCIPAL'))
    die("Acceso denegado.");

class RepuestoCrearModelo
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function obtenerCategoriasActivas()
    {
        try {
            $sql = "SELECT id_categoria, nombre_categoria FROM categorias WHERE estado = 1 ORDER BY nombre_categoria ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // 🔥 DOBLE GUARDADO TRANSACCIONAL
    public function crearRepuesto($datos)
    {
        $connMotos = null;

        try {
            // 1. Iniciamos la transacción en la BD local
            $this->conn->beginTransaction();

            // 2. Intentamos conectar a la BD Externa de Motorizados
            // ⚠️ IMPORTANTE: Actualiza las credenciales "root", "" cuando subas a producción
            try {
                $connMotos = new PDO("mysql:host=127.0.0.1;dbname=inees_mantenimientos;charset=utf8mb4", "root", "");
                $connMotos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $connMotos->beginTransaction();
            } catch (PDOException $e) {
                // Si la externa falla, abortamos la local también
                throw new Exception("Error al conectar con la BD Externa: " . $e->getMessage());
            }

            // 3. Guardar en BD Local (Inventario Almacén)
            $sqlLocal = "INSERT INTO repuestos (codigo_referencia, condicion, nombre_repuesto, valor_venta, estado, id_categoria) 
                        VALUES (:codigo, :condicion, :nombre, :valor, 1, :id_categoria)";

            $stmtLocal = $this->conn->prepare($sqlLocal);
            $stmtLocal->bindParam(':codigo', $datos['codigo_referencia']);
            $stmtLocal->bindParam(':condicion', $datos['condicion']);
            $stmtLocal->bindParam(':nombre', $datos['nombre_repuesto']);
            $stmtLocal->bindParam(':valor', $datos['valor_venta']);
            $stmtLocal->bindValue(':id_categoria', !empty($datos['id_categoria']) ? $datos['id_categoria'] : null, PDO::PARAM_INT);
            $stmtLocal->execute();

            // 4. Guardar en BD Externa (App Motorizados)
            // Según tu tabla externa `repuesto`, no tienen categoría ni condición, pero sí un flag de devolución.
            // Asumiremos que por defecto no requiere devolución (0)
            $sqlExterna = "INSERT INTO repuesto (nombre_repuesto, codigo_referencia, valor_venta, estado, requiere_devolucion) 
                            VALUES (:nombre, :codigo, :valor, 1, 0)";

            $stmtExterna = $connMotos->prepare($sqlExterna);

            // Aquí le agregamos la condición al nombre en la externa para que el motorizado sepa si es nuevo o recuperado
            $nombreMoto = trim($datos['nombre_repuesto']) . " (" . strtoupper($datos['condicion']) . ")";

            $stmtExterna->bindParam(':nombre', $nombreMoto);
            $stmtExterna->bindParam(':codigo', $datos['codigo_referencia']);
            $stmtExterna->bindParam(':valor', $datos['valor_venta']);
            $stmtExterna->execute();

            // 5. Si ambas inserciones fueron exitosas, hacemos COMMIT en ambas bases de datos
            $this->conn->commit();
            $connMotos->commit();

            return true;

        } catch (Exception $e) {
            // Si algo explota (ej: código duplicado en alguna BD), hacemos ROLLBACK en ambas
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            if ($connMotos !== null && $connMotos->inTransaction()) {
                $connMotos->rollBack();
            }

            error_log("Error crítico al sincronizar creación de repuesto: " . $e->getMessage());
            return false;
        }
    }

    public function existeCodigoReferencia($codigo)
    {
        if (empty($codigo))
            return false;
        try {
            // Validamos solo localmente, si existe aquí, no lo dejamos pasar.
            $sql = "SELECT COUNT(*) FROM repuestos WHERE codigo_referencia = :codigo";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':codigo' => $codigo]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}