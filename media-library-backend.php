<?php
/**
 * User Media Library Backend - Fixed Version
 */

require_once __DIR__ . '/includes/auth-helper.php';

// Debug logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_log("=== MEDIA LIBRARY DEBUG START === " . date('Y-m-d H:i:s'));

// Check if this is an API request (GET or POST with api parameter)
$isApiRequest = isset($_GET['api']) || (isset($_POST['api']) && $_SERVER['REQUEST_METHOD'] === 'POST');

// For API requests, just check authentication without updating session
if ($isApiRequest) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    // Don't update session activity for API calls
    $user = ['id' => $_SESSION['user_id']];
    $userId = $_SESSION['user_id'];
} else {
    // For page loads, use normal authentication (updates session)
    error_log("CHECKPOINT B: Before requireLogin");
    requireLogin();
    
    // CRITICAL FIX: Close session to prevent locking
    session_write_close();
    
    error_log("CHECKPOINT C: After requireLogin");
    
    $user = getCurrentUser();
    error_log("CHECKPOINT D: After getCurrentUser");
    
    $userId = $_SESSION['user_id'];
    error_log("CHECKPOINT E: User ID: " . $userId);
    
    $page_title = 'Media Library';
    error_log("CHECKPOINT F: Before dashboard-header include");
    require_once __DIR__ . '/includes/dashboard-header.php';
    error_log("CHECKPOINT G: After dashboard-header include");
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isApiRequest) {
    error_log("POST request received, action: " . ($_POST['action'] ?? 'none'));
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    try {
        error_log("Attempting database connection...");
        $pdo = getDBConnection();
        error_log("Database connection successful!");
        
        switch ($action) {
            case 'delete':
                error_log("Delete action started for media ID: " . ($_POST['media_id'] ?? 'null'));
                $mediaId = $_POST['media_id'] ?? null;
                if (!$mediaId) {
                    throw new Exception('Media ID required');
                }
                
                // Check ownership
                error_log("Checking ownership for media ID: $mediaId, user ID: $userId");
                $stmt = $pdo->prepare("SELECT * FROM user_media WHERE id = ? AND user_id = ?");
                $stmt->execute([$mediaId, $userId]);
                error_log("Ownership query executed, fetching result...");
                $media = $stmt->fetch();
                error_log("Media fetch result: " . ($media ? "found" : "not found"));
                
                if (!$media) {
                    throw new Exception('Media not found or access denied');
                }
                
                // Delete from database
                $stmt = $pdo->prepare("DELETE FROM user_media WHERE id = ? AND user_id = ?");
                $stmt->execute([$mediaId, $userId]);
                
                // Delete physical file if exists
                $filePath = __DIR__ . '/uploads/' . $media['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                echo json_encode(['success' => true, 'message' => 'Media deleted successfully']);
                break;
                
            case 'upload':
                if (!isset($_FILES['file'])) {
                    throw new Exception('No file uploaded');
                }
                
                $file = $_FILES['file'];
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Upload failed with error code: ' . $file['error']);
                }
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($file['type'], $allowedTypes)) {
                    throw new Exception('Invalid file type. Only JPEG, PNG, and WebP are allowed.');
                }
                
                // Validate file size (10MB max)
                $maxSize = 10 * 1024 * 1024;
                if ($file['size'] > $maxSize) {
                    throw new Exception('File too large. Maximum size is 10MB.');
                }
                
                // Create upload directory if it doesn't exist
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $extension;
                $filePath = $uploadDir . $filename;
                
                // Move uploaded file
                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    throw new Exception('Failed to move uploaded file');
                }
                
                // Save to database
                $stmt = $pdo->prepare("INSERT INTO user_media (user_id, filename, original_filename, file_path, file_size, mime_type, context) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $userId,
                    $filename,
                    $file['name'],
                    $filename,
                    $file['size'],
                    $file['type'],
                    'media'
                ]);
                
                echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Handle GET requests for data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api'])) {
    header('Content-Type: application/json');
    
    try {
        $pdo = getDBConnection();
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'media':
                $page = max(1, intval($_GET['page'] ?? 1));
                $limit = min(100, max(10, intval($_GET['limit'] ?? 20)));
                $offset = ($page - 1) * $limit;
                $search = $_GET['search'] ?? '';
                
                $sql = "SELECT * FROM user_media WHERE user_id = ?";
                $params = [$userId];
                
                if ($search) {
                    $sql .= " AND original_filename LIKE ?";
                    $params[] = "%$search%";
                }
                
                $sql .= " ORDER BY upload_date DESC LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $media = $stmt->fetchAll();
                
                // Format file sizes and URLs
                foreach ($media as &$item) {
                    $item['formatted_size'] = formatBytes($item['file_size']);
                    $item['url'] = '/uploads/' . $item['file_path'];
                }
                
                echo json_encode(['success' => true, 'media' => $media]);
                break;
                
            case 'stats':
                $stmt = $pdo->prepare("SELECT 
                    COUNT(*) as total_files,
                    SUM(file_size) as total_size
                    FROM user_media 
                    WHERE user_id = ?");
                $stmt->execute([$userId]);
                $stats = $stmt->fetch();
                
                echo json_encode(['success' => true, 'stats' => $stats]);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Only get initial stats for page loads (not API requests)
if (!$isApiRequest) {
    // Get initial stats
    error_log("Loading initial stats for user: $userId");
    $stats = ['total_files' => 0, 'total_size' => 0];
    try {
        error_log("Getting database connection for stats...");
        $pdo = getDBConnection();
        error_log("Database connection for stats successful!");
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total_files, SUM(file_size) as total_size FROM user_media WHERE user_id = ?");
        $stmt->execute([$userId]);
        error_log("Stats query executed");
        $result = $stmt->fetch();
        error_log("Stats result fetched: " . json_encode($result));
        if ($result) {
            $stats = $result;
        }
    } catch (Exception $e) {
        // Handle gracefully
    }
    
    // ... rest of existing page UI code ...
}
?>

<!-- HTML content for page rendering -->
<main class="main-content" id="main-content">
    <div class="p-4 md:p-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Media Library</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your media files</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fas fa-upload mr-2"></i> Upload
                </button>
                <button onclick="MediaLibrary.loadMedia()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                    <i class="fas fa-sync mr-2"></i> Refresh
                </button>
            </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-image text-indigo-600 text-xl dark:text-indigo-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Files</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="totalFiles"><?= number_format($stats['total_files'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-database text-indigo-600 text-xl dark:text-indigo-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Storage Used</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="totalSize"><?= formatBytes($stats['total_size'] ?? 0) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-cloud-upload-alt text-indigo-600 text-xl dark:text-indigo-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Upload Limit</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">10 MB</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Search -->
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Search media..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
            </div>
        </div>
        
        <!-- Media Grid -->
        <div id="mediaGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- Media items will be loaded here by JavaScript -->
        </div>
        
        <!-- Pagination -->
        <div id="pagination" class="flex justify-center mt-6 space-x-2">
            <!-- Pagination will be loaded here by JavaScript -->
        </div>
    </div>
</main>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Upload Files</h3>
                <button onclick="MediaLibrary.closeUploadModal()" 
                        class="text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select files</label>
                <input type="file" id="fileInput" multiple 
                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-medium
                              file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100
                              dark:file:bg-indigo-900 dark:file:text-indigo-100
                              dark:hover:file:bg-indigo-800">
            </div>
            
            <div id="fileList" class="mb-4 max-h-40 overflow-y-auto">
                <!-- Selected files will be listed here -->
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="MediaLibrary.closeUploadModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancel
                </button>
                <button type="button" id="uploadButton" 
                        onclick="MediaLibrary.uploadFiles()"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    Upload Files
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Media Library JavaScript functionality
const MediaLibrary = {
    currentPage: 1,
    
    init() {
        this.loadMedia();
        this.loadStats();
        
        // Add event listeners
        document.getElementById('searchInput').addEventListener('input', 
            debounce(() => this.loadMedia(), 300));
    },
    
    async loadMedia() {
        const search = document.getElementById('searchInput').value;
        
        try {
            const params = new URLSearchParams({
                api: 1,
                action: 'media',
                page: this.currentPage,
                limit: 20,
                search: search
            });
            
            const response = await fetch(`?${params}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderMedia(data.media);
            } else {
                this.showError(data.error || 'Failed to load media');
            }
        } catch (error) {
            console.error('Failed to load media:', error);
            this.showError('Failed to load media');
        }
    },
    
    renderMedia(media) {
        const grid = document.getElementById('mediaGrid');
        grid.innerHTML = '';
        
        if (media.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-file-image text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No media files</h3>
                    <p class="text-gray-500 dark:text-gray-400">Upload your first file to get started</p>
                </div>
            `;
            return;
        }
        
        media.forEach(item => {
            const card = document.createElement('div');
            card.className = 'bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow';
            card.innerHTML = `
                <div class="relative pb-[100%]"> <!-- Square container -->
                    <img src="${item.url}" alt="${item.original_filename}" 
                         class="absolute inset-0 w-full h-full object-cover" 
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZpbGw9IiM5OTkiPkltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                </div>
                <div class="p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white truncate" title="${item.original_filename}">${item.original_filename}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${item.formatted_size}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">${new Date(item.upload_date).toLocaleDateString()}</p>
                    <div class="flex justify-between items-center mt-3">
                        <button onclick="MediaLibrary.viewMedia('${item.url}')" 
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                            View
                        </button>
                        <button onclick="MediaLibrary.deleteMedia(${item.id})" 
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                            Delete
                        </button>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });
    },
    
    async uploadFiles() {
        const fileInput = document.getElementById('fileInput');
        const files = fileInput.files;
        
        if (files.length === 0) {
            this.showError('Please select at least one file');
            return;
        }
        
        const uploadButton = document.getElementById('uploadButton');
        uploadButton.disabled = true;
        uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...';
        
        try {
            for (let file of files) {
                const formData = new FormData();
                formData.append('action', 'upload');
                formData.append('file', file);
                
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                if (!data.success) {
                    throw new Error(`Failed to upload ${file.name}: ${data.error}`);
                }
            }
            
            this.showSuccess(`${files.length} file(s) uploaded successfully`);
            this.closeUploadModal();
            this.loadMedia();
            this.loadStats();
            
        } catch (error) {
            console.error('Upload failed:', error);
            this.showError(error.message);
        } finally {
            uploadButton.disabled = false;
            uploadButton.innerHTML = 'Upload Files';
        }
    },
    
    closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        document.getElementById('fileList').innerHTML = '';
        document.getElementById('fileInput').value = '';
    },
    
    async deleteMedia(mediaId) {
        if (!confirm('Are you sure you want to delete this media file?')) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('media_id', mediaId);
            
            const response = await fetch('', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                this.loadMedia();
                this.loadStats();
            } else {
                this.showError(data.error || 'Delete failed');
            }
        } catch (error) {
            console.error('Delete failed:', error);
            this.showError('Delete failed');
        }
    },
    
    viewMedia(url) {
        window.open(url, '_blank');
    },
    
    async loadStats() {
        try {
            const response = await fetch('?api=1&action=stats');
            const data = await response.json();
            
            if (data.success) {
                const stats = data.stats;
                document.getElementById('totalFiles').textContent = parseInt(stats.total_files).toLocaleString();
                document.getElementById('totalSize').textContent = this.formatBytes(stats.total_size);
            }
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    },
    
    formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    showSuccess(message) {
        this.showNotification(message, 'success');
    },
    
    showError(message) {
        this.showNotification(message, 'error');
    },
    
    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
};

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Format bytes function
function formatBytes(bytes) {
    if (bytes == 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(log(bytes) / log(k));
    return round(bytes / pow(k, i), 2) . ' ' . sizes[i];
}

// Initialize when DOM is loaded
if (!<?= json_encode($isApiRequest) ?>) {
    document.addEventListener('DOMContentLoaded', () => {
        MediaLibrary.init();
    });
}
</script>

<?php 
// Format bytes function
function formatBytes($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

if (!$isApiRequest) {
    require_once __DIR__ . '/includes/dashboard-footer.php';
}
?>
