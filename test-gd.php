<?php
// Test GD extension availability
echo "=== GD Extension Test ===\n";

if (extension_loaded('gd')) {
    echo "✅ GD Extension is loaded\n";
    
    $gd_info = gd_info();
    echo "GD Version: " . $gd_info['GD Version'] . "\n";
    
    // Check specific image format support
    echo "\nSupported formats:\n";
    echo "- JPEG: " . (imagetypes() & IMG_JPG ? '✅' : '❌') . "\n";
    echo "- PNG: " . (imagetypes() & IMG_PNG ? '✅' : '❌') . "\n";
    echo "- GIF: " . (imagetypes() & IMG_GIF ? '✅' : '❌') . "\n";
    echo "- WebP: " . (imagetypes() & IMG_WEBP ? '✅' : '❌') . "\n";
    
    echo "\nFunctions available:\n";
    echo "- imagecreatefromjpeg: " . (function_exists('imagecreatefromjpeg') ? '✅' : '❌') . "\n";
    echo "- imagecreatefrompng: " . (function_exists('imagecreatefrompng') ? '✅' : '❌') . "\n";
    echo "- imagewebp: " . (function_exists('imagewebp') ? '✅' : '❌') . "\n";
    echo "- imagejpeg: " . (function_exists('imagejpeg') ? '✅' : '❌') . "\n";
    echo "- imagepng: " . (function_exists('imagepng') ? '✅' : '❌') . "\n";
    
} else {
    echo "❌ GD Extension is NOT loaded\n";
    echo "You need to enable the GD extension in your PHP configuration.\n";
}

echo "\n=== Memory and Upload Limits ===\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
echo "Post Max Size: " . ini_get('post_max_size') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "\n";

echo "\n=== Test Complete ===\n";
?>