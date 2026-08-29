<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carga directa de las clases de PHPMailer sin pasar por el autoload global
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';

if (!function_exists('enviarNotificacionStockBajo')) {

    function enviarNotificacionStockBajo($listaSuperusuarios, $nombreProducto, $stockActual) {
        $mail = new PHPMailer(true);

        try {
            // --- CONFIGURACIÓN SMTP ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ineesmensajesautomaticos@gmail.com';          // Tu correo emisor
            $mail->Password   = 'bhoh svdq qvfl rxwy';                         // Tu contraseña de aplicación Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('ineesmensajesautomaticos@gmail.com', 'Sistema de Almacén Prosegur');

            $correosAgregados = 0;
            foreach ($listaSuperusuarios as $usuario) {
                if (!empty($usuario['email'])) {
                    $mail->addAddress($usuario['email'], $usuario['nombre'] ?? 'Superusuario');
                    $correosAgregados++;
                }
            }

            if ($correosAgregados === 0) return false;

            // --- CONTENIDO DEL CORREO ---
            $mail->isHTML(true);
            $mail->Subject = "⚠️ ALERTA CRÍTICA: Stock Menor a 5 Unidades - {$nombreProducto}";
            
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; background-color: #ffffff;'>
                    <div style='background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;'>
                        <h2 style='color: #991b1b; margin: 0; font-size: 18px;'>⚠️ Alerta de Stock Crítico</h2>
                    </div>
                    
                    <p style='color: #374151; font-size: 15px;'>Estimado Administrador,</p>
                    <p style='color: #374151; font-size: 14px; line-height: 1.5;'>
                        El stock del siguiente repuesto/producto ha descendido por debajo del límite de <strong>5 unidades</strong>:
                    </p>

                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f9fafb; border-radius: 8px; overflow: hidden;'>
                        <tr style='border-bottom: 1px solid #e5e7eb;'>
                            <td style='padding: 12px 16px; font-weight: bold; color: #4b5563;'>Producto:</td>
                            <td style='padding: 12px 16px; color: #111827; font-weight: bold;'>{$nombreProducto}</td>
                        </tr>
                        <tr>
                            <td style='padding: 12px 16px; font-weight: bold; color: #4b5563;'>Stock Disponible:</td>
                            <td style='padding: 12px 16px; color: #dc2626; font-weight: bold; font-size: 18px;'>{$stockActual} unidades</td>
                        </tr>
                    </table>

                    <p style='color: #4b5563; font-size: 14px;'>
                        Por favor, realiza la solicitud de reabastecimiento o compra a la brevedad.
                    </p>

                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center; margin: 0;'>
                        Notificación automática del Sistema de Gestión de Almacén.
                    </p>
                </div>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Error enviando alerta de stock bajo: " . $mail->ErrorInfo);
            return false;
        }
    }
}

if (!function_exists('enviarSolicitudActualizacionAlmacen')) {

    function enviarSolicitudActualizacionAlmacen($asunto, $mensajeDetalle, $usuarioNombre, $usuarioEmail = '', $destino = 'almacen@inees.co') {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ineesmensajesautomaticos@gmail.com';
            $mail->Password   = 'bhoh svdq qvfl rxwy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('ineesmensajesautomaticos@gmail.com', 'Sistema de Almacén Prosegur');
            $mail->addAddress($destino, 'Almacén INEES');

            if (!empty($usuarioEmail)) {
                $mail->addReplyTo($usuarioEmail, $usuarioNombre);
            }

            $mail->isHTML(true);
            $mail->Subject = "📦 SOLICITUD DE ACTUALIZACIÓN DE ALMACÉN: " . $asunto;

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 650px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 12px; padding: 28px; background-color: #ffffff;'>
                    <div style='background-color: #ebf8ff; border-left: 5px solid #3182ce; padding: 16px; margin-bottom: 24px; border-radius: 6px;'>
                        <h2 style='color: #2b6cb0; margin: 0; font-size: 20px;'>📦 Solicitud de Actualización de Almacén</h2>
                        <p style='color: #4a5568; margin: 4px 0 0 0; font-size: 13px;'>Destino predeterminado: <strong>{$destino}</strong></p>
                    </div>

                    <p style='color: #2d3748; font-size: 15px;'>Se ha generado una nueva solicitud de actualización de inventario / almacén:</p>

                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f7fafc; border-radius: 8px; overflow: hidden;'>
                        <tr style='border-bottom: 1px solid #e2e8f0;'>
                            <td style='padding: 12px 16px; font-weight: bold; color: #4a5568; width: 30%;'>Solicitante:</td>
                            <td style='padding: 12px 16px; color: #1a202c; font-weight: bold;'>{$usuarioNombre}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #e2e8f0;'>
                            <td style='padding: 12px 16px; font-weight: bold; color: #4a5568;'>Asunto:</td>
                            <td style='padding: 12px 16px; color: #2b6cb0; font-weight: bold;'>{$asunto}</td>
                        </tr>
                        <tr>
                            <td style='padding: 12px 16px; font-weight: bold; color: #4a5568; vertical-align: top;'>Detalles / Mensaje:</td>
                            <td style='padding: 12px 16px; color: #2d3748; line-height: 1.6; white-space: pre-wrap;'>{$mensajeDetalle}</td>
                        </tr>
                    </table>

                    <p style='color: #718096; font-size: 13px; margin-top: 20px;'>
                        Esta solicitud fue enviada automáticamente desde el Sistema de Almacén INEES al correo predeterminado: <strong>{$destino}</strong>.
                    </p>

                    <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;'>
                    <p style='color: #a0aec0; font-size: 12px; text-align: center; margin: 0;'>
                        Notificación automática del Sistema de Gestión de Almacén - INEES IT Seguridad.
                    </p>
                </div>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error enviando solicitud actualización almacén: " . $mail->ErrorInfo);
            return false;
        }
    }
}