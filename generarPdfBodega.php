<?php

/**
 * generarPdfBodega.php
 *
 * Entrada directa (archivo real, no pasa por el router) para generar el PDF de
 * "Registro de Entradas / Salidas de Bodega".
 *
 * Uso:
 *   /InventarioAlmacen/generarPdfBodega.php?tipo=&fecha_desde=2026-10-22&fecha_hasta=&id_bodega=&remision=&q=
 *   /InventarioAlmacen/generarPdfBodega.php?...&debug=1   (muestra el diagnóstico si falla)
 *
 * .htaccess no reescribe este archivo porque cumple RewriteCond %{REQUEST_FILENAME} !-f.
 * La lógica del PDF sigue viviendo en registroBodegaVerControlador (no se duplica nada).
 */

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';

    // .env opcional: si no existe, se usan los valores por defecto de la conexión.
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    } catch (Throwable $e) {
        // Se ignora a propósito.
    }
}

define('ENTRADA_PRINCIPAL', true);
date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/app/config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

// --- Mismo portero que index.php: sin sesión -> login ---
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . $base . '/login');
    exit();
}

// --- Mismo RBAC que index.php, aplicado a la ruta registroBodegaVer ---
$pagina = 'registroBodegaVer';

try {
    $conexionObj = new Conexion();
    $db = $conexionObj->getConexion();
    $nivel = (int) ($_SESSION['nivel_acceso'] ?? 0);

    if ($nivel === 1) {
        $sql = "SELECT controlador_ruta FROM rutas WHERE nombre_ruta = :pagina AND estado = 'activo'";
        $stmt = $db->prepare($sql);
        $stmt->execute([':pagina' => $pagina]);
    } else {
        $sql = "SELECT r.controlador_ruta
                FROM rol_permisos rp
                INNER JOIN rutas r ON rp.ruta_id = r.id_ruta
                WHERE rp.rol_id = :rol_id AND r.nombre_ruta = :pagina AND r.estado = 'activo'";
        $stmt = $db->prepare($sql);
        $stmt->execute([':rol_id' => $nivel, ':pagina' => $pagina]);
    }

    $controladorRuta = $stmt->fetchColumn();
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Error crítico en el RBAC: ' . htmlspecialchars($e->getMessage());
    exit();
}

if (!$controladorRuta || !file_exists(__DIR__ . '/' . $controladorRuta)) {
    http_response_code(403);
    echo '<h1>Acceso denegado</h1><p>No tienes permiso para generar este reporte.</p>';
    exit();
}

require_once __DIR__ . '/' . $controladorRuta;

$nombreClase = $pagina . 'Controlador';

if (!class_exists($nombreClase)) {
    http_response_code(500);
    echo "Error: no se encontró la clase '$nombreClase' en " . htmlspecialchars($controladorRuta) . '.';
    exit();
}

$controlador = new $nombreClase();

if (!method_exists($controlador, 'generarPdf')) {
    http_response_code(500);
    echo "Error: la clase '$nombreClase' no tiene el método generarPdf().";
    exit();
}

$controlador->generarPdf();
