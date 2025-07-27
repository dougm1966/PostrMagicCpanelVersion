<?php 
// Include auth helper and require user login
require_once __DIR__ . '/includes/auth-helper.php';
require_once __DIR__ . '/includes/avatar-upload.php';
requireLogin();

// Set page title
$page_title = "My Profile";

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $userId = $_SESSION['user_id'];
        
        if ($action === 'update_profile') {
            // Handle profile update
            $profileData = [
                'name' => $_POST['name'] ?? '',
                'bio' => $_POST['bio'] ?? '',
                'location' => $_POST['location'] ?? '',
                'website' => $_POST['website'] ?? '',
                'twitter_handle' => $_POST['twitter_handle'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'timezone' => $_POST['timezone'] ?? 'UTC',
                'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
                'marketing_emails' => isset($_POST['marketing_emails']) ? 1 : 0,
            ];
            
            // Validate profile data
            $validationErrors = validateProfileData($profileData, $userId);
            if (!empty($validationErrors)) {
                throw new Exception('Validation errors: ' . implode(', ', $validationErrors));
            }
            
            // Update profile
            if (updateUserProfile($userId, $profileData)) {
                $message = 'Profile updated successfully.';
            } else {
                throw new Exception('Failed to update profile.');
            }
            
        } elseif ($action === 'upload_avatar') {
            // Handle avatar upload
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = handleAvatarUpload($_FILES['avatar'], $userId);
                if ($uploadResult['success']) {
                    $message = $uploadResult['message'];
                } else {
                    throw new Exception($uploadResult['message']);
                }
            } else {
                throw new Exception('No avatar file selected.');
            }
            
        } elseif ($action === 'delete_avatar') {
            // Handle avatar deletion
            if (deleteUserAvatar($userId)) {
                $message = 'Avatar deleted successfully.';
            } else {
                throw new Exception('Failed to delete avatar.');
            }
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get current user data
$currentUser = getCurrentUser();
if (!$currentUser) {
    header('Location: /login.php');
    exit();
}

include __DIR__ . '/includes/dashboard-header.php';
?>

<main class="flex-1 p-4 lg:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Messages -->
        <?php if ($message): ?>
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-md">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-md">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Cover Photo -->
            <div class="h-40 bg-gradient-to-r from-primary to-secondary">
                <!-- Cover photo upload button (future feature) -->
                <div class="flex justify-end p-4">
                    <button class="bg-white/90 text-gray-700 hover:bg-white px-3 py-1.5 rounded-full text-sm font-medium shadow-sm transition-all duration-200 flex items-center" disabled>
                        <i class="fas fa-camera mr-1.5"></i> Update Cover
                    </button>
                </div>
            </div>
            
            <!-- Profile Info -->
            <div class="px-6 pb-6 -mt-16 relative">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between">
                    <div class="flex items-end">
                        <div class="relative group">
                            <?php if ($currentUser['avatar']): ?>
                                <img src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" alt="Profile" class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-lg object-cover">
                            <?php else: ?>
                                <div class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-lg bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-white text-4xl font-bold">
                                    <?= substr($currentUser['display_name'] ?: $currentUser['username'], 0, 1) ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Avatar Upload Form -->
                            <form method="POST" enctype="multipart/form-data" class="absolute bottom-2 right-2">
                                <input type="hidden" name="action" value="upload_avatar">
                                <label for="avatar-upload" class="bg-white p-2 rounded-full shadow-md text-gray-700 hover:bg-gray-100 transition-all duration-200 opacity-0 group-hover:opacity-100 cursor-pointer flex items-center justify-center">
                                    <i class="fas fa-camera text-sm"></i>
                                </label>
                                <input type="file" id="avatar-upload" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                            </form>
                        </div>
                        <div class="ml-6 mb-2">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($currentUser['display_name'] ?: $currentUser['username']) ?></h1>
                            <p class="text-gray-600 dark:text-gray-300">Member since <?= date('F Y', strtotime($currentUser['created_at'])) ?></p>
                            <?php if ($currentUser['bio']): ?>
                                <p class="text-gray-600 dark:text-gray-400 mt-1"><?= htmlspecialchars($currentUser['bio']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex gap-2">
                        <?php if ($currentUser['avatar']): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="delete_avatar">
                            <button type="submit" class="px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition-colors duration-200 font-medium text-sm" onclick="return confirm('Are you sure you want to delete your avatar?')">
                                <i class="fas fa-trash mr-1"></i> Remove Avatar
                            </button>
                        </form>
                        <?php endif; ?>
                        <button onclick="toggleEditMode()" id="edit-toggle-btn" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 font-medium">
                            <i class="fas fa-edit mr-2"></i> <span id="edit-btn-text">Edit Profile</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h2>
                    </div>
                    
                    <form method="POST" id="profile-form">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white" readonly>
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                    <input type="email" id="email" value="<?= htmlspecialchars($currentUser['email']) ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-50 dark:bg-gray-700 dark:text-gray-400 cursor-not-allowed" readonly disabled>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email cannot be changed here. Contact support if needed.</p>
                                </div>
                            </div>
                            
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bio</label>
                                <textarea id="bio" name="bio" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white" readonly><?= htmlspecialchars($currentUser['bio'] ?? '') ?></textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">A short bio about yourself (max 1000 characters)</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                                    <input type="text" id="location" name="location" value="<?= htmlspecialchars($currentUser['location'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white" readonly>
                                </div>
                                <div>
                                    <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Website</label>
                                    <input type="url" id="website" name="website" value="<?= htmlspecialchars($currentUser['website'] ?? '') ?>" placeholder="https://example.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white" readonly>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>" placeholder="+1 (555) 123-4567" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white" readonly>
                                </div>
                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Zone</label>
                                    <select id="timezone" name="timezone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white" disabled>
                                        <option value="UTC" <?= ($currentUser['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                        <option value="America/New_York" <?= ($currentUser['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>Eastern Time</option>
                                        <option value="America/Chicago" <?= ($currentUser['timezone'] ?? '') === 'America/Chicago' ? 'selected' : '' ?>>Central Time</option>
                                        <option value="America/Denver" <?= ($currentUser['timezone'] ?? '') === 'America/Denver' ? 'selected' : '' ?>>Mountain Time</option>
                                        <option value="America/Los_Angeles" <?= ($currentUser['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific Time</option>
                                        <option value="Europe/London" <?= ($currentUser['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>London</option>
                                        <option value="Europe/Paris" <?= ($currentUser['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' ?>>Paris</option>
                                        <option value="Asia/Tokyo" <?= ($currentUser['timezone'] ?? '') === 'Asia/Tokyo' ? 'selected' : '' ?>>Tokyo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end hidden" id="save-section">
                            <div class="flex gap-3">
                                <button type="button" onclick="toggleEditMode()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200 font-medium">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 font-medium">
                                    <i class="fas fa-save mr-2"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Social Profiles -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Social Profiles</h2>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <i class="fab fa-twitter"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Twitter</p>
                                <?php if ($currentUser['twitter_handle']): ?>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">@<?= htmlspecialchars($currentUser['twitter_handle']) ?></p>
                                <?php else: ?>
                                    <p class="text-sm text-gray-400">Not connected</p>
                                <?php endif; ?>
                            </div>
                            <div class="twitter-edit-field hidden">
                                <input type="text" name="twitter_handle" form="profile-form" value="<?= htmlspecialchars($currentUser['twitter_handle'] ?? '') ?>" placeholder="username" class="w-24 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-primary focus:border-primary dark:bg-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Account Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notification Settings</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Email Notifications</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Receive email notifications</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" form="profile-form" value="1" class="sr-only peer toggle-switch" <?= ($currentUser['email_notifications'] ?? 1) ? 'checked' : '' ?> disabled>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:peer-focus:ring-primary/80 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Marketing Emails</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Receive marketing emails</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="marketing_emails" form="profile-form" value="1" class="sr-only peer toggle-switch" <?= ($currentUser['marketing_emails'] ?? 1) ? 'checked' : '' ?> disabled>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:peer-focus:ring-primary/80 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Security -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Security</h2>
                    
                    <div class="space-y-4">
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">To update your password, please use the "Forgot Password" link on the login page.</p>
                            <a href="forgot-password.php" class="text-primary hover:text-primary-dark font-medium text-sm">
                                Reset Password <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        
                        <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                            <button class="w-full px-4 py-2 border border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200 font-medium" onclick="alert('Account deletion feature coming soon!')">
                                Delete Account
                            </button>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Once you delete your account, there is no going back. Please be certain.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
let editMode = false;

function toggleEditMode() {
    editMode = !editMode;
    const form = document.getElementById('profile-form');
    const inputs = form.querySelectorAll('input[type="text"], input[type="url"], input[type="tel"], textarea, select');
    const toggleSwitches = document.querySelectorAll('.toggle-switch');
    const saveSection = document.getElementById('save-section');
    const editBtn = document.getElementById('edit-toggle-btn');
    const editBtnText = document.getElementById('edit-btn-text');
    const twitterEditField = document.querySelector('.twitter-edit-field');
    
    if (editMode) {
        // Enable edit mode
        inputs.forEach(input => {
            if (input.id !== 'email') { // Keep email disabled
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
            }
        });
        toggleSwitches.forEach(toggle => toggle.removeAttribute('disabled'));
        saveSection.classList.remove('hidden');
        editBtn.innerHTML = '<i class="fas fa-times mr-2"></i><span id="edit-btn-text">Cancel</span>';
        editBtn.classList.remove('bg-primary', 'hover:bg-primary-dark');
        editBtn.classList.add('bg-gray-500', 'hover:bg-gray-600');
        
        if (twitterEditField) {
            twitterEditField.classList.remove('hidden');
        }
    } else {
        // Disable edit mode
        inputs.forEach(input => {
            input.setAttribute('readonly', '');
            if (input.tagName === 'SELECT') {
                input.setAttribute('disabled', '');
            }
        });
        toggleSwitches.forEach(toggle => toggle.setAttribute('disabled', ''));
        saveSection.classList.add('hidden');
        editBtn.innerHTML = '<i class="fas fa-edit mr-2"></i><span id="edit-btn-text">Edit Profile</span>';
        editBtn.classList.remove('bg-gray-500', 'hover:bg-gray-600');
        editBtn.classList.add('bg-primary', 'hover:bg-primary-dark');
        
        if (twitterEditField) {
            twitterEditField.classList.add('hidden');
        }
    }
}

// Character counter for bio
document.getElementById('bio').addEventListener('input', function() {
    const maxLength = 1000;
    const currentLength = this.value.length;
    const helpText = this.parentNode.querySelector('.text-xs');
    helpText.textContent = `A short bio about yourself (${currentLength}/${maxLength} characters)`;
    
    if (currentLength > maxLength) {
        helpText.classList.add('text-red-500');
    } else {
        helpText.classList.remove('text-red-500');
    }
});
</script>

<?php include __DIR__ . '/includes/dashboard-footer.php'; ?>
