<?php
/**
 * Update Database Configuration
 * This will fix the SQLite locking issues by updating the config
 */

echo "<h2>Database Configuration Update</h2>";

// Read current config
$configFile = __DIR__ . '/config/config.php';
$configContent = file_get_contents($configFile);

echo "<h3>Updating getDBConnection() function...</h3>";

// Create the improved database connection function
$newDBFunction = '
// Database Connection Function
function getDBConnection() {
    try {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT => 30,
        ];
        
        if (DB_TYPE === \'sqlite\') {
            // SQLite for local development
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
            // MySQL for production
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        }
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        if (APP_DEBUG) {
            die("Database connection failed: " . $e->getMessage() . 
                "<br><br>Environment: " . (DB_TYPE === \'sqlite\' ? \'Local (SQLite)\' : \'Production (MySQL)\') .
                "<br>Database Type: " . DB_TYPE);
        } else {
            die("A database error occurred. Please try again later.");
        }
    }
}';

// Replace the old function
$pattern = '/\/\/ Database Connection Function.*?function getDBConnection\(\) \{.*?\n\}/s';
if (preg_match($pattern, $configContent)) {
    $updatedConfig = preg_replace($pattern, $newDBFunction, $configContent);
    
    // Write the updated config
    if (file_put_contents($configFile, $updatedConfig)) {
        echo "✅ Database configuration updated successfully<br>";
        echo "✅ Added improved SQLite settings to prevent locks<br>";
        echo "✅ Increased timeout to 30 seconds<br>";
        echo "✅ Disabled problematic SQLite features<br>";
    } else {
        echo "❌ Could not write updated configuration<br>";
        exit;
    }
} else {
    echo "❌ Could not find database function in config<br>";
    exit;
}

// Test the new configuration
echo "<h3>Testing Updated Configuration...</h3>";
try {
    require_once $configFile;
    $pdo = getDBConnection();
    echo "✅ New database configuration works<br>";
    
    // Test a query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ Database query successful: " . $result['count'] . " users<br>";
    
} catch (Exception $e) {
    echo "❌ New configuration test failed: " . $e->getMessage() . "<br>";
}

echo "<h3>✅ Configuration Update Complete</h3>";
echo "<p><strong>The database lock issue should now be resolved.</strong></p>";

echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li><a href='/admin/profile.php'>Test Admin Profile</a></li>";
echo "<li><a href='/user-profile.php'>Test User Profile</a></li>";
echo "<li><a href='/login.php'>Login if needed</a></li>";
echo "</ol>";

echo "<h3>What Was Fixed:</h3>";
echo "<ul>";
echo "<li>✅ Increased database timeout from default to 30 seconds</li>";
echo "<li>✅ Disabled WAL journal mode (prevents locks)</li>";
echo "<li>✅ Set synchronous mode to NORMAL (faster, less prone to locks)</li>";
echo "<li>✅ Added busy timeout of 30 seconds</li>";
echo "<li>✅ Disabled memory mapping (can cause locks)</li>";
echo "<li>✅ Set temp storage to memory (faster)</li>";
echo "</ul>";
?>
