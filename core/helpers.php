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

if (!function_exists('scalarString')) {
    function scalarString($value, $default = '') {
        return is_string($value) || is_numeric($value) ? (string)$value : (string)$default;
    }
}

if (!function_exists('assetUrl')) {
    function assetUrl($path = '') {
        if (empty($path)) {
            return '';
        }
        // If already an absolute URL (e.g. Google profile picture: https://lh3.googleusercontent.com/...)
        if (preg_match('#^https://#i', $path)) {
            return $path;
        }
        $path = '/' . ltrim($path, '/');
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = dirname($scriptName);
        if ($scriptDir !== '/' && $scriptDir !== '\\' && $scriptDir !== '.') {
            return rtrim($scriptDir, '/') . $path;
        }
        return $path;
    }
}

if (!function_exists('getUserInitials')) {
    function getUserInitials($name) {
        $clean = trim((string)$name);
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
        if (empty($words)) {
            return 'U';
        }
        $sub = function($str, $start, $len) {
            return function_exists('mb_substr') ? mb_substr($str, $start, $len) : substr($str, $start, $len);
        };
        $upper = function($str) {
            return function_exists('mb_strtoupper') ? mb_strtoupper($str) : strtoupper($str);
        };

        if (count($words) === 1) {
            return $upper($sub($words[0], 0, 2));
        }
        $first = $sub($words[0], 0, 1);
        $last = $sub(end($words), 0, 1);
        return $upper($first . $last);
    }
}

if (!function_exists('getUserAvatarGradient')) {
    function getUserAvatarGradient($name) {
        $palettes = [
            ['#2563eb', '#1d4ed8'], // Royal Blue
            ['#0284c7', '#0369a1'], // Ocean Cyan
            ['#4f46e5', '#3730a3'], // Indigo
            ['#7c3aed', '#5b21b6'], // Deep Violet
            ['#059669', '#047857'], // Emerald
            ['#0d9488', '#0f766e'], // Teal
            ['#d97706', '#b45309'], // Warm Amber
            ['#e11d48', '#be123c'], // Rose
        ];
        $hash = abs(crc32((string)$name));
        return $palettes[$hash % count($palettes)];
    }
}

if (!function_exists('getInitialsAvatarSvg')) {
    function getInitialsAvatarSvg($name, $size = 120) {
        $initials = htmlspecialchars(getUserInitials($name), ENT_QUOTES, 'UTF-8');
        list($c1, $c2) = getUserAvatarGradient($name);
        $fontSize = (int)round($size * 0.40);
        $uniqueId = 'grad_' . substr(md5($name . $size), 0, 8);
        return '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 '.$size.' '.$size.'">'
            . '<defs>'
            . '<linearGradient id="'.$uniqueId.'" x1="0%" y1="0%" x2="100%" y2="100%">'
            . '<stop offset="0%" stop-color="'.$c1.'"/>'
            . '<stop offset="100%" stop-color="'.$c2.'"/>'
            . '</linearGradient>'
            . '</defs>'
            . '<circle cx="'.($size/2).'" cy="'.($size/2).'" r="'.($size/2).'" fill="url(#'.$uniqueId.')"/>'
            . '<text x="50%" y="53%" fill="#ffffff" font-family="-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif" font-size="'.$fontSize.'" font-weight="600" text-anchor="middle" dominant-baseline="middle">'.$initials.'</text>'
            . '</svg>';
    }
}

if (!function_exists('getInitialsAvatarDataUri')) {
    function getInitialsAvatarDataUri($name, $size = 120) {
        $svg = getInitialsAvatarSvg($name, $size);
        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}

if (!function_exists('userAvatarUrl')) {
    function userAvatarUrl($picture, $name = '', $size = 120) {
        $picture = trim((string)$picture);
        // If empty or default placeholder logo, return the initials avatar SVG
        if (empty($picture) || $picture === 'images/logo.png') {
            return getInitialsAvatarDataUri($name, $size);
        }
        // If external URL (e.g. Google avatar)
        if (preg_match('#^https://#i', $picture)) {
            return $picture;
        }
        // If local file, check if it actually exists in public directory
        $publicDir = realpath(__DIR__ . '/../public');
        if ($publicDir) {
            $localFile = $publicDir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($picture, '/\\'));
            $realLocalFile = realpath($localFile);
            if (!$realLocalFile || !str_starts_with($realLocalFile, $publicDir . DIRECTORY_SEPARATOR) || !is_file($realLocalFile)) {
                return getInitialsAvatarDataUri($name, $size);
            }
        } else {
            return getInitialsAvatarDataUri($name, $size);
        }
        return assetUrl($picture);
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
    function validateUploadedImage($tmpPath) {
        // 1. Check using getimagesize() (validates image magic bytes)
        $imageInfo = @getimagesize($tmpPath);
        if ($imageInfo !== false && !empty($imageInfo['mime'])) {
            if (($imageInfo[0] ?? 0) < 1 || ($imageInfo[1] ?? 0) < 1
                || $imageInfo[0] > 8000 || $imageInfo[1] > 8000
                || ($imageInfo[0] * $imageInfo[1]) > 20000000) {
                return false;
            }
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

        return false;
    }
}
