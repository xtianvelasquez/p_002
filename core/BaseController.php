<?php
namespace App\Core;

class BaseController {
    protected function renderView($viewName, $data = []) {
        extract($data);
        
        $viewFile = dirname(__DIR__) . '/modules/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "View not found: " . $viewName;
        }
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
}
