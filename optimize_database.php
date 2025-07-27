<?php
/**
 * Database Optimization Script
 * Enables SQLite WAL mode to resolve locking issues
 */

require_once __DIR__ . '/config/config.php';

echo "<h2>Database Optimization</h2>\n";

try {
    $pdo = getDBConnection();
    
    if (DB_TYPE === 'sqlite') {
        echo "Optimizing SQLite database...<br>\n";
        
        // Enable WAL mode (Write-Ahead Logging)
        $pdo->exec("PRAGMA journal_mode = WAL");
        echo "✓ WAL mode enabled<br>\n";
        
        // Set busy timeout to prevent locks
        $pdo->exec("PRAGMA busy_timeout = 5000");
        echo "✓ Busy timeout set to 5000ms<br>\n";
        
        // Optimize cache size
        $pdo->exec("PRAGMA cache_size = 10000");
        echo "✓ Cache size optimized<br>\n";
        
        // Enable synchronous normal mode (safer than OFF)
        $pdo->exec("PRAGMA synchronous = NORMAL");
        echo "✓ Synchronous mode set to NORMAL<br>\n";
        
        // Check current settings
        $settings = $pdo->query("PRAGMA journal_mode");
        $mode = $settings->fetchColumn();
        echo "Current journal mode: $mode<br>\n";
        
        echo "<br><strong>Database optimization complete!</strong><br>\n";
        echo "Try accessing the media libraries again.<br>\n";
        
    } else {
        echo "MySQL database detected - no SQLite optimization needed.<br>\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>\n";
}

echo '<br><a href="media-library.php">Test User Media Library</a> | ';
echo '<a href="admin/media.php">Test Admin Media Library</a>';
?>
