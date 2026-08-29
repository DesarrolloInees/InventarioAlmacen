<?php
define('ENTRADA_PRINCIPAL', true);

// 1. Requerir el modelo y el helper
require_once __DIR__ . '/app/models/inventario/inventarioModelo.php';
require_once __DIR__ . '/app/helpers/correoHelper.php';

try {
    // 2. Conexión directa a la base de datos (Ajusta la contraseña si la tuya no está en blanco)
    $host   = 'localhost';
    $dbname = 'solicitudprosegur';
    $usuario= 'root';
    $pass   = ''; 

    $conexion = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $usuario, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $modelo = new inventarioModelo($conexion);

    echo "<h3>1. Obteniendo Superusuarios...</h3>";
    $superusuarios = $modelo->obtenerSuperusuarios();
    
    echo "<pre>";
    print_r($superusuarios);
    echo "</pre>";

    if (empty($superusuarios)) {
        die("<b style='color:red;'>No se encontraron superusuarios activos con cargo 'Super Usuario'.</b>");
    }

    // Datos simulados de un producto que bajó de 5 en stock
    $nombreProductoTest = "Repuesto de Prueba - Teclado Matricial";
    $stockSimulado      = 3; // Menor a 5 para disparar la regla

    echo "<h3>2. Probando inserción de Notificación en la BD...</h3>";
    foreach ($superusuarios as $admin) {
        $mensaje = "⚠️ Stock crítico: El producto '{$nombreProductoTest}' tiene solo {$stockSimulado} unidades disponibles.";
        $resBD = $modelo->crearNotificacionSistema($admin['usuario_id'], $mensaje, 'alerta');
        
        if ($resBD) {
            echo "✅ Notificación creada en la BD para usuario ID: " . $admin['usuario_id'] . "<br>";
        } else {
            echo "❌ Error al crear notificación en BD para usuario ID: " . $admin['usuario_id'] . "<br>";
        }
    }

    echo "<h3>3. Probando Envío de Correo vía PHPMailer...</h3>";
    $envioExitoso = enviarNotificacionStockBajo($superusuarios, $nombreProductoTest, $stockSimulado);

    if ($envioExitoso) {
        echo "<h2 style='color:green;'>🎉 ¡PRUEBA EXITOSA! Revisa la bandeja de entrada de tu correo.</h2>";
    } else {
        echo "<h2 style='color:red;'>❌ Falló el envío del correo. Revisa las credenciales SMTP en 'correoHelper.php'.</h2>";
    }

} catch (Exception $e) {
    echo "<b style='color:red;'>Error general: " . $e->getMessage() . "</b>";
}