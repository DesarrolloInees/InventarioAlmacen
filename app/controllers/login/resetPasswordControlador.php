<?php
// app/controllers/login/resetPasswordControlador.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

require_once __DIR__ . '/../../config/conexion.php';

class resetPasswordControlador
{
    private $modelo;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $conexionObj = new Conexion();
        $this->db = $conexionObj->getConexion();
        require_once __DIR__ . "/../../models/login/loginModelo.php";
        $this->modelo = new LoginModelo($this->db);
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarResetPassword();
            return;
        }

        $data = [
            'baseURL' => BASE_URL,
            'error'   => $_GET['error'] ?? null,
            'success' => $_GET['success'] ?? null,
            'email'   => $_GET['email'] ?? ''
        ];
        require_once "app/views/login/resetPasswordVista.php";
    }

    public function procesarResetPassword()
    {
        $email  = trim($_POST['email'] ?? '');
        $codigo = trim($_POST['codigo'] ?? '');
        $p1     = $_POST['nueva_password'] ?? '';
        $p2     = $_POST['confirmar_password'] ?? '';

        // 1. Validación de campos requeridos
        if (empty($email) || empty($codigo) || empty($p1) || empty($p2)) {
            $this->redireccionarError("Por favor completa todos los campos requeridos.", $email);
        }

        // 2. Validación de coincidencia
        if ($p1 !== $p2) {
            $this->redireccionarError("no_coinciden", $email);
        }

        // 3. Validación de complejidad (Regex)
        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,}$/";
        if (!preg_match($regex, $p1)) {
            $this->redireccionarError("no_segura", $email);
        }

        // 4. Buscar usuario por email
        $usuario = $this->modelo->obtenerUsuarioPorEmail($email);
        if (!$usuario) {
            $this->redireccionarError("No se encontró ningún usuario con el correo especificado.", $email);
        }

        // 5. Verificar Código en BD
        $idCodigoReset = $this->modelo->verificarCodigoReset($email, $codigo);
        if (!$idCodigoReset) {
            $this->redireccionarError("codigo_invalido", $email);
        }

        // 6. Actualizar Contraseña del Usuario
        $hash = password_hash($p1, PASSWORD_BCRYPT);
        $actualizado = $this->modelo->actualizarPassword($usuario['usuario_id'], $hash);

        if ($actualizado) {
            $this->modelo->marcarCodigoComoUsado($idCodigoReset);
            header("Location: " . BASE_URL . "login?success=" . urlencode("¡Tu contraseña ha sido actualizada exitosamente! Ya puedes iniciar sesión."));
            exit();
        } else {
            $this->redireccionarError("Ocurrió un error interno al actualizar la contraseña.", $email);
        }
    }

    private function redireccionarError($mensaje, $email)
    {
        $url = BASE_URL . "resetPassword?error=" . urlencode($mensaje) . "&email=" . urlencode($email);
        header("Location: " . $url);
        exit();
    }
}