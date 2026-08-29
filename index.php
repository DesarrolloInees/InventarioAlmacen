<?php

/**
 * index.php (v5.2 - MASTER OPTIMIZADO Y ROBUSTO)
 * Fusión: Seguridad estricta RBAC + Arquitectura MVC Flexible + Manejo de Conexión Seguro.
 */

// --- 0. CARGAR COMPOSER Y ENV ---
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// --- 1. CONFIGURACIÓN INICIAL ---
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Bogota');
define('ENTRADA_PRINCIPAL', true);

// Definir URL Base automáticamente
$es_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
            ($_SERVER['SERVER_PORT'] == 443) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');

$protocol = $es_https ? "https://" : "http://";
$base_path = str_replace('/index.php', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME']));
define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . $base_path . '/');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CONEXIÓN A BASE DE DATOS ROBUSTA ---
$rutaConexion = __DIR__ . '/app/config/conexion.php';

if (file_exists($rutaConexion)) {
    require_once $rutaConexion;
} else {
    die("<b>Error crítico:</b> No se localizó el archivo de conexión en <code>{$rutaConexion}</code>.");
}

// Resolver instanciación evaluando Namespaces o clase global
if (class_exists('Conexion')) {
    $conexionObj = new Conexion();
} elseif (class_exists('App\Config\Conexion')) {
    $conexionObj = new \App\Config\Conexion();
} else {
    die("<b>Error crítico:</b> El archivo <code>conexion.php</code> fue cargado correctamente, pero la clase <code>Conexion</code> no fue encontrada dentro de él. Revisa la declaración del nombre de la clase o sus namespaces.");
}

$db = $conexionObj->getConexion();


// --- 2. LÓGICA DE ENRUTAMIENTO ---
$pagina = $_GET['pagina'] ?? 'inicio';

// Puente para parámetros GET adicionales (ej: ordenDetalle/2025-12-10)
if (isset($_GET['id']) && $pagina === 'ordenDetalle') {
    $_GET['fecha'] = $_GET['id'];
}


// --- 3. GESTIÓN DE SEGURIDAD (EL PORTERO) ---

// Páginas públicas (accesibles sin sesión)
$paginas_publicas = [
    'login',
    'registro',
    'solicitarCodigo',
    'resetPassword',
    'procesarResetPassword',
    'enviarCodigo',
    'cambiarPassword',
    'procesarCambioPassword',
    'mensajeEnviado',
    'error404',
    'notificacionProbar' // Se agrega temporalmente para la prueba de campanita y correo
];

$esta_logueado = isset($_SESSION['usuario_id']);

// LOGOUT
if ($pagina === 'logout') {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . 'login');
    exit();
}

// Control de acceso general
if (!$esta_logueado && !in_array($pagina, $paginas_publicas)) {
    header('Location: ' . BASE_URL . 'login');
    exit();
}

if ($esta_logueado && $pagina === 'login') {
    header('Location: ' . BASE_URL . 'inicio');
    exit();
}


// --- 4. CEREBRO RBAC (BUSCAR RUTA EN BD) ---
$controlador_ruta_archivo = null;

// BYPASS DE PRUEBA: Permite probar NotificacionController sin depender de la tabla rutas en la BD  
if ($pagina === 'notificacionProbar') {
    $controlador_ruta_archivo = 'app/controllers/NotificacionController.php';
} else {
    try {
        if ($esta_logueado) {
            $nivel_usuario = $_SESSION['nivel_acceso'] ?? 0;

            // SuperAdmin (1): Acceso directo si la ruta está activa
            if ($nivel_usuario == 1) {
                $sql = "SELECT controlador_ruta FROM rutas WHERE nombre_ruta = :pagina AND estado = 'activo'";
                $stmt = $db->prepare($sql);
                $stmt->execute([':pagina' => $pagina]);
            } else {
                // Usuarios con Roles: Verificación en rol_permisos
                $sql = "SELECT r.controlador_ruta 
                        FROM rol_permisos rp
                        INNER JOIN rutas r ON rp.ruta_id = r.id_ruta
                        WHERE rp.rol_id = :rol_id AND r.nombre_ruta = :pagina AND r.estado = 'activo'";
                $stmt = $db->prepare($sql);
                $stmt->execute([':rol_id' => $nivel_usuario, ':pagina' => $pagina]);
            }
            $controlador_ruta_archivo = $stmt->fetchColumn();

        } elseif (in_array($pagina, $paginas_publicas)) {
            // Rutas públicas registradas en BD
            $sql = "SELECT controlador_ruta FROM rutas WHERE nombre_ruta = :pagina AND estado = 'activo'";
            $stmt = $db->prepare($sql);
            $stmt->execute([':pagina' => $pagina]);
            $controlador_ruta_archivo = $stmt->fetchColumn();
        }
    } catch (PDOException $e) {
        die("Error crítico en el Router RBAC: " . $e->getMessage());
    }
}


// ============================================================
// RUTA AJAX DE NOTIFICACIONES
// ============================================================
if (
    $pagina === 'notificacion' &&
    isset($_GET['accion']) &&
    in_array($_GET['accion'], ['marcarLeida', 'obtenerNoLeidas'])
) {
    if (!$esta_logueado) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);

        echo json_encode([
            'ok' => false,
            'mensaje' => 'Sesión no válida.'
        ]);

        exit();
    }

    $controlador_ruta_archivo = 'app/controllers/notificacion/NotificacionController.php';
}

// --- 5. CARGA Y EJECUCIÓN (MVC POO) ---
if ($controlador_ruta_archivo && file_exists(__DIR__ . '/' . $controlador_ruta_archivo)) {

    require_once __DIR__ . '/' . $controlador_ruta_archivo;

    // Detectar si la clase usa 'Controlador' o 'Controller' (compatibilidad doble)
    $opcionesClases = [
        ucfirst($pagina) . 'Controlador',
        ucfirst($pagina) . 'Controller',
        'NotificacionController' // Para la prueba específica
    ];

    $nombreClase = null;
    foreach ($opcionesClases as $clase) {
        if (class_exists($clase)) {
            $nombreClase = $clase;
            break;
        }
    }

    if ($nombreClase) {
        $controlador = new $nombreClase();

        // Determinar acción/método a ejecutar
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? ($pagina === 'notificacionProbar' ? 'probar' : 'index');

        if (method_exists($controlador, $accion)) {
            $controlador->{$accion}();
        } elseif ($accion === 'index' && method_exists($controlador, 'cargarVista')) {
            // Compatibilidad hacia atrás
            $controlador->cargarVista();
        } else {
            echo "Error: El método '<b>{$accion}</b>' no fue encontrado en '<b>{$nombreClase}</b>'.";
        }
    } else {
        echo "Error: No se encontró ninguna clase válida para la ruta '<b>{$pagina}</b>'.";
    }

} else {
    // Manejo limpia de Error 404 o Faltantes
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Error 404</h1><p>Página no encontrada o no tienes permisos para acceder.</p>";
}