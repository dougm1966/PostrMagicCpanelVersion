<?php
declare(strict_types=1);

echo "Testing auth system in isolation...\n";
error_log("AUTH TEST: Starting");

try {
    require_once __DIR__ . '/includes/auth-helper.php';
    error_log("AUTH TEST: auth-helper included");
    
    requireLogin();
    error_log("AUTH TEST: requireLogin passed");
    
    $user = getCurrentUser();
    error_log("AUTH TEST: getCurrentUser returned: " . print_r($user, true));
    
    echo "✓ Auth system works\n";
    echo "User: " . print_r($user, true) . "\n";
    
} catch (Exception $e) {
    error_log("AUTH TEST: Error - " . $e->getMessage());
    echo "❌ Auth error: " . $e->getMessage() . "\n";
}
?>
