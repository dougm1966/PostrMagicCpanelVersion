<?php 
include __DIR__ . '/includes/dashboard-header.php';
?>

<main class="flex-1 p-4 lg:p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
            <button id="uploadMediaBtn" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark flex items-center">
                <i class="fas fa-plus mr-2"></i> Upload Media
            </button>
        </div>
        
        <!-- Media Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <!-- Media Item -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="aspect-square bg-gray-100 relative">
                    <img src="https://example.com/placeholder.jpg" alt="Event poster" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2">
                        <button class="bg-white rounded-full p-1.5 shadow hover:bg-gray-50">
                            <i class="fas fa-ellipsis-v text-gray-600"></i>
                        </button>
                    </div>
                </div>
                <div class="p-3">
                    <p class="text-sm font-medium text-gray-900 truncate">Pool Tournament Poster</p>
                    <p class="text-xs text-gray-500">JPG • 1.2 MB</p>
                </div>
            </div>
            
            <!-- Repeat for other media items -->
            
            <!-- Add more media items here -->
        </div>
    </div>
</main>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-900">Upload Media</h2>
            <button id="closeUploadModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="uploadForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Files</label>
                <div class="flex items-center justify-center w-full">
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
