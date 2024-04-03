<?php

namespace Socket\Client\Router;

use Socket\Client\traits\ENVConfig;

class Router
{
    use ENVConfig;

    private array $routes;
    private array $env;
    private string $prefix;
    private string $path;

    public function __construct()
    {
        $this->routes = array();
        $this->prefix = '';
        $this->env = $this->env();
        $this->env["CUSTOM_404"] = function () {
            echo json_encode(["cbr backend api system 404"]);
        };
    }

    /**
     * @param string $method
     * @param array|string $path
     * @param $callback
     * @return void
     */
    public function custom(string $method, array|string $path, $callback): void
    {
        if (is_array($path)) {
            foreach ($path as $p) {
                $this->routes[$method]["$this->prefix$p"] = $callback;
            }
        } else {
            $path = "$this->prefix$path";
            $this->routes[$method][$path] = $callback;
        }
    }

    public function any(array|string $path, $callback): void
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];
        foreach ($methods as $method) {
            $this->$method($path, $callback);
        }
    }


    /**
     * @param array|string $path
     * @param $callback
     * @return void
     */
    public function get(array|string $path, $callback): void
    {
        if (is_array($path)) {
            foreach ($path as $p) {
                $this->routes["GET"]["$this->prefix$p"] = $callback;
            }
        } else {
            $path = "$this->prefix$path";
            $this->routes["GET"][$path] = $callback;
        }
    }

    /**
     * @param array|string $path
     * @param $callback
     * @return void
     */
    public function post(array|string $path, $callback): void
    {
        if (is_array($path)) {
            foreach ($path as $p) {
                $this->routes["POST"]["$this->prefix$p"] = $callback;
            }
        } else {
            $path = "$this->prefix$path";
            $this->routes["POST"][$path] = $callback;
        }
    }

    public function put(array|string $path, $callback): void
    {
        if (is_array($path)) {
            foreach ($path as $p) {
                $this->routes["PUT"]["$this->prefix$p"] = $callback;
            }
        } else {
            $path = "$this->prefix$path";
            $this->routes["PUT"][$path] = $callback;
        }
    }

    public function patch(array|string $path, $callback): void
    {
        if (is_array($path)) {
            foreach ($path as $p) {
                $this->routes["PATCH"]["$this->prefix$p"] = $callback;
            }
        } else {
            $path = "$this->prefix$path";
            $this->routes["PATCH"][$path] = $callback;
        }
    }

    public function options(array|string $path, $callback): void
    {
        if (is_array($path)) {
            foreach ($path as $p) {
                $this->routes["OPTIONS"]["$this->prefix$p"] = $callback;
            }
        } else {
            $path = "$this->prefix$path";
            $this->routes["OPTIONS"][$path] = $callback;
        }
    }

    public function delete(array|string $path, $callback): void
    {
        if (is_array($path)) {
            foreach ($path as $p) {
                $this->routes["DELETE"]["$this->prefix$p"] = $callback;
            }
        } else {
            $path = "$this->prefix$path";
            $this->routes["DELETE"][$path] = $callback;
        }
    }

    /**
     * @param string $prefix
     * @param callable $callback
     * @return void
     */
    public function prefix(string $prefix, callable $callback): void
    {
        $previousPrefix = $this->prefix;
        if (strlen($prefix) === 1 && $prefix[0] === '/') {
            $prefix = str_replace("/", "", $this->prefix);
        }
        $this->prefix .= $prefix;
        $callback($this);
        $this->prefix = $previousPrefix;
    }

    /**
     * @return void
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $hostnames = $this->env["HOST_NAMES_SERVER"];
        $hostPath = $this->env["HOST_NAMES_SERVER_PATH"];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace("/$hostnames", '', $path);
        $path = str_replace("$hostPath", '', $path);

        // Kontrol ekleniyor
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $callback) {
                $pattern = preg_replace('/{([^\/]+)}/', '([^/]+)', $route);
                $pattern = "@^" . $pattern . "$@i";
               if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches);
                    $callback(...$matches);
                    return;
                }
            }
        }
        $this->show404();
    }

    /**
     * @return void
     */
    public function show404(): void
    {
        $custom404 = $this->env["CUSTOM_404"];
        http_response_code(404);
        if ($custom404 !== null && is_callable($custom404)) {
            $custom404();
        } else {
            echo json_encode(["cbr backend api system"]);
            exit();
        }
    }

    /**
     * @return array|mixed
     */
    public function postOrJsonControl(): mixed
    {
        if (!empty($_POST)) {
            return $_POST;
        }
        return json_decode(file_get_contents("php://input"));
    }

}