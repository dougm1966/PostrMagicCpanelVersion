<?php
// Test Database Connection Script

echo "<h2>PHP Database Connection Test</h2>\n";
echo "<pre>\n";

// Check PHP version
echo "PHP Version: " . phpversion() . "\n\n";

// Check if PDO is available
echo "PDO Available: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "\n";

// Check if PDO MySQL driver is available
echo "PDO MySQL Driver: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "\n";

// List all PDO drivers
if (extension_loaded('pdo')) {
    echo "Available PDO Drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";
}

echo "\n--- Testing Database Connections ---\n\n";

// Test connection from includes/config.php
echo "Testing connection with includes/config.php credentials:\n";
$db_host = 'localhost';
$db_name = 'postrmagic_gititon';
$db_user = 'postrmagic_gititon';
$db_pass = 'b6gwej8^X';

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "✓ Connection successful!\n";
    
    // Test query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "MySQL Version: " . $result['version'] . "\n";
    
    // Check if any tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . count($tables) . "\n";
    if (count($tables) > 0) {
        echo "Table list: " . implode(', ', $tables) . "\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
}

echo "\n--- PHP Extension Information ---\n";
// Show all loaded extensions
echo "Loaded extensions:\n";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    if (stripos($ext, 'mysql') !== false || stripos($ext, 'pdo') !== false) {
        echo "  - $ext\n";
    }
}

echo "\n--- PHP INI Settings ---\n";
echo "PHP Configuration File: " . php_ini_loaded_file() . "\n";
echo "Additional INI files: " . php_ini_scanned_files() . "\n";

echo "</pre>";