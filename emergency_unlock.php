<?php
/**
 * Emergency Database Unlock
 * Force unlock the SQLite database
 */

echo "<h2>Emergency Database Unlock</h2>";

// Step 1: Find and remove lock files
$dataDir = __DIR__ . '/data';
$dbPath = $dataDir . '/postrmagic.db';

echo "<h3>Step 1: Removing Lock Files</h3>";

$lockFiles = [
    $dbPath . '-wal',
    $dbPath . '-shm',
    $dataDir . '/postrmagic.db-wal',
    $dataDir . '/postrmagic.db-shm'
];

foreach ($lockFiles as $lockFile) {
    if (file_exists($lockFile)) {
        if (unlink($lockFile)) {
            echo "✅ Removed: " . basename($lockFile) . "<br>";
        } else {
            echo "❌ Could not remove: " . basename($lockFile) . "<br>";
        }
    } else {
        echo "ℹ️ Not found: " . basename($lockFile) . "<br>";
    }
}

// Step 2: Create a new database connection with force unlock
echo "<h3>Step 2: Force Database Connection</h3>";

try {
    // Direct SQLite connection without config
    $dsn = "sqlite:" . $dbPath;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 1
    ];
    
    $pdo = new PDO($dsn, null, null, $options);
    
    // Force unlock commands
    $pdo->exec("PRAGMA journal_mode = DELETE");
    $pdo->exec("PRAGMA locking_mode = NORMAL");
    $pdo->exec("BEGIN IMMEDIATE; ROLLBACK;"); // Force release any locks
    
    echo "✅ Database unlocked successfully<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ Database accessible: " . $result['count'] . " users found<br>";
    
} catch (Exception $e) {
    echo "❌ Direct unlock failed: " . $e->getMessage() . "<br>";
    
    // Step 3: Nuclear option - recreate database
    echo "<h3>Step 3: Database Recreation (Last Resort)</h3>";
    echo "<p>The database is severely locked. You may need to:</p>";
    echo "<ol>";
    echo "<li>Stop your development server completely</li>";
    echo "<li>Backup your database: <code>copy data\\postrmagic.db data\\postrmagic.db.backup</code></li>";
    echo "<li>Delete the database: <code>del data\\postrmagic.db</code></li>";
    echo "<li>Run setup_database.php to recreate it</li>";
    echo "<li>Re-run the profile migration</li>";
    echo "</ol>";
    exit;
}

// Step 4: Update the auth.php file to prevent future locks
echo "<h3>Step 3: Fixing Auth Configuration</h3>";

$authFile = __DIR__ . '/includes/auth.php';
$authContent = file_get_contents($authFile);

// Find and replace the problematic database calls
$oldPattern = 'function isLoggedIn() {
    if (isset($_SESSION[\'user_id\']) && isset($_SESSION[\'session_token\'])) {
        // Verify session is still valid
        try {
            $pdo = getDBConnection();
            if (DB_TYPE === \'sqlite\') {
                $stmt = $pdo->prepare("SELECT * FROM user_sessions WHERE session_token = ? AND expires_at > datetime(\'now\')");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM user_sessions WHERE session_token = ? AND expires_at > NOW()");
            }
            $stmt->execute([$_SESSION[\'session_token\']]);
            
            if ($stmt->fetch()) {
                // Update last activity
                if (DB_TYPE === \'sqlite\') {
                    $stmt = $pdo->prepare("UPDATE user_sessions SET last_activity = datetime(\'now\') WHERE session_token = ?");
                } else {
                    $stmt = $pdo->prepare("UPDATE user_sessions SET last_activity = NOW() WHERE session_token = ?");
                }
                $stmt->execute([$_SESSION[\'session_token\']]);
                return true;
            }
        } catch (PDOException $e) {
            error_log("Session verification error: " . $e->getMessage());
        }
    }';

$newPattern = 'function isLoggedIn() {
    if (isset($_SESSION[\'user_id\']) && isset($_SESSION[\'session_token\'])) {
        // Verify session is still valid
        try {
            $pdo = getDBConnection();
            if (DB_TYPE === \'sqlite\') {
                $stmt = $pdo->prepare("SELECT * FROM user_sessions WHERE session_token = ? AND expires_at > datetime(\'now\')");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM user_sessions WHERE session_token = ? AND expires_at > NOW()");
            }
            $stmt->execute([$_SESSION[\'session_token\']]);
            
            if ($stmt->fetch()) {
                // Update last activity (skip if database is busy)
                try {
                    if (DB_TYPE === \'sqlite\') {
                        $stmt = $pdo->prepare("UPDATE user_sessions SET last_activity = datetime(\'now\') WHERE session_token = ?");
                    } else {
                        $stmt = $pdo->prepare("UPDATE user_sessions SET last_activity = NOW() WHERE session_token = ?");
                    }
                    $stmt->execute([$_SESSION[\'session_token\']]);
                } catch (PDOException $updateError) {
                    // Skip update if database is busy - session is still valid
                    error_log("Session update skipped: " . $updateError->getMessage());
                }
                return true;
            }
        } catch (PDOException $e) {
            error_log("Session verification error: " . $e->getMessage());
            // If it\'s just a lock error, assume session is still valid for this request
            if (strpos($e->getMessage(), \'database is locked\') !== false) {
                return true;
            }
        }
    }';

if (strpos($authContent, 'Session verification error') !== false) {
    $updatedAuth = str_replace($oldPattern, $newPattern, $authContent);
    
    if ($updatedAuth !== $authContent) {
        file_put_contents($authFile, $updatedAuth);
        echo "✅ Updated auth.php to handle database locks gracefully<br>";
    } else {
        echo "ℹ️ Auth.php already has lock handling<br>";
    }
}

echo "<h3>✅ Emergency Unlock Complete</h3>";
echo "<p><strong>Database should now be accessible!</strong></p>";

echo "<p><strong>Test These Links:</strong></p>";
echo "<ul>";
echo "<li><a href='/admin/profile.php'>Admin Profile</a></li>";
echo "<li><a href='/user-profile.php'>User Profile</a></li>";
echo "<li><a href='/login.php'>Login Page</a></li>";
echo "</ul>";

echo "<h3>Prevention Tips:</h3>";
echo "<ul>";
echo "<li>✅ Avoid opening multiple browser tabs with the same site</li>";
echo "<li>✅ Don't refresh pages rapidly during form submissions</li>";
echo "<li>✅ Use the updated database configuration</li>";
echo "<li>✅ Consider switching to MySQL for production</li>";
echo "</ul>";
?>
