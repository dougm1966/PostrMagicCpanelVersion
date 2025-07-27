<?php
/**
 * Cleanup Script for Temporary Uploads
 * Removes expired temporary poster uploads and their files
 * 
 * This script should be run periodically via cron job:
 * 0 */6 * * * php /path/to/cleanup-temp-uploads.php >/dev/null 2>&1
 */

require_once __DIR__ . '/includes/temp-upload-manager.php';

// Only allow CLI execution for security
if (php_sapi_name() !== 'cli' && !defined('ALLOW_WEB_CLEANUP')) {
    die('This script can only be run from command line.');
}

echo "Starting temporary upload cleanup...\n";

try {
    $tempUploadManager = new TempUploadManager();
    
    // Get statistics before cleanup
    echo "Getting statistics before cleanup...\n";
    $beforeStats = $tempUploadManager->getUploadStats();
    
    if ($beforeStats['success']) {
        $stats = $beforeStats['stats'];
        echo "Before cleanup:\n";
        echo "  Total uploads: " . $stats['total_uploads'] . "\n";
        echo "  Expired uploads: " . $stats['expired_uploads'] . "\n";
        
        if (isset($stats['status_breakdown'])) {
            echo "  Status breakdown:\n";
            foreach ($stats['status_breakdown'] as $status => $count) {
                echo "    {$status}: {$count}\n";
            }
        }
    }
    
    // Perform cleanup
    echo "\nCleaning up expired uploads...\n";
    $cleanupResult = $tempUploadManager->cleanupExpired();
    
    if ($cleanupResult['success']) {
        echo "Cleanup completed successfully!\n";
        echo "  Files deleted: " . $cleanupResult['deleted_files'] . "\n";
        echo "  Records deleted: " . $cleanupResult['deleted_records'] . "\n";
        
        // Get statistics after cleanup
        $afterStats = $tempUploadManager->getUploadStats();
        if ($afterStats['success']) {
            echo "\nAfter cleanup:\n";
            echo "  Total uploads: " . $afterStats['stats']['total_uploads'] . "\n";
            echo "  Expired uploads: " . $afterStats['stats']['expired_uploads'] . "\n";
        }
        
    } else {
        echo "Cleanup failed: " . ($cleanupResult['error'] ?? 'Unknown error') . "\n";
        exit(1);
    }
    
    echo "\nCleanup script completed successfully.\n";
    
} catch (Exception $e) {
    echo "Error during cleanup: " . $e->getMessage() . "\n";
    exit(1);
}
?>
