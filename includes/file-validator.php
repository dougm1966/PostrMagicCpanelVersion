<?php
/**
 * File Validation System for PostrMagic
 * Handles context-aware file validation for media library and poster uploads
 */

require_once __DIR__ . '/../config/config.php';

class FileValidator {
    
    private $contexts = [
        'media' => [
            'allowed_types' => ALLOWED_MEDIA_TYPES,
            'max_size' => MAX_UPLOAD_SIZE,
            'description' => 'Media Library'
        ],
        'poster' => [
            'allowed_types' => ALLOWED_POSTER_TYPES,
            'max_size' => MAX_UPLOAD_SIZE,
            'description' => 'Poster Upload'
        ]
    ];
    
    /**
     * Validate uploaded file based on context
     */
    public function validateFile($filePath, $context = 'media', $originalName = null) {
        if (!isset($this->contexts[$context])) {
            return $this->error('Invalid validation context');
        }
        
        $contextConfig = $this->contexts[$context];
        
        // Check if file exists
        if (!file_exists($filePath)) {
            return $this->error('File does not exist');
        }
        
        // Get file info
        $fileSize = filesize($filePath);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        // Validate file size
        if ($fileSize > $contextConfig['max_size']) {
            $maxSizeMB = round($contextConfig['max_size'] / (1024 * 1024), 1);
            return $this->error("File size exceeds maximum allowed size of {$maxSizeMB}MB");
        }
        
        // Validate MIME type
        if (!in_array($mimeType, $contextConfig['allowed_types'])) {
            $allowedTypes = $this->getHumanReadableTypes($contextConfig['allowed_types']);
            return $this->error("File type not allowed. Supported types: {$allowedTypes}");
        }
        
        // Additional validation for images
        if (strpos($mimeType, 'image/') === 0) {
            $imageValidation = $this->validateImage($filePath, $mimeType);
            if (!$imageValidation['valid']) {
                return $imageValidation;
            }
        }
        
        // Additional validation for PDFs (poster context only)
        if ($mimeType === 'application/pdf' && $context === 'poster') {
            $pdfValidation = $this->validatePDF($filePath);
            if (!$pdfValidation['valid']) {
                return $pdfValidation;
            }
        }
        
        // Generate safe filename
        $safeFileName = $this->generateSafeFileName($originalName ?: basename($filePath), $mimeType);
        
        return [
            'valid' => true,
            'mimeType' => $mimeType,
            'fileSize' => $fileSize,
            'context' => $context,
            'safeFileName' => $safeFileName,
            'isImage' => strpos($mimeType, 'image/') === 0,
            'isPDF' => $mimeType === 'application/pdf'
        ];
    }
    
    /**
     * Validate image-specific properties
     */
    private function validateImage($filePath, $mimeType) {
        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            return $this->error('Invalid or corrupted image file');
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        // Check minimum dimensions
        if ($width < 10 || $height < 10) {
            return $this->error('Image dimensions too small (minimum 10x10 pixels)');
        }
        
        // Check maximum dimensions (prevent extremely large images)
        if ($width > 10000 || $height > 10000) {
            return $this->error('Image dimensions too large (maximum 10000x10000 pixels)');
        }
        
        // Verify MIME type matches actual image type
        $detectedMime = $imageInfo['mime'];
        if ($detectedMime !== $mimeType) {
            return $this->error('File extension does not match actual file type');
        }
        
        return [
            'valid' => true,
            'width' => $width,
            'height' => $height,
            'aspectRatio' => $width / $height
        ];
    }
    
    /**
     * Validate PDF-specific properties
     */
    private function validatePDF($filePath) {
        // Basic PDF validation - check for PDF header
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return $this->error('Cannot read PDF file');
        }
        
        $header = fread($handle, 8);
        fclose($handle);
        
        if (strpos($header, '%PDF-') !== 0) {
            return $this->error('Invalid PDF file format');
        }
        
        return ['valid' => true];
    }
    
    /**
     * Generate a safe filename
     */
    private function generateSafeFileName($originalName, $mimeType) {
        // Get file extension from MIME type
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf'
        ];
        
        $extension = $extensions[$mimeType] ?? 'bin';
        
        // Clean the original filename
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nameWithoutExt);
        $safeName = trim($safeName, '_');
        
        // Ensure name is not empty
        if (empty($safeName)) {
            $safeName = 'file_' . time();
        }
        
        // Limit length
        $safeName = substr($safeName, 0, 50);
        
        return $safeName . '.' . $extension;
    }
    
    /**
     * Get human-readable file type descriptions
     */
    private function getHumanReadableTypes($mimeTypes) {
        $readable = [];
        foreach ($mimeTypes as $mimeType) {
            switch ($mimeType) {
                case 'image/jpeg':
                    $readable[] = 'JPEG';
                    break;
                case 'image/png':
                    $readable[] = 'PNG';
                    break;
                case 'image/webp':
                    $readable[] = 'WebP';
                    break;
                case 'application/pdf':
                    $readable[] = 'PDF';
                    break;
                default:
                    $readable[] = $mimeType;
            }
        }
        return implode(', ', $readable);
    }
    
    /**
     * Return error result
     */
    private function error($message) {
        return [
            'valid' => false,
            'error' => $message
        ];
    }
    
    /**
     * Get allowed types for context
     */
    public function getAllowedTypes($context = 'media') {
        return $this->contexts[$context]['allowed_types'] ?? [];
    }
    
    /**
     * Get max file size for context
     */
    public function getMaxFileSize($context = 'media') {
        return $this->contexts[$context]['max_size'] ?? MAX_UPLOAD_SIZE;
    }
    
    /**
     * Get human-readable file size limit
     */
    public function getMaxFileSizeFormatted($context = 'media') {
        $bytes = $this->getMaxFileSize($context);
        return round($bytes / (1024 * 1024), 1) . 'MB';
    }
    
    /**
     * Check if file type is allowed for context
     */
    public function isTypeAllowed($mimeType, $context = 'media') {
        return in_array($mimeType, $this->getAllowedTypes($context));
    }
    
    /**
     * Validate multiple files
     */
    public function validateFiles($files, $context = 'media') {
        $results = [];
        foreach ($files as $key => $file) {
            if (is_array($file) && isset($file['tmp_name'])) {
                // $_FILES format
                $results[$key] = $this->validateFile($file['tmp_name'], $context, $file['name']);
            } else {
                // Direct file path
                $results[$key] = $this->validateFile($file, $context);
            }
        }
        return $results;
    }
}

/**
 * Helper function to get configured file validator
 */
function getFileValidator() {
    return new FileValidator();
}

/**
 * Quick file validation helper
 */
function validateUploadedFile($filePath, $context = 'media', $originalName = null) {
    $validator = new FileValidator();
    return $validator->validateFile($filePath, $context, $originalName);
}

/**
 * Check if uploaded file type is valid for context
 */
function isValidFileType($mimeType, $context = 'media') {
    $validator = new FileValidator();
    return $validator->isTypeAllowed($mimeType, $context);
}
?>