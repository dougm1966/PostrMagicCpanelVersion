<?php
/**
 * Migration Script: Add Profile Fields to Users Table
 * Run this once to add extended profile fields to the existing users table
 */

require_once __DIR__ . '/config/config.php';

echo "<h2>Profile Fields Migration</h2>\n";
echo "<p>Adding extended profile fields to users table...</p>\n";

try {
    $pdo = getDBConnection();
    
    echo "<p>Environment detected: " . (DB_TYPE === 'sqlite' ? 'Local (SQLite)' : 'Production (MySQL)') . "</p>\n";
    echo "<br>\n";
    
    // Add profile fields to users table
    $profileFields = [
        'name' => DB_TYPE === 'sqlite' ? 'TEXT DEFAULT NULL' : 'VARCHAR(255) DEFAULT NULL',
        'avatar' => DB_TYPE === 'sqlite' ? 'TEXT DEFAULT NULL' : 'VARCHAR(255) DEFAULT NULL',
        'bio' => DB_TYPE === 'sqlite' ? 'TEXT DEFAULT NULL' : 'TEXT DEFAULT NULL',
        'location' => DB_TYPE === 'sqlite' ? 'TEXT DEFAULT NULL' : 'VARCHAR(255) DEFAULT NULL',
        'website' => DB_TYPE === 'sqlite' ? 'TEXT DEFAULT NULL' : 'VARCHAR(255) DEFAULT NULL',
        'twitter_handle' => DB_TYPE === 'sqlite' ? 'TEXT DEFAULT NULL' : 'VARCHAR(255) DEFAULT NULL',
        'phone' => DB_TYPE === 'sqlite' ? 'TEXT DEFAULT NULL' : 'VARCHAR(50) DEFAULT NULL',
        'timezone' => DB_TYPE === 'sqlite' ? 'TEXT DEFAULT \'UTC\'' : 'VARCHAR(100) DEFAULT \'UTC\'',
        'email_notifications' => DB_TYPE === 'sqlite' ? 'INTEGER DEFAULT 1' : 'BOOLEAN DEFAULT TRUE',
        'marketing_emails' => DB_TYPE === 'sqlite' ? 'INTEGER DEFAULT 1' : 'BOOLEAN DEFAULT TRUE'
    ];
    
    foreach ($profileFields as $fieldName => $fieldDefinition) {
        try {
            // Check if column already exists
            if (DB_TYPE === 'sqlite') {
                $stmt = $pdo->prepare("PRAGMA table_info(users)");
                $stmt->execute();
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $columnExists = false;
                foreach ($columns as $column) {
                    if ($column['name'] === $fieldName) {
                        $columnExists = true;
                        break;
                    }
                }
            } else {
                $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE ?");
                $stmt->execute([$fieldName]);
                $columnExists = $stmt->fetch() !== false;
            }
            
            if (!$columnExists) {
                $sql = "ALTER TABLE users ADD COLUMN {$fieldName} {$fieldDefinition}";
                $pdo->exec($sql);
                echo "✓ Added column: {$fieldName}<br>\n";
            } else {
                echo "ℹ Column already exists: {$fieldName}<br>\n";
            }
        } catch (PDOException $e) {
            echo "⚠ Error adding column {$fieldName}: " . $e->getMessage() . "<br>\n";
        }
    }
    
    // Update existing users with default display names
    $stmt = $pdo->prepare("UPDATE users SET name = username WHERE name IS NULL OR name = ''");
    $stmt->execute();
    $updatedCount = $stmt->rowCount();
    
    if ($updatedCount > 0) {
        echo "✓ Updated {$updatedCount} user(s) with default display names<br>\n";
    }
    
    echo "<br><strong>Profile fields migration complete!</strong><br>\n";
    echo "<p>The following fields have been added to the users table:</p>\n";
    echo "<ul>\n";
    foreach ($profileFields as $fieldName => $fieldDefinition) {
        echo "<li><strong>{$fieldName}</strong>: " . (
            $fieldName === 'name' ? 'User display name' :
            ($fieldName === 'avatar' ? 'Profile picture filename' :
            ($fieldName === 'bio' ? 'User biography' :
            ($fieldName === 'location' ? 'User location' :
            ($fieldName === 'website' ? 'Personal website URL' :
            ($fieldName === 'twitter_handle' ? 'Twitter username' :
            ($fieldName === 'phone' ? 'Contact phone number' :
            ($fieldName === 'timezone' ? 'User timezone preference' :
            ($fieldName === 'email_notifications' ? 'Email notification preference' :
            ($fieldName === 'marketing_emails' ? 'Marketing email preference' : 'Profile field'))))))))))
        ) . "</li>\n";
    }
    echo "</ul>\n";
    echo "<a href='admin/profile.php'>Test Admin Profile</a> | <a href='user-profile.php'>Test User Profile</a>\n";
    
} catch (PDOException $e) {
    echo "Migration Error: " . $e->getMessage();
}
?>