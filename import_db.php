<?php
// PHP Script to initialize PostgreSQL Database for One Calenday
$config = require __DIR__ . '/config/config.php';

$portsToTry = [(string)$config['port']];
if ((string)$config['port'] === '5432') {
    $portsToTry[] = '5433';
} elseif ((string)$config['port'] === '5433') {
    $portsToTry[] = '5432';
}

$activePort = null;
$pdo = null;

foreach ($portsToTry as $port) {
    try {
        $dsn = "pgsql:host={$config['host']};port={$port}";
        $pdo = new PDO($dsn, $config['user'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $activePort = $port;
        break;
    } catch (Exception $e) {
        // Continue to next port
    }
}

if (!$pdo) {
    die("Failed to connect to PostgreSQL server on host '{$config['host']}'. Please verify PostgreSQL is running.\n");
}

try {
    // Check if database exists
    $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = ?");
    $stmt->execute([$config['dbname']]);
    if (!$stmt->fetch()) {
        $pdo->exec("CREATE DATABASE \"" . $config['dbname'] . "\"");
        echo "Database '{$config['dbname']}' created successfully.\n";
    } else {
        echo "Database '{$config['dbname']}' already exists.\n";
    }
} catch (Exception $e) {
    die("Failed to create database '{$config['dbname']}': " . $e->getMessage() . "\n");
}

try {
    // Connect to the specific database
    $dsn = "pgsql:host={$config['host']};port={$activePort};dbname={$config['dbname']}";
    $dbPdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $sqlFile = __DIR__ . '/p_002_postgres.sql';
    if (!file_exists($sqlFile)) {
        die("p_002_postgres.sql file not found.\n");
    }
    
    $sql = file_get_contents($sqlFile);
    $dbPdo->exec($sql);
    echo "Database schema and seed data imported successfully into '{$config['dbname']}'!\n";
} catch (Exception $e) {
    die("Import failed: " . $e->getMessage() . "\n");
}
