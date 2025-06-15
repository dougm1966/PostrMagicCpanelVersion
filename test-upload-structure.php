<?php
require_once 'includes/upload-manager.php';

echo "=== Upload Directory Structure Test ===\n";

try {
    $uploadManager = new UploadManager();
    
    // Initialize directory structure
    echo "Initializing upload directory structure...\n";
    $results = $uploadManager->initializeDirectories();
    
    foreach ($results as $directory => $result) {
        $status = $result['status'];
        if ($status === 'created') {
            echo "✅ Created: $directory\n";
        } elseif ($status === 'exists') {
            echo "ℹ️  Exists: $directory\n";
        } else {
            echo "❌ Error: $directory - {$result['message']}\n";
        }
    }
    
    // Test path generation for different contexts
    echo "\nTesting path generation...\n";
    $contexts = ['media', 'poster', 'temp', 'thumbnail', 'webp', 'profile'];
    
    foreach ($contexts as $context) {
        $path = $uploadManager->getUploadPath($context, 123); // User ID 123
        echo "✅ $context path: $path\n";
    }
    
    // Test filename generation
    echo "\nTesting filename generation...\n";
    $testFilenames = [
        'my-awesome-photo.jpg',
        'event poster with spaces.png',
        'special@chars#file.webp',
        'no-extension-file',
        'file.with.multiple.dots.jpg'
    ];
    
    foreach ($testFilenames as $original) {
        $generated = $uploadManager->generateUniqueFilename($original, 'media', 123);
        echo "✅ '$original' → '$generated'\n";
    }
    
    // Test URL generation
    echo "\nTesting URL generation...\n";
    $testRelativePath = 'media/2024/06/15/user_123/test_file.jpg';
    $url = $uploadManager->getFileUrl($testRelativePath);
    echo "✅ Relative path: $testRelativePath\n";
    echo "✅ Generated URL: $url\n";
    
    // Test disk usage (will be empty initially)
    echo "\nChecking disk usage...\n";
    $usage = $uploadManager->getDiskUsage();
    echo "✅ Total size: " . number_format($usage['total_size']) . " bytes\n";
    echo "✅ Total files: {$usage['file_count']}\n";
    
    foreach ($usage['contexts'] as $context => $stats) {
        echo "   - $context: {$stats['files']} files, " . number_format($stats['size']) . " bytes\n";
    }
    
    // Test cleanup (should find no files to clean up initially)
    echo "\nTesting temp file cleanup...\n";
    $cleaned = $uploadManager->cleanupTempFiles();
    echo "✅ Cleaned up " . count($cleaned) . " temporary files\n";
    
    // Create a test file to verify permissions
    echo "\nTesting file creation permissions...\n";
    $testFile = $uploadManager->getUploadPath('temp') . '/test_permissions.txt';
    if (file_put_contents($testFile, 'Test file for permissions') !== false) {
        echo "✅ Test file created successfully\n";
        
        $perms = substr(sprintf('%o', fileperms($testFile)), -4);
        echo "✅ File permissions: $perms\n";
        
        // Clean up test file
        unlink($testFile);
        echo "✅ Test file cleaned up\n";
    } else {
        echo "❌ Failed to create test file\n";
    }
    
} catch (Exception $e) {
    echo "❌ Upload structure test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Upload Structure Test Complete ===\n";
?>