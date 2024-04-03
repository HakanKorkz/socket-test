<?php

namespace Socket\Client\Connect;

use Socket\Client\traits\ENVConfig;
use PDO;
use PDOException;

class DbConnection
{
    use ENVConfig;

    public function connect()
    {
        $env = $this->env();
        $host = $env["DATABASE_HOST"];
        $user = $env["DATABASE_USER"];
        $pass = $env["DATABASE_PASSWORD"];
        $dbname = $env["DATABASE_NAME"];
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $conn = null;
            die("Connection failed: " . $e->getMessage());
        }
        return $conn;
    }

    /**
     * @return string
     */
    protected function baseUrl(): string
    {
        $scheme = $_SERVER['REQUEST_SCHEME'];
        $host = $_SERVER['HTTP_HOST'];
        $host_names_server = $this->env()['HOST_NAMES_SERVER'];
        if ($host === 'localhost') {
            $baseUrl = "{$scheme}://{$host}/{$host_names_server}/";
        } else {
            $baseUrl = "{$scheme}://{$host}/";
        }
        return $baseUrl;
    }
}
