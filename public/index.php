<?php
session_start();

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

function routeUrl($path = '') {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $path = '/' . ltrim($path, '/');
    return $scriptName . $path;
}

function assetUrl($path = '') {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $base = dirname($scriptName);
    $base = str_replace('\\', '/', $base);
    if ($base === '/') {
        $base = '';
    }
    return $base . '/' . ltrim($path, '/');
}

use App\Core\Router;

$router = new Router();

// Auth routes
$router->add('GET', '/', 'Auth\Controllers\AuthController@index');
$router->add('GET', '/login', 'Auth\Controllers\AuthController@loginForm');
$router->add('POST', '/login', 'Auth\Controllers\AuthController@login');
$router->add('GET', '/signup', 'Auth\Controllers\AuthController@signupForm');
$router->add('POST', '/signup', 'Auth\Controllers\AuthController@signup');
$router->add('GET', '/logout', 'Auth\Controllers\AuthController@logout');
$router->add('GET', '/zone', 'Auth\Controllers\AuthController@zone');

// User routes
$router->add('GET', '/profile', 'User\Controllers\UserController@profile');
$router->add('POST', '/profile', 'User\Controllers\UserController@profileUpdate');
$router->add('GET', '/search', 'User\Controllers\UserController@search');
$router->add('POST', '/search', 'User\Controllers\UserController@searchSubmit');
$router->add('GET', '/feedbacks', 'User\Controllers\UserController@feedbacks');

// Appointment routes
$router->add('GET', '/received', 'Appointment\Controllers\AppointmentController@panel');
$router->add('GET', '/sent', 'Appointment\Controllers\AppointmentController@sent');
$router->add('GET', '/appointment/book', 'Appointment\Controllers\AppointmentController@bookForm');
$router->add('POST', '/appointment/book', 'Appointment\Controllers\AppointmentController@book');
$router->add('GET', '/appointment/view', 'Appointment\Controllers\AppointmentController@view');
$router->add('GET', '/appointment/edit', 'Appointment\Controllers\AppointmentController@edit');
$router->add('GET', '/appointment/status', 'Appointment\Controllers\AppointmentController@updateStatus');
$router->add('GET', '/appointment/delete', 'Appointment\Controllers\AppointmentController@deleteForm');
$router->add('GET', '/appointment/delete/confirm', 'Appointment\Controllers\AppointmentController@deleteConfirm');
$router->add('GET', '/appointment/delete-sent', 'Appointment\Controllers\AppointmentController@deleteSentForm');
$router->add('GET', '/appointment/delete-sent/confirm', 'Appointment\Controllers\AppointmentController@deleteSentConfirm');

$router->dispatch();
