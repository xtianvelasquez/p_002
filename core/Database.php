<?php
namespace App\Core;

use PDO;
use Exception;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $configFile = dirname(__DIR__) . '/config/config.php';
        if (!file_exists($configFile)) {
            throw new Exception("Configuration file not found.");
        }
        $config = require $configFile;
        
        $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        
        try {
            $this->connection = new PDO($dsn, $config['user'], $config['password'], $options);
        } catch (Exception $e) {
            // Log error or die gracefully like the original code did
            die("We're currently experiencing some technical difficulties. Please try again later.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
}
