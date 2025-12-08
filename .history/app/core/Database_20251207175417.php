<?php
class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host    = 'sql211.infinityfree.com';          // host MySQL
        $db      = 'if0_40622584_mesa_ayuda';          // ← pon aquí tu DB name completo
        $user    = 'if0_40622584';                     // ← tu MySQL Username
        $pass    = 'tlOvjrdEGpA8J';                    // ← la del hosting / MySQL
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $this->connection = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
