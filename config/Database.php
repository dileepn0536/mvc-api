<?php
class Database {
    private $host = "localhost";
    private $db = "mvc_app";
    private $user = "root";
    private $pass = "";

    private $conn = null;
    private static $instance = null;

    private function __construct()
    {
        try {
            $this->conn = new PDO(
                "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8mb4",
                getenv('DB_USER'),
                getenv('DB_PASS'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch(PDOException $e) {
            throw new Exception("Database connection failed");
        }
    }

    public static function getInstance()
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }

    private function __clone()
    {
        
    }

    public function __wakeup() {}
}
?>