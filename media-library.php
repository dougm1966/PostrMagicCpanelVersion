<?php 
include __DIR__ . '/includes/dashboard-header.php';
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
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="cancelUpload" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none">
                        Upload Files
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Initial Posts Modal -->
<div id="initialPostsModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-purple-500 via-pink-500 to-orange-400 p-4 flex items-center justify-between">
            <h3 class="text-xl font-semibold text-white">Your Initial Posts</h3>
            <button id="closeInitialPostsModal" class="text-white hover:bg-white/20 rounded-full p-1 transition-colors duration-200">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-64px)]">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="initialPostsContainer">
                <!-- Initial posts will be inserted here by JavaScript -->
            </div>
        </div>
    </div>
</div>

<main class="flex-1 p-4 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Media Library</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your uploaded media files</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="openInitialPostsModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center whitespace-nowrap">
                    <i data-lucide="share-2" class="w-4 h-4 mr-2"></i>
                    View Initial Posts
                </button>
                <button id="uploadMediaBtn" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark flex items-center whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i> Upload Media
                </button>
                <button onclick="openInitialPostsModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center whitespace-nowrap">
                    <i data-lucide="share-2" class="w-4 h-4 mr-2"></i>
                    View Initial Posts
                </button>
            </div>
        </div>
            
            <!-- Search and Filter -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="mediaSearch" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" placeholder="Search media...">
                </div>
                <select id="mediaFilter" class="block w-full sm:w-auto pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary focus:border-primary rounded-md sm:text-sm dark:bg-gray-700 dark:text-white">
                    <option value="all">All Media</option>
                    <option value="image">Images</option>
                    <option value="video">Videos</option>
                    <option value="document">Documents</option>
                </select>
            </div>
            
            <!-- Media Grid -->
            <div id="mediaGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <!-- Media items will be loaded here -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden group">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 relative">
                        <img src="https://via.placeholder.com/300" alt="Placeholder" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <button class="p-2 bg-white bg-opacity-80 rounded-full text-gray-800 hover:bg-opacity-100 mr-2">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="p-2 bg-white bg-opacity-80 rounded-full text-gray-800 hover:bg-opacity-100">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">example-image.jpg</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">2.4 MB • 1920x1080</p>
                    </div>
                </div>
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
    const uploadBtn = document.getElementById('uploadMediaBtn');
    const closeUploadBtn = document.getElementById('closeUploadModal');
    
    // Initial Posts Modal
    const initialPostsModal = document.getElementById('initialPostsModal');
    const closeInitialPostsBtn = document.getElementById('closeInitialPostsModal');
    const initialPostsContainer = document.getElementById('initialPostsContainer');
    
    // Sample initial posts data - replace with actual data from your backend
    const initialPosts = [
        {
            id: 1,
            platform: 'facebook',
            title: 'Facebook Post',
            content: 'Check out our latest event! Join us for an amazing time with friends and family.',
            image: 'https://via.placeholder.com/300x200?text=Facebook+Post',
            engagement: '245 likes • 42 comments • 31 shares'
        },
        {
            id: 2,
            platform: 'instagram',
            title: 'Instagram Post',
            content: 'Join us for an unforgettable experience! #Event #FunTimes',
            image: 'https://via.placeholder.com/300x300?text=Instagram+Post',
            engagement: '1,234 likes • 89 comments'
        },
        {
            id: 3,
            platform: 'twitter',
            title: 'Twitter Post',
            content: 'Excited to announce our upcoming event! Save the date and join us for a day full of fun activities. #EventAlert',
            image: 'https://via.placeholder.com/300x200?text=Twitter+Post',
            engagement: '567 retweets • 1.2k likes'
        }
    ];
    
    // Function to render initial posts
    function renderInitialPosts() {
        if (!initialPostsContainer) return;
        
        initialPostsContainer.innerHTML = initialPosts.map(post => `
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full ${post.platform === 'facebook' ? 'bg-blue-100 dark:bg-blue-900/30' : post.platform === 'instagram' ? 'bg-pink-100 dark:bg-pink-900/30' : 'bg-blue-50 dark:bg-blue-900/20'} flex items-center justify-center">
                                <i class="fab fa-${post.platform} ${post.platform === 'facebook' ? 'text-blue-600 dark:text-blue-400' : post.platform === 'instagram' ? 'text-pink-600 dark:text-pink-400' : 'text-blue-500 dark:text-blue-400'}"></i>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">${post.title}</span>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Just now</span>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">${post.content}</p>
                    <div class="aspect-w-16 aspect-h-9 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden mb-3">
                        <img src="${post.image}" alt="${post.title}" class="w-full h-40 object-cover">
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${post.engagement}</div>
                </div>
            </div>
        `).join('');
        
        // Re-initialize Lucide icons after dynamic content is added
        lucide.createIcons();
    }
    
    // Function to open initial posts modal
    window.openInitialPostsModal = function() {
        initialPostsModal.classList.remove('hidden');
        renderInitialPosts();
        document.body.style.overflow = 'hidden';
    };
    
    // Function to close initial posts modal
    function closeInitialPostsModal() {
        initialPostsModal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking the close button
    if (closeInitialPostsBtn) {
        closeInitialPostsBtn.addEventListener('click', closeInitialPostsModal);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === initialPostsModal) {
            closeInitialPostsModal();
        }
    });
    
    // Open upload modal
    if (uploadBtn) {
        uploadBtn.addEventListener('click', () => {
            uploadModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close upload modal
    function closeUploadModal() {
        uploadModal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    if (closeUploadBtn) {
        closeUploadBtn.addEventListener('click', closeUploadModal);
    }
    const cancelUpload = document.getElementById('cancelUpload');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const mediaUploadForm = document.getElementById('mediaUploadForm');
    
    // Open modal
    uploadBtn.addEventListener('click', () => {
        uploadModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
    
    // Close modal
    function closeUploadModal() {
        uploadModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    closeModal.addEventListener('click', closeUploadModal);
    cancelUpload.addEventListener('click', closeUploadModal);
    
    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        fileList.innerHTML = ''; // Clear previous files
        
        if (this.files.length > 0) {
            Array.from(this.files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded';
                fileItem.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-file-image text-gray-500 mr-2"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-xs">${file.name}</span>
                    </div>
                    <span class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                `;
                fileList.appendChild(fileItem);
            });
        }
    });
    
    // Handle form submission
    mediaUploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Add your upload logic here
        console.log('Uploading files...');
        // Close modal after upload
        setTimeout(closeUploadModal, 1000);
    });
    
    // Close upload modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === uploadModal) {
            closeUploadModal();
        }
    });
    
    // Close modals when pressing Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (!initialPostsModal.classList.contains('hidden')) {
                closeInitialPostsModal();
            } else if (!uploadModal.classList.contains('hidden')) {
                closeUploadModal();
            }
        }
    });
    
    function openInitialPostsModal() {
        initialPostsModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeInitialPostsModal() {
        initialPostsModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    closeInitialPostsModal.addEventListener('click', closeInitialPostsModal);
    
    window.addEventListener('click', (e) => {
        if (e.target === initialPostsModal) {
            closeInitialPostsModal();
        }
    });
});
</script>

                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF up to 10MB</p>
                        </div>
                        <input type="file" class="hidden" multiple>
                    </label>
                </div>
            </div>
            
            <div>
                <label for="mediaTags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                <input type="text" id="mediaTags" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Add tags to organize your media">
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" id="cancelUpload" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-dark">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// JavaScript to handle modal
const uploadModal = document.getElementById('uploadModal');
const uploadBtn = document.getElementById('uploadMediaBtn');
const closeModal = document.getElementById('closeUploadModal');
const cancelBtn = document.getElementById('cancelUpload');

uploadBtn.addEventListener('click', () => {
    uploadModal.classList.remove('hidden');
});

closeModal.addEventListener('click', () => {
    uploadModal.classList.add('hidden');
});

cancelBtn.addEventListener('click', () => {
    uploadModal.classList.add('hidden');
});
</script>

<?php include __DIR__ . '/includes/dashboard-footer.php'; ?>
