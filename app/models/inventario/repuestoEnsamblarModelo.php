<?php
if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

class RepuestoEnsamblarModelo {
    private $conn;
    
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    }

    // Obtener todos los repuestos activos para los comboboxes
    public function obtenerRepuestosActivos() {
        try {
            $sql = "SELECT id_repuesto, nombre_repuesto, codigo_referencia FROM repuestos WHERE estado = 1 ORDER BY nombre_repuesto ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // Obtener qué repuestos ya tienen una fórmula/receta configurada
    public function obtenerRepuestosConFormula() {
        try {
            $sql = "SELECT DISTINCT r.id_repuesto, r.nombre_repuesto, r.codigo_referencia 
                    FROM repuesto_ensamblado_detalles red
                    INNER JOIN repuestos r ON red.id_repuesto_padre = r.id_repuesto
                    WHERE r.estado = 1 ORDER BY r.nombre_repuesto ASC";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // Obtener los componentes y el stock actual de una fórmula específica
    public function obtenerComponentesFormula($id_padre) {
        try {
            $sql = "SELECT red.id_repuesto_hijo, red.cantidad_necesaria, r.nombre_repuesto, r.codigo_referencia, COALESCE(s.cantidad_total, 0) AS stock_disponible
                    FROM repuesto_ensamblado_detalles red
                    INNER JOIN repuestos r ON red.id_repuesto_hijo = r.id_repuesto
                    LEFT JOIN inventario_stock s ON r.id_repuesto = s.id_repuesto
                    WHERE red.id_repuesto_padre = :id_padre";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_padre' => $id_padre]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    // PROCESO MAESTRO: Ejecutar el ensamble físico en el almacén
    public function ejecutarEnsamble($id_padre, $cantidad_a_armar, $id_usuario) {
        try {
            $componentes = $this->obtenerComponentesFormula($id_padre);
            if (empty($componentes)) return "El repuesto seleccionado no tiene una fórmula configurada.";

            // 1. VALIDACIÓN PREVIA: Verificar si hay stock de todo
            foreach ($componentes as $comp) {
                $total_requerido = $comp['cantidad_necesaria'] * $cantidad_a_armar;
                if ($comp['stock_disponible'] < $total_requerido) {
                    return "Stock insuficiente para el componente: " . $comp['nombre_repuesto'] . " (Se requieren {$total_requerido}, tienes {$comp['stock_disponible']}).";
                }
            }

            // 2. TRANSACCIÓN: Si todo está OK, procesamos los movimientos de inventario
            $this->conn->beginTransaction();

            // Descontar componentes (SALIDAS)
            foreach ($componentes as $comp) {
                $cantidad_gastar = $comp['cantidad_necesaria'] * $cantidad_a_armar;

                // Registrar el movimiento de salida por ensamble
                $sqlMovSalida = "INSERT INTO movimientos_inventario (id_repuesto, tipo_movimiento, cantidad, id_usuario_registra, observacion) 
                                 VALUES (:id_rep, 'SALIDA', :cant, :id_user, :obs)";
                $stmtSalida = $this->conn->prepare($sqlMovSalida);
                $stmtSalida->execute([
                    ':id_rep' => $comp['id_repuesto_hijo'],
                    ':cant' => $cantidad_gastar,
                    ':id_user' => $id_usuario,
                    ':obs' => "Consumo automático por ensamble de {$cantidad_a_armar} unidades del repuesto final ID #{$id_padre}"
                ]);

                // Actualizar la tabla de stock general
                $sqlStockSub = "UPDATE inventario_stock SET cantidad_total = cantidad_total - :cant WHERE id_repuesto = :id_rep";
                $stmtStockSub = $this->conn->prepare($sqlStockSub);
                $stmtStockSub->execute([':cant' => $cantidad_gastar, ':id_rep' => $comp['id_repuesto_hijo']]);
            }

            // Cargar el producto final armado (ENTRADA)
            $sqlMovEntrada = "INSERT INTO movimientos_inventario (id_repuesto, tipo_movimiento, cantidad, id_usuario_registra, observacion) 
                             VALUES (:id_padre, 'ENTRADA', :cant, :id_user, 'Ingreso por ensamble interno de piezas en el almacén')";
            $stmtEntrada = $this->conn->prepare($sqlMovEntrada);
            $stmtEntrada->execute([
                ':id_padre' => $id_padre,
                ':cant' => $cantidad_a_armar,
                ':id_user' => $id_usuario
            ]);

            // Actualizar o Insertar el stock del repuesto final armado
            $sqlStockAdd = "INSERT INTO inventario_stock (id_repuesto, cantidad_total) VALUES (:id_rep, :cant)
                            ON DUPLICATE KEY UPDATE cantidad_total = cantidad_total + :cant";
            $stmtStockAdd = $this->conn->prepare($sqlStockAdd);
            $stmtStockAdd->execute([':id_rep' => $id_padre, ':cant' => $cantidad_a_armar]);

            $this->conn->commit();
            return true; // Ensamble exitoso

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return "Error crítico de base de datos al ensamblar: " . $e->getMessage();
        }
    }
}