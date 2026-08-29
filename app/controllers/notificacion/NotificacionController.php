<?php

require_once __DIR__ . '/../../models/notificacion/notificacionInventarioModelo.php';

class NotificacionController
{
    private $db;
    private $dbInventario;
    private $modelo;

    public function __construct()
    {
        $conexionObj = new Conexion();
    
        // Base principal: solicitudprosegur
        $this->db = $conexionObj->getConexion();
    
        // Base de inventario: inventario-almacen
        $this->dbInventario = $conexionObj->getConexionRemota();
    
        // Modelo con las dos conexiones
        $this->modelo = new NotificacionInventarioModelo(
            $this->db,
            $this->dbInventario
        );
    }


    /**
     * MARCAR UNA NOTIFICACIÓN COMO LEÍDA
     *
     * Se utiliza mediante AJAX desde la campanita.
     */
    public function marcarLeida()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json; charset=UTF-8');

        // ---------------------------------------------------------
        // 1. VERIFICAR SESIÓN
        // ---------------------------------------------------------

        $idUsuario = $_SESSION['usuario_id']
            ?? $_SESSION['id_usuario']
            ?? null;

        if (!$idUsuario) {

            http_response_code(401);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'Sesión no válida.'
            ]);

            exit();
        }


        // ---------------------------------------------------------
        // 2. OBTENER ID DE LA NOTIFICACIÓN
        // ---------------------------------------------------------
        //
        // Aceptamos varios nombres para evitar problemas con
        // el Javascript que ya tengas funcionando.
        //
        // Preferido:
        // id_notificacion
        //
        // También acepta:
        // notificacion_id
        // id
        // ---------------------------------------------------------

        $idNotificacion = null;


        // POST normal
        if (isset($_POST['id_notificacion'])) {
            $idNotificacion = $_POST['id_notificacion'];
        }

        // Compatibilidad con notificacion_id
        elseif (isset($_POST['notificacion_id'])) {
            $idNotificacion = $_POST['notificacion_id'];
        }

        // Compatibilidad con id
        elseif (isset($_POST['id'])) {
            $idNotificacion = $_POST['id'];
        }


        // GET como respaldo
        elseif (isset($_GET['id_notificacion'])) {
            $idNotificacion = $_GET['id_notificacion'];
        }

        elseif (isset($_GET['notificacion_id'])) {
            $idNotificacion = $_GET['notificacion_id'];
        }

        elseif (isset($_GET['id'])) {
            $idNotificacion = $_GET['id'];
        }


        // ---------------------------------------------------------
        // 3. SOPORTE PARA FETCH CON JSON
        // ---------------------------------------------------------

        if (!$idNotificacion) {

            $contenido = file_get_contents('php://input');

            if (!empty($contenido)) {

                $datosJson = json_decode($contenido, true);

                if (is_array($datosJson)) {

                    if (isset($datosJson['id_notificacion'])) {
                        $idNotificacion = $datosJson['id_notificacion'];
                    }

                    elseif (isset($datosJson['notificacion_id'])) {
                        $idNotificacion = $datosJson['notificacion_id'];
                    }

                    elseif (isset($datosJson['id'])) {
                        $idNotificacion = $datosJson['id'];
                    }
                }
            }
        }


        // ---------------------------------------------------------
        // 4. VALIDAR ID
        // ---------------------------------------------------------

        $idNotificacion = filter_var(
            $idNotificacion,
            FILTER_VALIDATE_INT
        );

        if (!$idNotificacion) {

            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'Notificación no válida.'
            ]);

            exit();
        }


        // ---------------------------------------------------------
        // 5. VERIFICAR MODELO
        // ---------------------------------------------------------

        if (!$this->modelo) {

            http_response_code(500);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'No se pudo cargar el modelo de notificaciones.'
            ]);

            exit();
        }


        // ---------------------------------------------------------
        // 6. MARCAR COMO LEÍDA
        // ---------------------------------------------------------

        $resultado = $this->modelo->marcarComoLeida(
            $idNotificacion,
            $idUsuario
        );


        // ---------------------------------------------------------
        // 7. RESPUESTA AJAX
        // ---------------------------------------------------------

        echo json_encode([
            'ok' => $resultado,
            'mensaje' => $resultado
                ? 'Notificación marcada como leída.'
                : 'No se pudo marcar la notificación.'
        ]);

        exit();
    }


    /**
     * OBTENER NOTIFICACIONES NO LEÍDAS (AJAX)
     */
    public function obtenerNoLeidas()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json; charset=UTF-8');

        $idUsuario = $_SESSION['usuario_id']
            ?? $_SESSION['id_usuario']
            ?? null;

        if (!$idUsuario || !$this->modelo) {
            echo json_encode(['ok' => false, 'notificaciones' => [], 'total' => 0]);
            exit();
        }

        $notificaciones = $this->modelo->obtenerNotificacionesNoLeidas($idUsuario);

        echo json_encode([
            'ok' => true,
            'notificaciones' => $notificaciones,
            'total' => count($notificaciones)
        ]);
        exit();
    }


    /**
     * PRUEBA DE CAMPANITA + CORREO
     *
     * Esta parte se conserva.
     */
    public function probar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_usuario = $_SESSION['usuario_id']
            ?? $_SESSION['id_usuario']
            ?? 1;

        $resultado_campanita = false;
        $resultado_correo = false;

        echo "<h2>🧪 Prueba de Notificación Simultánea (Campanita + Correo)</h2>";


        // ---------------------------------------------------------
        // 1. GUARDAR EN BASE DE DATOS
        // ---------------------------------------------------------

        try {

            if (class_exists('Conexion')) {

                $conexionObj = new Conexion();

                $conexion = $conexionObj->getConexion();

            } else {

                $conexion = $GLOBALS['db'] ?? null;
            }


            if ($conexion) {

                $stmt = $conexion->prepare(
                    "INSERT INTO notificaciones
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
                    )"
                );


                $resultado_campanita = $stmt->execute([

                    ':usuario_id' => $id_usuario,

                    ':tipo' => 'prueba',

                    ':titulo' => 'Prueba de Alerta',

                    ':mensaje' =>
                        'Esta es una prueba de la campanita y el correo al tiempo.'
                ]);
            }


            if ($resultado_campanita) {

                echo "
                    <p style='color: green;'>
                        ✅ <b>Campanita:</b>
                        Registro insertado en la BD para el usuario ID:
                        <b>{$id_usuario}</b>.
                    </p>
                ";

            } else {

                echo "
                    <p style='color: red;'>
                        ❌ <b>Campanita:</b>
                        No se pudo insertar en la BD.
                    </p>
                ";
            }

        } catch (Exception $e) {

            echo "
                <p style='color: red;'>
                    ❌ <b>Error Campanita:</b>
                    " . htmlspecialchars($e->getMessage()) . "
                </p>
            ";
        }


        // ---------------------------------------------------------
        // 2. ENVIAR CORREO
        // ---------------------------------------------------------

        try {

            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                $mail->isSMTP();

                $mail->Host =
                    $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';

                $mail->SMTPAuth = true;

                $mail->Username =
                    trim($_ENV['SMTP_USER'] ?? '');

                $mail->Password =
                    trim($_ENV['SMTP_PASS'] ?? '');

                $mail->SMTPSecure =
                    PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

                $mail->Port =
                    $_ENV['SMTP_PORT'] ?? 587;

                $mail->CharSet = 'UTF-8';


                $remitente = $mail->Username;

                $destino =
                    !empty($_ENV['MAIL_TO'])
                    ? trim($_ENV['MAIL_TO'])
                    : $remitente;


                $mail->setFrom(
                    $remitente,
                    $_ENV['SMTP_NAME'] ?? 'Sistema Solicitudes'
                );

                $mail->addAddress($destino);

                $mail->isHTML(true);

                $mail->Subject =
                    'Prueba de Notificacion y Correo';

                $mail->Body =
                    '<h3>¡Hola!</h3>
                    <p>
                        Esta es una prueba para verificar que
                        la campanita y el correo se envían simultáneamente.
                    </p>';


                $mail->send();

                $resultado_correo = true;


                echo "
                    <p style='color: green;'>
                        ✅ <b>Correo:</b>
                        Mensaje enviado exitosamente a
                        <b>{$destino}</b>.
                    </p>
                ";

            } else {

                echo "
                    <p style='color: orange;'>
                        ⚠️ <b>Correo:</b>
                        No se encontró la clase PHPMailer cargada.
                    </p>
                ";
            }

        } catch (Exception $e) {

            echo "
                <p style='color: red;'>
                    ❌ <b>Error Correo:</b>
                    " . htmlspecialchars($e->getMessage()) . "
                </p>
            ";
        }


        // ---------------------------------------------------------
        // RESUMEN
        // ---------------------------------------------------------

        echo "<hr>";


        if ($resultado_campanita && $resultado_correo) {

            echo "
                <h3 style='color: green;'>
                    🎉 ¡ÉXITO TOTAL!
                    Ambas acciones se ejecutaron correctamente.
                </h3>
            ";

        } else {

            echo "
                <h3 style='color: orange;'>
                    ⚠️ La prueba finalizó con observaciones
                    arriba detalladas.
                </h3>
            ";
        }
    }
}