<?php
/**
 * Media Management System for PostrMagic
 * Handles user-isolated media storage with full CRUD operations
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/upload-manager.php';
require_once __DIR__ . '/image-processor.php';
require_once __DIR__ . '/file-validator.php';

class MediaManager {
    
    private $pdo;
    private $uploadManager;
    private $imageProcessor;
    private $fileValidator;
    private $isMySQL;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->uploadManager = new UploadManager();
        $this->imageProcessor = new ImageProcessor();
        $this->fileValidator = new FileValidator();
        $this->isMySQL = (DB_TYPE === 'mysql');
    }
    
    /**
     * Upload media file for a specific user
     */
    public function uploadMedia($file, $userId, $context = 'media', $tags = []) {
        // Validate file
        $validation = $this->fileValidator->validateFile(
            $file['tmp_name'], 
            $context, 
            $file['name']
        );
        
        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['error']];
        }
        
        try {
            // Move file to organized structure
            $moveResult = $this->uploadManager->moveUploadedFile(
                $file['tmp_name'],
                $file['name'],
                $context,
                $userId
            );
            
            if (!$moveResult['success']) {
                return ['success' => false, 'error' => $moveResult['error']];
            }
            
            $uploadedPath = $moveResult['path'];
            $filename = $moveResult['filename'];
            $relativePath = $moveResult['relativePath'];
            
            // Process image if it's an image file
            $optimization = null;
            $thumbnailPath = null;
            $webpPath = null;
            $width = null;
            $height = null;
            
            if ($validation['isImage']) {
                // Generate thumbnail
                $thumbnailFilename = 'thumb_' . $filename;
                $thumbnailPath = $this->uploadManager->getFullPath(
                    $thumbnailFilename, 
                    'thumbnail', 
                    $userId
                );
                
                $thumbResult = $this->imageProcessor->generateThumbnail(
                    $uploadedPath, 
                    $thumbnailPath, 
                    300
                );
                
                if (!$thumbResult['success']) {
                    $thumbnailPath = null;
                } else {
                    $thumbnailPath = $this->uploadManager->getRelativePath($thumbnailPath);
                }
                
                // Optimize original image
                $optimizedPath = $this->uploadManager->getFullPath(
                    'opt_' . $filename, 
                    $context, 
                    $userId
                );
                
                $optimization = $this->imageProcessor->optimizeImage(
                    $uploadedPath, 
                    $optimizedPath, 
                    [
                        'maxWidth' => 1920,
                        'maxHeight' => 1080,
                        'createWebP' => true,
                        'quality' => 85
                    ]
                );
                
                if ($optimization['success']) {
                    // Replace original with optimized version
                    if (file_exists($optimizedPath)) {
                        unlink($uploadedPath);
                        rename($optimizedPath, $uploadedPath);
                    }
                    
                    // Set WebP path if created
                    if (isset($optimization['savedFiles']['webp'])) {
                        $webpPath = $this->uploadManager->getRelativePath($optimization['savedFiles']['webp']);
                    }
                    
                    $width = $optimization['newDimensions']['width'];
                    $height = $optimization['newDimensions']['height'];
                }
            }
            
            // Store in database
            $mediaId = $this->saveMediaToDatabase([
                'user_id' => $userId,
                'filename' => $filename,
                'original_filename' => $file['name'],
                'file_path' => $relativePath,
                'file_size' => filesize($uploadedPath),
                'mime_type' => $validation['mimeType'],
                'width' => $width,
                'height' => $height,
                'thumbnail_path' => $thumbnailPath,
                'webp_path' => $webpPath,
                'context' => $context,
                'is_optimized' => $optimization ? 1 : 0,
                'optimization_data' => $optimization ? json_encode($optimization) : null
            ]);
            
            // Add tags if provided
            if (!empty($tags) && $mediaId) {
                $this->addTagsToMedia($mediaId, $tags, $userId);
            }
            
            return [
                'success' => true,
                'media_id' => $mediaId,
                'filename' => $filename,
                'path' => $relativePath,
                'url' => $moveResult['url'],
                'thumbnail_url' => $thumbnailPath ? $this->uploadManager->getFileUrl($thumbnailPath) : null,
                'webp_url' => $webpPath ? $this->uploadManager->getFileUrl($webpPath) : null,
                'optimization' => $optimization
            ];
            
        } catch (Exception $e) {
            error_log("Media upload failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get user's media with pagination and filtering
     */
    public function getUserMedia($userId, $options = []) {
        $options = array_merge([
            'page' => 1,
            'limit' => 20,
            'context' => null,
            'search' => null,
            'tags' => [],
            'sort' => 'upload_date',
            'order' => 'DESC'
        ], $options);
        
        $offset = ($options['page'] - 1) * $options['limit'];
        
        // Build query
        $sql = "SELECT m.*, 
                       GROUP_CONCAT(mt.tag_name) as tags
                FROM user_media m
                LEFT JOIN media_tag_relations mtr ON m.id = mtr.media_id
                LEFT JOIN media_tags mt ON mtr.tag_id = mt.id
                WHERE m.user_id = ?";
        
        $params = [$userId];
        
        // Add filters
        if ($options['context']) {
            $sql .= " AND m.context = ?";
            $params[] = $options['context'];
        }
        
        if ($options['search']) {
            $sql .= " AND (m.original_filename LIKE ? OR m.filename LIKE ?)";
            $searchTerm = '%' . $options['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($options['tags'])) {
            $tagPlaceholders = str_repeat('?,', count($options['tags']) - 1) . '?';
            $sql .= " AND mt.tag_name IN ($tagPlaceholders)";
            $params = array_merge($params, $options['tags']);
        }
        
        $sql .= " GROUP BY m.id";
        
        // Add sorting
        $allowedSorts = ['upload_date', 'filename', 'file_size', 'original_filename'];
        $sort = in_array($options['sort'], $allowedSorts) ? $options['sort'] : 'upload_date';
        $order = strtoupper($options['order']) === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY m.$sort $order";
        
        // Add pagination
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $options['limit'];
        $params[] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $media = $stmt->fetchAll();
        
        // Process results
        foreach ($media as &$item) {
            $item = $this->processMediaItem($item);
        }
        
        // Get total count
        $countSql = "SELECT COUNT(DISTINCT m.id) 
                     FROM user_media m
                     LEFT JOIN media_tag_relations mtr ON m.id = mtr.media_id
                     LEFT JOIN media_tags mt ON mtr.tag_id = mt.id
                     WHERE m.user_id = ?";
        
        $countParams = [$userId];
        if ($options['context']) {
            $countSql .= " AND m.context = ?";
            $countParams[] = $options['context'];
        }
        if ($options['search']) {
            $countSql .= " AND (m.original_filename LIKE ? OR m.filename LIKE ?)";
            $searchTerm = '%' . $options['search'] . '%';
            $countParams[] = $searchTerm;
            $countParams[] = $searchTerm;
        }
        if (!empty($options['tags'])) {
            $tagPlaceholders = str_repeat('?,', count($options['tags']) - 1) . '?';
            $countSql .= " AND mt.tag_name IN ($tagPlaceholders)";
            $countParams = array_merge($countParams, $options['tags']);
        }
        
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($countParams);
        $totalCount = $countStmt->fetchColumn();
        
        return [
            'media' => $media,
            'pagination' => [
                'current_page' => $options['page'],
                'total_pages' => ceil($totalCount / $options['limit']),
                'total_items' => $totalCount,
                'items_per_page' => $options['limit']
            ]
        ];
    }
    
    /**
     * Get single media item by ID (with user permission check)
     */
    public function getMediaById($mediaId, $userId, $isAdmin = false) {
        $sql = "SELECT m.*, 
                       GROUP_CONCAT(mt.tag_name) as tags
                FROM user_media m
                LEFT JOIN media_tag_relations mtr ON m.id = mtr.media_id
                LEFT JOIN media_tags mt ON mtr.tag_id = mt.id
                WHERE m.id = ?";
        
        $params = [$mediaId];
        
        // Add user restriction unless admin
        if (!$isAdmin) {
            $sql .= " AND m.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " GROUP BY m.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $media = $stmt->fetch();
        
        if (!$media) {
            return null;
        }
        
        return $this->processMediaItem($media);
    }
    
    /**
     * Delete media file and database record
     */
    public function deleteMedia($mediaId, $userId, $isAdmin = false) {
        // Get media details first
        $media = $this->getMediaById($mediaId, $userId, $isAdmin);
        
        if (!$media) {
            return ['success' => false, 'error' => 'Media not found or access denied'];
        }
        
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Delete tag relations
            $stmt = $this->pdo->prepare("DELETE FROM media_tag_relations WHERE media_id = ?");
            $stmt->execute([$mediaId]);
            
            // Delete media record
            $stmt = $this->pdo->prepare("DELETE FROM user_media WHERE id = ?");
            $stmt->execute([$mediaId]);
            
            // Commit transaction
            $this->pdo->commit();
            
            // Delete physical files
            $baseUploadDir = rtrim(UPLOAD_DIR, '/');
            $filesToDelete = [
                $baseUploadDir . '/' . $media['file_path']
            ];
            
            if ($media['thumbnail_path']) {
                $filesToDelete[] = $baseUploadDir . '/' . $media['thumbnail_path'];
            }
            
            if ($media['webp_path']) {
                $filesToDelete[] = $baseUploadDir . '/' . $media['webp_path'];
            }
            
            foreach ($filesToDelete as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            
            return ['success' => true, 'message' => 'Media deleted successfully'];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Media deletion failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete media'];
        }
    }
    
    /**
     * Update media metadata
     */
    public function updateMedia($mediaId, $userId, $updates, $isAdmin = false) {
        // Verify ownership
        $media = $this->getMediaById($mediaId, $userId, $isAdmin);
        if (!$media) {
            return ['success' => false, 'error' => 'Media not found or access denied'];
        }
        
        $allowedFields = ['original_filename'];
        $updateFields = [];
        $params = [];
        
        foreach ($updates as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updateFields[] = "$field = ?";
                $params[] = $value;
            }
        }
        
        if (empty($updateFields)) {
            return ['success' => false, 'error' => 'No valid fields to update'];
        }
        
        $params[] = $mediaId;
        
        $sql = "UPDATE user_media SET " . implode(', ', $updateFields) . ", updated_date = CURRENT_TIMESTAMP WHERE id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return ['success' => true, 'message' => 'Media updated successfully'];
        } catch (Exception $e) {
            error_log("Media update failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update media'];
        }
    }
    
    /**
     * Get all media for admin (cross-user access)
     */
    public function getAllMedia($options = []) {
        $options = array_merge([
            'page' => 1,
            'limit' => 50,
            'search' => null,
            'context' => null,
            'user_id' => null,
            'sort' => 'upload_date',
            'order' => 'DESC'
        ], $options);
        
        $offset = ($options['page'] - 1) * $options['limit'];
        
        $sql = "SELECT m.*, 
                       u.name as user_name, 
                       u.email as user_email,
                       GROUP_CONCAT(mt.tag_name) as tags
                FROM user_media m
                LEFT JOIN users u ON m.user_id = u.id
                LEFT JOIN media_tag_relations mtr ON m.id = mtr.media_id
                LEFT JOIN media_tags mt ON mtr.tag_id = mt.id
                WHERE 1=1";
        
        $params = [];
        
        if ($options['search']) {
            $sql .= " AND (m.original_filename LIKE ? OR m.filename LIKE ? OR u.name LIKE ? OR u.email LIKE ?)";
            $searchTerm = '%' . $options['search'] . '%';
            $params = array_fill(0, 4, $searchTerm);
        }
        
        if ($options['context']) {
            $sql .= " AND m.context = ?";
            $params[] = $options['context'];
        }
        
        if ($options['user_id']) {
            $sql .= " AND m.user_id = ?";
            $params[] = $options['user_id'];
        }
        
        $sql .= " GROUP BY m.id";
        
        // Add sorting
        $allowedSorts = ['upload_date', 'filename', 'file_size', 'user_name'];
        $sort = in_array($options['sort'], $allowedSorts) ? $options['sort'] : 'upload_date';
        $order = strtoupper($options['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        if ($sort === 'user_name') {
            $sql .= " ORDER BY u.name $order";
        } else {
            $sql .= " ORDER BY m.$sort $order";
        }
        
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $options['limit'];
        $params[] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $media = $stmt->fetchAll();
        
        // Process results
        foreach ($media as &$item) {
            $item = $this->processMediaItem($item);
        }
        
        return [
            'media' => $media,
            'pagination' => [
                'current_page' => $options['page'],
                'total_pages' => 1, // TODO: Implement count query
                'total_items' => count($media),
                'items_per_page' => $options['limit']
            ]
        ];
    }
    
    /**
     * Save media metadata to database
     */
    private function saveMediaToDatabase($data) {
        $sql = "INSERT INTO user_media (
                    user_id, filename, original_filename, file_path, file_size, 
                    mime_type, width, height, thumbnail_path, webp_path, 
                    context, is_optimized, optimization_data
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['user_id'],
            $data['filename'],
            $data['original_filename'],
            $data['file_path'],
            $data['file_size'],
            $data['mime_type'],
            $data['width'],
            $data['height'],
            $data['thumbnail_path'],
            $data['webp_path'],
            $data['context'],
            $data['is_optimized'],
            $data['optimization_data']
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Process media item for output
     */
    private function processMediaItem($item) {
        // Convert tags string to array
        $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
        
        // Generate URLs
        $item['url'] = $this->uploadManager->getFileUrl($item['file_path']);
        
        if ($item['thumbnail_path']) {
            $item['thumbnail_url'] = $this->uploadManager->getFileUrl($item['thumbnail_path']);
        }
        
        if ($item['webp_path']) {
            $item['webp_url'] = $this->uploadManager->getFileUrl($item['webp_path']);
        }
        
        // Parse optimization data
        if ($item['optimization_data']) {
            $item['optimization'] = json_decode($item['optimization_data'], true);
        }
        
        // Format file size
        $item['formatted_size'] = $this->formatFileSize($item['file_size']);
        
        // Determine if it's an image
        $item['is_image'] = strpos($item['mime_type'], 'image/') === 0;
        
        return $item;
    }
    
    /**
     * Add tags to media
     */
    private function addTagsToMedia($mediaId, $tags, $userId) {
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) continue;
            
            // Get or create tag
            $tagId = $this->getOrCreateTag($tagName, $userId);
            
            // Add relation
            try {
                $stmt = $this->pdo->prepare("INSERT IGNORE INTO media_tag_relations (media_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$mediaId, $tagId]);
            } catch (PDOException $e) {
                // Ignore duplicate key errors
            }
        }
    }
    
    /**
     * Get or create tag for user
     */
    private function getOrCreateTag($tagName, $userId) {
        // Try to find existing tag
        $stmt = $this->pdo->prepare("SELECT id FROM media_tags WHERE user_id = ? AND tag_name = ?");
        $stmt->execute([$userId, $tagName]);
        $tagId = $stmt->fetchColumn();
        
        if ($tagId) {
            // Update usage count
            $stmt = $this->pdo->prepare("UPDATE media_tags SET usage_count = usage_count + 1 WHERE id = ?");
            $stmt->execute([$tagId]);
            return $tagId;
        }
        
        // Create new tag
        $stmt = $this->pdo->prepare("INSERT INTO media_tags (user_id, tag_name, usage_count) VALUES (?, ?, 1)");
        $stmt->execute([$userId, $tagName]);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Format file size for display
     */
    private function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}

/**
 * Helper function to get configured media manager
 */
function getMediaManager() {
    return new MediaManager();
}
?>