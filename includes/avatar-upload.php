<?php
/**
 * Avatar Upload Handler
 * Secure avatar upload functionality with image validation and resizing
 */

require_once __DIR__ . '/auth.php';

/**
 * Handle avatar upload
 * @param array $file $_FILES array for the uploaded file
 * @param int $userId User ID
 * @return array Result array with success status and message
 */
function handleAvatarUpload($file, $userId) {
    $result = ['success' => false, 'message' => '', 'filename' => null];
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $result['message'] = 'No file was uploaded';
        return $result;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'File upload error: ' . getUploadErrorMessage($file['error']);
        return $result;
    }
    
    // Validate file size (2MB max for avatars)
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        $result['message'] = 'Avatar file size must be less than 2MB';
        return $result;
    }
    
    // Validate file type using multiple methods
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = null;
    
    // Try multiple methods to detect mime type
    if (function_exists('mime_content_type')) {
        $fileType = mime_content_type($file['tmp_name']);
    } elseif (function_exists('finfo_file')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    } else {
        // Fallback: check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extensionMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        $fileType = $extensionMap[$extension] ?? null;
    }
    
    if (!$fileType || !in_array($fileType, $allowedTypes)) {
        $result['message'] = 'Invalid file type. Please upload JPEG, PNG, GIF, or WebP images only';
        return $result;
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = dirname(__DIR__) . '/uploads/avatars';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            $result['message'] = 'Failed to create upload directory';
            return $result;
        }
    }
    
    // Generate unique filename
    $extension = getImageExtension($fileType);
    $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . '/' . $filename;
    
    // Get current avatar to delete later
    $currentUser = getCurrentUser();
    $oldAvatar = $currentUser['avatar'] ?? null;
    
    // Check if GD extension is available for image processing
    if (extension_loaded('gd')) {
        // Process and resize the image
        if (processAvatarImage($file['tmp_name'], $filePath, $fileType)) {
            // Update database
            if (updateUserAvatar($userId, $filename)) {
                // Delete old avatar if it exists
                if ($oldAvatar && file_exists($uploadDir . '/' . $oldAvatar)) {
                    unlink($uploadDir . '/' . $oldAvatar);
                }
                
                $result['success'] = true;
                $result['message'] = 'Avatar uploaded successfully';
                $result['filename'] = $filename;
                $result['url'] = '/uploads/avatars/' . $filename;
            } else {
                // Clean up uploaded file if database update failed
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $result['message'] = 'Failed to update avatar in database';
            }
        } else {
            $result['message'] = 'Failed to process avatar image';
        }
    } else {
        // GD extension not available - just copy the file without processing
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Update database
            if (updateUserAvatar($userId, $filename)) {
                // Delete old avatar if it exists
                if ($oldAvatar && file_exists($uploadDir . '/' . $oldAvatar)) {
                    unlink($uploadDir . '/' . $oldAvatar);
                }
                
                $result['success'] = true;
                $result['message'] = 'Avatar uploaded successfully (no resizing - GD extension not available)';
                $result['filename'] = $filename;
                $result['url'] = '/uploads/avatars/' . $filename;
            } else {
                // Clean up uploaded file if database update failed
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $result['message'] = 'Failed to update avatar in database';
            }
        } else {
            $result['message'] = 'Failed to save avatar file';
        }
    }
    
    return $result;
}

/**
 * Process avatar image (resize and optimize)
 * @param string $sourcePath Source image path
 * @param string $destPath Destination path
 * @param string $mimeType Image MIME type
 * @return bool Success status
 */
function processAvatarImage($sourcePath, $destPath, $mimeType) {
    $avatarSize = 200; // 200x200 pixels
    
    // Create image resource from source
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    $sourceWidth = imagesx($sourceImage);
    $sourceHeight = imagesy($sourceImage);
    
    // Calculate crop dimensions for square avatar
    $cropSize = min($sourceWidth, $sourceHeight);
    $cropX = ($sourceWidth - $cropSize) / 2;
    $cropY = ($sourceHeight - $cropSize) / 2;
    
    // Create new image for avatar
    $avatarImage = imagecreatetruecolor($avatarSize, $avatarSize);
    
    // Handle transparency for PNG and GIF
    if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
        imagealphablending($avatarImage, false);
        imagesavealpha($avatarImage, true);
        $transparent = imagecolorallocatealpha($avatarImage, 255, 255, 255, 127);
        imagefilledrectangle($avatarImage, 0, 0, $avatarSize, $avatarSize, $transparent);
    }
    
    // Crop and resize
    imagecopyresampled(
        $avatarImage, $sourceImage,
        0, 0, $cropX, $cropY,
        $avatarSize, $avatarSize, $cropSize, $cropSize
    );
    
    // Save the processed image
    $success = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $success = imagejpeg($avatarImage, $destPath, 90);
            break;
        case 'image/png':
            $success = imagepng($avatarImage, $destPath, 8);
            break;
        case 'image/gif':
            $success = imagegif($avatarImage, $destPath);
            break;
        case 'image/webp':
            $success = imagewebp($avatarImage, $destPath, 90);
            break;
    }
    
    // Clean up memory
    imagedestroy($sourceImage);
    imagedestroy($avatarImage);
    
    return $success;
}

/**
 * Get file extension for image type
 * @param string $mimeType MIME type
 * @return string File extension
 */
function getImageExtension($mimeType) {
    switch ($mimeType) {
        case 'image/jpeg':
            return 'jpg';
        case 'image/png':
            return 'png';
        case 'image/gif':
            return 'gif';
        case 'image/webp':
            return 'webp';
        default:
            return 'jpg';
    }
}

/**
 * Get human-readable upload error message
 * @param int $errorCode PHP upload error code
 * @return string Error message
 */
function getUploadErrorMessage($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return 'File is too large (server limit)';
        case UPLOAD_ERR_FORM_SIZE:
            return 'File is too large (form limit)';
        case UPLOAD_ERR_PARTIAL:
            return 'File was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension';
        default:
            return 'Unknown upload error';
    }
}

/**
 * Delete user avatar
 * @param int $userId User ID
 * @return bool Success status
 */
function deleteUserAvatar($userId) {
    $currentUser = getCurrentUser();
    if (!$currentUser || $currentUser['id'] != $userId) {
        return false;
    }
    
    $avatar = $currentUser['avatar'];
    if ($avatar) {
        $uploadDir = dirname(__DIR__) . '/uploads/avatars';
        $filePath = $uploadDir . '/' . $avatar;
        
        // Delete file if it exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Update database
        return updateUserAvatar($userId, null);
    }
    
    return true;
}
?>