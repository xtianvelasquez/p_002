<?php
namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database {
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $config = require dirname(__DIR__) . '/config/config.php';
        $host = (string)$config['host'];
        if (!preg_match('/^[A-Za-z0-9.:-]{1,253}$/', $host)
            || !preg_match('/^[A-Za-z0-9_]{1,63}$/', (string)$config['dbname'])) {
            throw new RuntimeException('DB_HOST or DB_NAME contains unsupported characters.');
        }
        $port = filter_var($config['port'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
        if ($port === false) {
            throw new RuntimeException('DB_PORT must be a valid TCP port.');
        }

        $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $config['dbname']);
        try {
            self::$instance = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
            ]);
        } catch (PDOException $exception) {
            throw new RuntimeException('Unable to connect to the configured database.', 0, $exception);
        }

        return self::$instance;
    }
}
