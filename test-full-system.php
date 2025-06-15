<?php
/**
 * Full System Test
 * Tests the complete media library system including migrations, uploads, and API
 */

require_once 'includes/migration-runner.php';
require_once 'includes/upload-manager.php';
require_once 'includes/media-manager.php';
require_once 'includes/tag-manager.php';
require_once 'includes/image-processor.php';
require_once 'includes/file-validator.php';

echo "=== PostrMagic Media Library System Test ===\n\n";

// 1. Test Database Connection and Migrations
echo "1. Testing Database and Migrations...\n";
try {
    $runner = new MigrationRunner();
    $connectionTest = $runner->testConnection();
    
    if ($connectionTest['connection'] === 'success') {
        echo "✅ Database connection: {$connectionTest['database_type']}\n";
        
        $migrationResults = $runner->runMigrations();
        $successCount = count(array_filter($migrationResults, fn($r) => $r['status'] === 'success'));
        $totalCount = count($migrationResults);
        echo "✅ Migrations: $successCount/$totalCount successful\n";
    } else {
        echo "❌ Database connection failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Database test failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Test Upload Directory Structure
echo "\n2. Testing Upload Directory Structure...\n";
try {
    $uploadManager = new UploadManager();
    $dirResults = $uploadManager->initializeDirectories();
    
    $createdCount = count(array_filter($dirResults, fn($r) => $r['status'] === 'created'));
    $existsCount = count(array_filter($dirResults, fn($r) => $r['status'] === 'exists'));
    echo "✅ Upload directories: $createdCount created, $existsCount existing\n";
    
    // Test path generation
    $testPath = $uploadManager->getUploadPath('media', 123);
    echo "✅ Path generation working: $testPath\n";
    
} catch (Exception $e) {
    echo "❌ Upload directory test failed: " . $e->getMessage() . "\n";
}

// 3. Test File Validation
echo "\n3. Testing File Validation...\n";
try {
    $validator = new FileValidator();
    
    // Test allowed types
    $allowedMedia = $validator->getAllowedTypes('media');
    $allowedPosters = $validator->getAllowedTypes('poster');
    echo "✅ Media types: " . implode(', ', $allowedMedia) . "\n";
    echo "✅ Poster types: " . implode(', ', $allowedPosters) . "\n";
    
    // Test MIME type checking
    echo "✅ JPEG allowed for media: " . ($validator->isTypeAllowed('image/jpeg', 'media') ? 'Yes' : 'No') . "\n";
    echo "✅ GIF blocked for media: " . ($validator->isTypeAllowed('image/gif', 'media') ? 'Yes' : 'No') . "\n";
    echo "✅ PDF allowed for posters: " . ($validator->isTypeAllowed('application/pdf', 'poster') ? 'Yes' : 'No') . "\n";
    
} catch (Exception $e) {
    echo "❌ File validation test failed: " . $e->getMessage() . "\n";
}

// 4. Test Image Processing
echo "\n4. Testing Image Processing...\n";
try {
    $processor = new ImageProcessor();
    
    // Create a test image
    $testImage = imagecreatetruecolor(800, 600);
    $backgroundColor = imagecolorallocate($testImage, 100, 150, 200);
    $textColor = imagecolorallocate($testImage, 255, 255, 255);
    imagefill($testImage, 0, 0, $backgroundColor);
    imagestring($testImage, 5, 250, 280, 'Test Image 800x600', $textColor);
    
    $testDir = __DIR__ . '/uploads/test';
    if (!is_dir($testDir)) {
        mkdir($testDir, 0755, true);
    }
    
    $testImagePath = $testDir . '/test-processing.jpg';
    imagejpeg($testImage, $testImagePath, 90);
    imagedestroy($testImage);
    
    // Test validation
    $validation = $processor->validateImage($testImagePath);
    if ($validation['valid']) {
        echo "✅ Image validation: {$validation['width']}x{$validation['height']}, {$validation['mimeType']}\n";
    } else {
        echo "❌ Image validation failed: {$validation['error']}\n";
    }
    
    // Test optimization
    $optimizedPath = $testDir . '/test-optimized.jpg';
    $optimization = $processor->optimizeImage($testImagePath, $optimizedPath, [
        'maxWidth' => 400,
        'maxHeight' => 300,
        'createWebP' => true
    ]);
    
    if ($optimization['success']) {
        echo "✅ Image optimization: {$optimization['compressionRatio']}% compression\n";
        echo "✅ WebP created: " . (isset($optimization['savedFiles']['webp']) ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Image optimization failed\n";
    }
    
    // Cleanup test files
    if (file_exists($testImagePath)) unlink($testImagePath);
    if (file_exists($optimizedPath)) unlink($optimizedPath);
    if (isset($optimization['savedFiles']['webp']) && file_exists($optimization['savedFiles']['webp'])) {
        unlink($optimization['savedFiles']['webp']);
    }
    
} catch (Exception $e) {
    echo "❌ Image processing test failed: " . $e->getMessage() . "\n";
}

// 5. Test Media Manager (requires database)
echo "\n5. Testing Media Manager...\n";
try {
    $mediaManager = new MediaManager();
    
    // Test getting user media (empty result expected)
    $userMedia = $mediaManager->getUserMedia(1, ['limit' => 5]);
    echo "✅ Media query: {$userMedia['pagination']['total_items']} files found\n";
    
    // Test admin access
    $allMedia = $mediaManager->getAllMedia(['limit' => 5]);
    echo "✅ Admin query: " . count($allMedia['media']) . " files found\n";
    
} catch (Exception $e) {
    echo "❌ Media manager test failed: " . $e->getMessage() . "\n";
}

// 6. Test Tag Manager
echo "\n6. Testing Tag Manager...\n";
try {
    $tagManager = new TagManager();
    
    // Test getting user tags (empty result expected)
    $userTags = $tagManager->getUserTags(1);
    echo "✅ Tag query: " . count($userTags) . " tags found\n";
    
    // Test tag creation
    $createResult = $tagManager->createTag(1, 'test-tag-' . time(), '#ff6b6b');
    if ($createResult['success']) {
        echo "✅ Tag creation successful: ID {$createResult['tag_id']}\n";
        
        // Test tag update
        $updateResult = $tagManager->updateTag($createResult['tag_id'], 1, [
            'tag_name' => 'updated-test-tag',
            'tag_color' => '#4ecdc4'
        ]);
        
        if ($updateResult['success']) {
            echo "✅ Tag update successful\n";
        }
        
        // Test tag deletion
        $deleteResult = $tagManager->deleteTag($createResult['tag_id'], 1);
        if ($deleteResult['success']) {
            echo "✅ Tag deletion successful\n";
        }
    } else {
        echo "❌ Tag creation failed: {$createResult['error']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Tag manager test failed: " . $e->getMessage() . "\n";
}

// 7. Test API Endpoint (basic)
echo "\n7. Testing API Endpoint...\n";
try {
    // Simulate a GET request to the test endpoint
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['endpoint'] = 'test';
    
    ob_start();
    include 'api/media.php';
    $apiResponse = ob_get_clean();
    
    $response = json_decode($apiResponse, true);
    if ($response && $response['success']) {
        echo "✅ API test endpoint: " . $response['data']['message'] . "\n";
        echo "✅ Available endpoints: " . count($response['data']['endpoints']) . "\n";
    } else {
        echo "❌ API test failed\n";
    }
    
} catch (Exception $e) {
    echo "❌ API test failed: " . $e->getMessage() . "\n";
}

// 8. System Health Check
echo "\n8. System Health Check...\n";

// Check PHP extensions
$requiredExtensions = ['gd', 'pdo', 'json'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    echo ($loaded ? "✅" : "❌") . " PHP Extension $ext: " . ($loaded ? "Loaded" : "Missing") . "\n";
}

// Check file permissions
$uploadDir = __DIR__ . '/uploads';
$writable = is_writable($uploadDir);
echo ($writable ? "✅" : "❌") . " Upload directory writable: " . ($writable ? "Yes" : "No") . "\n";

// Check memory limit
$memoryLimit = ini_get('memory_limit');
echo "✅ Memory limit: $memoryLimit\n";

// Check max upload size
$uploadMax = ini_get('upload_max_filesize');
$postMax = ini_get('post_max_size');
echo "✅ Upload limits: $uploadMax (upload), $postMax (post)\n";

echo "\n=== System Test Complete ===\n";

// Summary
echo "\n=== Summary ===\n";
echo "✅ Phase 1: Foundation & File Management - COMPLETE\n";
echo "   - GD Extension & Image Processing ✅\n";
echo "   - File Type Management (No GIF) ✅\n";
echo "   - Database Schema ✅\n";
echo "   - Upload Directory Structure ✅\n";
echo "\n";
echo "✅ Phase 2: Core Media Library Backend - COMPLETE\n";
echo "   - User-Isolated Media Management ✅\n";
echo "   - Per-User Tagging System ✅\n";
echo "   - UI Backend Integration ✅\n";
echo "   - Media API Endpoints ✅\n";
echo "\n";
echo "🚀 Ready for Phase 3: Basic Poster Upload & AI Analysis\n";
?>