<?php
/**
 * Anonymous Poster Upload System
 * Allows users to upload posters for AI analysis without account registration
 */

require_once 'includes/file-validator.php';
require_once 'includes/upload-manager.php';
require_once 'includes/temp-upload-manager.php';
require_once 'includes/migration-runner.php';
require_once 'includes/vision-processor.php';
require_once 'includes/content-generator.php';
require_once 'config/config.php';

// Ensure database is up to date
try {
    $runner = new MigrationRunner();
    $runner->runMigrations();
} catch (Exception $e) {
    error_log("Migration failed: " . $e->getMessage());
}

// Initialize managers
$uploadManager = new UploadManager();
$tempUploadManager = new TempUploadManager();

// Handle form submission
$uploadResult = null;
$uploadError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['poster'])) {
    try {
        $validator = new FileValidator();
        $file = $_FILES['poster'];
        
        // Validate the uploaded poster
        $validation = $validator->validateFile($file, 'poster');
        
        if (!$validation['valid']) {
            $uploadError = $validation['error'];
        } else {
            // Create temporary upload directory for posters
            $tempDir = $uploadManager->getUploadPath('temp/posters', 0);
            
            // Generate unique filename for temporary storage
            $tempFileName = TempUploadManager::generateTempFilename($file['name']);
            $tempFilePath = $tempDir . '/' . $tempFileName;
            
            // Ensure directory exists
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Move uploaded file to temporary location
            if (move_uploaded_file($file['tmp_name'], $tempFilePath)) {
                // Store metadata for the temporary upload
                $metadata = [
                    'original_filename' => $file['name'],
                    'temp_filename' => $tempFileName,
                    'temp_path' => $tempFilePath,
                    'file_size' => $file['size'],
                    'mime_type' => $file['type'],
                    'upload_time' => time(),
                    'expires_at' => time() + (48 * 60 * 60), // 48 hours
                    'contact_info' => $_POST['contact_info'] ?? null,
                    'additional_notes' => $_POST['notes'] ?? null
                ];
                
                // Store in database
                $dbResult = $tempUploadManager->storeTempUpload($metadata);
                
                if ($dbResult['success']) {
                    $contactType = TempUploadManager::classifyContactInfo($metadata['contact_info']);
                    $notificationMessage = $metadata['contact_info'] ? 
                        "You will receive a notification when the analysis is complete." :
                        "Check back later or provide contact info for notifications.";
                    
                    $uploadResult = [
                        'success' => true,
                        'temp_id' => $tempFileName,
                        'upload_id' => $dbResult['upload_id'],
                        'message' => 'Poster uploaded successfully! AI analysis will begin shortly.',
                        'next_steps' => $notificationMessage,
                        'contact_type' => $contactType
                    ];
                    
                    // Process with AI pipeline
                    processWithAI($tempFilePath, $dbResult['upload_id'], $metadata);
                    
                } else {
                    // Clean up uploaded file if database storage failed
                    if (file_exists($tempFilePath)) {
                        unlink($tempFilePath);
                    }
                    $uploadError = 'Failed to process upload. Please try again.';
                }
                
            } else {
                $uploadError = 'Failed to save uploaded file. Please try again.';
            }
        }
    } catch (Exception $e) {
        error_log("Poster upload error: " . $e->getMessage());
        $uploadError = 'Upload failed due to server error. Please try again.';
    }
}

/**
 * Process uploaded poster with AI pipeline
 */
function processWithAI($imagePath, $uploadId, $metadata) {
    try {
        // Initialize processors
        $visionProcessor = new VisionProcessor();
        $contentGenerator = new ContentGenerator();
        
        // Step 1: Vision Analysis & Category Detection
        $visionResult = $visionProcessor->processPoster($imagePath, [
            'upload_id' => $uploadId,
            'contact_info' => $metadata['contact_info'] ?? null
        ]);
        
        if (!$visionResult['success']) {
            error_log("Vision processing failed for upload {$uploadId}: " . $visionResult['error']);
            return;
        }
        
        // Check if event was rejected (doesn't fit any category)
        if ($visionResult['rejected']) {
            error_log("Event rejected for upload {$uploadId}: " . $visionResult['reason']);
            // Event is stored in rejected_events table for admin review
            return;
        }
        
        $extractedData = $visionResult['data'];
        $eventCategory = $visionResult['category'];
        
        // Step 2: Generate Content for Social Media
        // Start with free Facebook post (3 free posts per user initially)
        $contentResult = $contentGenerator->generateContent(
            'facebook', 
            $extractedData, 
            $eventCategory,
            [
                'upload_id' => $uploadId,
                'user_id' => null, // Anonymous upload
                'contact_info' => $metadata['contact_info'] ?? null
            ]
        );
        
        if ($contentResult['success']) {
            error_log("Content generated successfully for upload {$uploadId}");
            
            // TODO: Send notification to user if contact info provided
            if (!empty($metadata['contact_info'])) {
                // Queue notification email/SMS
                error_log("Should notify {$metadata['contact_info']} about completed analysis for upload {$uploadId}");
            }
        } else {
            error_log("Content generation failed for upload {$uploadId}: " . $contentResult['error']);
        }
        
    } catch (Exception $e) {
        error_log("AI processing failed for upload {$uploadId}: " . $e->getMessage());
    }
}

