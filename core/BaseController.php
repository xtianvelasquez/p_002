<?php
namespace App\Core;

class BaseController {
    protected function renderView($viewName, $data = [], $layout = 'main') {
        extract($data, EXTR_SKIP);
        
        $viewFile = dirname(__DIR__) . '/modules/' . $viewName . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo 'The requested page could not be rendered.';
            return;
        }

        if ($layout) {
            $layoutFile = dirname(__DIR__) . '/modules/Layout/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                ob_start();
                try {
                    include $viewFile;
                    $content = ob_get_clean();
                } catch (\Throwable $exception) {
                    ob_end_clean();
                    throw $exception;
                }
                include $layoutFile;
                return;
            }
        }

        include $viewFile;
    }

    protected function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        http_response_code($statusCode);
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

    protected function redirect($url) {
        if (strpos($url, 'http') !== 0 && strpos($url, '/') === 0) {
            $url = routeUrl($url);
        }
        header("Location: " . $url);
        exit;
    }

    protected function setFlash($type, $message) {
        if (function_exists('setFlash')) {
            setFlash($type, $message);
        }
    }

    protected function validateCsrf() {
        $token = scalarString($_POST['_csrf'] ?? '');
        if (empty($token) || empty($_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], $token)) {
            http_response_code(403);
            die("Invalid or missing CSRF token.");
        }
    }
}
