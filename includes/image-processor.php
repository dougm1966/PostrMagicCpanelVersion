<?php
/**
 * Image Processing Library for PostrMagic
 * Handles image optimization, resizing, compression, and WebP conversion
 * Compatible with cPanel hosting environments
 */

class ImageProcessor {
    
    private $maxMemoryLimit = '256M';
    private $defaultQuality = 85;
    private $webpQuality = 80;
    
    public function __construct() {
        // Increase memory limit for image processing
        if (ini_get('memory_limit') !== '-1') {
            ini_set('memory_limit', $this->maxMemoryLimit);
        }
        
        // Check GD extension - don't crash if missing, just log warning
        if (!extension_loaded('gd')) {
            error_log('Warning: GD extension not available. Image processing will be limited.');
        }
    }
    
    /**
     * Optimize an uploaded image
     * @param string $sourcePath Source image path
     * @param string $destinationPath Destination path
     * @param array $options Processing options
     * @return array Processing results
     */
    public function optimizeImage($sourcePath, $destinationPath, $options = []) {
        // Check if GD is available
        if (!extension_loaded('gd')) {
            // Fallback: just copy the file if GD isn't available
            if (copy($sourcePath, $destinationPath)) {
                return [
                    'success' => true,
                    'message' => 'File copied (GD extension not available for optimization)',
                    'originalDimensions' => ['width' => 0, 'height' => 0],
                    'newDimensions' => ['width' => 0, 'height' => 0],
                    'originalSize' => filesize($sourcePath),
                    'optimizedSize' => filesize($destinationPath),
                    'compressionRatio' => 0,
                    'savedFiles' => ['original' => $destinationPath],
                    'mimeType' => mime_content_type($sourcePath)
                ];
            } else {
                throw new Exception('Failed to copy file and GD extension not available');
            }
        }
        
        $options = array_merge([
            'maxWidth' => 1920,
            'maxHeight' => 1080,
            'quality' => $this->defaultQuality,
            'createWebP' => true,
            'stripMetadata' => true,
            'maintainAspectRatio' => true
        ], $options);
        
        if (!file_exists($sourcePath)) {
            throw new Exception('Source image file not found');
        }
        
        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            throw new Exception('Invalid image file');
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Calculate new dimensions
        $dimensions = $this->calculateDimensions(
            $originalWidth, 
            $originalHeight, 
            $options['maxWidth'], 
            $options['maxHeight'],
            $options['maintainAspectRatio']
        );
        
        // Create source image resource
        $sourceImage = $this->createImageFromFile($sourcePath, $mimeType);
        if (!$sourceImage) {
            throw new Exception('Failed to create image resource from source');
        }
        
        // Create destination image
        $destImage = imagecreatetruecolor($dimensions['width'], $dimensions['height']);
        
        // Preserve transparency for PNG images
        if ($mimeType === 'image/png') {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefill($destImage, 0, 0, $transparent);
        }
        
        // Resize image
        $resizeSuccess = imagecopyresampled(
            $destImage, $sourceImage,
            0, 0, 0, 0,
            $dimensions['width'], $dimensions['height'],
            $originalWidth, $originalHeight
        );
        
        if (!$resizeSuccess) {
            imagedestroy($sourceImage);
            imagedestroy($destImage);
            throw new Exception('Failed to resize image');
        }
        
        // Save optimized image
        $savedFiles = [];
        $fileInfo = pathinfo($destinationPath);
        $baseName = $fileInfo['dirname'] . '/' . $fileInfo['filename'];
        
        // Save in original format
        $originalSaved = $this->saveImage($destImage, $destinationPath, $mimeType, $options['quality']);
        if ($originalSaved) {
            $savedFiles['original'] = $destinationPath;
        }
        
        // Create WebP version if supported and requested
        if ($options['createWebP'] && function_exists('imagewebp')) {
            $webpPath = $baseName . '.webp';
            $webpSaved = imagewebp($destImage, $webpPath, $this->webpQuality);
            if ($webpSaved) {
                $savedFiles['webp'] = $webpPath;
            }
        }
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destImage);
        
        // Get file sizes
        $originalSize = file_exists($sourcePath) ? filesize($sourcePath) : 0;
        $optimizedSize = file_exists($destinationPath) ? filesize($destinationPath) : 0;
        
