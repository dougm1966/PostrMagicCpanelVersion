<?php
require_once 'includes/image-processor.php';

echo "=== Image Processing Test ===\n";

try {
    $processor = new ImageProcessor();
    echo "✅ ImageProcessor created successfully\n";
    
    // Test directory creation
    $testDir = __DIR__ . '/uploads/test';
    if (!is_dir($testDir)) {
        mkdir($testDir, 0755, true);
        echo "✅ Test directory created: $testDir\n";
    }
    
    // Create a simple test image (since we don't have real uploads yet)
    $testImage = imagecreatetruecolor(800, 600);
    $backgroundColor = imagecolorallocate($testImage, 100, 150, 200);
    $textColor = imagecolorallocate($testImage, 255, 255, 255);
    imagefill($testImage, 0, 0, $backgroundColor);
    imagestring($testImage, 5, 250, 280, 'Test Image 800x600', $textColor);
    
    $testImagePath = $testDir . '/test-source.jpg';
    imagejpeg($testImage, $testImagePath, 90);
    imagedestroy($testImage);
    echo "✅ Test image created: $testImagePath\n";
    
    // Test image validation
    $validation = $processor->validateImage($testImagePath);
    if ($validation['valid']) {
        echo "✅ Image validation passed\n";
        echo "   - Dimensions: {$validation['width']}x{$validation['height']}\n";
        echo "   - MIME Type: {$validation['mimeType']}\n";
        echo "   - File Size: " . number_format($validation['fileSize']) . " bytes\n";
    } else {
        echo "❌ Image validation failed: {$validation['error']}\n";
    }
    
    // Test image optimization
    $optimizedPath = $testDir . '/test-optimized.jpg';
    $result = $processor->optimizeImage($testImagePath, $optimizedPath, [
        'maxWidth' => 400,
        'maxHeight' => 300,
        'quality' => 80,
        'createWebP' => true
    ]);
    
    if ($result['success']) {
        echo "✅ Image optimization successful\n";
        echo "   - Original: {$result['originalDimensions']['width']}x{$result['originalDimensions']['height']}\n";
        echo "   - Optimized: {$result['newDimensions']['width']}x{$result['newDimensions']['height']}\n";
        echo "   - Original Size: " . number_format($result['originalSize']) . " bytes\n";
        echo "   - Optimized Size: " . number_format($result['optimizedSize']) . " bytes\n";
        echo "   - Compression: {$result['compressionRatio']}%\n";
        echo "   - Files created: " . implode(', ', array_keys($result['savedFiles'])) . "\n";
    } else {
        echo "❌ Image optimization failed: {$result['error']}\n";
    }
    
    // Test thumbnail generation
    $thumbnailPath = $testDir . '/test-thumbnail.jpg';
    $thumbResult = $processor->generateThumbnail($testImagePath, $thumbnailPath, 150);
    
    if ($thumbResult['success']) {
        echo "✅ Thumbnail generation successful\n";
        echo "   - Thumbnail: {$thumbResult['newDimensions']['width']}x{$thumbResult['newDimensions']['height']}\n";
    } else {
        echo "❌ Thumbnail generation failed\n";
    }
    
    // Test helper function
    $helperResult = optimizeUploadedImage($testImagePath, $testDir . '/test-helper.jpg');
    if ($helperResult['success']) {
        echo "✅ Helper function works correctly\n";
    } else {
        echo "❌ Helper function failed: {$helperResult['error']}\n";
    }
    
    echo "\n=== Processing Support Check ===\n";
    $mimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    foreach ($mimeTypes as $mimeType) {
        $supported = $processor->isProcessingSupported($mimeType);
        $status = $supported ? '✅' : '❌';
        echo "$status $mimeType\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
