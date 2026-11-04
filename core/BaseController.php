<?php
namespace App\Core;

class BaseController {
    protected function renderView($viewName, $data = [], $layout = 'main') {
        extract($data);
        
        $viewFile = dirname(__DIR__) . '/modules/' . $viewName . '.php';
        if (!file_exists($viewFile)) {
            echo "View not found: " . $viewName;
            return;
        }

        if ($layout) {
            $layoutFile = dirname(__DIR__) . '/modules/Layout/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                ob_start();
                include $viewFile;
                $content = ob_get_clean();
                include $layoutFile;
                return;
            }
        }

        include $viewFile;
    }

    protected function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
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
        $token = $_POST['_csrf'] ?? '';
        if (empty($token) || empty($_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], $token)) {
            http_response_code(403);
            die("Invalid or missing CSRF token.");
        }
    }
}
