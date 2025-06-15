<?php
/**
 * Tag Management System for PostrMagic
 * Handles user-isolated tagging with CRUD operations
 */

require_once __DIR__ . '/../config/config.php';

class TagManager {
    
    private $pdo;
    private $isMySQL;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->isMySQL = (DB_TYPE === 'mysql');
    }
    
    /**
     * Get all tags for a user
     */
    public function getUserTags($userId, $options = []) {
        $options = array_merge([
            'search' => null,
            'sort' => 'tag_name',
            'order' => 'ASC',
            'limit' => null
        ], $options);
        
        $sql = "SELECT id, tag_name, tag_color, usage_count, created_date 
                FROM media_tags 
                WHERE user_id = ?";
        
        $params = [$userId];
        
        if ($options['search']) {
            $sql .= " AND tag_name LIKE ?";
            $params[] = '%' . $options['search'] . '%';
        }
        
        // Add sorting
        $allowedSorts = ['tag_name', 'usage_count', 'created_date'];
        $sort = in_array($options['sort'], $allowedSorts) ? $options['sort'] : 'tag_name';
        $order = strtoupper($options['order']) === 'DESC' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $sort $order";
        
        if ($options['limit']) {
            $sql .= " LIMIT ?";
            $params[] = $options['limit'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Create a new tag for a user
     */
    public function createTag($userId, $tagName, $tagColor = '#6366f1') {
        $tagName = trim($tagName);
        
        // Validate tag name
        if (empty($tagName)) {
            return ['success' => false, 'error' => 'Tag name cannot be empty'];
        }
        
        if (strlen($tagName) > 50) {
            return ['success' => false, 'error' => 'Tag name cannot exceed 50 characters'];
        }
        
        // Check for duplicates
        if ($this->tagExists($userId, $tagName)) {
            return ['success' => false, 'error' => 'Tag already exists'];
        }
        
        // Validate color
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $tagColor)) {
            $tagColor = '#6366f1'; // Default color
        }
        
        try {
            $stmt = $this->pdo->prepare("INSERT INTO media_tags (user_id, tag_name, tag_color) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $tagName, $tagColor]);
            
            $tagId = $this->pdo->lastInsertId();
            
            return [
                'success' => true,
                'tag_id' => $tagId,
                'message' => 'Tag created successfully'
            ];
        } catch (Exception $e) {
            error_log("Tag creation failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create tag'];
        }
    }
    
    /**
     * Update an existing tag
     */
    public function updateTag($tagId, $userId, $updates) {
        // Verify ownership
        if (!$this->userOwnsTag($userId, $tagId)) {
            return ['success' => false, 'error' => 'Tag not found or access denied'];
        }
        
        $allowedFields = ['tag_name', 'tag_color'];
        $updateFields = [];
        $params = [];
        
        foreach ($updates as $field => $value) {
            if (in_array($field, $allowedFields)) {
                if ($field === 'tag_name') {
                    $value = trim($value);
                    if (empty($value)) {
                        return ['success' => false, 'error' => 'Tag name cannot be empty'];
                    }
                    if (strlen($value) > 50) {
                        return ['success' => false, 'error' => 'Tag name cannot exceed 50 characters'];
                    }
                    // Check for duplicates (excluding current tag)
                    if ($this->tagExistsExcluding($userId, $value, $tagId)) {
                        return ['success' => false, 'error' => 'Tag name already exists'];
                    }
                }
                
                if ($field === 'tag_color' && !preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                    $value = '#6366f1'; // Default color
                }
                
                $updateFields[] = "$field = ?";
                $params[] = $value;
            }
        }
        
        if (empty($updateFields)) {
            return ['success' => false, 'error' => 'No valid fields to update'];
        }
        
        $params[] = $tagId;
        $params[] = $userId;
        
        try {
            $sql = "UPDATE media_tags SET " . implode(', ', $updateFields) . " WHERE id = ? AND user_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return ['success' => true, 'message' => 'Tag updated successfully'];
        } catch (Exception $e) {
            error_log("Tag update failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update tag'];
        }
    }
    
    /**
     * Delete a tag
     */
    public function deleteTag($tagId, $userId) {
        // Verify ownership
        if (!$this->userOwnsTag($userId, $tagId)) {
            return ['success' => false, 'error' => 'Tag not found or access denied'];
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // Delete tag relations first
            $stmt = $this->pdo->prepare("DELETE FROM media_tag_relations WHERE tag_id = ?");
            $stmt->execute([$tagId]);
            
            // Delete the tag
            $stmt = $this->pdo->prepare("DELETE FROM media_tags WHERE id = ? AND user_id = ?");
            $stmt->execute([$tagId, $userId]);
            
            $this->pdo->commit();
            
            return ['success' => true, 'message' => 'Tag deleted successfully'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Tag deletion failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete tag'];
        }
    }
    
    /**
     * Get tag by ID (with ownership check)
     */
    public function getTag($tagId, $userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM media_tags WHERE id = ? AND user_id = ?");
        $stmt->execute([$tagId, $userId]);
        
        return $stmt->fetch();
    }
    
    /**
     * Add tags to media
     */
    public function addTagsToMedia($mediaId, $tags, $userId) {
        $results = [];
        
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) continue;
            
            // Get or create tag
            $tagId = $this->getOrCreateTag($tagName, $userId);
            
            // Add relation
            try {
                $stmt = $this->pdo->prepare("INSERT IGNORE INTO media_tag_relations (media_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$mediaId, $tagId]);
                
                $results[] = ['tag_name' => $tagName, 'success' => true];
            } catch (PDOException $e) {
                $results[] = ['tag_name' => $tagName, 'success' => false, 'error' => $e->getMessage()];
            }
        }
        
        return $results;
    }
    
    /**
     * Remove tags from media
     */
    public function removeTagsFromMedia($mediaId, $tagIds, $userId) {
        // Verify all tags belong to the user
        $placeholders = str_repeat('?,', count($tagIds) - 1) . '?';
        $stmt = $this->pdo->prepare("SELECT id FROM media_tags WHERE id IN ($placeholders) AND user_id = ?");
        $stmt->execute(array_merge($tagIds, [$userId]));
        $validTagIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($validTagIds)) {
            return ['success' => false, 'error' => 'No valid tags found'];
        }
        
        try {
            // Remove relations
            $placeholders = str_repeat('?,', count($validTagIds) - 1) . '?';
            $stmt = $this->pdo->prepare("DELETE FROM media_tag_relations WHERE media_id = ? AND tag_id IN ($placeholders)");
            $stmt->execute(array_merge([$mediaId], $validTagIds));
            
            // Update usage counts
            $this->updateTagUsageCounts($validTagIds);
            
            return ['success' => true, 'removed_count' => $stmt->rowCount()];
        } catch (Exception $e) {
            error_log("Tag removal failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to remove tags'];
        }
    }
    
    /**
     * Get tags for a specific media item
     */
    public function getMediaTags($mediaId) {
        $sql = "SELECT mt.id, mt.tag_name, mt.tag_color 
                FROM media_tags mt
                JOIN media_tag_relations mtr ON mt.id = mtr.tag_id
                WHERE mtr.media_id = ?
                ORDER BY mt.tag_name";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$mediaId]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get tag usage statistics for a user
     */
    public function getTagStats($userId) {
        $sql = "SELECT 
                    COUNT(*) as total_tags,
                    AVG(usage_count) as avg_usage,
                    MAX(usage_count) as max_usage,
                    SUM(usage_count) as total_usage
                FROM media_tags 
                WHERE user_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $stats = $stmt->fetch();
        
        // Get most used tags
        $sql = "SELECT tag_name, usage_count 
                FROM media_tags 
                WHERE user_id = ? 
                ORDER BY usage_count DESC 
                LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $stats['most_used'] = $stmt->fetchAll();
        
        return $stats;
    }
    
    /**
     * Search tags by name
     */
    public function searchTags($userId, $query, $limit = 20) {
        $sql = "SELECT id, tag_name, tag_color, usage_count 
                FROM media_tags 
                WHERE user_id = ? AND tag_name LIKE ?
                ORDER BY usage_count DESC, tag_name ASC
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, '%' . $query . '%', $limit]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get suggested tags based on media content
     */
    public function getSuggestedTags($userId, $mediaType = null, $limit = 10) {
        $sql = "SELECT DISTINCT mt.tag_name, mt.tag_color, mt.usage_count
                FROM media_tags mt
                JOIN media_tag_relations mtr ON mt.id = mtr.tag_id
                JOIN user_media um ON mtr.media_id = um.id
                WHERE mt.user_id = ?";
        
        $params = [$userId];
        
        if ($mediaType) {
            $sql .= " AND um.mime_type LIKE ?";
            $params[] = $mediaType . '%';
        }
        
        $sql .= " ORDER BY mt.usage_count DESC, mt.tag_name ASC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Bulk update tag colors
     */
    public function bulkUpdateTagColors($userId, $colorMappings) {
        try {
            $this->pdo->beginTransaction();
            
            foreach ($colorMappings as $tagId => $color) {
                if ($this->userOwnsTag($userId, $tagId) && preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                    $stmt = $this->pdo->prepare("UPDATE media_tags SET tag_color = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$color, $tagId, $userId]);
                }
            }
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Tag colors updated successfully'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Bulk tag color update failed: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update tag colors'];
        }
    }
    
    /**
     * Check if tag exists for user
     */
    private function tagExists($userId, $tagName) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM media_tags WHERE user_id = ? AND tag_name = ?");
        $stmt->execute([$userId, $tagName]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Check if tag exists for user (excluding specific tag ID)
     */
    private function tagExistsExcluding($userId, $tagName, $excludeTagId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM media_tags WHERE user_id = ? AND tag_name = ? AND id != ?");
        $stmt->execute([$userId, $tagName, $excludeTagId]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Check if user owns a tag
     */
    private function userOwnsTag($userId, $tagId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM media_tags WHERE id = ? AND user_id = ?");
        $stmt->execute([$tagId, $userId]);
        return $stmt->fetchColumn() > 0;
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
     * Update usage counts for tags
     */
    private function updateTagUsageCounts($tagIds) {
        foreach ($tagIds as $tagId) {
            // Count current usage
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM media_tag_relations WHERE tag_id = ?");
            $stmt->execute([$tagId]);
            $count = $stmt->fetchColumn();
            
            // Update usage count
            $stmt = $this->pdo->prepare("UPDATE media_tags SET usage_count = ? WHERE id = ?");
            $stmt->execute([$count, $tagId]);
        }
    }
}

/**
 * Helper function to get configured tag manager
 */
function getTagManager() {
    return new TagManager();
}
?>