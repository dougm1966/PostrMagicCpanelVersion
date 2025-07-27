<?php
/**
 * Test Admin Profile Update
 * Debug what's happening with the admin profile password update
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/auth-helper.php';
requireAdmin();

echo "<h2>Admin Profile Update Debug</h2>";

// Test 1: Check current user data structure
echo "<h3>Test 1: Current User Data</h3>";
$currentUser = getCurrentUser();
echo "<pre>";
print_r($currentUser);
echo "</pre>";

// Test 2: Check if we can read from users table with new fields
echo "<h3>Test 2: Users Table Query Test</h3>";
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Users table query successful<br>";
    echo "User has " . count($user) . " fields<br>";
    
    // Check for required fields
    $requiredFields = ['id', 'username', 'email', 'password'];
    foreach ($requiredFields as $field) {
        if (isset($user[$field])) {
            echo "✅ Field '$field' exists<br>";
        } else {
            echo "❌ Field '$field' missing<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Users table query failed: " . $e->getMessage() . "<br>";
}

// Test 3: Simulate password verification
echo "<h3>Test 3: Password Verification Test</h3>";
if (isset($user) && isset($user['password'])) {
    echo "Current password hash exists: Yes<br>";
    echo "Hash length: " . strlen($user['password']) . " characters<br>";
    
    // Test with a known password (admin/admin setup)
    if (password_verify('admin', $user['password'])) {
        echo "✅ Password verification with 'admin' works<br>";
    } else {
        echo "❌ Password verification with 'admin' failed<br>";
    }
} else {
    echo "❌ No password field found<br>";
}

// Test 4: Test form submission logic
echo "<h3>Test 4: Form Submission Logic</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:<br>";
    echo "<pre>";
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'password') !== false) {
            echo "$key: [HIDDEN]<br>";
        } else {
            echo "$key: " . htmlspecialchars($value) . "<br>";
        }
    }
    echo "</pre>";
    
    $action = $_POST['action'] ?? '';
    echo "Action: $action<br>";
    
    if ($action === 'update_profile') {
        try {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $current_password = $_POST['current_password'] ?? '';
            
            echo "Username: $username<br>";
            echo "Email: $email<br>";
            echo "Current password provided: " . (empty($current_password) ? 'No' : 'Yes') . "<br>";
            
            // Verify current password
            if (!empty($current_password) && password_verify($current_password, $user['password'])) {
                echo "✅ Current password verified<br>";
            } else {
                echo "❌ Current password verification failed<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Error in form processing: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "No POST data (use form below to test)<br>";
}

// Test 5: Simple update test form
echo "<h3>Test 5: Simple Update Test</h3>";
?>
<form method="POST">
    <input type="hidden" name="action" value="update_profile">
    <p>
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($currentUser['username']) ?>" required>
    </p>
    <p>
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($currentUser['email']) ?>" required>
    </p>
    <p>
        <label>Current Password:</label><br>
        <input type="password" name="current_password" required>
    </p>
    <p>
        <label>New Password (optional):</label><br>
        <input type="password" name="new_password">
    </p>
    <p>
        <label>Confirm New Password:</label><br>
        <input type="password" name="confirm_password">
    </p>
    <p>
        <button type="submit">Test Update</button>
    </p>
</form>

<p><a href="<?= BASE_URL ?>admin/profile.php">Back to Admin Profile</a></p>
