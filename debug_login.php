<?php
// Debug login - shows what's happening during authentication
require_once __DIR__ . '/includes/auth.php';

// Clear any session errors (session already started by config)
unset($_SESSION['error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<h3>Debug Login Attempt</h3>";
    echo "Login: '" . htmlspecialchars($login) . "'<br>";
    echo "Password: '" . htmlspecialchars($password) . "'<br>";
    echo "Environment: " . DB_TYPE . "<br><br>";
    
    try {
        $pdo = getDBConnection();
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND is_active = 1");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "✓ User found:<br>";
            echo "ID: " . $user['id'] . "<br>";
            echo "Email: " . $user['email'] . "<br>";
            echo "Username: " . $user['username'] . "<br>";
            echo "Role: " . $user['role'] . "<br>";
            echo "Active: " . ($user['is_active'] ? 'Yes' : 'No') . "<br>";
            echo "Stored password hash: " . substr($user['password'], 0, 30) . "...<br><br>";
            
            // Test password verification
            if (password_verify($password, $user['password'])) {
                echo "✓ Password verification: SUCCESS<br>";
                echo "<p style='color: green;'>Authentication should work!</p>";
            } else {
                echo "✗ Password verification: FAILED<br>";
                echo "<p style='color: red;'>Password doesn't match stored hash</p>";
            }
        } else {
            echo "✗ No user found with email/username: '" . htmlspecialchars($login) . "'<br>";
            
            // Show all users for debugging
            echo "<br>All users in database:<br>";
            $stmt = $pdo->query("SELECT id, email, username, is_active FROM users");
            $users = $stmt->fetchAll();
            foreach ($users as $u) {
                echo "- Email: '{$u['email']}', Username: '{$u['username']}', Active: " . ($u['is_active'] ? 'Yes' : 'No') . "<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Login</title>
</head>
<body>
    <h2>Debug Login Form</h2>
    <form method="POST">
        <p>
            <label>Email/Username:</label><br>
            <input type="text" name="email" value="">
        </p>
        <p>
            <label>Password:</label><br>
            <input type="text" name="password" value="">
        </p>
        <p>
            <button type="submit">Test Login</button>
        </p>
    </form>
    
    <p>Try these credentials:</p>
    <ul>
        <li>admin / admin</li>
        <li>bob / bob</li>
        <li>admin@admin.com / admin</li>
        <li>bob@bob.com / bob</li>
    </ul>
    
    <p><a href="login.php">← Back to Login</a> | <a href="view_database.php">View Database</a></p>
</body>
</html>