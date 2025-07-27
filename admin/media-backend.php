<?php
/**
 * Admin Media Library Backend
 * Provides admin access to all user media with management capabilities
 */

require_once __DIR__ . '/../includes/auth-helper.php';
require_once __DIR__ . '/../includes/media-manager.php';
require_once __DIR__ . '/../includes/tag-manager.php';
require_once __DIR__ . '/../includes/migration-runner.php';

// Check if this is an API request (to avoid session updates)
$isApiRequest = isset($_GET['api']) || (isset($_POST['action']) && $_SERVER['REQUEST_METHOD'] === 'POST');

if ($isApiRequest) {
    // For API requests, only check authentication without updating session
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        exit();
    }
    $user = [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['user_email'] ?? '',
        'username' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user'
    ];
} else {
    // For page loads, perform normal authentication with session updates
    requireAdmin();
    $user = getCurrentUser();
}

// Initialize database if needed
try {
    $runner = new MigrationRunner();
    $migrationResults = $runner->runMigrations();
} catch (Exception $e) {
    error_log("Migration failed: " . $e->getMessage());
}

// Initialize upload directories
try {
    $uploadManager = getUploadManager();
    $uploadManager->initializeDirectories();
} catch (Exception $e) {
    error_log("Upload directory initialization failed: " . $e->getMessage());
}

$mediaManager = getMediaManager();
$tagManager = getTagManager();

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'delete':
            handleAdminDelete();
            break;
            
        case 'bulk_delete':
            handleBulkDelete();
            break;
            
        case 'update':
            handleAdminUpdate();
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    exit;
}

// Handle GET requests for data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api'])) {
    header('Content-Type: application/json');
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'media':
            handleGetAllMedia();
            break;
            
        case 'stats':
            handleGetStats();
            break;
            
        case 'users':
            handleGetUsers();
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    exit;
}

function handleAdminDelete() {
    global $mediaManager;
    
    $mediaId = $_POST['media_id'] ?? null;
    
    if (!$mediaId) {
        echo json_encode(['success' => false, 'error' => 'Media ID required']);
        return;
    }
    
    // Admin can delete any media (pass isAdmin=true)
    $result = $mediaManager->deleteMedia($mediaId, 0, true);
    echo json_encode($result);
}

function handleBulkDelete() {
    global $mediaManager;
    
    $mediaIds = $_POST['media_ids'] ?? [];
    
    if (empty($mediaIds)) {
        echo json_encode(['success' => false, 'error' => 'No media IDs provided']);
        return;
    }
    
    $results = [];
    $successCount = 0;
    
    foreach ($mediaIds as $mediaId) {
        $result = $mediaManager->deleteMedia($mediaId, 0, true);
        $results[] = ['media_id' => $mediaId, 'result' => $result];
        if ($result['success']) {
            $successCount++;
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Deleted $successCount of " . count($mediaIds) . " items",
        'results' => $results
    ]);
}

function handleAdminUpdate() {
    global $mediaManager;
    
    $mediaId = $_POST['media_id'] ?? null;
    $updates = [];
    
    if (!$mediaId) {
        echo json_encode(['success' => false, 'error' => 'Media ID required']);
        return;
    }
    
    if (isset($_POST['filename'])) {
        $updates['original_filename'] = $_POST['filename'];
    }
    
    // Admin can update any media (pass isAdmin=true)
    $result = $mediaManager->updateMedia($mediaId, 0, $updates, true);
    echo json_encode($result);
}

function handleGetAllMedia() {
    global $mediaManager;
    
    $options = [
        'page' => $_GET['page'] ?? 1,
        'limit' => $_GET['limit'] ?? 50,
        'search' => $_GET['search'] ?? null,
        'context' => $_GET['context'] ?? null,
        'user_id' => $_GET['user_id'] ?? null,
        'sort' => $_GET['sort'] ?? 'upload_date',
        'order' => $_GET['order'] ?? 'DESC'
    ];
    
    $result = $mediaManager->getAllMedia($options);
    echo json_encode($result);
}

