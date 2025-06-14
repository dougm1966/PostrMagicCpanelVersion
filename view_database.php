<?php
// Simple database viewer for development
require_once __DIR__ . '/config/config.php';

echo "<h2>PostrMagic Database Viewer</h2>\n";
echo "<p>Environment: " . (DB_TYPE === 'sqlite' ? 'Local (SQLite)' : 'Production (MySQL)') . "</p>\n";
echo "<p>Database Type: " . DB_TYPE . "</p>\n";

if (DB_TYPE === 'sqlite') {
    echo "<p>Database File: " . DB_PATH . "</p>\n";
}

echo "<hr>\n";

try {
    $pdo = getDBConnection();
    
    // Show Users Table
    echo "<h3>Users Table</h3>\n";
    $stmt = $pdo->query("SELECT id, email, username, role, created_at, last_login, is_active FROM users ORDER BY id");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p>No users found. <a href='setup_database.php'>Run setup_database.php</a> to create the default admin user.</p>\n";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>\n";
        echo "<tr><th>ID</th><th>Email</th><th>Username</th><th>Role</th><th>Active</th><th>Created</th><th>Last Login</th></tr>\n";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "<td>" . ($user['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
            echo "<td>" . htmlspecialchars($user['last_login'] ?? 'Never') . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    echo "<br>\n";
    
    // Show Sessions Table
    echo "<h3>Active Sessions</h3>\n";
    $stmt = $pdo->query("SELECT s.id, s.user_id, u.username, s.ip_address, s.last_activity, s.expires_at 
                        FROM user_sessions s 
                        LEFT JOIN users u ON s.user_id = u.id 
                        WHERE s.expires_at > datetime('now')
                        ORDER BY s.last_activity DESC");
    $sessions = $stmt->fetchAll();
    
    if (empty($sessions)) {
        echo "<p>No active sessions.</p>\n";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>\n";
        echo "<tr><th>Session ID</th><th>User</th><th>IP Address</th><th>Last Activity</th><th>Expires</th></tr>\n";
        
        foreach ($sessions as $session) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($session['id']) . "</td>";
            echo "<td>" . htmlspecialchars($session['username'] ?? 'Unknown') . "</td>";
            echo "<td>" . htmlspecialchars($session['ip_address'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($session['last_activity']) . "</td>";
            echo "<td>" . htmlspecialchars($session['expires_at']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    echo "<br>\n";
    
    // Show all tables
    echo "<h3>Database Tables</h3>\n";
    if (DB_TYPE === 'sqlite') {
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    } else {
        $stmt = $pdo->query("SHOW TABLES");
    }
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>\n";
    foreach ($tables as $table) {
        if ($table !== 'sqlite_sequence') { // Skip SQLite internal table
            echo "<li>" . htmlspecialchars($table) . "</li>\n";
        }
    }
    echo "</ul>\n";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<hr>\n";
echo "<p><a href='login.php'>← Back to Login</a> | <a href='setup_database.php'>Run Database Setup</a> | <a href='test_db_connection.php'>Test Connection</a></p>\n";
?>