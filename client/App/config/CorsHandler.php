<?php

namespace Socket\Client\config;

use Exception;

class CorsHandler
{
    private array $allowedOrigins;

    /**
     * @param string $allowedOriginsFile
     * @throws Exception
     */
    public function __construct(string $allowedOriginsFile)
    {
        $allowedOriginsJson = file_get_contents($allowedOriginsFile);
        $allowedOrigins = json_decode($allowedOriginsJson, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($allowedOrigins)) {
            throw new Exception('Allowed origins file does not contain an array | İzin verilen kaynak dosyası bir dizi içermiyor');
        }
        $this->allowedOrigins = $allowedOrigins;
    }

    /**
     * @return void|null
     */
    public function handleCors()
    {
        if (!empty($_SERVER['HTTP_ORIGIN'])) {
            $requestOrigin = filter_var(trim($_SERVER['HTTP_ORIGIN']), FILTER_VALIDATE_URL);
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                header('Access-Control-Allow-Origin: ' . "$requestOrigin");
                header('Access-Control-Allow-Headers: Content-Type');
                header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
                exit;
            }
            if ($this->isOriginAllowed($requestOrigin,)) {
                header('Access-Control-Allow-Origin: ' . $requestOrigin);
                header('Access-Control-Allow-Headers: Content-Type');
                header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            } else {
                http_response_code(403);
                header('Content-Type: text/plain; charset=utf-8');
                echo json_encode(["danger"=>'Yasaklı erişim isteğinde bulundunuz.']);
                exit;
            }
        }
        return null;
    }

    /**
     * @param string $requestOrigin
     * @return bool
     */
    private function isOriginAllowed(string $requestOrigin): bool
    {
        $allowedOrigins = $this->allowedOrigins["allowedOrigins"];
        if (in_array("$requestOrigin", $allowedOrigins)) {
            return true;
        }
        return false;
    }
}
