<?php
require_once __DIR__ . "/config/config.php";
echo "<h2>Debug Login Issues</h2>";
try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful<br>";
    $stmt = $pdo->query("SELECT id, email, username, role, is_active FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Users in database:</h3>";
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Email: {$user['email']}, Username: {$user['username']}, Role: {$user['role']}, Active: {$user['is_active']}<br>";
    }
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin' OR email = 'admin@admin.com'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        echo "<h3>Admin user found:</h3>";
        echo "Password hash: " . substr($admin['password'], 0, 20) . "...<br>";
        $verify = password_verify('admin', $admin['password']);
        echo "Password 'admin' verification: " . ($verify ? "✅ VALID" : "❌ INVALID") . "<br>";
    } else {
        echo "❌ No admin user found<br>";
    }
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
}
?>
