<?php

// ============================================================
// PRUEBA DE NOTIFICACIÓN + CORREO
// ============================================================

// Permitir cargar la configuración protegida
define('ENTRADA_PRINCIPAL', true);

// ------------------------------------------------------------
// Composer / PHPMailer
// ------------------------------------------------------------
require_once __DIR__ . '/vendor/autoload.php';

// ------------------------------------------------------------
// Variables de entorno
// ------------------------------------------------------------
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// ------------------------------------------------------------
// Conexión
// ------------------------------------------------------------
require_once __DIR__ . '/app/config/conexion.php';

// ------------------------------------------------------------
// PHPMailer
// ------------------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// ============================================================
// DATOS DE PRUEBA
// ============================================================

$id_usuario_destino = 1;

$correo_destino = $_ENV['MAIL_TO'] ?? '';

$titulo_alerta = 'Prueba de Notificación Creada';

$mensaje_alerta = 'Esta es una prueba para verificar la campanita y el envío de correo al mismo tiempo.';

$resultado_campanita = false;
$resultado_correo = false;


// ============================================================
// HTML INICIAL
// ============================================================

echo '<h2>🧪 Prueba de Campanita y Correo</h2>';


// ============================================================
// PASO 1 — CONEXIÓN A BASE DE DATOS
// ============================================================

try {

    $conexionObj = new Conexion();

    $db = $conexionObj->getConexion();

    echo '<p style="color:green;">
            ✅ Conexión a solicitudprosegur establecida.
          </p>';

} catch (Exception $e) {

    echo '<p style="color:red;">
            ❌ Error de conexión: '
            . htmlspecialchars($e->getMessage()) .
         '</p>';

    exit;
}


// ============================================================
// PASO 2 — INSERTAR NOTIFICACIÓN
// ============================================================

try {

    $sql = "INSERT INTO notificaciones
            (
                usuario_id,
                tipo,
                titulo,
                mensaje,
                leida,
                fecha_creacion
            )
            VALUES
            (
                :usuario_id,
                :tipo,
                :titulo,
                :mensaje,
                0,
                NOW()
            )";

    $stmt = $db->prepare($sql);

    $resultado_campanita = $stmt->execute([
        ':usuario_id' => $id_usuario_destino,
        ':tipo'       => 'prueba',
        ':titulo'     => $titulo_alerta,
        ':mensaje'    => $mensaje_alerta
    ]);

    if ($resultado_campanita) {

        echo '<p style="color:green;">
                ✅ <b>Campanita:</b>
                Notificación creada correctamente
                para el usuario #'
                . $id_usuario_destino .
                '.
              </p>';

    }

} catch (Exception $e) {

    echo '<p style="color:red;">
            ❌ <b>Error en Campanita:</b> '
            . htmlspecialchars($e->getMessage()) .
          '</p>';
}


// ============================================================
// PASO 3 — ENVIAR CORREO
// ============================================================

try {

    if (empty($correo_destino)) {

        throw new Exception(
            'No existe MAIL_TO en el archivo .env'
        );
    }

    $mail = new PHPMailer(true);

    // SMTP
    $mail->isSMTP();

    $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = $_ENV['SMTP_USER'] ?? '';

    $mail->Password = $_ENV['SMTP_PASS'] ?? '';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = (int)(
        $_ENV['SMTP_PORT'] ?? 587
    );

    $mail->CharSet = 'UTF-8';


    // Remitente
    $mail->setFrom(
        $mail->Username,
        $_ENV['SMTP_NAME'] ?? 'Sistema de Solicitudes'
    );


    // Destinatario
    $mail->addAddress($correo_destino);


    // Contenido
    $mail->isHTML(true);

    $mail->Subject = $titulo_alerta;

    $mail->Body = "
        <div style='
            font-family: Arial, sans-serif;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        '>

            <h2 style='color:#2563eb;'>
                {$titulo_alerta}
            </h2>

            <p>
                {$mensaje_alerta}
            </p>

            <hr>

            <small style='color:#888;'>
                Mensaje enviado automáticamente
                desde el sistema de solicitudes.
            </small>

        </div>
    ";


    // Enviar
    $mail->send();

    $resultado_correo = true;

    echo '<p style="color:green;">
            ✅ <b>Correo:</b>
            enviado exitosamente a
            <b>'
            . htmlspecialchars($correo_destino) .
            '</b>.
          </p>';

} catch (Exception $e) {

    echo '<p style="color:red;">
            ❌ <b>Error en Correo:</b> '
            . htmlspecialchars(
                isset($mail)
                    ? $mail->ErrorInfo
                    : $e->getMessage()
            ) .
          '</p>';
}


// ============================================================
// RESULTADO FINAL
// ============================================================

echo '<hr>';

if ($resultado_campanita && $resultado_correo) {

    echo '
        <h2 style="color:green;">
            🎉 ¡PRUEBA EXITOSA!
        </h2>

        <p>
            La notificación fue guardada en la base de datos
            y el correo fue enviado correctamente.
        </p>
    ';

} elseif ($resultado_campanita) {

    echo '
        <h2 style="color:orange;">
            ⚠️ Campanita OK / Correo pendiente
        </h2>

        <p>
            La notificación se guardó correctamente,
            pero el correo presentó un problema.
        </p>
    ';

} elseif ($resultado_correo) {

    echo '
        <h2 style="color:orange;">
            ⚠️ Correo OK / Campanita pendiente
        </h2>

        <p>
            El correo fue enviado,
            pero la notificación no pudo guardarse.
        </p>
    ';

} else {

    echo '
        <h2 style="color:red;">
            ❌ La prueba falló
        </h2>

        <p>
            Revisa los errores mostrados arriba.
        </p>
    ';
}