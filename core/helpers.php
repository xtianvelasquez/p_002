<?php

if (!function_exists('routeUrl')) {
    function routeUrl($path = '') {
        $path = '/' . ltrim($path, '/');
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        
        // If the current request explicitly includes the script name (e.g. /index.php/...)
        if ($scriptName !== '' && strpos($requestUri, $scriptName) === 0) {
            return $scriptName . $path;
        }

        // Subdirectory support (e.g. /p_002/public)
        $scriptDir = dirname($scriptName);
        if ($scriptDir !== '/' && $scriptDir !== '\\' && $scriptDir !== '.') {
            return rtrim($scriptDir, '/') . $path;
        }

        return $path;
    }
}

if (!function_exists('assetUrl')) {
    function assetUrl($path = '') {
        $path = '/' . ltrim($path, '/');
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = dirname($scriptName);
        if ($scriptDir !== '/' && $scriptDir !== '\\' && $scriptDir !== '.') {
            return rtrim($scriptDir, '/') . $path;
        }
        return $path;
    }
}

if (!function_exists('csrfToken')) {
    function csrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }
}

if (!function_exists('csrfField')) {
    function csrfField() {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('setFlash')) {
    function setFlash($type, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('getFlashes')) {
    function getFlashes() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flashes;
    }
}

if (!function_exists('validateUploadedImage')) {
    function validateUploadedImage($tmpPath, $originalName = '') {
        // 1. Check using getimagesize() (validates image magic bytes)
        $imageInfo = @getimagesize($tmpPath);
        if ($imageInfo !== false && !empty($imageInfo['mime'])) {
            $mime = strtolower($imageInfo['mime']);
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/pjpeg' => 'jpg',
                'image/png'  => 'png',
            ];
            if (isset($allowed[$mime])) {
                return $allowed[$mime];
            }
            return false;
        }

        // 2. If finfo class exists, check MIME type
        if (class_exists('finfo')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpPath);
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/pjpeg' => 'jpg',
                'image/png'  => 'png',
            ];
            if (isset($allowed[$mime])) {
                return $allowed[$mime];
            }
            return false;
        }

        // 3. Fallback to extension check
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($ext === 'jpeg' || $ext === 'jpg') {
            return 'jpg';
        }
        if ($ext === 'png') {
            return 'png';
        }

        return false;
    }
}
