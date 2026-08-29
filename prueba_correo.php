<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

echo "<h1>📧 Prueba de envío de correo</h1>";

try {
    $smtpHost = $_ENV['SMTP_HOST'] ?? '';
    $smtpUser = $_ENV['SMTP_USER'] ?? '';
    $smtpPass = $_ENV['SMTP_PASS'] ?? '';
    $smtpPort = (int)($_ENV['SMTP_PORT'] ?? 587);
    $smtpName = $_ENV['SMTP_NAME'] ?? 'Sistema Solicitudes';
    $mailTo   = $_ENV['MAIL_TO'] ?? '';

    if ($smtpHost === '' || $smtpUser === '' || $smtpPass === '' || $mailTo === '') {
        throw new Exception(
            'Falta alguna variable SMTP en el archivo .env. ' .
            'Verifica SMTP_HOST, SMTP_USER, SMTP_PASS y MAIL_TO.'
        );
    }

    echo "<p style='color:green;'>✅ Configuración SMTP encontrada.</p>";

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $smtpPort;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($smtpUser, $smtpName);
    $mail->addAddress($mailTo);

    $mail->isHTML(true);
    $mail->Subject = 'Prueba de correo - Sistema de Solicitudes';
    $mail->Body = '
        <div style="font-family:Arial,sans-serif;max-width:600px;">
            <h2>📧 Prueba de envío correcta</h2>
            <p>Este correo fue enviado desde el <strong>Sistema de Solicitudes</strong>.</p>
            <p>La conexión SMTP con Gmail está funcionando correctamente.</p>
            <hr>
            <p style="color:#666;">Fecha de prueba: ' . date('Y-m-d H:i:s') . '</p>
        </div>
    ';
    $mail->AltBody =
        "Prueba de correo del Sistema de Solicitudes.\n\n" .
        "La conexión SMTP con Gmail está funcionando correctamente.\n\n" .
        "Fecha de prueba: " . date('Y-m-d H:i:s');

    $mail->send();

    echo "<p style='color:green;font-size:18px;'>✅ CORREO ENVIADO CORRECTAMENTE</p>";
    echo "<p>Revisa la bandeja de entrada configurada en <strong>MAIL_TO</strong>.</p>";

} catch (Throwable $e) {
    echo "<h2 style='color:red;'>❌ ERROR</h2>";
    echo "<pre style='background:#f5f5f5;padding:15px;color:red;white-space:pre-wrap;'>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";
}
?>