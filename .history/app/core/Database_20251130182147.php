<?php
// app/core/Database.php
require_once __DIR__ . '/../config/config.php';

class Database {
    private $host;
    private $dbname;
    private $user;
    private $pass;
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $this->host   = defined('DB_HOST') ? DB_HOST : 'localhost';
        $this->dbname = defined('DB_NAME') ? DB_NAME : 'mesa_ayuda';
        $this->user   = defined('DB_USER') ? DB_USER : 'root';
        $this->pass   = defined('DB_PASS') ? DB_PASS : '';

        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            // Control de errores sencillo
            die("Error de conexión a BD: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
