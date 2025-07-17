<?php
declare(strict_types=1);

session_start();

echo "=== Session Data Test ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Session status: " . session_status() . "\n";
echo "Session data:\n";
print_r($_SESSION);

// Check if user is logged in according to auth system
require_once __DIR__ . '/includes/auth.php';

echo "\nAuth system check:\n";
echo "isLoggedIn(): " . (isLoggedIn() ? 'true' : 'false') . "\n";

if (isset($_SESSION['user_id'])) {
    echo "User ID in session: " . $_SESSION['user_id'] . "\n";
    echo "User role in session: " . ($_SESSION['user_role'] ?? 'MISSING') . "\n";
    
    // Check user in database
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "\nUser from database:\n";
            print_r($user);
        } else {
            echo "\nUser not found in database!\n";
        }
    } catch (Exception $e) {
        echo "\nError checking database: " . $e->getMessage() . "\n";
    }
}
?>