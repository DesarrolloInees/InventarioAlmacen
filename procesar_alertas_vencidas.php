<?php

// ============================================================
// PROCESADOR DE ALERTAS DE SOLICITUDES VENCIDAS
// ============================================================

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Seguridad
define('ENTRADA_PRINCIPAL', true);

// Conexión
require_once __DIR__ . '/app/config/conexion.php';

// Modelo
require_once __DIR__ . '/app/models/solicitudes/solicitudesModelo.php';

echo "<h1>🔔 Procesador de alertas vencidas</h1>";

try {

    // ========================================================
    // 1. CONEXIÓN
    // ========================================================

    $conexion = new Conexion();
    $db = $conexion->getConexion();

    echo "<p style='color:green;'>✅ Conexión a la base de datos correcta.</p>";

    // ========================================================
    // 2. MODELO
    // ========================================================

    $modelo = new solicitudesModelo($db);

    // ========================================================
    // 3. BUSCAR SOLICITUDES VENCIDAS
    // ========================================================

    $solicitudes = $modelo->obtenerSolicitudesVencidas();

    if (empty($solicitudes)) {

        echo "<p style='color:#555;'>
                ℹ️ No hay solicitudes vencidas pendientes de alerta.
              </p>";

        exit;
    }

    echo "<p style='color:#333;'>
            🔎 Se encontraron "
            . count($solicitudes)
            . " solicitud(es) vencida(s).
          </p>";

    // ========================================================
    // 4. CONFIGURACIÓN SMTP
    // ========================================================

    $smtpHost = $_ENV['SMTP_HOST'] ?? '';
    $smtpUser = $_ENV['SMTP_USER'] ?? '';
    $smtpPass = $_ENV['SMTP_PASS'] ?? '';
    $smtpPort = (int)($_ENV['SMTP_PORT'] ?? 587);
    $smtpName = $_ENV['SMTP_NAME'] ?? 'Sistema Solicitudes';
    $mailTo   = $_ENV['MAIL_TO'] ?? '';

    if (
        $smtpHost === '' ||
        $smtpUser === '' ||
        $smtpPass === '' ||
        $mailTo === ''
    ) {
        throw new Exception(
            'Falta configuración SMTP en el archivo .env.'
        );
    }

    // ========================================================
    // 5. PROCESAR CADA SOLICITUD
    // ========================================================

    foreach ($solicitudes as $solicitud) {

        $solicitudId = (int)$solicitud['solicitud_id'];

        echo "<hr>";
        echo "<h3>Solicitud #{$solicitudId}</h3>";

        // ----------------------------------------------------
        // 5.1 Verificar si ya existe alerta
        // ----------------------------------------------------

        $sqlExiste = "
            SELECT alerta_id, estado
            FROM alertas_solicitudes
            WHERE solicitud_id = :solicitud_id
              AND tipo_alerta = 'vencimiento'
            LIMIT 1
        ";

        $stmtExiste = $db->prepare($sqlExiste);
        $stmtExiste->execute([
            ':solicitud_id' => $solicitudId
        ]);

        $alertaExistente = $stmtExiste->fetch(PDO::FETCH_ASSOC);

        if ($alertaExistente) {

            echo "<p style='color:#777;'>
                    ℹ️ Ya existe una alerta registrada para esta solicitud.
                  </p>";

            continue;
        }

        // ----------------------------------------------------
        // 5.2 Crear PHPMailer
        // ----------------------------------------------------

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtpPort;
            $mail->CharSet    = 'UTF-8';

            // Remitente
            $mail->setFrom(
                $smtpUser,
                $smtpName
            );

            // Destinatario
            $mail->addAddress($mailTo);

            // ------------------------------------------------
            // Datos de la solicitud
            // ------------------------------------------------

            $asunto = htmlspecialchars(
                $solicitud['asunto'] ?? 'Sin asunto'
            );

            $prioridad = htmlspecialchars(
                $solicitud['prioridad'] ?? 'Sin asignar'
            );

            $estado = htmlspecialchars(
                $solicitud['estado'] ?? ''
            );

            $fechaLimite = htmlspecialchars(
                $solicitud['fecha_limite'] ?? ''
            );

            $solicitante = htmlspecialchars(
                $solicitud['nombre_solicitante'] ?? 'N/A'
            );

            $area = htmlspecialchars(
                $solicitud['nombre_area'] ?? 'Sin área'
            );

            // ------------------------------------------------
            // Correo
            // ------------------------------------------------

            $mail->isHTML(true);

            $mail->Subject =
                "⚠️ Solicitud vencida #{$solicitudId}";

            $mail->Body = "
                <div style='
                    font-family:Arial,sans-serif;
                    max-width:700px;
                    margin:auto;
                '>

                    <h2 style='color:#c62828;'>
                        ⚠️ Solicitud vencida
                    </h2>

                    <p>
                        El sistema detectó una solicitud cuyo
                        tiempo límite ya venció.
                    </p>

                    <hr>

                    <p>
                        <strong>Solicitud:</strong>
                        #{$solicitudId}
                    </p>

                    <p>
                        <strong>Asunto:</strong>
                        {$asunto}
                    </p>

                    <p>
                        <strong>Prioridad:</strong>
                        {$prioridad}
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        {$estado}
                    </p>

                    <p>
                        <strong>Solicitante:</strong>
                        {$solicitante}
                    </p>

                    <p>
                        <strong>Área:</strong>
                        {$area}
                    </p>

                    <p>
                        <strong>Fecha límite:</strong>
                        {$fechaLimite}
                    </p>

                    <hr>

                    <p style='color:#666;font-size:13px;'>
                        Este mensaje fue generado automáticamente
                        por el Sistema de Solicitudes.
                    </p>

                </div>
            ";

            $mail->AltBody =
                "SOLICITUD VENCIDA\n\n" .
                "Solicitud: #{$solicitudId}\n" .
                "Asunto: " . ($solicitud['asunto'] ?? '') . "\n" .
                "Prioridad: " . ($solicitud['prioridad'] ?? '') . "\n" .
                "Estado: " . ($solicitud['estado'] ?? '') . "\n" .
                "Solicitante: " . ($solicitud['nombre_solicitante'] ?? '') . "\n" .
                "Área: " . ($solicitud['nombre_area'] ?? '') . "\n" .
                "Fecha límite: " . ($solicitud['fecha_limite'] ?? '');

            // ------------------------------------------------
            // 5.3 ENVIAR
            // ------------------------------------------------

            $mail->send();

            echo "<p style='color:green;'>
                    ✅ Correo enviado correctamente.
                  </p>";

            // ------------------------------------------------
            // 5.4 REGISTRAR ALERTA
            // ------------------------------------------------

            $sqlInsertar = "
                INSERT INTO alertas_solicitudes
                (
                    solicitud_id,
                    tipo_alerta,
                    fecha_envio,
                    estado,
                    mensaje_error
                )
                VALUES
                (
                    :solicitud_id,
                    'vencimiento',
                    NOW(),
                    'enviada',
                    NULL
                )
            ";

            $stmtInsertar = $db->prepare($sqlInsertar);

            $stmtInsertar->execute([
                ':solicitud_id' => $solicitudId
            ]);

            echo "<p style='color:green;'>
                    💾 Alerta registrada correctamente.
                  </p>";

        } catch (Throwable $e) {

            echo "<p style='color:red;'>
                    ❌ Error enviando alerta:
                    "
                    . htmlspecialchars($e->getMessage())
                    . "
                  </p>";

            // ------------------------------------------------
            // Registrar error
            // ------------------------------------------------

            $sqlError = "
                INSERT INTO alertas_solicitudes
                (
                    solicitud_id,
                    tipo_alerta,
                    fecha_envio,
                    estado,
                    mensaje_error
                )
                VALUES
                (
                    :solicitud_id,
                    'vencimiento',
                    NOW(),
                    'error',
                    :mensaje_error
                )
            ";

            try {

                $stmtError = $db->prepare($sqlError);

                $stmtError->execute([
                    ':solicitud_id' => $solicitudId,
                    ':mensaje_error' => $e->getMessage()
                ]);

            } catch (Throwable $errorRegistro) {

                error_log(
                    "No se pudo registrar error de alerta: "
                    . $errorRegistro->getMessage()
                );
            }
        }
    }

    echo "<hr>";

    echo "<h2 style='color:green;'>
            ✅ Proceso terminado
          </h2>";

} catch (Throwable $e) {

    echo "<h2 style='color:red;'>
            ❌ ERROR GENERAL
          </h2>";

    echo "<pre style='
            background:#f5f5f5;
            padding:15px;
            color:red;
            white-space:pre-wrap;
          '>";

    echo htmlspecialchars(
        $e->getMessage()
    );

    echo "</pre>";
}