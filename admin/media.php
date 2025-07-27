<?php
/**
 * Admin Media Library - Fixed Version
 */

require_once __DIR__ . '/../includes/auth-helper.php';

// Check if this is an API request (GET or POST with api parameter)
$isApiRequest = isset($_GET['api']) || (isset($_POST['api']) && $_SERVER['REQUEST_METHOD'] === 'POST');

// For API requests, just check admin access without updating session
if ($isApiRequest) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    // Don't update session activity for API calls
    $user = ['id' => $_SESSION['user_id'], 'is_admin' => true];
} else {
    // For page loads, use normal admin authentication (updates session)
    requireAdmin();
    $user = getCurrentUser();
    
    $page_title = 'Media Library (Admin)';
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isApiRequest) {
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
                
                // Get file info first
                $stmt = $pdo->prepare("SELECT * FROM user_media WHERE id = ?");
                $stmt->execute([$mediaId]);
                $media = $stmt->fetch();
                
                if (!$media) {
                    throw new Exception('Media not found');
                }
                
                // Delete from database
                $stmt = $pdo->prepare("DELETE FROM user_media WHERE id = ?");
                $stmt->execute([$mediaId]);
                
                // Delete physical file if exists
                $filePath = __DIR__ . '/../uploads/' . $media['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                echo json_encode(['success' => true, 'message' => 'Media deleted successfully']);
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
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'media':
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = min(100, max(10, intval($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;
            $search = $_GET['search'] ?? '';
            
            $sql = "SELECT m.*, u.username 
                   FROM user_media m 
                   LEFT JOIN users u ON m.user_id = u.id 
                   WHERE 1=1";
            $params = [];
            
            if ($search) {
                $sql .= " AND (m.original_filename LIKE ? OR u.username LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            $sql .= " ORDER BY m.upload_date DESC LIMIT ? OFFSET ?";
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
            $stmt = $pdo->query("SELECT 
                COUNT(*) as total_files,
                SUM(file_size) as total_size,
                COUNT(DISTINCT user_id) as users_with_media
                FROM user_media");
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
    $stmt = $pdo->query("SELECT COUNT(*) as total_files, SUM(file_size) as total_size FROM user_media");
    $result = $stmt->fetch();
    if ($result) {
        $stats = $result;
    }
} catch (Exception $e) {
    // Handle gracefully
}

// Include dashboard header
require_once __DIR__ . '/../includes/dashboard-header.php';
?>

<main class="main-content" id="main-content">
    <div class="p-4 md:p-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Media Library (Admin)</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage all user media files</p>
            </div>
            <div class="flex gap-3">
                <button id="refreshBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-sync mr-2"></i> Refresh
                </button>
            </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-images text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Files</p>
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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Size</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="totalSize">
                            <?= formatBytes($stats['total_size'] ?? 0) ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">System Status</p>
                        <p class="text-xl font-semibold text-green-600">Active</p>
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
                <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Search media files...">
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
        
        <!-- Pagination -->
        <div id="pagination" class="mt-8 flex items-center justify-center">
            <!-- Pagination will be inserted here -->
        </div>
    </div>
</main>

<script>
// Admin Media Library functionality
const AdminMediaLibrary = {
    currentPage: 1,
    itemsPerPage: 20,
    currentSearch: '',
    
    init() {
        this.bindEvents();
        this.loadMedia();
        this.loadStats();
    },
    
    bindEvents() {
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.currentSearch = e.target.value;
            this.currentPage = 1;
            this.debounceSearch();
        });
        
        document.getElementById('refreshBtn').addEventListener('click', () => {
            this.loadMedia();
            this.loadStats();
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
            grid.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500"><i class="fas fa-images text-4xl mb-4"></i><p>No media files found</p></div>';
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
                    <button class="p-2 bg-white bg-opacity-80 rounded-full text-red-600 hover:bg-opacity-100" onclick="AdminMediaLibrary.deleteMedia(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="p-2">
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate" title="${item.original_filename}">${item.original_filename}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">${item.username || 'Unknown User'}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">${item.formatted_size}</p>
            </div>
        `;
        return div;
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
    AdminMediaLibrary.init();
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

require_once __DIR__ . '/../includes/dashboard-footer.php';
