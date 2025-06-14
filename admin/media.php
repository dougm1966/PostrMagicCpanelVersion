<?php 
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
requireAdmin();

// Set page title
$page_title = "Media Library";

// Include dashboard header (includes sidebars)
require_once __DIR__ . '/../includes/dashboard-header.php';
?>

<!-- Upload Media Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Upload Media</h3>
                <button id="closeUploadModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="mediaUploadForm" class="space-y-4">
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Drag and drop files here or</p>
                    <label class="cursor-pointer bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark inline-block">
                        <span>Select Files</span>
                        <input type="file" class="hidden" id="fileInput" multiple>
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">PNG, JPG, GIF up to 10MB</p>
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

<main class="main-content" id="main-content">
    <div class="p-4 md:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Media Library</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage all your media files in one place</p>
            </div>
            <div class="flex space-x-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:flex-none">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-800 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm" placeholder="Search media...">
                </div>
                <button id="openUploadModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <i data-lucide="upload" class="mr-2 h-4 w-4"></i>
                    Upload
                </button>
            </div>
        </div>

        <!-- Media Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <!-- Media Item -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="aspect-w-16 aspect-h-9 bg-gray-100 dark:bg-gray-700 relative">
                    <img src="https://via.placeholder.com/300x200" alt="Media thumbnail" class="w-full h-32 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <button class="p-2 rounded-full bg-white bg-opacity-80 text-gray-800 hover:bg-opacity-100 mr-2">
                            <i data-lucide="eye" class="h-4 w-4"></i>
                        </button>
                        <button class="p-2 rounded-full bg-white bg-opacity-80 text-gray-800 hover:bg-opacity-100 mr-2">
                            <i data-lucide="copy" class="h-4 w-4"></i>
                        </button>
                        <button class="p-2 rounded-full bg-white bg-opacity-80 text-red-600 hover:bg-opacity-100">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
                <div class="p-2">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">example-image.jpg</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">2.4 MB • 2 days ago</p>
                </div>
                <div class="absolute top-2 right-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary/10 text-primary">
                        JPG
                    </span>
                </div>
            </div>
            
            <!-- More media items would be dynamically generated here -->
            
            <!-- Upload Card -->
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex flex-col items-center justify-center p-6 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" id="uploadTrigger">
                <i data-lucide="plus" class="h-8 w-8 text-gray-400 mb-2"></i>
                <p class="text-sm text-gray-600 dark:text-gray-300">Upload new file</p>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-8 flex items-center justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-medium">1</span> to <span class="font-medium">12</span> of <span class="font-medium">124</span> results
            </div>
            <div class="flex space-x-2">
                <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Previous
                </button>
                <button class="px-3 py-1 border border-primary-500 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100">
                    1
                </button>
                <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    2
                </button>
                <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    3
                </button>
                <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Next
                </button>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Upload Modal
    const uploadModal = document.getElementById('uploadModal');
    const openUploadModal = document.getElementById('openUploadModal');
    const closeUploadModal = document.getElementById('closeUploadModal');
    const cancelUpload = document.getElementById('cancelUpload');
    const uploadTrigger = document.getElementById('uploadTrigger');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    
    // Open modal when clicking upload button
    if (openUploadModal) {
        openUploadModal.addEventListener('click', function() {
            uploadModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close modal functions
    function closeModal() {
        uploadModal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    if (closeUploadModal) closeUploadModal.addEventListener('click', closeModal);
    if (cancelUpload) cancelUpload.addEventListener('click', closeModal);
    
    // Close modal when clicking outside
    uploadModal.addEventListener('click', function(e) {
        if (e.target === uploadModal) {
            closeModal();
        }
    });
    
    // Handle file selection
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            updateFileList(e.target.files);
        });
    }
    
    // Handle drag and drop
    if (uploadTrigger) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadTrigger.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadTrigger.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadTrigger.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadTrigger.classList.add('border-primary', 'bg-primary/5');
        }

        function unhighlight() {
            uploadTrigger.classList.remove('border-primary', 'bg-primary/5');
        }

        uploadTrigger.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            updateFileList(files);
            // Open the upload modal when files are dropped
            uploadModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    // Update file list display
    function updateFileList(files) {
        fileList.innerHTML = '';
        
        if (files.length === 0) return;
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded';
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'flex items-center';
            
            const fileIcon = document.createElement('div');
            fileIcon.className = 'mr-2';
            fileIcon.innerHTML = getFileIcon(file);
            
            const fileName = document.createElement('div');
            fileName.className = 'text-sm text-gray-700 dark:text-gray-300';
            fileName.textContent = file.name;
            
            const fileSize = document.createElement('div');
            fileSize.className = 'text-xs text-gray-500 dark:text-gray-400';
            fileSize.textContent = formatFileSize(file.size);
            
            fileInfo.appendChild(fileIcon);
            fileInfo.appendChild(fileName);
            
            const fileActions = document.createElement('button');
            fileActions.className = 'text-red-500 hover:text-red-700';
            fileActions.innerHTML = '<i data-lucide="x" class="h-4 w-4"></i>';
            fileActions.addEventListener('click', function() {
                fileItem.remove();
            });
            
            fileItem.appendChild(fileInfo);
            fileItem.appendChild(fileActions);
            
            fileList.appendChild(fileItem);
        }
        
        // Refresh Lucide icons
        lucide.createIcons();
    }
    
    // Helper function to get file icon based on file type
    function getFileIcon(file) {
        const fileType = file.type.split('/')[0];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (fileType === 'image') {
            return '<i data-lucide="image" class="h-5 w-5 text-blue-500"></i>';
        } else if (fileType === 'video') {
            return '<i data-lucide="video" class="h-5 w-5 text-purple-500"></i>';
        } else if (fileType === 'audio') {
            return '<i data-lucide="music" class="h-5 w-5 text-yellow-500"></i>';
        } else if (fileExtension === 'pdf') {
            return '<i data-lucide="file-text" class="h-5 w-5 text-red-500"></i>';
        } else if (['doc', 'docx'].includes(fileExtension)) {
            return '<i data-lucide="file-text" class="h-5 w-5 text-blue-600"></i>';
        } else if (['xls', 'xlsx'].includes(fileExtension)) {
            return '<i data-lucide="file-text" class="h-5 w-5 text-green-600"></i>';
        } else if (['zip', 'rar', '7z'].includes(fileExtension)) {
            return '<i data-lucide="folder-archive" class="h-5 w-5 text-yellow-600"></i>';
        } else {
            return '<i data-lucide="file" class="h-5 w-5 text-gray-500"></i>';
        }
    }
    
    // Helper function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Form submission
    const mediaUploadForm = document.getElementById('mediaUploadForm');
    if (mediaUploadForm) {
        mediaUploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would typically handle the file upload via AJAX
            alert('File upload functionality would be implemented here');
            closeModal();
        });
    }
});
</script>

<?php 
// Include dashboard footer
require_once __DIR__ . '/../includes/dashboard-footer.php'; 
?>
