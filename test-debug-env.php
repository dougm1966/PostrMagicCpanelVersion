<?php
declare(strict_types=1);

echo "=== Environment Debug Test ===\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'UNDEFINED') . "\n";

require_once __DIR__ . '/config/config.php';

echo "DB_TYPE: " . (defined('DB_TYPE') ? DB_TYPE : 'UNDEFINED') . "\n";
echo "DB_PATH: " . (defined('DB_PATH') ? DB_PATH : 'UNDEFINED') . "\n";

// Test basic PDO SQLite
try {
    echo "Testing raw SQLite PDO...\n";
    $pdo = new PDO('sqlite:data/postrmagic.db');
    echo "✓ Raw SQLite PDO works\n";
    
    echo "Testing getDBConnection()...\n";
    $db = getDBConnection();
    echo "✓ getDBConnection() works\n";
    
    echo "Testing user_media query...\n";
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM user_media");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ user_media query works, count: " . $result['count'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
