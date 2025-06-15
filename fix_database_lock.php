<?php
/**
 * Fix SQLite Database Lock Issue
 * This script will help resolve the database lock problem
 */

echo "<h2>SQLite Database Lock Fix</h2>";

// Step 1: Check if database file exists and is accessible
$dbPath = __DIR__ . '/data/postrmagic.db';
echo "<h3>Step 1: Database File Check</h3>";
echo "Database path: $dbPath<br>";

if (file_exists($dbPath)) {
    echo "✅ Database file exists<br>";
    echo "File size: " . formatBytes(filesize($dbPath)) . "<br>";
    echo "File permissions: " . substr(sprintf('%o', fileperms($dbPath)), -4) . "<br>";
    echo "File modified: " . date('Y-m-d H:i:s', filemtime($dbPath)) . "<br>";
} else {
    echo "❌ Database file does not exist<br>";
    exit;
}

// Step 2: Check for lock files
echo "<h3>Step 2: Lock File Check</h3>";
$lockFiles = [
    $dbPath . '-wal',
    $dbPath . '-shm',
    $dbPath . '.lock'
];

foreach ($lockFiles as $lockFile) {
    if (file_exists($lockFile)) {
        echo "⚠️ Lock file exists: " . basename($lockFile) . "<br>";
        echo "Size: " . formatBytes(filesize($lockFile)) . "<br>";
    } else {
        echo "✅ No lock file: " . basename($lockFile) . "<br>";
    }
}

// Step 3: Try to connect with timeout and WAL mode disabled
echo "<h3>Step 3: Database Connection Test</h3>";
try {
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT => 5, // 5 second timeout
    ];
    
    $dsn = "sqlite:" . $dbPath;
    $pdo = new PDO($dsn, null, null, $options);
    
    // Disable WAL mode which can cause locks
    $pdo->exec("PRAGMA journal_mode = DELETE");
    $pdo->exec("PRAGMA synchronous = NORMAL");
    $pdo->exec("PRAGMA cache_size = 10000");
    $pdo->exec("PRAGMA temp_store = MEMORY");
    $pdo->exec("PRAGMA mmap_size = 0");
    
    echo "✅ Database connection successful<br>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ Users table accessible, " . $result['count'] . " users found<br>";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    
    // Step 4: Force unlock if possible
    echo "<h3>Step 4: Force Unlock Attempt</h3>";
    
    // Try to remove lock files manually
    foreach ($lockFiles as $lockFile) {
        if (file_exists($lockFile)) {
            if (unlink($lockFile)) {
                echo "✅ Removed lock file: " . basename($lockFile) . "<br>";
            } else {
                echo "❌ Could not remove lock file: " . basename($lockFile) . "<br>";
            }
        }
    }
    
    // Try connection again
    try {
        $pdo = new PDO($dsn, null, null, $options);
        $pdo->exec("PRAGMA journal_mode = DELETE");
        echo "✅ Database unlocked and accessible after cleanup<br>";
    } catch (PDOException $e2) {
        echo "❌ Still locked after cleanup: " . $e2->getMessage() . "<br>";
    }
}

// Step 5: Create improved config with better SQLite settings
echo "<h3>Step 5: Database Configuration Fix</h3>";

if (isset($pdo)) {
    echo "✅ Creating improved database configuration...<br>";
    
    // Create a backup config section for better SQLite handling
    $configAddition = '
/**
 * Improved SQLite Configuration
 * Add these settings to your getDBConnection() function
 */
function getImprovedDBConnection() {
    try {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT => 30,
        ];
        
        if (DB_TYPE === \'sqlite\') {
            $data_dir = dirname(DB_PATH);
            if (!is_dir($data_dir)) {
                mkdir($data_dir, 0755, true);
            }
            $dsn = "sqlite:" . DB_PATH;
            $pdo = new PDO($dsn, null, null, $options);
            
            // Improved SQLite settings to prevent locks
            $pdo->exec("PRAGMA journal_mode = DELETE");
            $pdo->exec("PRAGMA synchronous = NORMAL");
            $pdo->exec("PRAGMA cache_size = 10000");
            $pdo->exec("PRAGMA temp_store = MEMORY");
            $pdo->exec("PRAGMA mmap_size = 0");
            $pdo->exec("PRAGMA busy_timeout = 30000");
            
            return $pdo;
        } else {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        }
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        throw $e;
    }
}
';
    
    echo "<pre>" . htmlspecialchars($configAddition) . "</pre>";
    echo "✅ Ready to update database configuration<br>";
    
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='update_db_config.php'>Update Database Configuration</a></li>";
    echo "<li><a href='/admin/profile.php'>Test Admin Profile Again</a></li>";
    echo "<li><a href='/user-profile.php'>Test User Profile</a></li>";
    echo "</ol>";
    
} else {
    echo "❌ Database still not accessible<br>";
    echo "<p><strong>Manual Fix Required:</strong></p>";
    echo "<ol>";
    echo "<li>Stop your development server</li>";
    echo "<li>Delete any .db-wal and .db-shm files in the data directory</li>";
    echo "<li>Restart your development server</li>";
    echo "<li>Try again</li>";
    echo "</ol>";
}

function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}
?>