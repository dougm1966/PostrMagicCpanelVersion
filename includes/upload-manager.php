<?php
/**
 * Upload Directory Manager for PostrMagic
 * Handles organized file storage structure and permissions
 */

require_once __DIR__ . '/../config/config.php';

class UploadManager {
    
    private $baseUploadDir;
    private $tempDir;
    
    public function __construct() {
        $this->baseUploadDir = rtrim(UPLOAD_DIR, '/');
        $this->tempDir = $this->baseUploadDir . '/temp';
    }
    
    /**
     * Initialize upload directory structure
     */
    public function initializeDirectories() {
        $directories = [
            $this->baseUploadDir,
            $this->tempDir,
            $this->baseUploadDir . '/media',
            $this->baseUploadDir . '/posters',
            $this->baseUploadDir . '/thumbnails',
            $this->baseUploadDir . '/optimized',
            $this->baseUploadDir . '/webp',
            $this->baseUploadDir . '/profiles'
        ];
        
        $results = [];
        
        foreach ($directories as $dir) {
            $result = $this->createDirectoryIfNotExists($dir);
            $results[$dir] = $result;
        }
        
        // Create .htaccess for security
        $this->createSecurityFiles();
        
        return $results;
    }
    
    /**
     * Get organized upload path for a file
     */
    public function getUploadPath($context = 'media', $userId = null, $subDirectory = null) {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        // Base path by context
        switch ($context) {
            case 'poster':
                $basePath = $this->baseUploadDir . '/posters';
                break;
            case 'temp':
                $basePath = $this->tempDir;
                break;
            case 'thumbnail':
                $basePath = $this->baseUploadDir . '/thumbnails';
                break;
            case 'webp':
                $basePath = $this->baseUploadDir . '/webp';
                break;
            case 'profile':
                $basePath = $this->baseUploadDir . '/profiles';
                break;
            default:
                $basePath = $this->baseUploadDir . '/media';
        }
        
        // Add date structure for organization (except temp and profiles)
        if (!in_array($context, ['temp', 'profile'])) {
            $basePath .= "/$year/$month/$day";
        }
        
        // Add user directory for user-specific content
        if ($userId && $context !== 'temp') {
            $basePath .= "/user_$userId";
        }
        
        // Add subdirectory if specified
        if ($subDirectory) {
            $basePath .= "/$subDirectory";
        }
        
        // Ensure directory exists
        $this->createDirectoryIfNotExists($basePath);
        
        return $basePath;
    }
    
    /**
     * Generate unique filename
     */
    public function generateUniqueFilename($originalName, $context = 'media', $userId = null) {
        $pathInfo = pathinfo($originalName);
        $extension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : '';
        $baseName = isset($pathInfo['filename']) ? $pathInfo['filename'] : 'file';
        
        // Clean base name
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $baseName = substr($baseName, 0, 30); // Limit length
        
        // Add timestamp and random component for uniqueness
        $timestamp = time();
        $random = bin2hex(random_bytes(4));
        
        $filename = "{$baseName}_{$timestamp}_{$random}";
        if ($extension) {
            $filename .= ".$extension";
        }
        
        // Ensure uniqueness in target directory
        $uploadPath = $this->getUploadPath($context, $userId);
        $fullPath = "$uploadPath/$filename";
        $counter = 1;
        
        while (file_exists($fullPath)) {
            $filename = "{$baseName}_{$timestamp}_{$random}_{$counter}";
            if ($extension) {
                $filename .= ".$extension";
            }
            $fullPath = "$uploadPath/$filename";
            $counter++;
        }
        
        return $filename;
    }
    
    /**
     * Get full file path
     */
    public function getFullPath($filename, $context = 'media', $userId = null, $subDirectory = null) {
        $uploadPath = $this->getUploadPath($context, $userId, $subDirectory);
        return "$uploadPath/$filename";
    }
    
    /**
     * Get relative path from upload directory
     */
    public function getRelativePath($fullPath) {
        return str_replace($this->baseUploadDir . '/', '', $fullPath);
    }
    
    /**
     * Get URL for uploaded file
     */
    public function getFileUrl($relativePath) {
        $baseUrl = rtrim(BASE_URL, '/');
        return "$baseUrl/uploads/$relativePath";
    }
    
    /**
     * Clean up temporary files older than specified time
     */
    public function cleanupTempFiles($maxAge = 172800) { // 48 hours default
        $cutoffTime = time() - $maxAge;
        $cleaned = [];
        
        if (!is_dir($this->tempDir)) {
            return $cleaned;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->tempDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getMTime() < $cutoffTime) {
                $filePath = $file->getPathname();
                if (unlink($filePath)) {
                    $cleaned[] = $filePath;
                }
            }
        }
        
