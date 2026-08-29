<?php
// app/controllers/login/registroControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';

class registroControlador
{
    private $db;

    public function __construct()
    {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && in_array($_POST['accion'], ['registrar_cliente', 'registrar_tecnico'])) {
            $this->procesarRegistro();
        }
    }

    public function index()
    {
        $this->cargarVista();
    }

    public function cargarVista()
    {
        $datos_plantilla = [];
        if (defined('BASE_URL')) {
            $datos_plantilla['baseURL'] = BASE_URL;
        }

        require_once __DIR__ . '/../../views/login/registroVista.php';
    }

    public function procesarRegistro()
    {
        ob_clean();
        header('Content-Type: application/json');

        $nombre           = trim($_POST['nombre'] ?? '');
        $empresa          = trim($_POST['empresa'] ?? '');
        $cedula           = trim($_POST['cedula'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $celular          = trim($_POST['celular'] ?? '');
        $tipo_solicitud   = trim($_POST['tipo_solicitud'] ?? '');
        $usuario          = trim($_POST['usuario'] ?? '');
        $password         = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // 1. Validaciones de campos requeridos
        if (empty($nombre) || empty($empresa) || empty($usuario) || empty($email) || empty($password) || empty($tipo_solicitud)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Por favor completa todos los campos obligatorios (*), incluyendo tu empresa y el tipo de solicitud.'
            ]);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'El correo electrónico ingresado no tiene un formato válido.'
            ]);
            exit;
        }

        if ($password !== $password_confirm) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Las contraseñas ingresadas no coinciden.'
            ]);
            exit;
        }

        if (strlen($password) < 6) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'La contraseña debe tener al menos 6 caracteres.'
            ]);
            exit;
        }

        try {
            // 2. Verificar que el usuario no exista previamente
            $stmtUser = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :u");
            $stmtUser->execute([':u' => $usuario]);
            if ($stmtUser->fetchColumn() > 0) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'El nombre de usuario ya se encuentra registrado. Elige otro.'
                ]);
                exit;
            }

            // 3. Verificar que el email no exista previamente
            $stmtEmail = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :e");
            $stmtEmail->execute([':e' => $email]);
            if ($stmtEmail->fetchColumn() > 0) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'El correo electrónico ya se encuentra registrado.'
                ]);
                exit;
            }

            // 4. Encriptar contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // 5. Insertar usuario de Rol Cliente (idTipoUsuario = 3, nivel_acceso = 3, estado = 'pendiente')
            $sqlInsert = "INSERT INTO usuarios 
                            (usuario, nombre, cedula, email, celular, empresa, tipo_solicitud, password_hash, nivel_acceso, idTipoUsuario, cargo, estado, forzar_cambio_pwd) 
                          VALUES 
                            (:usuario, :nombre, :cedula, :email, :celular, :empresa, :tipo_solicitud, :hash, 3, 3, 'Cliente', 'pendiente', 0)";

            $stmt = $this->db->prepare($sqlInsert);
            $exito = $stmt->execute([
                ':usuario'        => $usuario,
                ':nombre'         => $nombre,
                ':cedula'         => $cedula ?: null,
                ':email'          => $email,
                ':celular'        => $celular ?: null,
                ':empresa'        => $empresa,
                ':tipo_solicitud' => $tipo_solicitud,
                ':hash'           => $password_hash
            ]);

            if ($exito) {
                echo json_encode([
                    'status'   => 'success',
                    'message'  => '¡Solicitud de registro enviada con éxito! Tu solicitud está en proceso. El Super Administrador deberá aprobar tu cuenta antes de que puedas iniciar sesión.',
                    'redirect' => BASE_URL . 'login'
                ]);
                exit;
            } else {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'No se pudo completar el registro en la base de datos.'
                ]);
                exit;
            }

        } catch (PDOException $e) {
            error_log("Error en registro de usuario cliente: " . $e->getMessage());
            echo json_encode([
                'status'  => 'error',
                'message' => 'Error interno en el servidor al procesar el registro.'
            ]);
            exit;
        }
    }
}