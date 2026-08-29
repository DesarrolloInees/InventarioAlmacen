<?php
// app/controllers/login/solicitarCodigoControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/config_global.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class solicitarCodigoControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();

        require_once __DIR__ . "/../../models/login/loginModelo.php";
        $this->modelo = new LoginModelo($this->db);
    }

    public function index()
    {
        require_once "app/views/login/solicitarCodigoVista.php";
    }

    public function enviarCodigo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $usuario = $this->modelo->obtenerUsuarioPorEmail($email);

            if ($usuario) {
                $codigo = random_int(100000, 999999);
                $codigo_hash = password_hash((string)$codigo, PASSWORD_BCRYPT);
                $expiracion = date('Y-m-d H:i:s', time() + 900); // 15 minutos

                $this->modelo->guardarCodigoReset($email, $codigo_hash, $expiracion);
                $this->ejecutarEnvioEmail($email, $codigo, $usuario['nombre'] ?? 'Usuario');
            }

            // Redirigir pasando la acción y el email
            header('Location: ' . BASE_URL . 'solicitarCodigo?accion=mensajeEnviado&email=' . urlencode($email));
            exit();
        }
    }

    public function mensajeEnviado()
    {
        require_once "app/views/login/mensajeEnviadoVista.php";
    }

    private function ejecutarEnvioEmail($email, $codigo, $nombreUsuario = 'Usuario')
    {
        require_once __DIR__ . '/../../../vendor/autoload.php';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = defined('ALERTAS_SMTP_HOST') ? ALERTAS_SMTP_HOST : 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('ALERTAS_SMTP_USER') ? ALERTAS_SMTP_USER : 'ineesmensajesautomaticos@gmail.com';
            $mail->Password   = defined('ALERTAS_SMTP_PASS') ? ALERTAS_SMTP_PASS : 'bhoh svdq qvfl rxwy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = defined('ALERTAS_SMTP_PORT') ? ALERTAS_SMTP_PORT : 465;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(ALERTAS_REMITENTE_EMAIL, 'Sistema I-Nexis Almacén');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = '🔑 Código de Recuperación de Contraseña - I-Nexis Almacén';

            // HTML elegante corporativo
            $body  = '<div style="font-family: Arial, sans-serif; max-width: 550px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">';
            $body .= '<div style="background: linear-gradient(135deg, #0054A6 0%, #0F172A 100%); padding: 24px; text-align: center; color: white;">';
            $body .= '<h2 style="margin: 0; font-size: 20px;">Restablecimiento de Contraseña</h2>';
            $body .= '<p style="margin: 6px 0 0 0; font-size: 13px; color: #93c5fd;">Sistema de Gestión de Almacén e Inventario INEES</p>';
            $body .= '</div>';

            $body .= '<div style="padding: 24px; background-color: #ffffff;">';
            $body .= '<p style="font-size: 14px; color: #334155; line-height: 1.5;">Hola <strong>' . htmlspecialchars($nombreUsuario) . '</strong>,</p>';
            $body .= '<p style="font-size: 14px; color: #334155; line-height: 1.5;">Recibimos una solicitud para restablecer la contraseña de tu cuenta. Tu código de verificación de 6 dígitos es:</p>';

            $body .= '<div style="text-align: center; margin: 25px 0;">';
            $body .= '<span style="display: inline-block; background-color: #f1f5f9; color: #0054A6; border: 2px dashed #0054A6; padding: 14px 32px; font-size: 28px; font-weight: bold; letter-spacing: 6px; border-radius: 12px;">' . $codigo . '</span>';
            $body .= '</div>';

            $body .= '<p style="font-size: 12px; color: #64748b; text-align: center;">⏱️ Este código expira en <strong>15 minutos</strong>.</p>';
            $body .= '<p style="font-size: 12px; color: #94a3b8; line-height: 1.4; margin-top: 20px;">Si no solicitaste este cambio, puedes ignorar este correo de forma segura. Tu contraseña actual no cambiará.</p>';

            $body .= '</div>';
            $body .= '<div style="background-color: #f8fafc; padding: 16px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0;">';
            $body .= 'Mensaje automático enviado el ' . date('d/m/Y H:i:s') . ' | I-Stock INEES';
            $body .= '</div></div>';

            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("[Recuperación Pwd SMTP Error] " . $mail->ErrorInfo);
            return false;
        }
    }
}
