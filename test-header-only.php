<?php
declare(strict_types=1);

echo "Testing dashboard header in isolation...\n";
error_log("HEADER TEST: Starting");

try {
    $page_title = "Test Page";
    error_log("HEADER TEST: Before include");
    require_once __DIR__ . '/includes/dashboard-header.php';
    error_log("HEADER TEST: After include");
    
    echo "✓ Dashboard header works\n";
    
} catch (Exception $e) {
    error_log("HEADER TEST: Error - " . $e->getMessage());
    echo "❌ Header error: " . $e->getMessage() . "\n";
}
?>