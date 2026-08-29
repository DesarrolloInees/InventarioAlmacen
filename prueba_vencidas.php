<?php

// ============================================================
// PRUEBA DE SOLICITUDES VENCIDAS
// ============================================================

require_once __DIR__ . '/vendor/autoload.php';

// Cargar .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Seguridad
define('ENTRADA_PRINCIPAL', true);

// Conexión
require_once __DIR__ . '/app/config/conexion.php';

// Modelo
require_once __DIR__ . '/app/models/solicitudes/solicitudesModelo.php';

echo "<h1>🧪 Prueba de solicitudes vencidas</h1>";

try {

    // --------------------------------------------------------
    // Crear conexión
    // --------------------------------------------------------

    $conexion = new Conexion();
    $db = $conexion->getConexion();

    echo "<p style='color:green;'>
            ✅ Conexión a la base de datos correcta.
          </p>";

    // --------------------------------------------------------
    // Crear modelo
    // --------------------------------------------------------

    $modelo = new solicitudesModelo($db);

    // --------------------------------------------------------
    // Buscar solicitudes vencidas
    // --------------------------------------------------------

    $solicitudes = $modelo->obtenerSolicitudesVencidas();

    echo "<hr>";

    echo "<h2>Resultado</h2>";

    if (empty($solicitudes)) {

        echo "<p style='color:red;'>
                ❌ No se encontraron solicitudes vencidas.
              </p>";

    } else {

        echo "<p style='color:green;'>
                ✅ Se encontraron "
                . count($solicitudes)
                . " solicitud(es) vencida(s).
              </p>";

        echo "<hr>";

        foreach ($solicitudes as $solicitud) {

            echo "<div style='
                    border:1px solid #ccc;
                    padding:15px;
                    margin:10px 0;
                    border-radius:8px;
                  '>";

            echo "<h3>
                    Solicitud #"
                    . htmlspecialchars(
                        $solicitud['solicitud_id']
                    )
                    . "
                  </h3>";

            echo "<p>
                    <strong>Asunto:</strong> "
                    . htmlspecialchars(
                        $solicitud['asunto']
                    )
                    . "
                  </p>";

            echo "<p>
                    <strong>Prioridad:</strong> "
                    . htmlspecialchars(
                        $solicitud['prioridad']
                    )
                    . "
                  </p>";

            echo "<p>
                    <strong>Estado:</strong> "
                    . htmlspecialchars(
                        $solicitud['estado']
                    )
                    . "
                  </p>";

            echo "<p>
                    <strong>Fecha solicitud:</strong> "
                    . htmlspecialchars(
                        $solicitud['fecha_solicitud']
                    )
                    . "
                  </p>";

            echo "<p>
                    <strong>Fecha límite:</strong> "
                    . htmlspecialchars(
                        $solicitud['fecha_limite']
                    )
                    . "
                  </p>";

            echo "<p>
                    <strong>Solicitante:</strong> "
                    . htmlspecialchars(
                        $solicitud['nombre_solicitante']
                    )
                    . "
                  </p>";

            echo "</div>";
        }
    }

} catch (Throwable $e) {

    echo "<h2 style='color:red;'>
            ❌ ERROR
          </h2>";

    echo "<pre style='
            background:#f5f5f5;
            padding:15px;
            color:red;
          '>";

    echo htmlspecialchars(
        $e->getMessage()
    );

    echo "</pre>";
}