        return [
            'success' => true,
            'originalDimensions' => ['width' => $originalWidth, 'height' => $originalHeight],
            'newDimensions' => $dimensions,
            'originalSize' => $originalSize,
            'optimizedSize' => $optimizedSize,
            'compressionRatio' => $originalSize > 0 ? round((1 - ($optimizedSize / $originalSize)) * 100, 2) : 0,
            'savedFiles' => $savedFiles,
            'mimeType' => $mimeType
        ];
    }
    
    /**
     * Create image resource from file
     */
    private function createImageFromFile($filePath, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($filePath);
            case 'image/png':
                return imagecreatefrompng($filePath);
            case 'image/gif':
                return imagecreatefromgif($filePath);
            case 'image/webp':
                return function_exists('imagecreatefromwebp') ? imagecreatefromwebp($filePath) : false;
            default:
                return false;
        }
    }
    
    /**
     * Save image to file
     */
    private function saveImage($imageResource, $filePath, $mimeType, $quality) {
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        switch ($mimeType) {
            case 'image/jpeg':
                return imagejpeg($imageResource, $filePath, $quality);
            case 'image/png':
                // PNG quality is 0-9, convert from 0-100
                $pngQuality = 9 - round(($quality / 100) * 9);
                return imagepng($imageResource, $filePath, $pngQuality);
            case 'image/gif':
                return imagegif($imageResource, $filePath);
            case 'image/webp':
                return function_exists('imagewebp') ? imagewebp($imageResource, $filePath, $quality) : false;
            default:
                return false;
        }
    }
    
    /**
     * Calculate new dimensions while maintaining aspect ratio
     */
    private function calculateDimensions($originalWidth, $originalHeight, $maxWidth, $maxHeight, $maintainAspectRatio = true) {
        if (!$maintainAspectRatio) {
            return ['width' => $maxWidth, 'height' => $maxHeight];
        }
        
        // If image is smaller than max dimensions, keep original size
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return ['width' => $originalWidth, 'height' => $originalHeight];
        }
        
        $aspectRatio = $originalWidth / $originalHeight;
        
        if ($maxWidth / $maxHeight > $aspectRatio) {
            $newHeight = $maxHeight;
            $newWidth = round($maxHeight * $aspectRatio);
        } else {
            $newWidth = $maxWidth;
            $newHeight = round($maxWidth / $aspectRatio);
        }
        
        return ['width' => $newWidth, 'height' => $newHeight];
    }
    
    /**
     * Generate thumbnail
     */
    public function generateThumbnail($sourcePath, $thumbnailPath, $size = 300, $quality = 85) {
        if (!extension_loaded('gd')) {
            // Fallback: just copy the file if GD isn't available
            if (copy($sourcePath, $thumbnailPath)) {
                return ['success' => true, 'message' => 'File copied as thumbnail (GD not available)'];
            } else {
                return ['success' => false, 'error' => 'Failed to copy file as thumbnail'];
            }
        }
        
        return $this->optimizeImage($sourcePath, $thumbnailPath, [
            'maxWidth' => $size,
            'maxHeight' => $size,
            'quality' => $quality,
            'createWebP' => false,
            'maintainAspectRatio' => true
        ]);
    }
    
    /**
     * Validate image file
     */
    public function validateImage($filePath, $allowedTypes = ['image/jpeg', 'image/png', 'image/webp']) {
        if (!file_exists($filePath)) {
            return ['valid' => false, 'error' => 'File does not exist'];
        }
        
        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            return ['valid' => false, 'error' => 'Invalid image file'];
        }
        
        $mimeType = $imageInfo['mime'];
        if (!in_array($mimeType, $allowedTypes)) {
            return ['valid' => false, 'error' => 'Unsupported image type: ' . $mimeType];
        }
        
        $fileSize = filesize($filePath);
        $maxSize = $this->getMaxUploadSize();
        if ($fileSize > $maxSize) {
            return ['valid' => false, 'error' => 'File size exceeds maximum allowed size'];
        }
        
        return [
            'valid' => true,
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'mimeType' => $mimeType,
            'fileSize' => $fileSize
        ];
    }
    
    /**
     * Get maximum upload size from PHP configuration
     */
    private function getMaxUploadSize() {
        $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
        $postMax = $this->parseSize(ini_get('post_max_size'));
        return min($uploadMax, $postMax);
    }
    
    /**
     * Parse size string to bytes
     */
    private function parseSize($size) {
        $size = trim($size);
        $last = strtolower($size[strlen($size)-1]);
        $size = (int) $size;
        
        switch ($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }
        
        return $size;
    }
    
    /**
     * Clean up temporary files
     */
    public function cleanup($filePaths) {
        foreach ((array) $filePaths as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
    
    /**
     * Get image dimensions without loading full image
     */
    public function getImageDimensions($filePath) {
        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            return false;
        }
        
        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'mimeType' => $imageInfo['mime']
        ];
    }
    
    /**
     * Check if image processing is supported for this file type
     */
    public function isProcessingSupported($mimeType) {
        $supportedTypes = ['image/jpeg', 'image/png'];
        
        if (function_exists('imagecreatefromwebp')) {
            $supportedTypes[] = 'image/webp';
        }
        
        // Note: We're excluding GIF as per requirements
        
        return in_array($mimeType, $supportedTypes);
    }
}

/**
 * Helper function to get a configured ImageProcessor instance
 */
function getImageProcessor() {
    return new ImageProcessor();
}

/**
 * Quick image optimization function
 */
function optimizeUploadedImage($sourcePath, $destinationPath, $options = []) {
    try {
        $processor = new ImageProcessor();
        return $processor->optimizeImage($sourcePath, $destinationPath, $options);
    } catch (Exception $e) {
        error_log("Image optimization failed: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
?>
