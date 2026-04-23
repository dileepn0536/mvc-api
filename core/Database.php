<?php
class Database {

    private $conn = null;
    private static $instance = null;

    private function __construct()
    {
        try {
            $url = getenv('MYSQL_URL');
            $parts = parse_url($url);
            $host = $parts['host'] ?? getenv('MYSQLHOST');
            $port = $parts['port'] ?? getenv('MYSQLPORT');
            $dbname = $parts['path'] ? ltrim($parts['path'], '/') : getenv('MYSQLDATABASE');
            $user = $parts['user'] ?? getenv('MYSQLUSER');
            $password = $parts['pass'] ?? getenv('MYSQLPASSWORD');

            if(!$host || !$port || !$dbname || !$user || !$password) {
                throw new Exception("Database configuration is incomplete");
            }
            $this->conn = new PDO(
                "mysql:host=" . $host . ";port=" . $port . ";dbname=" . $dbname . ";charset=utf8mb4",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch(PDOException $e) {
            throw new Exception($e->getMessage());
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