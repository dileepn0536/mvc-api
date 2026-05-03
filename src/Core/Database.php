<?php
namespace Dileep\Mvc\Core;

use PDO;
use PDOException;
use Exception;

class Database {

    private ?PDO $conn = null;
    private static ?Database $instance = null;

    private function __construct()
    {
        try {
            $url = env('MYSQL_URL');
            
            if ($url) {
                $parts = parse_url($url);

                $host = $parts['host'] ?? env('MYSQLHOST');
                $port = $parts['port'] ?? env('MYSQLPORT', 3306);
                $dbname = $parts['path'] ? ltrim($parts['path'], '/') : env('MYSQLDATABASE');
                $user = $parts['user'] ?? env('MYSQLUSER');
                $password = $parts['pass'] ?? env('MYSQLPASSWORD');
            } else {
                $host = env('DB_HOST', 'localhost');
                $port = env('DB_PORT', 3306);
                $dbname = env('DB_NAME', 'mvc_app');
                $user = env('DB_USER', 'root');
                $password = env('DB_PASS', '');
            }

            if (!$host || !$dbname || !$user) {
                throw new Exception("Database configuration is incomplete");
            }

            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                $host,
                $port,
                $dbname
            );

            $this->conn = new PDO(
                $dsn,
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

        } catch (PDOException $e) {
            throw new Exception("Database connection failed");
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }

    private function __clone() {}
    public function __wakeup() {}
}