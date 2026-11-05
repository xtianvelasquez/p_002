<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $isSecureRequest = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isSecureRequest,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header('Cache-Control: no-store, private');

set_exception_handler(static function (Throwable $exception): void {
    error_log((string)$exception);
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
    }
    echo 'An unexpected error occurred. Please try again later.';
});

require_once dirname(__DIR__) . '/core/helpers.php';

$applicationTimezone = getenv('APP_TIMEZONE') ?: 'Asia/Manila';
if (in_array($applicationTimezone, timezone_identifiers_list(), true)) {
    date_default_timezone_set($applicationTimezone);
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = dirname(__DIR__) . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;

$router = new Router();

// Auth routes
$router->add('GET', '/', 'Auth\Controllers\AuthController@index');
$router->add('GET', '/login', 'Auth\Controllers\AuthController@loginForm');
$router->add('POST', '/login', 'Auth\Controllers\AuthController@login');
$router->add('GET', '/signup', 'Auth\Controllers\AuthController@signupForm');
$router->add('POST', '/signup', 'Auth\Controllers\AuthController@signup');
$router->add('GET', '/logout', 'Auth\Controllers\AuthController@logout');
$router->add('POST', '/logout', 'Auth\Controllers\AuthController@logout');
$router->add('GET', '/auth/google', 'Auth\Controllers\AuthController@googleRedirect');
$router->add('GET', '/auth/google/callback', 'Auth\Controllers\AuthController@googleCallback');

// User routes
$router->add('GET', '/profile', 'User\Controllers\UserController@profile');
$router->add('POST', '/profile', 'User\Controllers\UserController@profileUpdate');
$router->add('GET', '/search', 'User\Controllers\UserController@search');
$router->add('POST', '/search', 'User\Controllers\UserController@searchSubmit');

// Appointment routes
$router->add('GET', '/received', 'Appointment\Controllers\AppointmentController@panel');
$router->add('GET', '/sent', 'Appointment\Controllers\AppointmentController@sent');
$router->add('GET', '/u/{account}', 'Appointment\Controllers\AppointmentController@bookForm');
$router->add('GET', '/user/{account}', 'Appointment\Controllers\AppointmentController@bookForm');
$router->add('GET', '/appointment/book', 'Appointment\Controllers\AppointmentController@bookForm');
$router->add('POST', '/appointment/book', 'Appointment\Controllers\AppointmentController@book');
$router->add('GET', '/api/appointment/slots', 'Appointment\Controllers\AppointmentController@apiSlots');
$router->add('GET', '/appointment/view', 'Appointment\Controllers\AppointmentController@view');
$router->add('GET', '/appointment/edit', 'Appointment\Controllers\AppointmentController@edit');
$router->add('POST', '/appointment/status', 'Appointment\Controllers\AppointmentController@updateStatus');
$router->add('GET', '/appointment/delete', 'Appointment\Controllers\AppointmentController@deleteForm');
$router->add('POST', '/appointment/delete', 'Appointment\Controllers\AppointmentController@deleteConfirm');
$router->add('GET', '/appointment/export-ics', 'Appointment\Controllers\AppointmentController@exportIcs');
$router->add('GET', '/appointment/delete-sent', 'Appointment\Controllers\AppointmentController@deleteSentForm');
$router->add('POST', '/appointment/delete-sent', 'Appointment\Controllers\AppointmentController@deleteSentConfirm');

$router->dispatch();
