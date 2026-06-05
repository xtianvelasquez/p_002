<?php
// PHP Script to initialize PostgreSQL Database for One Calenday
$config = require __DIR__ . '/config/config.php';

try {
    // Connect to PostgreSQL (default server to check/create the DB)
    $dsn = "pgsql:host={$config['host']};port={$config['port']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Check if database exists
    $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = ?");
    $stmt->execute([$config['dbname']]);
    if (!$stmt->fetch()) {
        $pdo->exec("CREATE DATABASE " . $config['dbname']);
        echo "Database '{$config['dbname']}' created successfully.\n";
    } else {
        echo "Database '{$config['dbname']}' already exists.\n";
    }
} catch (Exception $e) {
    die("Failed to connect to PostgreSQL or create database: " . $e->getMessage() . "\n");
}

try {
    // Connect to the specific database
    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $sqlFile = __DIR__ . '/p_002_postgres.sql';
    if (!file_exists($sqlFile)) {
        die("p_002_postgres.sql file not found.\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Execute SQL file
    $pdo->exec($sql);
    echo "Database schema and seed data imported successfully!\n";
} catch (Exception $e) {
    die("Import failed: " . $e->getMessage() . "\n");
}
