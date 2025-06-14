<?php
// Reset users - removes all users and creates the correct ones
require_once __DIR__ . '/config/config.php';

try {
    $pdo = getDBConnection();
    
    echo "<h2>Resetting Users</h2>\n";
    
    // Delete all existing users
    $pdo->exec("DELETE FROM user_sessions");
    $pdo->exec("DELETE FROM users");
    echo "✓ Cleared all existing users<br>\n";
    
    // Create the correct users
    $users = [
        [
            'email' => 'bob@bob.com',
            'username' => 'bob',
            'password' => 'bob',
            'role' => 'user'
        ],
        [
            'email' => 'admin@admin.com', 
            'username' => 'admin',
            'password' => 'admin',
            'role' => 'admin'
        ]
    ];
    
    foreach ($users as $userData) {
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userData['email'], $userData['username'], $hashedPassword, $userData['role']]);
        echo "✓ Created user: " . $userData['email'] . " (password: " . $userData['password'] . ")<br>\n";
    }
    
    echo "<br><strong>Users reset complete!</strong><br>\n";
    echo "<p>You can now login with:</p>\n";
    echo "<ul>\n";
    echo "<li><strong>bob@bob.com</strong> / <strong>bob</strong> (regular user)</li>\n";
    echo "<li><strong>admin@admin.com</strong> / <strong>admin</strong> (admin user)</li>\n";
    echo "</ul>\n";
    echo "<br><a href='login.php'>Go to Login Page</a> | <a href='view_database.php'>View Database</a>\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>