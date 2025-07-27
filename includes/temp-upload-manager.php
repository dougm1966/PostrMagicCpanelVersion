<?php
/**
 * Temporary Upload Manager
 * Handles temporary poster uploads before AI analysis
 */

require_once __DIR__ . '/../config/config.php';

class TempUploadManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Store temporary upload metadata
     */
    public function storeTempUpload($metadata) {
        try {
            $sql = "INSERT INTO temporary_uploads (
                temp_filename, 
                original_filename, 
                temp_path, 
                file_size, 
                mime_type, 
                expires_at, 
                contact_info, 
                additional_notes, 
                ip_address, 
                user_agent
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([
                $metadata['temp_filename'],
                $metadata['original_filename'],
                $metadata['temp_path'],
                $metadata['file_size'],
                $metadata['mime_type'],
                date('Y-m-d H:i:s', $metadata['expires_at']),
                $metadata['contact_info'],
                $metadata['additional_notes'],
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
            
            if ($success) {
                return [
                    'success' => true,
                    'upload_id' => $this->pdo->lastInsertId(),
                    'temp_filename' => $metadata['temp_filename']
                ];
            } else {
                return ['success' => false, 'error' => 'Failed to store upload metadata'];
            }
            
        } catch (Exception $e) {
            error_log("Failed to store temp upload: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error'];
        }
    }
    
    /**
     * Get temporary upload by filename
     */
    public function getTempUpload($tempFilename) {
        try {
            $sql = "SELECT * FROM temporary_uploads WHERE temp_filename = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$tempFilename]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to get temp upload: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update analysis status and results
     */
    public function updateAnalysisResult($tempFilename, $status, $result = null) {
        try {
            $sql = "UPDATE temporary_uploads 
                    SET analysis_status = ?, analysis_result = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE temp_filename = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([
                $status,
                $result ? json_encode($result) : null,
                $tempFilename
            ]);
            
            return ['success' => $success];
            
        } catch (Exception $e) {
            error_log("Failed to update analysis result: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error'];
        }
    }
    
    /**
     * Get pending uploads for AI analysis
     */
    public function getPendingUploads($limit = 10) {
        try {
            $sql = "SELECT * FROM temporary_uploads 
                    WHERE analysis_status = 'pending' 
                    AND expires_at > NOW() 
                    ORDER BY upload_time ASC 
                    LIMIT ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to get pending uploads: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Clean up expired temporary uploads
     */
    public function cleanupExpired() {
        try {
            // Get expired uploads to delete files
            $sql = "SELECT temp_path FROM temporary_uploads WHERE expires_at < NOW()";
            $stmt = $this->pdo->query($sql);
            $expiredUploads = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Delete physical files
            $deletedFiles = 0;
            foreach ($expiredUploads as $upload) {
                if (file_exists($upload['temp_path'])) {
                    if (unlink($upload['temp_path'])) {
                        $deletedFiles++;
                    }
                }
            }
            
            // Delete database records
            $sql = "DELETE FROM temporary_uploads WHERE expires_at < NOW()";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $deletedRecords = $stmt->rowCount();
            
            return [
                'success' => true,
                'deleted_files' => $deletedFiles,
                'deleted_records' => $deletedRecords
            ];
            
        } catch (Exception $e) {
            error_log("Failed to cleanup expired uploads: " . $e->getMessage());
            return ['success' => false, 'error' => 'Cleanup failed'];
        }
    }
    
    /**
     * Get upload statistics
     */
    public function getUploadStats() {
        try {
            $stats = [];
            
            // Total uploads
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM temporary_uploads");
            $stats['total_uploads'] = $stmt->fetchColumn();
            
            // Status breakdown
            $stmt = $this->pdo->query("SELECT analysis_status, COUNT(*) as count FROM temporary_uploads GROUP BY analysis_status");
            $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stats['status_breakdown'] = [];
            foreach ($statusCounts as $status) {
                $stats['status_breakdown'][$status['analysis_status']] = $status['count'];
            }
            
            // Recent uploads (last 24 hours)
            $stmt = $this->pdo->query("SELECT COUNT(*) as recent FROM temporary_uploads WHERE upload_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $stats['recent_uploads'] = $stmt->fetchColumn();
            
            // Expired uploads
            $stmt = $this->pdo->query("SELECT COUNT(*) as expired FROM temporary_uploads WHERE expires_at < NOW()");
            $stats['expired_uploads'] = $stmt->fetchColumn();
            
            return ['success' => true, 'stats' => $stats];
            
        } catch (Exception $e) {
            error_log("Failed to get upload stats: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to get statistics'];
        }
    }
    
    /**
     * Generate secure temporary filename
     */
    public static function generateTempFilename($originalFilename) {
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        return 'temp_poster_' . uniqid() . '_' . time() . '.' . $extension;
    }
    
    /**
     * Check if contact info looks like email or phone
     */
    public static function classifyContactInfo($contactInfo) {
        if (empty($contactInfo)) {
            return null;
        }
        
        // Check if it looks like an email
        if (filter_var($contactInfo, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        
        // Check if it looks like a phone number (basic check)
        $cleanContact = preg_replace('/[^\d]/', '', $contactInfo);
        if (strlen($cleanContact) >= 10 && strlen($cleanContact) <= 15) {
            return 'phone';
        }
        
        return 'unknown';
    }
}

/**
 * Global helper function
 */
function getTempUploadManager() {
    static $manager = null;
    if ($manager === null) {
        $manager = new TempUploadManager();
    }
    return $manager;
}
