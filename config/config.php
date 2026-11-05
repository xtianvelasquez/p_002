<?php
// Load .env if present
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("$key=$val");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }
}

$appEnv = strtolower(trim(getenv('APP_ENV') ?: 'development'));
$dbPassword = getenv('DB_PASSWORD');
if ($appEnv === 'production' && ($dbPassword === false || $dbPassword === '')) {
    throw new RuntimeException('DB_PASSWORD must be configured in production.');
}

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '5432',
    'dbname' => getenv('DB_NAME') ?: 'blue',
    'user' => getenv('DB_USER') ?: 'postgres',
    'password' => $dbPassword === false ? '' : $dbPassword,
    'app_env' => $appEnv,
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Manila',
    'google_client_id' => getenv('GOOGLE_CLIENT_ID') ?: ($_ENV['GOOGLE_CLIENT_ID'] ?? ''),
    'google_client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: ($_ENV['GOOGLE_CLIENT_SECRET'] ?? ''),
    'google_redirect_uri' => getenv('GOOGLE_REDIRECT_URI') ?: ($_ENV['GOOGLE_REDIRECT_URI'] ?? ''),
];
