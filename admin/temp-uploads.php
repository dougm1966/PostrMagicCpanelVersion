<?php
/**
 * Admin Temporary Uploads Manager
 * Monitor and manage temporary poster uploads
 */

require_once __DIR__ . '/../includes/auth-helper.php';
require_once __DIR__ . '/../includes/temp-upload-manager.php';

// Ensure user is admin
requireAdmin();
$user = getCurrentUser();

$tempUploadManager = new TempUploadManager();

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'cleanup':
            $result = $tempUploadManager->cleanupExpired();
            echo json_encode($result);
            break;
            
        case 'delete':
            $tempFilename = $_POST['temp_filename'] ?? '';
            if ($tempFilename) {
                // Get upload info first
                $upload = $tempUploadManager->getTempUpload($tempFilename);
                if ($upload && file_exists($upload['temp_path'])) {
                    unlink($upload['temp_path']);
                }
                // Delete from database would go here
                echo json_encode(['success' => true, 'message' => 'Upload deleted']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid filename']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    exit;
}

// Handle GET API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api'])) {
    header('Content-Type: application/json');
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'stats':
            $result = $tempUploadManager->getUploadStats();
            echo json_encode($result);
            break;
            
        case 'uploads':
            $uploads = $tempUploadManager->getPendingUploads(50);
            echo json_encode(['success' => true, 'uploads' => $uploads]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    exit;
}

$pageTitle = 'Temporary Uploads (Admin)';
$stats = $tempUploadManager->getUploadStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        'primary-dark': '#4f46e5',
                        secondary: '#ec4899'
                    }
                }
            },
            darkMode: 'class'
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    
    <!-- Include Admin Sidebar -->
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    
    <!-- Main Content -->
    <main class="main-content" id="main-content">
        <div class="p-4 md:p-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Temporary Uploads</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor poster uploads awaiting AI analysis</p>
                </div>
                <div class="flex gap-3">
                    <button id="cleanupBtn" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                        <i class="fas fa-broom mr-2"></i> Cleanup Expired
                    </button>
                    <button id="refreshBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-sync mr-2"></i> Refresh
                    </button>
                </div>
            </div>
            
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-upload text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Uploads</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="totalUploads">
                                <?= $stats['success'] ? $stats['stats']['total_uploads'] : '0' ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-2xl text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Analysis</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="pendingUploads">
                                <?= $stats['success'] && isset($stats['stats']['status_breakdown']['pending']) ? 
                                    $stats['stats']['status_breakdown']['pending'] : '0' ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Recent (24h)</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="recentUploads">
                                <?= $stats['success'] ? $stats['stats']['recent_uploads'] : '0' ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-trash text-2xl text-red-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Expired</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="expiredUploads">
                                <?= $stats['success'] ? $stats['stats']['expired_uploads'] : '0' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Uploads Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Recent Uploads</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        File
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Contact
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Upload Time
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Expires
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="uploadsTable" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
const TempUploadsAdmin = {
    init() {
        this.bindEvents();
        this.loadUploads();
        this.loadStats();
    },
    
    bindEvents() {
        document.getElementById('refreshBtn').addEventListener('click', () => {
            this.loadUploads();
            this.loadStats();
        });
        
        document.getElementById('cleanupBtn').addEventListener('click', () => {
            this.cleanupExpired();
        });
    },
    
    async loadStats() {
        try {
            const response = await fetch('?api=1&action=stats');
            const data = await response.json();
            
            if (data.success) {
                const stats = data.stats;
                document.getElementById('totalUploads').textContent = stats.total_uploads || '0';
                document.getElementById('pendingUploads').textContent = stats.status_breakdown?.pending || '0';
                document.getElementById('recentUploads').textContent = stats.recent_uploads || '0';
                document.getElementById('expiredUploads').textContent = stats.expired_uploads || '0';
            }
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    },
    
    async loadUploads() {
        try {
            const response = await fetch('?api=1&action=uploads');
            const data = await response.json();
            
            if (data.success) {
                this.renderUploads(data.uploads);
            }
        } catch (error) {
            console.error('Failed to load uploads:', error);
        }
    },
    
    renderUploads(uploads) {
        const table = document.getElementById('uploadsTable');
        table.innerHTML = '';
        
        if (uploads.length === 0) {
            table.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray-500">No uploads found</td></tr>';
            return;
        }
        
        uploads.forEach(upload => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <i class="fas fa-file-image text-gray-400 mr-3"></i>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${upload.original_filename}</div>
                            <div class="text-sm text-gray-500">${this.formatFileSize(upload.file_size)}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    ${upload.contact_info || 'Not provided'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${this.getStatusColor(upload.analysis_status)}">
                        ${upload.analysis_status}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${this.formatDate(upload.upload_time)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${this.formatDate(upload.expires_at)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="TempUploadsAdmin.deleteUpload('${upload.temp_filename}')" 
                            class="text-red-600 hover:text-red-900">Delete</button>
                </td>
            `;
            table.appendChild(row);
        });
    },
    
    getStatusColor(status) {
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'processing': 'bg-blue-100 text-blue-800',
            'completed': 'bg-green-100 text-green-800',
            'failed': 'bg-red-100 text-red-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    },
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    },
    
    async cleanupExpired() {
        if (!confirm('Are you sure you want to clean up all expired uploads?')) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'cleanup');
            
            const response = await fetch('temp-uploads.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(`Cleanup completed: ${data.deleted_files} files and ${data.deleted_records} records deleted`, 'success');
                this.loadUploads();
                this.loadStats();
            } else {
                this.showNotification(data.error || 'Cleanup failed', 'error');
            }
        } catch (error) {
            console.error('Cleanup failed:', error);
            this.showNotification('Cleanup failed', 'error');
        }
    },
    
    async deleteUpload(tempFilename) {
        if (!confirm('Are you sure you want to delete this upload?')) {
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('temp_filename', tempFilename);
            
            const response = await fetch('temp-uploads.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Upload deleted successfully', 'success');
                this.loadUploads();
                this.loadStats();
            } else {
                this.showNotification(data.error || 'Delete failed', 'error');
            }
        } catch (error) {
            console.error('Delete failed:', error);
            this.showNotification('Delete failed', 'error');
        }
    },
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        } text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    TempUploadsAdmin.init();
});
</script>

</body>
</html>