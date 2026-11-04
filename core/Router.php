<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => '/' . trim($path, '/'),
            'handler' => $handler
        ];
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        $path = '/';
        if (!empty($_SERVER['PATH_INFO'])) {
            $path = $_SERVER['PATH_INFO'];
        } elseif (!empty($_GET['route'])) {
            $path = $_GET['route'];
        } else {
            $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
            $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            
            if ($scriptName !== '' && strpos($requestUri, $scriptName) === 0) {
                $path = substr($requestUri, strlen($scriptName));
            } else {
                $scriptDir = dirname($scriptName);
                $scriptDirNormalized = str_replace('\\', '/', $scriptDir);
                if ($scriptDirNormalized !== '/' && $scriptDirNormalized !== '.' && strpos($requestUri, $scriptDirNormalized) === 0) {
                    $path = substr($requestUri, strlen($scriptDirNormalized));
                } else {
                    $path = $requestUri;
                }
            }
        }
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                foreach ($_GET as $key => $val) {
                    if ($key !== 'route' && !isset($params[$key])) {
                        $params[$key] = $val;
                    }
                }

                list($controllerClass, $methodName) = explode('@', $route['handler']);
                $fullControllerClass = "App\\Modules\\" . $controllerClass;
                if (class_exists($fullControllerClass)) {
                    $controller = new $fullControllerClass();
                    call_user_func_array([$controller, $methodName], [$params]);
                    return;
                }
            }
        }

        // If no route matches, redirect based on auth status
        if (isset($_SESSION['userId'])) {
            $this->redirectRoute('/received');
        } else {
            $this->redirectRoute('/login');
        }
        exit;
    }

    private function redirectRoute($path = '') {
        header("Location: " . routeUrl($path));
        exit;
    }
}
