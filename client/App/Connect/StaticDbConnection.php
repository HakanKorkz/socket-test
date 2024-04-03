<?php

namespace Socket\Client\Connect;

use Socket\Client\traits\ENVConfig;
use PDO;
use PDOException;

class StaticDbConnection
{
    use ENVConfig;

    private static PDO|null $conn = null;
    private static string $host;
    private static string $user;
    private static string $pass;
    private static string $dbname;

    private function __construct()
    {
        // Make constructor private to prevent instantiation of this class.
    }

    public static function connect(): PDO
    {
        if (self::$conn === null) {
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
            @ob_start();
            @session_start();
            date_default_timezone_set('Europe/Istanbul');
            $env = (new StaticDbConnection)->env();
            self::$host = $env["DATABASE_HOST"];
            self::$user = $env["DATABASE_USER"];
            self::$pass = $env["DATABASE_PASSWORD"];
            self::$dbname = $env["DATABASE_NAME"];
            try {
                self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8", self::$user, self::$pass);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }

    public function __wakeup()
    {
        // Make __wakeup() private to prevent deserialization of this class.
    }

    private function __clone()
    {
        // Make __clone() private to prevent cloning of this class.
    }
}
