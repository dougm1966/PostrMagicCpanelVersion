<?php
/**
 * Debug Migration Script
 * This will help us identify what's causing the 500 error
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Migration - Step by Step</h2>\n";

// Step 1: Check if config file exists
echo "<h3>Step 1: Checking config file...</h3>\n";
$configFile = __DIR__ . '/config/config.php';
if (file_exists($configFile)) {
    echo "✅ Config file exists: $configFile<br>\n";
} else {
    echo "❌ Config file missing: $configFile<br>\n";
    exit;
}

// Step 2: Try to include config
echo "<h3>Step 2: Including config...</h3>\n";
try {
    require_once $configFile;
    echo "✅ Config file included successfully<br>\n";
    echo "DB_TYPE: " . (defined('DB_TYPE') ? DB_TYPE : 'NOT DEFINED') . "<br>\n";
} catch (Exception $e) {
    echo "❌ Error including config: " . $e->getMessage() . "<br>\n";
    exit;
}

// Step 3: Check if getDBConnection function exists
echo "<h3>Step 3: Checking database function...</h3>\n";
if (function_exists('getDBConnection')) {
    echo "✅ getDBConnection function exists<br>\n";
} else {
    echo "❌ getDBConnection function not found<br>\n";
    exit;
}

// Step 4: Try database connection
echo "<h3>Step 4: Testing database connection...</h3>\n";
try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful<br>\n";
    echo "Database type: " . DB_TYPE . "<br>\n";
    
    if (DB_TYPE === 'sqlite') {
        echo "SQLite path: " . DB_PATH . "<br>\n";
        echo "SQLite file exists: " . (file_exists(DB_PATH) ? 'Yes' : 'No') . "<br>\n";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>\n";
    exit;
}

// Step 5: Check current users table structure
echo "<h3>Step 5: Checking users table structure...</h3>\n";
try {
    if (DB_TYPE === 'sqlite') {
        $stmt = $pdo->prepare("PRAGMA table_info(users)");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Current users table columns:<br>\n";
        foreach ($columns as $column) {
            echo "- " . $column['name'] . " (" . $column['type'] . ")<br>\n";
        }
    } else {
        $stmt = $pdo->prepare("DESCRIBE users");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Current users table columns:<br>\n";
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")<br>\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking table structure: " . $e->getMessage() . "<br>\n";
    echo "This might mean the users table doesn't exist yet. Let's check...<br>\n";
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        echo "Users table exists but might have permission issues.<br>\n";
    } catch (Exception $e2) {
        echo "❌ Users table doesn't exist: " . $e2->getMessage() . "<br>\n";
        echo "<strong>You need to run setup_database.php first!</strong><br>\n";
        exit;
    }
}

echo "<h3>✅ All checks passed! Ready to add profile fields.</h3>\n";
echo "<a href='add_profile_fields_safe.php'>Proceed with safe migration</a><br>\n";
echo "<a href='setup_database.php'>Or run database setup first</a><br>\n";
?>