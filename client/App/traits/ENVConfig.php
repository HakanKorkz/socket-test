<?php

namespace Socket\Client\traits;

use Dotenv\Dotenv;

trait ENVConfig
{
    private string  $localPath;
    private static string $StaticLocalPath;

    public function __construct()
    {
        $this->localPath = dirname(__DIR__, 2);
        self::$StaticLocalPath = $this->localPath;
    }

    public static function staticEnv(): array|string
    {
        $localPath = self::$StaticLocalPath;
        $envPath = "$localPath/";
        $dotenv = Dotenv::createImmutable($envPath);
        $dotenv->load();
        return $_ENV ?: '';
    }

    protected function env(): array|string
    {
        $this->localPath=dirname(__DIR__, 2);
        $envPath =  "$this->localPath/";
        $dotenv = Dotenv::createImmutable($envPath);
        $dotenv->load();
        return $_ENV ?: '';
    }
}

