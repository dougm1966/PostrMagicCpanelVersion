<?php
// Database setup script - Run this once to create the necessary tables

// First check if PDO is available
if (!extension_loaded('pdo')) {
    die('PDO extension is not installed. Please install php-pdo.');
}

echo "<h2>Database Setup for PostrMagic</h2>\n";
echo "<p>Checking PHP extensions...</p>\n";
echo "✓ PDO extension: Available<br>\n";

// Load config to determine database type
require_once __DIR__ . '/config/config.php';

if (DB_TYPE === 'mysql') {
    if (!extension_loaded('pdo_mysql')) {
        die('PDO MySQL driver is not installed. Please install php-pdo-mysql or enable it in your php.ini file.');
    }
    echo "✓ PDO MySQL driver: Available<br>\n";
} else {
    if (!extension_loaded('pdo_sqlite')) {
        die('PDO SQLite driver is not installed. Please install php-pdo-sqlite or enable it in your php.ini file.');
    }
    echo "✓ PDO SQLite driver: Available<br>\n";
}
echo "<br>\n";

try {
    $pdo = getDBConnection();
    
    echo "<p>Environment detected: " . (DB_TYPE === 'sqlite' ? 'Local (SQLite)' : 'Production (MySQL)') . "</p>\n";
    echo "<p>Database Type: " . DB_TYPE . "</p>\n";
    echo "<br>\n";
    
    // Create users table - syntax differs between SQLite and MySQL
    if (DB_TYPE === 'sqlite') {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'user' CHECK(role IN ('user', 'admin')),
            remember_token TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_login DATETIME NULL DEFAULT NULL,
            is_active INTEGER DEFAULT 1
        )";
    } else {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            username VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            remember_token VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL DEFAULT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            INDEX idx_email (email),
            INDEX idx_username (username),
            INDEX idx_role (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }
    
    $pdo->exec($sql);
    echo "✓ Users table created successfully<br>\n";
    
    // Create user_sessions table for managing active sessions
    if (DB_TYPE === 'sqlite') {
        $sql = "CREATE TABLE IF NOT EXISTS user_sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            session_token TEXT UNIQUE NOT NULL,
            ip_address TEXT,
            user_agent TEXT,
            last_activity DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
    } else {
        $sql = "CREATE TABLE IF NOT EXISTS user_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_token VARCHAR(255) UNIQUE NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_session_token (session_token),
            INDEX idx_user_id (user_id),
            INDEX idx_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }
    
    $pdo->exec($sql);
    echo "✓ User sessions table created successfully<br>\n";
    
    // Create password_resets table
    if (DB_TYPE === 'sqlite') {
        $sql = "CREATE TABLE IF NOT EXISTS password_resets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            token TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL
        )";
    } else {
        $sql = "CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NOT NULL,
            INDEX idx_email (email),
            INDEX idx_token (token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }
    
    $pdo->exec($sql);
    echo "✓ Password resets table created successfully<br>\n";
    
    // Create test users
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
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$userData['email'], $userData['username']]);
        
        if (!$stmt->fetch()) {
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, username, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userData['email'], $userData['username'], $hashedPassword, $userData['role']]);
            echo "✓ User created: " . $userData['email'] . " (password: " . $userData['password'] . ")<br>\n";
        } else {
            echo "ℹ User already exists: " . $userData['email'] . "<br>\n";
        }
    }
    
    echo "<br><strong>Database setup complete!</strong><br>\n";
    echo "<a href='login.php'>Go to Login Page</a>\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}