        return $cleaned;
    }
    
    /**
     * Get disk usage statistics
     */
    public function getDiskUsage() {
        $stats = [
            'total_size' => 0,
            'file_count' => 0,
            'contexts' => []
        ];
        
        $contexts = ['media', 'posters', 'thumbnails', 'webp', 'temp', 'profiles'];
        
        foreach ($contexts as $context) {
            $contextPath = $this->getContextBasePath($context);
            $contextStats = $this->getDirectorySize($contextPath);
            
            $stats['contexts'][$context] = $contextStats;
            $stats['total_size'] += $contextStats['size'];
            $stats['file_count'] += $contextStats['files'];
        }
        
        return $stats;
    }
    
    /**
     * Create directory if it doesn't exist
     */
    private function createDirectoryIfNotExists($path) {
        if (is_dir($path)) {
            return ['status' => 'exists', 'path' => $path];
        }
        
        if (mkdir($path, 0755, true)) {
            // Set proper permissions for cPanel environment
            chmod($path, 0755);
            return ['status' => 'created', 'path' => $path];
        } else {
            return ['status' => 'error', 'path' => $path, 'message' => 'Failed to create directory'];
        }
    }
    
    /**
     * Create security files (.htaccess, index.php)
     */
    private function createSecurityFiles() {
        // Create .htaccess to prevent direct access to certain file types
        $htaccessContent = "# PostrMagic Upload Security\n";
        $htaccessContent .= "Options -Indexes\n";
        $htaccessContent .= "Options -ExecCGI\n";
        $htaccessContent .= "\n# Allow image files\n";
        $htaccessContent .= "<FilesMatch \"\\.(jpg|jpeg|png|webp|gif)$\">\n";
        $htaccessContent .= "    Order Allow,Deny\n";
        $htaccessContent .= "    Allow from all\n";
        $htaccessContent .= "</FilesMatch>\n";
        $htaccessContent .= "\n# Block PHP and other executable files\n";
        $htaccessContent .= "<FilesMatch \"\\.(php|phtml|php3|php4|php5|pl|py|jsp|asp|sh|cgi)$\">\n";
        $htaccessContent .= "    Order Allow,Deny\n";
        $htaccessContent .= "    Deny from all\n";
        $htaccessContent .= "</FilesMatch>\n";
        
        $htaccessPath = $this->baseUploadDir . '/.htaccess';
        file_put_contents($htaccessPath, $htaccessContent);
        
        // Create index.php to prevent directory listing
        $indexContent = "<?php\n// PostrMagic Upload Directory - Access Denied\nheader('HTTP/1.0 403 Forbidden');\nexit('Access denied');\n?>";
        $indexPath = $this->baseUploadDir . '/index.php';
        file_put_contents($indexPath, $indexContent);
        
        // Create index files in subdirectories
        $subDirs = ['media', 'posters', 'thumbnails', 'webp', 'temp', 'profiles'];
        foreach ($subDirs as $subDir) {
            $subDirPath = $this->baseUploadDir . '/' . $subDir;
            if (is_dir($subDirPath)) {
                file_put_contents($subDirPath . '/index.php', $indexContent);
            }
        }
    }
    
    /**
     * Get context base path
     */
    private function getContextBasePath($context) {
        switch ($context) {
            case 'poster':
            case 'posters':
                return $this->baseUploadDir . '/posters';
            case 'temp':
                return $this->tempDir;
            case 'thumbnails':
                return $this->baseUploadDir . '/thumbnails';
            case 'webp':
                return $this->baseUploadDir . '/webp';
            case 'profiles':
                return $this->baseUploadDir . '/profiles';
            default:
                return $this->baseUploadDir . '/media';
        }
    }
    
    /**
     * Get directory size and file count
     */
    private function getDirectorySize($directory) {
        $size = 0;
        $files = 0;
        
        if (!is_dir($directory)) {
            return ['size' => 0, 'files' => 0];
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
                $files++;
            }
        }
        
        return ['size' => $size, 'files' => $files];
    }
    
    /**
     * Move uploaded file to organized structure
     */
    public function moveUploadedFile($sourcePath, $originalName, $context = 'media', $userId = null) {
        $filename = $this->generateUniqueFilename($originalName, $context, $userId);
        $destinationPath = $this->getFullPath($filename, $context, $userId);
        
        if (move_uploaded_file($sourcePath, $destinationPath)) {
            chmod($destinationPath, 0644); // Set appropriate permissions
            
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $destinationPath,
                'relativePath' => $this->getRelativePath($destinationPath),
                'url' => $this->getFileUrl($this->getRelativePath($destinationPath))
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Failed to move uploaded file'
            ];
        }
    }
}

/**
 * Helper function to get configured upload manager
 */
function getUploadManager() {
    return new UploadManager();
}

/**
 * Helper function to initialize upload directories
 */
function initializeUploadDirectories() {
    $manager = new UploadManager();
    return $manager->initializeDirectories();
}
?>