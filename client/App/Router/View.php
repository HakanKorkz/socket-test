<?php

namespace Socket\Client\Router;
use Socket\Client\traits\ENVConfig;

class View
{
    use ENVConfig;
    protected static array $data = [];

    public static function bind($key, $value): void
    {
        static::$data[$key] = $value;
    }

    public static function render(string $view, array $layout = []): void
    {
        $dir=dirname(__DIR__,2);
        $dirPath = "$dir/views/";
        extract(static::$data);
        $path = $dirPath . $view . '.php';
        if (isset($layout) && !empty($layout["header"]) && !empty($layout["footer"])) {
            $header = $dirPath . $layout["header"] . '.php';
            $footer = $dirPath . $layout["footer"] . '.php';
        }
        if (file_exists($path)) {
            if (isset($layout) && !empty($layout["header"]) && !empty($layout["footer"])) {
                require_once $header;
                require_once $path;
                require_once $footer;
            } else {
                require_once $path;
            }

        } else {
            echo 'View not found: ' . $path;
        }
    }
}