function handleGetStats() {
    global $mediaManager;
    
    try {
        $pdo = getDBConnection();
        
        // Get total counts
        $stmt = $pdo->query("SELECT 
            COUNT(*) as total_files,
            SUM(file_size) as total_size,
            COUNT(DISTINCT user_id) as users_with_media,
            AVG(file_size) as avg_file_size
            FROM user_media");
        $stats = $stmt->fetch();
        
        // Get context breakdown
        $stmt = $pdo->query("SELECT context, COUNT(*) as count, SUM(file_size) as size 
                           FROM user_media GROUP BY context");
        $contextStats = $stmt->fetchAll();
        
        // Get recent uploads
        $stmt = $pdo->query("SELECT DATE(upload_date) as date, COUNT(*) as count 
                           FROM user_media 
                           WHERE upload_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                           GROUP BY DATE(upload_date) 
                           ORDER BY date DESC");
        $recentUploads = $stmt->fetchAll();
        
        // Get top uploaders
        $stmt = $pdo->query("SELECT u.name, u.email, COUNT(*) as upload_count, SUM(um.file_size) as total_size
                           FROM user_media um
                           JOIN users u ON um.user_id = u.id
                           GROUP BY um.user_id
                           ORDER BY upload_count DESC
                           LIMIT 10");
        $topUploaders = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'context_breakdown' => $contextStats,
            'recent_uploads' => $recentUploads,
            'top_uploaders' => $topUploaders
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to get statistics']);
    }
}

function handleGetUsers() {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->query("SELECT u.id, u.name, u.email, 
                           COUNT(um.id) as media_count,
                           SUM(um.file_size) as total_size,
                           MAX(um.upload_date) as last_upload
                           FROM users u
                           LEFT JOIN user_media um ON u.id = um.user_id
                           WHERE u.role != 'admin'
                           GROUP BY u.id
                           ORDER BY media_count DESC");
        
        $users = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'users' => $users]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to get users']);
    }
}

// Get initial data for the page
$page_title = 'Media Library (Admin)';
$initialStats = [];

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) as total_files, SUM(file_size) as total_size FROM user_media");
    $initialStats = $stmt->fetch();
} catch (Exception $e) {
    $initialStats = ['total_files' => 0, 'total_size' => 0];
}

// Only include dashboard header for page loads (not API requests)
if (!$isApiRequest) {
    require_once __DIR__ . '/../includes/dashboard-header.php';
}
?>

    <!-- Main Content -->
    <main class="main-content" id="main-content">
        <div class="p-4 md:p-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Media Library (Admin)</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage all user media files</p>
                </div>
                <div class="flex gap-3">
                    <button id="bulkDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 hidden">
                        <i class="fas fa-trash mr-2"></i> Delete Selected
                    </button>
                    <button id="viewStatsBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-chart-bar mr-2"></i> View Stats
                    </button>
                </div>
            </div>
            
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-images text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Files</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="totalFiles">
                                <?= number_format($initialStats['total_files'] ?? 0) ?>
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
                                <?= formatBytes($initialStats['total_size'] ?? 0) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-2xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Users</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="activeUsers">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-2xl text-orange-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Uploads</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="todayUploads">-</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Search and Filter -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="mediaSearch" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Search media, users, filenames...">
                </div>
                <select id="contextFilter" class="block w-full sm:w-auto pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary focus:border-primary rounded-md sm:text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">All Contexts</option>
                    <option value="media">Media</option>
                    <option value="poster">Posters</option>
                </select>
                <select id="userFilter" class="block w-full sm:w-auto pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary focus:border-primary rounded-md sm:text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">All Users</option>
                    <!-- Will be populated by JavaScript -->
                </select>
                <button id="selectAllBtn" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700">
                    Select All
                </button>
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
            <div id="pagination" class="mt-8 flex items-center justify-between">
                <!-- Pagination will be inserted here -->
            </div>
        </div>
    </main>

<script>
// Admin Media Library functionality
const AdminMediaLibrary = {
    currentPage: 1,
    itemsPerPage: 50,
    currentFilters: {},
    selectedItems: new Set(),
    
    init() {
        this.bindEvents();
        this.loadMedia();
        this.loadUsers();
        this.loadStats();
    },
    
    bindEvents() {
        // Search and filters
        document.getElementById('mediaSearch').addEventListener('input', () => this.debounceSearch());
        document.getElementById('contextFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('userFilter').addEventListener('change', () => this.applyFilters());
        
        // Bulk actions
        document.getElementById('selectAllBtn').addEventListener('click', () => this.toggleSelectAll());
        document.getElementById('bulkDeleteBtn').addEventListener('click', () => this.bulkDelete());
        document.getElementById('viewStatsBtn').addEventListener('click', () => this.showStats());
    },
    
    async loadMedia() {
        const loadingIndicator = document.getElementById('loadingIndicator');
        loadingIndicator.classList.remove('hidden');
        
        try {
            const params = new URLSearchParams({
                api: '1',
                action: 'media',
                page: this.currentPage,
                limit: this.itemsPerPage,
                ...this.currentFilters
            });
            
            const response = await fetch(`?${params}`);
            const data = await response.json();
            
            if (data.media) {
                this.renderMedia(data.media);
                this.renderPagination(data.pagination);
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
        
        mediaItems.forEach(item => {
            const mediaElement = this.createMediaElement(item);
            grid.appendChild(mediaElement);
        });
        
        if (mediaItems.length === 0) {
            grid.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">No media files found</div>';
        }
        
        this.updateBulkActions();
    },
    
    createMediaElement(item) {
        const div = document.createElement('div');
        div.className = 'bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden group relative';
        div.innerHTML = `
            <div class="absolute top-2 left-2 z-10">
                <input type="checkbox" class="media-checkbox" data-media-id="${item.id}" onchange="AdminMediaLibrary.toggleSelect(${item.id})">
            </div>
            <div class="aspect-square bg-gray-100 dark:bg-gray-700 relative">
                <img src="${item.thumbnail_url || item.url}" alt="${item.original_filename}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <button class="p-2 bg-white bg-opacity-80 rounded-full text-gray-800 hover:bg-opacity-100 mr-2" onclick="AdminMediaLibrary.viewMedia(${item.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="p-2 bg-white bg-opacity-80 rounded-full text-red-600 hover:bg-opacity-100" onclick="AdminMediaLibrary.deleteMedia(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="p-2">
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">${item.original_filename}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">${item.user_name || 'Unknown User'}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">${item.formatted_size}</p>
            </div>
        `;
        return div;
    },
    
    toggleSelect(mediaId) {
        if (this.selectedItems.has(mediaId)) {
            this.selectedItems.delete(mediaId);
        } else {
            this.selectedItems.add(mediaId);
        }
        this.updateBulkActions();
    },
    
    toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.media-checkbox');
        const allSelected = checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allSelected;
            const mediaId = parseInt(checkbox.dataset.mediaId);
            if (!allSelected) {
                this.selectedItems.add(mediaId);
            } else {
                this.selectedItems.delete(mediaId);
            }
        });
        
        this.updateBulkActions();
    },
    
    updateBulkActions() {
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectAllBtn = document.getElementById('selectAllBtn');
        
        if (this.selectedItems.size > 0) {
            bulkDeleteBtn.classList.remove('hidden');
            bulkDeleteBtn.textContent = `Delete ${this.selectedItems.size} Selected`;
            selectAllBtn.textContent = 'Deselect All';
        } else {
            bulkDeleteBtn.classList.add('hidden');
            selectAllBtn.textContent = 'Select All';
        }
    },
    
    async bulkDelete() {
        if (this.selectedItems.size === 0) return;
        
        if (!confirm(`Are you sure you want to delete ${this.selectedItems.size} media files?`)) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'bulk_delete');
            formData.append('media_ids', JSON.stringify(Array.from(this.selectedItems)));
            
            const response = await fetch('media-backend.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                this.selectedItems.clear();
                this.loadMedia();
                this.loadStats();
            } else {
                this.showError(data.error || 'Bulk delete failed');
            }
        } catch (error) {
            console.error('Bulk delete failed:', error);
            this.showError('Bulk delete failed');
        }
    },
    
    async loadUsers() {
        try {
            const response = await fetch('?api=1&action=users');
            const data = await response.json();
            
            if (data.success) {
                const userFilter = document.getElementById('userFilter');
                userFilter.innerHTML = '<option value="">All Users</option>';
                
                data.users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.media_count} files)`;
                    userFilter.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Failed to load users:', error);
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
                // Update other stats as needed
            }
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    },
    
    formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    showSuccess(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    },
    
    showError(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    AdminMediaLibrary.init();
    lucide.createIcons();
});
</script>

<?php 
// Only include dashboard footer for page loads (not API requests)
if (!$isApiRequest) {
    // Include dashboard footer
    require_once __DIR__ . '/../includes/dashboard-footer.php'; 
}
?>

<?php
function formatBytes($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>
