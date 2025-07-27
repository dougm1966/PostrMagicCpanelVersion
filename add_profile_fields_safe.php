<?php
/**
 * Safe Migration Script: Add Profile Fields to Users Table
 * This version includes better error handling and step-by-step execution
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Profile Fields Migration (Safe Version)</h2>\n";
echo "<p>Adding extended profile fields to users table...</p>\n";

// Step 1: Include config safely
try {
    require_once __DIR__ . '/config/config.php';
    echo "✅ Config loaded successfully<br>\n";
} catch (Exception $e) {
    echo "❌ Config error: " . $e->getMessage() . "<br>\n";
    exit;
}

// Step 2: Connect to database
try {
    $pdo = getDBConnection();
    echo "✅ Database connected<br>\n";
    echo "Environment: " . (DB_TYPE === 'sqlite' ? 'Local (SQLite)' : 'Production (MySQL)') . "<br>\n";
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "<br>\n";
    exit;
}

// Step 3: Check if users table exists
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
    echo "✅ Users table exists with $userCount users<br>\n";
} catch (Exception $e) {
    echo "❌ Users table not found. Please run setup_database.php first.<br>\n";
    echo "<a href='setup_database.php'>Run Database Setup</a><br>\n";
    exit;
}

// Step 4: Define profile fields to add
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

echo "<br><h3>Step 5: Adding profile fields...</h3>\n";

$addedFields = 0;
$skippedFields = 0;
$errors = [];

foreach ($profileFields as $fieldName => $fieldDefinition) {
    try {
        // Check if column already exists
        $columnExists = false;
        
        if (DB_TYPE === 'sqlite') {
            $stmt = $pdo->prepare("PRAGMA table_info(users)");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            // Add the column
            $sql = "ALTER TABLE users ADD COLUMN {$fieldName} {$fieldDefinition}";
            $pdo->exec($sql);
            echo "✅ Added column: <strong>{$fieldName}</strong><br>\n";
            $addedFields++;
        } else {
            echo "ℹ️ Column already exists: <strong>{$fieldName}</strong><br>\n";
            $skippedFields++;
        }
        
    } catch (PDOException $e) {
        $error = "Error adding column {$fieldName}: " . $e->getMessage();
        $errors[] = $error;
        echo "⚠️ $error<br>\n";
    }
}

// Step 6: Update existing users with default names
echo "<br><h3>Step 6: Setting default names for existing users...</h3>\n";
try {
    $stmt = $pdo->prepare("UPDATE users SET name = username WHERE name IS NULL OR name = ''");
    $stmt->execute();
    $updatedCount = $stmt->rowCount();
    
    if ($updatedCount > 0) {
        echo "✅ Updated $updatedCount user(s) with default display names<br>\n";
    } else {
        echo "ℹ️ No users needed default name updates<br>\n";
    }
} catch (Exception $e) {
    echo "⚠️ Error updating default names: " . $e->getMessage() . "<br>\n";
}

// Step 7: Create uploads directory
echo "<br><h3>Step 7: Creating uploads directory...</h3>\n";
$uploadsDir = __DIR__ . '/uploads';
$avatarsDir = $uploadsDir . '/avatars';

if (!is_dir($uploadsDir)) {
    if (mkdir($uploadsDir, 0755, true)) {
        echo "✅ Created uploads directory<br>\n";
    } else {
        echo "⚠️ Could not create uploads directory<br>\n";
    }
} else {
    echo "ℹ️ Uploads directory already exists<br>\n";
}

if (!is_dir($avatarsDir)) {
    if (mkdir($avatarsDir, 0755, true)) {
        echo "✅ Created avatars directory<br>\n";
    } else {
        echo "⚠️ Could not create avatars directory<br>\n";
    }
} else {
    echo "ℹ️ Avatars directory already exists<br>\n";
}

// Summary
echo "<br><h2>Migration Summary</h2>\n";
echo "<div style='background: #f0f9ff; padding: 15px; border-radius: 8px; border: 1px solid #0ea5e9;'>\n";
echo "<strong>Results:</strong><br>\n";
echo "• Fields added: $addedFields<br>\n";
echo "• Fields already existed: $skippedFields<br>\n";
echo "• Errors: " . count($errors) . "<br>\n";

if (count($errors) > 0) {
    echo "<br><strong>Errors encountered:</strong><br>\n";
    foreach ($errors as $error) {
        echo "• " . htmlspecialchars($error) . "<br>\n";
    }
}

if ($addedFields > 0 || $skippedFields > 0) {
    echo "<br><strong>✅ Migration completed!</strong><br>\n";
    echo "The following fields are now available in the users table:<br>\n";
    echo "<ul>\n";
    foreach ($profileFields as $fieldName => $fieldDefinition) {
        $description = [
            'name' => 'User display name',
            'avatar' => 'Profile picture filename',
            'bio' => 'User biography',
            'location' => 'User location',
            'website' => 'Personal website URL',
            'twitter_handle' => 'Twitter username',
            'phone' => 'Contact phone number',
            'timezone' => 'User timezone preference',
            'email_notifications' => 'Email notification preference',
            'marketing_emails' => 'Marketing email preference'
        ][$fieldName] ?? 'Profile field';
        
        echo "<li><strong>{$fieldName}</strong>: {$description}</li>\n";
    }
    echo "</ul>\n";
    
    echo "<br><strong>Next Steps:</strong><br>\n";
    echo "• <a href='/admin/profile.php'>Test Admin Profile</a><br>\n";
    echo "• <a href='/user-profile.php'>Test User Profile</a><br>\n";
    echo "• <a href='/login.php'>Login to test functionality</a><br>\n";
} else {
    echo "<br><strong>⚠️ No changes made</strong><br>\n";
    echo "All fields may already exist, or there were errors preventing migration.<br>\n";
}

echo "</div>\n";
?>
