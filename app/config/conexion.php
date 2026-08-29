<?php

// Evitar acceso directo
if (!defined('ENTRADA_PRINCIPAL')) {
    die('Acceso denegado');
}

class Conexion {
    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset;
    private $pdo;

    private $pdoRemoto;

    public function __construct() {
        // Cargar variables de entorno ($_ENV) o valores por defecto
        $this->host    = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db      = $_ENV['DB_NAME'] ?? 'solicitudprosegur';
        $this->user    = $_ENV['DB_USER'] ?? 'root';
        $this->pass    = $_ENV['DB_PASS'] ?? '';
        $this->charset = 'utf8mb4';
    }

    public function getConexion() {
        if ($this->pdo === null) {
            try {
                $dns = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
                $opciones = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                $this->pdo = new PDO($dns, $this->user, $this->pass, $opciones);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return $this->pdo;
    }

    /**
     * Establece la conexión con la base de datos remota (inventario_almacen).
     * En caso de fallo por red o timeout, retorna la conexión local como fallback seguro.
     */
    public function getConexionRemota() {
        if ($this->pdoRemoto === null) {
            $remoteHost    = $_ENV['DB_REMOTE_HOST'] ?? '192.168.2.254';
            $remoteDb      = $_ENV['DB_REMOTE_NAME'] ?? 'inventario-almacen';
            $remoteUser    = $_ENV['DB_REMOTE_USER'] ?? 'root';
            $remotePass    = $_ENV['DB_REMOTE_PASS'] ?? '';
            $remotePort    = $_ENV['DB_REMOTE_PORT'] ?? '3306';
            $remoteTimeout = (int)($_ENV['DB_REMOTE_TIMEOUT'] ?? 3);

            $dns = "mysql:host={$remoteHost};port={$remotePort};dbname={$remoteDb};charset={$this->charset}";
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => $remoteTimeout
            ];

            try {
                $this->pdoRemoto = new PDO($dns, $remoteUser, $remotePass, $opciones);
            } catch (PDOException $e) {
                error_log("FALLO_CONEXION_REMOTE_PDO ({$remoteHost}/{$remoteDb}): " . $e->getMessage());
                // Fallback 1: Intentar conectar a la BD remota en localhost (desarrollo local)
                try {
                    $dnsLocal = "mysql:host={$this->host};dbname={$remoteDb};charset={$this->charset}";
                    $this->pdoRemoto = new PDO($dnsLocal, $this->user, $this->pass, $opciones);
                } catch (PDOException $e2) {
                    error_log("FALLO_CONEXION_REMOTE_LOCAL_FALLBACK: " . $e2->getMessage());
                    // Fallback 2: Retornar la conexión local predeterminada
                    return $this->getConexion();
                }
            }
        }

        return $this->pdoRemoto;
    }
}