<?php
// Quick admin password reset script
require_once __DIR__ . '/config/config.php';

try {
    $pdo = getDBConnection();
    
    // Reset admin password to 'admin'
    $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
    
    echo "New password hash: " . substr($hashedPassword, 0, 20) . "...<br>";
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin' OR email = 'admin@admin.com'");
    $result = $stmt->execute([$hashedPassword]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Admin password reset successfully!<br>";
        echo "Username: admin<br>";
        echo "Email: admin@admin.com<br>";
        echo "Password: admin<br><br>";
        echo "<a href='login.php'>Go to Login</a>";
    } else {
        echo "❌ Admin user not found. Creating new admin user...<br>";
        
        // Create admin user if doesn't exist
        $stmt = $pdo->prepare("INSERT INTO users (email, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin@admin.com', 'admin', $hashedPassword, 'admin']);
        
        echo "✅ Admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Email: admin@admin.com<br>";
        echo "Password: admin<br><br>";
        echo "<a href='login.php'>Go to Login</a>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
}
?>