$pageTitle = 'Upload Poster for Analysis';
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
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-magic text-3xl text-primary"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= APP_NAME ?></h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">AI-Powered Poster Analysis</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="text-primary hover:text-primary-dark font-medium">Sign In</a>
                    <a href="register.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">Sign Up</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <?php if ($uploadResult): ?>
            <!-- Success Message -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-green-800">Upload Successful!</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p><?= htmlspecialchars($uploadResult['message']) ?></p>
                            <p class="mt-1"><?= htmlspecialchars($uploadResult['next_steps']) ?></p>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-green-600">Reference ID: <?= htmlspecialchars($uploadResult['temp_id']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($uploadError): ?>
            <!-- Error Message -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-red-800">Upload Failed</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p><?= htmlspecialchars($uploadError) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Upload Form -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-secondary p-6">
                <h2 class="text-2xl font-bold text-white">Upload Your Poster</h2>
                <p class="text-blue-100 mt-2">Our AI will analyze your poster and extract event details automatically</p>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                
                <!-- File Upload Area -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Upload Poster <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div id="dropZone" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-primary transition-colors cursor-pointer">
                            <div id="dropZoneContent">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-lg text-gray-600 dark:text-gray-300 mb-2">Drop your poster here or click to browse</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Supports JPG, PNG, WebP, and PDF files up to 10MB</p>
                            </div>
                            <div id="filePreview" class="hidden">
                                <!-- File preview will be inserted here -->
                            </div>
                        </div>
                        <input type="file" id="posterFile" name="poster" accept="image/*,application/pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-2">
                    <label for="contact_info" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Contact Information (Optional)
                    </label>
                    <input type="text" id="contact_info" name="contact_info" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="Email or phone number for notifications">
                    <p class="text-xs text-gray-500 dark:text-gray-400">If provided, we'll notify you when analysis is complete</p>
                </div>

                <!-- Additional Notes -->
                <div class="space-y-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Additional Notes (Optional)
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Any additional information about the event or poster"></textarea>
                </div>

                <!-- Features List -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">What our AI can extract:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-blue-700 dark:text-blue-300">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Event titles and descriptions
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Dates and times
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Location and venue details
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Contact information
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Ticket prices and registration
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Social media handles
                        </div>
                    </div>
                </div>

                <!-- Privacy Notice -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">Privacy & Terms:</h3>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Uploaded files are temporarily stored for 48 hours</li>
                        <li>• AI analysis is performed automatically and securely</li>
                        <li>• We do not store personal data without explicit consent</li>
                        <li>• Analysis results may be used to create public event listings</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button type="submit" 
                            class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg hover:from-primary-dark hover:to-pink-600 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-magic mr-2"></i>
                        Analyze Poster with AI
                    </button>
                </div>
            </form>
        </div>

        <!-- How It Works Section -->
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-upload text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">1. Upload</h3>
                    <p class="text-gray-600 dark:text-gray-300">Upload your event poster in any supported format</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-brain text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">2. AI Analysis</h3>
                    <p class="text-gray-600 dark:text-gray-300">Our AI extracts all event details automatically</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-share-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">3. Share & Promote</h3>
                    <p class="text-gray-600 dark:text-gray-300">Get formatted posts ready for social media</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
            <p class="text-gray-400 text-sm mt-2">Powered by AI-driven event analysis</p>
        </div>
    </footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('posterFile');
    const dropZoneContent = document.getElementById('dropZoneContent');
    const filePreview = document.getElementById('filePreview');

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0]);
        }
    });

    // Drag and drop handlers
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropZone.classList.add('border-primary', 'bg-primary/5');
    }

    function unhighlight() {
        dropZone.classList.remove('border-primary', 'bg-primary/5');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelection(files[0]);
        }
    }

    function handleFileSelection(file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please select a JPG, PNG, WebP, or PDF file.');
            return;
        }

        // Validate file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB.');
            return;
        }

        // Show file preview
        showFilePreview(file);
    }

    function showFilePreview(file) {
        dropZoneContent.classList.add('hidden');
        filePreview.classList.remove('hidden');
        
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileIcon = file.type.startsWith('image/') ? 'fa-image' : 'fa-file-pdf';
        
        filePreview.innerHTML = `
            <div class="flex items-center justify-center space-x-4">
                <i class="fas ${fileIcon} text-3xl text-primary"></i>
                <div class="text-left">
                    <p class="font-medium text-gray-900 dark:text-white">${file.name}</p>
                    <p class="text-sm text-gray-500">${fileSize} MB</p>
                </div>
                <button type="button" onclick="clearFile()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }

    // Global function to clear file selection
    window.clearFile = function() {
        fileInput.value = '';
        filePreview.classList.add('hidden');
        dropZoneContent.classList.remove('hidden');
    };
});
</script>

</body>
</html>