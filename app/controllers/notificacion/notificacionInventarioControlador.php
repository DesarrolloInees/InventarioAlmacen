<?php
// app/controllers/notificacion/notificacionInventarioControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/notificacion/notificacionInventarioModelo.php';
require_once __DIR__ . '/../../../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('America/Bogota');

class NotificacionInventarioControlador
{
    private $modelo, $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        $this->modelo = new NotificacionInventarioModelo($this->db);
    }

    public function index()
    {
        if (isset($_POST['accion_ajax']) && $_POST['accion_ajax'] === 'procesarNotificacionesStock') {
            $this->procesarNotificacionesStock();
            exit;
        }

        $data = [
            'titulo' => 'Gestión de Alertas de Stock Bajo',
            'umbral_actual' => ALERTAS_UMBRAL_STOCK_CRITICO
        ];
        $vistaContenido = "app/views/notificacion/notificacionInventarioVista.php";
        include "app/views/plantillaVista.php";
    }

    private function enviarCorreoAlerta($destinatario, $asunto, $cuerpo)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = ALERTAS_SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = ALERTAS_SMTP_USER;
            $mail->Password   = ALERTAS_SMTP_PASS;
            $mail->SMTPSecure = ALERTAS_SMTP_PORT == 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = ALERTAS_SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(ALERTAS_REMITENTE_EMAIL, ALERTAS_REMITENTE_NOMBRE);

            if (ALERTAS_MODO_PRUEBA) {
                $mail->addAddress(ALERTAS_CORREO_PRUEBAS);
                $asunto = "[MODO PRUEBA] " . $asunto;
            } else {
                $correos = explode(';', $destinatario);
                foreach ($correos as $c) {
                    $mail->addAddress(trim($c));
                }
            }

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpo . "<br><br><hr><small>Reporte automatizado generado el " . date('Y-m-d H:i:s') . "</small>";
            
            return $mail->send();
        } catch (Exception $e) {
            error_log("[Alerta Stock SMTP] Error enviando correo: " . $mail->ErrorInfo);
            return false;
        }
    }

    private function enviarWhatsAppAlerta($mensaje)
    {
        if (defined('ALERTAS_WHATSAPP_APIKEY') && !empty(ALERTAS_WHATSAPP_APIKEY)) {
            $phone = ALERTAS_WHATSAPP_PHONE;
            $apikey = ALERTAS_WHATSAPP_APIKEY;
            
            $url = "https://api.callmebot.com/whatsapp.php?phone=" . urlencode($phone) . "&text=" . urlencode($mensaje) . "&apikey=" . urlencode($apikey);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            
            return $response !== false;
        }
        return false;
    }

    public function procesarNotificacionesStock($esLlamadoInterno = false)
    {
        if (!$esLlamadoInterno) ob_start();
        
        try {
            $itemsCriticos = $this->modelo->obtenerStockCritico(ALERTAS_UMBRAL_STOCK_CRITICO);
            $correoEnviado = false;
            $whatsAppEnviado = false;
            $mensajeHTML = "";

            if (!empty($itemsCriticos)) {
                $mensajeHTML .= "<h2 style='color: #856404; font-family: sans-serif;'>⚠️ ALERTA: Stock Crítico Detectado</h2>";
                $mensajeHTML .= "<p style='font-family: sans-serif; color: #555;'>El siguiente reporte detalla los insumos con <b>" . ALERTAS_UMBRAL_STOCK_CRITICO . " unidades o menos</b>.</p>";
                $mensajeHTML .= "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%; font-family: sans-serif;'>";
                $mensajeHTML .= "<thead style='background-color: #fff3cd; color: #856404;'>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Código/SKU</th>
                                        <th>Artículo</th>
                                        <th>Últ. Proveedor</th>
                                        <th>Condición</th>
                                        <th style='text-align: center;'>Stock Físico</th>
                                    </tr>
                                </thead><tbody>";
                
                $mensajeWA = "⚠️ *ALERTA I-NEXIS ALMACÉN*\n";
                $mensajeWA .= "Se detectaron " . count($itemsCriticos) . " artículos en estado crítico (≤ " . ALERTAS_UMBRAL_STOCK_CRITICO . " unidades):\n\n";

                $contador = 0;
                foreach ($itemsCriticos as $item) {
                    $colorStock = $item['cantidad_total'] <= 2 ? "color: #721c24; font-weight: bold; background-color: #f8d7da;" : "color: #856404;";
                    
                    $mensajeHTML .= "<tr>
                                        <td style='font-size: 11px; font-weight: bold;'>" . $item['tipo_articulo'] . "</td>
                                        <td style='font-family: monospace;'>" . htmlspecialchars($item['codigo_referencia']) . "</td>
                                        <td><b>" . htmlspecialchars($item['nombre_articulo']) . "</b></td>
                                        <td style='color: #1d4ed8; font-weight: bold; font-size: 12px;'>" . htmlspecialchars($item['proveedor']) . "</td>
                                        <td style='text-transform: uppercase; font-size: 11px; text-align: center;'>" . $item['condicion'] . "</td>
                                        <td style='text-align: center; " . $colorStock . "'><b>" . $item['cantidad_total'] . " Und.</b></td>
                                    </tr>";
                    
                    if ($contador < 5) {
                        $mensajeWA .= "📌 *" . $item['nombre_articulo'] . "*\n";
                        $mensajeWA .= "   • SKU: " . $item['codigo_referencia'] . "\n";
                        $mensajeWA .= "   • Prov: " . $item['proveedor'] . "\n";
                        $mensajeWA .= "   • *Stock Actual: " . $item['cantidad_total'] . " Und.*\n\n";
                    }
                    $contador++;
                }
                
                $mensajeHTML .= "</tbody></table>";
                $mensajeHTML .= "<p style='margin-top: 20px; font-family: sans-serif;'><a href='" . BASE_URL . "inventarioVer' style='background-color: #ffc107; color: black; padding: 10px 20px; text-decoration: none; font-weight: bold; border-radius: 5px;'>Ingresar al Almacén</a></p>";

                if (count($itemsCriticos) > 5) {
                    $mensajeWA .= "Y " . (count($itemsCriticos) - 5) . " artículos más bajo el umbral.\n\n";
                }
                $mensajeWA .= "🔗 Revisa el stock unificado aquí: " . BASE_URL . "inventarioVer";

                $asunto = "⚠️ [ALERTA DE INVENTARIO] Stock Crítico Detectado (≤ " . ALERTAS_UMBRAL_STOCK_CRITICO . " Unidades)";
                $correoEnviado = $this->enviarCorreoAlerta(ALERTAS_CORREO_DESTINO, $asunto, $mensajeHTML);
                $whatsAppEnviado = $this->enviarWhatsAppAlerta($mensajeWA);
            }

            $respuestaArray = [
                'exito' => true,
                'items_encontrados' => count($itemsCriticos),
                'correo_enviado' => $correoEnviado,
                'whatsapp_enviado' => $whatsAppEnviado
            ];

        } catch (\Throwable $e) {
            // NUEVO: Forzamos a que XAMPP registre el error exacto en su log antes de fallar
            error_log("[GATILLO CRÍTICO FATAL] Error en procesarNotificacionesStock: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
            $respuestaArray = ['exito' => false, 'error' => $e->getMessage()];
        }

        if ($esLlamadoInterno) {
            return $respuestaArray;
        } else {
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($respuestaArray, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}