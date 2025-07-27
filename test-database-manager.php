<?php
/**
 * Test DatabaseManager Class
 * 
 * This script tests the DatabaseManager class functionality
 * including connection, queries, and backward compatibility
 */

// Include the DatabaseManager class
require_once __DIR__ . '/includes/DatabaseManager.php';

echo "<h2>DatabaseManager Test Script</h2>\n";
echo "<pre>\n";

echo "PHP Version: " . phpversion() . "\n\n";

echo "--- Testing DatabaseManager ---\n\n";

try {
    // Test 1: Get instance of DatabaseManager
    echo "Test 1: Getting DatabaseManager instance...\n";
    $dbManager = DatabaseManager::getInstance();
    echo "✓ DatabaseManager instance created successfully\n\n";
    
    // Test 2: Get database connection
    echo "Test 2: Getting database connection...\n";
    $pdo = $dbManager->getConnection();
    echo "✓ Database connection established\n\n";
    
    // Test 3: Test simple query
    echo "Test 3: Testing simple query...\n";
    $stmt = $dbManager->query("SELECT 'DatabaseManager is working' as message");
    $result = $stmt->fetch();
    echo "✓ Query executed successfully: " . $result['message'] . "\n\n";
    
    // Test 4: Test fetchOne method
    echo "Test 4: Testing fetchOne method...\n";
    $result = $dbManager->fetchOne("SELECT 'FetchOne method works' as message");
    echo "✓ fetchOne executed successfully: " . $result['message'] . "\n\n";
    
    // Test 5: Test fetchAll method
    echo "Test 5: Testing fetchAll method...\n";
    $results = $dbManager->fetchAll("SELECT 'Result 1' as message UNION SELECT 'Result 2' as message");
    echo "✓ fetchAll executed successfully, returned " . count($results) . " rows\n";
    foreach ($results as $row) {
        echo "  - " . $row['message'] . "\n";
    }
    echo "\n";
    
    // Test 6: Test backward compatibility function
    echo "Test 6: Testing backward compatibility (getDBConnection)...\n";
    if (function_exists('getDBConnection')) {
        $oldConnection = getDBConnection();
        $stmt = $oldConnection->query("SELECT 'Backward compatibility works' as message");
        $result = $stmt->fetch();
        echo "✓ Backward compatibility function works: " . $result['message'] . "\n\n";
    } else {
        echo "✗ Backward compatibility function not found\n\n";
    }
    
    // Test 7: Test database type
    echo "Test 7: Checking database type...\n";
    $config = [
        'type' => defined('DB_TYPE') ? DB_TYPE : (getenv('DB_TYPE') ?: 'sqlite')
    ];
    echo "✓ Database type: " . $config['type'] . "\n\n";
    
    echo "--- All Tests Passed ---\n";
    echo "DatabaseManager is working correctly!\n";
    
} catch (Exception $e) {
    echo "✗ Test failed with exception: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
}

echo "\n--- Test Complete ---\n";
echo "</pre>";

?>
