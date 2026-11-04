<?php
namespace App\Core;

use PDO;
use Exception;

class Database {
    private static $instance = null;
    private static $cachedPort = null;
    /**
     * @var PDO
     */
    private $connection;

    private function __construct() {
        $configFile = dirname(__DIR__) . '/config/config.php';
        if (!file_exists($configFile)) {
            throw new Exception("Configuration file not found.");
        }
        $config = require $configFile;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $host = ($config['host'] === 'localhost') ? '127.0.0.1' : $config['host'];
        $configuredPort = (int)$config['port'];

        // If we already resolved an active port in this process/session, use it directly
        if (self::$cachedPort !== null) {
            $targetPort = self::$cachedPort;
        } else {
            $targetPort = $configuredPort;

            // Fast TCP probe (15ms timeout) to avoid Windows 4-second TCP timeout on closed ports
            $probe = @fsockopen($host, $targetPort, $errCode, $errMsg, 0.03);
            if ($probe) {
                fclose($probe);
                self::$cachedPort = $targetPort;
            } else {
                // If configured port is closed, probe standard alternate port (5433 or 5432)
                $alternatePort = ($targetPort === 5432) ? 5433 : 5432;
                $altProbe = @fsockopen($host, $alternatePort, $errCode, $errMsg, 0.03);
                if ($altProbe) {
                    fclose($altProbe);
                    $targetPort = $alternatePort;
                    self::$cachedPort = $targetPort;
                }
            }
        }

        $dsn = "pgsql:host={$host};port={$targetPort};dbname={$config['dbname']}";

        try {
            $this->connection = new PDO($dsn, $config['user'], $config['password'], $options);
            self::$cachedPort = $targetPort;
        } catch (Exception $e) {
            // As a final failover if probe was false positive, try alternate
            $fallbackPort = ($targetPort === 5432) ? 5433 : 5432;
            $fallbackDsn = "pgsql:host={$host};port={$fallbackPort};dbname={$config['dbname']}";
            try {
                $this->connection = new PDO($fallbackDsn, $config['user'], $config['password'], $options);
                self::$cachedPort = $fallbackPort;
            } catch (Exception $inner) {
                die("We're currently experiencing some technical difficulties. Please try again later.");
            }
        }
    }

    /**
     * @return PDO
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
}
