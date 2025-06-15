<?php
/**
 * User Media Library Backend - Fixed Version
 */

require_once __DIR__ . '/includes/auth-helper.php';

// Ensure user is logged in
requireLogin();
$user = getCurrentUser();
$userId = $_SESSION['user_id'];

$page_title = 'Media Library';
require_once __DIR__ . '/includes/dashboard-header.php';

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = getDBConnection();
        
        switch ($action) {
            case 'delete':
                $mediaId = $_POST['media_id'] ?? null;
                if (!$mediaId) {
                    throw new Exception('Media ID required');
                }
                
                // Check ownership
                $stmt = $pdo->prepare("SELECT * FROM user_media WHERE id = ? AND user_id = ?");
                $stmt->execute([$mediaId, $userId]);
                $media = $stmt->fetch();
                
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
                
                // Insert into database
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
                    $item['thumbnail_url'] = $item['thumbnail_path'] ? '/uploads/' . $item['thumbnail_path'] : $item['url'];
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

// Get initial stats
$stats = ['total_files' => 0, 'total_size' => 0];
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_files, SUM(file_size) as total_size FROM user_media WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    if ($result) {
        $stats = $result;
    }
} catch (Exception $e) {
    // Handle gracefully
}

// Include dashboard header
require_once __DIR__ . '/includes/dashboard-header.php';
?>

<main class="main-content" id="main-content">
    <div class="p-4 md:p-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Media Library</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your uploaded media files</p>
            </div>
            <div class="flex gap-3">
                <button id="uploadBtn" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                    <i class="fas fa-upload mr-2"></i> Upload Files
                </button>
                <button id="refreshBtn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-sync mr-2"></i> Refresh
                </button>
            </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-images text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Your Files</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="totalFiles">
                            <?= number_format($stats['total_files'] ?? 0) ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-hdd text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Storage Used</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="totalSize">
                            <?= formatBytes($stats['total_size'] ?? 0) ?>
                        </p>
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
                <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Search your media files...">
            </div>
        </div>
        
        <!-- Media Grid -->
        <div id="mediaGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
            <!-- Media items will be loaded here -->
        </div>
        
        <!-- Loading indicator -->
        <div id="loadingIndicator" class="text-center py-8 hidden">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
            <p class="text-gray-500 mt-2">Loading media...</p>
        </div>
    </div>
</main>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Upload Media</h3>
                <button id="closeUploadModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="uploadForm" class="space-y-4">
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Drag and drop files here or</p>
                    <label class="cursor-pointer bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark inline-block">
                        <span>Select Files</span>
                        <input type="file" class="hidden" id="fileInput" multiple accept="image/jpeg,image/png,image/webp">
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">JPG, PNG, WebP up to 10MB</p>
                </div>
                
                <div id="fileList" class="space-y-2 max-h-40 overflow-y-auto">
                    <!-- Files will be listed here -->
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="cancelUpload" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Upload Files
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// User Media Library functionality
const MediaLibrary = {
    currentPage: 1,
    itemsPerPage: 20,
    currentSearch: '',
    selectedFiles: [],
    
    init() {
        this.bindEvents();
        this.loadMedia();
        this.loadStats();
    },
    
    bindEvents() {
        // Search
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.currentSearch = e.target.value;
            this.currentPage = 1;
            this.debounceSearch();
        });
        
        // Buttons
        document.getElementById('refreshBtn').addEventListener('click', () => {
            this.loadMedia();
            this.loadStats();
        });
        
        document.getElementById('uploadBtn').addEventListener('click', () => {
            document.getElementById('uploadModal').classList.remove('hidden');
        });
        
        document.getElementById('closeUploadModal').addEventListener('click', () => {
            this.closeUploadModal();
        });
        
        document.getElementById('cancelUpload').addEventListener('click', () => {
            this.closeUploadModal();
        });
        
        // File input
        document.getElementById('fileInput').addEventListener('change', (e) => {
            this.handleFileSelection(e.target.files);
        });
        
        // Upload form
        document.getElementById('uploadForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.uploadFiles();
        });
    },
    
    debounceSearch() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.loadMedia();
        }, 300);
    },
    
    async loadMedia() {
        const loadingIndicator = document.getElementById('loadingIndicator');
        const mediaGrid = document.getElementById('mediaGrid');
        
        loadingIndicator.classList.remove('hidden');
        
        try {
            const params = new URLSearchParams({
                api: '1',
                action: 'media',
                page: this.currentPage,
                limit: this.itemsPerPage,
                search: this.currentSearch
            });
            
            const response = await fetch(`?${params}`);
            const data = await response.json();
            
            if (data.success && data.media) {
                this.renderMedia(data.media);
            } else {
                this.showError(data.error || 'Failed to load media');
            }
        } catch (error) {
            console.error('Failed to load media:', error);
            this.showError('Failed to load media files');
        } finally {
            loadingIndicator.classList.add('hidden');
        }
    },
    
    renderMedia(mediaItems) {
        const grid = document.getElementById('mediaGrid');
        grid.innerHTML = '';
        
        if (mediaItems.length === 0) {
            grid.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500"><i class="fas fa-images text-4xl mb-4"></i><p>No media files found. Upload some files to get started!</p></div>';
            return;
        }
        
        mediaItems.forEach(item => {
            const mediaElement = this.createMediaElement(item);
            grid.appendChild(mediaElement);
        });
    },
    
    createMediaElement(item) {
        const div = document.createElement('div');
        div.className = 'bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden group relative';
        div.innerHTML = `
            <div class="aspect-square bg-gray-100 dark:bg-gray-700 relative">
                <img src="${item.thumbnail_url}" alt="${item.original_filename}" class="w-full h-full object-cover" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMDAgMTAwTDEyNSA3NUwxNzUgMTI1SDE3NVY0MjVIMjVWMTI1TDc1IDc1TDEwMCAxMDBaIiBmaWxsPSIjOUNBM0FGIi8+CjwvZGc+">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <button class="p-2 bg-white bg-opacity-80 rounded-full text-red-600 hover:bg-opacity-100 mr-2" onclick="MediaLibrary.deleteMedia(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="p-2 bg-white bg-opacity-80 rounded-full text-blue-600 hover:bg-opacity-100" onclick="MediaLibrary.viewMedia('${item.url}')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="p-2">
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate" title="${item.original_filename}">${item.original_filename}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">${item.formatted_size}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">${new Date(item.upload_date).toLocaleDateString()}</p>
            </div>
        `;
        return div;
    },
    
    handleFileSelection(files) {
        this.selectedFiles = Array.from(files);
        this.renderFileList();
    },
    
    renderFileList() {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';
        
        this.selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded';
            fileItem.innerHTML = `
                <div>
                    <p class="text-sm font-medium">${file.name}</p>
                    <p class="text-xs text-gray-500">${this.formatBytes(file.size)}</p>
                </div>
                <button type="button" onclick="MediaLibrary.removeFile(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    },
    
    removeFile(index) {
        this.selectedFiles.splice(index, 1);
        this.renderFileList();
    },
    
    async uploadFiles() {
        if (this.selectedFiles.length === 0) {
            this.showError('Please select files to upload');
            return;
        }
        
        const uploadButton = document.querySelector('#uploadForm button[type="submit"]');
        uploadButton.disabled = true;
        uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...';
        
        try {
            for (const file of this.selectedFiles) {
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
            
            this.showSuccess(`${this.selectedFiles.length} file(s) uploaded successfully`);
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
        this.selectedFiles = [];
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

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    MediaLibrary.init();
});
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

require_once __DIR__ . '/includes/dashboard-footer.php';