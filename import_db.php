<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Run this installer from the command line.\n");
}

// This SQL file drops and recreates all application tables. Existing data is
// preserved unless the operator explicitly passes --reset.
$resetRequested = in_array('--reset', $argv ?? [], true);
$config = require __DIR__ . '/config/config.php';

if (!preg_match('/^[A-Za-z0-9_]+$/', (string)$config['dbname'])) {
    fwrite(STDERR, "DB_NAME may only contain letters, numbers, and underscores.\n");
    exit(1);
}

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
        $quotedDbName = '"' . str_replace('"', '""', $config['dbname']) . '"';
        $pdo->exec("CREATE DATABASE {$quotedDbName}");
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

    $existingTables = $dbPdo->query("SELECT COUNT(*) FROM pg_tables WHERE schemaname = current_schema() AND tablename IN ('user_account', 'user_profile', 'user_availability', 'appointment_details')")->fetchColumn();
    if ((int)$existingTables > 0 && !$resetRequested) {
        fwrite(STDERR, "Application tables already exist. This importer replaces them and deletes their data. Pass --reset only when you intend to rebuild the database.\n");
        exit(1);
    }
    
    $sqlFile = __DIR__ . '/p_002_postgres.sql';
    if (!file_exists($sqlFile)) {
        die("p_002_postgres.sql file not found.\n");
    }
    
    $sql = file_get_contents($sqlFile);
    $dbPdo->beginTransaction();
    $dbPdo->exec($sql);
    $dbPdo->commit();
    echo "Database schema and seed data imported successfully into '{$config['dbname']}'!\n";
} catch (Exception $e) {
    if (isset($dbPdo) && $dbPdo->inTransaction()) $dbPdo->rollBack();
    die("Import failed: " . $e->getMessage() . "\n");
}
