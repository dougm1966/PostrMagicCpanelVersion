<?php
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
require_once __DIR__ . '/../includes/avatar-upload.php';
requireAdmin();

// Set page title
$page_title = "Admin Profile";

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = getDBConnection();
        $userId = $_SESSION['user_id'];
        
        if ($action === 'update_extended_profile') {
            // Handle extended profile fields
            $profileData = [
                'name' => $_POST['name'] ?? '',
                'bio' => $_POST['bio'] ?? '',
                'location' => $_POST['location'] ?? '',
                'website' => $_POST['website'] ?? '',
                'twitter_handle' => $_POST['twitter_handle'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'timezone' => $_POST['timezone'] ?? 'UTC',
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
            
        } elseif ($action === 'update_profile') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Get current user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify current password
            if (!password_verify($current_password, $currentUser['password'])) {
                throw new Exception('Current password is incorrect.');
            }
            
            // Check if username/email already exists for other users
            $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $userId]);
            if ($stmt->fetch()) {
                throw new Exception('Username or email already exists.');
            }
            
            // Update profile
            if (!empty($new_password)) {
                if ($new_password !== $confirm_password) {
                    throw new Exception('New passwords do not match.');
                }
                if (strlen($new_password) < 6) {
                    throw new Exception('New password must be at least 6 characters long.');
                }
                
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$username, $email, $hashedPassword, $userId]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $email, $userId]);
            }
            
            // Update session
            $_SESSION['user_email'] = $email;
            $_SESSION['username'] = $username;
            
            $message = 'Profile updated successfully.';
            
        } elseif ($action === 'update_preferences') {
            // Create admin_preferences table if it doesn't exist
            $createTable = "
                CREATE TABLE IF NOT EXISTS admin_preferences (
                    user_id INTEGER PRIMARY KEY,
                    email_notifications BOOLEAN DEFAULT 1,
                    dark_mode BOOLEAN DEFAULT 0,
                    dashboard_layout VARCHAR(50) DEFAULT 'default',
                    items_per_page INTEGER DEFAULT 20,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )";
            
            if (DB_TYPE === 'mysql') {
                $createTable = str_replace('INTEGER PRIMARY KEY', 'INT PRIMARY KEY', $createTable);
                $createTable = str_replace('DATETIME DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT NOW()', $createTable);
            }
            
            $pdo->exec($createTable);
            
            $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
            $dark_mode = isset($_POST['dark_mode']) ? 1 : 0;
            $dashboard_layout = $_POST['dashboard_layout'] ?? 'default';
            $items_per_page = intval($_POST['items_per_page'] ?? 20);
            
            // Insert or update preferences
            $stmt = $pdo->prepare("
                INSERT INTO admin_preferences (user_id, email_notifications, dark_mode, dashboard_layout, items_per_page)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                email_notifications = VALUES(email_notifications),
                dark_mode = VALUES(dark_mode),
                dashboard_layout = VALUES(dashboard_layout),
                items_per_page = VALUES(items_per_page),
                updated_at = CURRENT_TIMESTAMP
            ");
            
            if (DB_TYPE === 'sqlite') {
                $stmt = $pdo->prepare("
                    INSERT OR REPLACE INTO admin_preferences (user_id, email_notifications, dark_mode, dashboard_layout, items_per_page)
                    VALUES (?, ?, ?, ?, ?)
                ");
            }
            
            $stmt->execute([$userId, $email_notifications, $dark_mode, $dashboard_layout, $items_per_page]);
            
            $message = 'Preferences updated successfully.';
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get current user data
$currentUser = getCurrentUser();

// Get current preferences
$preferences = null;
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM admin_preferences WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Preferences might not exist yet
}

// Default preferences
if (!$preferences) {
    $preferences = [
        'email_notifications' => 1,
        'dark_mode' => 0,
        'dashboard_layout' => 'default',
        'items_per_page' => 20
    ];
}

// Include dashboard header
require_once __DIR__ . '/../includes/dashboard-header.php';
?>

<!-- Main Content -->
<main class="main-content" id="main-content">
    <!-- Top Bar -->
    <div class="top-bar bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 md:px-6 beautiful-shadow relative z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button id="sidebar-toggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i data-lucide="menu" class="h-5 w-5 text-gray-600 dark:text-gray-300"></i>
                </button>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Admin Profile</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage your account settings and preferences</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4 md:p-6 space-y-6">
        <!-- Messages -->
        <?php if ($message): ?>
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-md">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-md">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Account Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Account Information</h3>
                    </div>
                    <form method="POST" class="p-6 space-y-8">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                                <input type="text" id="username" name="username" value="<?= htmlspecialchars($currentUser['username']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($currentUser['email']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-6">Change Password</h4>
                            
                            <div class="space-y-6">
                                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password (optional)</label>
                                        <input type="password" id="new_password" name="new_password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                                    </div>
                                    
                                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Extended Profile Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Profile Information</h3>
                    </div>
                    <form method="POST" class="p-6 space-y-6">
                        <input type="hidden" name="action" value="update_extended_profile">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Display Name</label>
                                <input type="text" id="name" name="name" value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" placeholder="Your full name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location</label>
                                <input type="text" id="location" name="location" value="<?= htmlspecialchars($currentUser['location'] ?? '') ?>" placeholder="City, Country" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                            </div>
                        </div>
                        
                        <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                            <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3"><?= htmlspecialchars($currentUser['bio'] ?? '') ?></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum 1000 characters</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Website</label>
                                <input type="url" id="website" name="website" value="<?= htmlspecialchars($currentUser['website'] ?? '') ?>" placeholder="https://example.com" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="twitter_handle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Twitter Handle</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">@</span>
                                    <input type="text" id="twitter_handle" name="twitter_handle" value="<?= htmlspecialchars($currentUser['twitter_handle'] ?? '') ?>" placeholder="username" class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 dark:border-gray-600 focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>" placeholder="+1 (555) 123-4567" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                                <select id="timezone" name="timezone" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
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
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Preferences -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Admin Preferences</h3>
                    </div>
                    <form method="POST" class="p-6 space-y-8">
                        <input type="hidden" name="action" value="update_preferences">
                        
                        <div class="space-y-6">
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" id="email_notifications" name="email_notifications" <?= $preferences['email_notifications'] ? 'checked' : '' ?> class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <label for="email_notifications" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Email Notifications</label>
                                </div>
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" id="dark_mode" name="dark_mode" <?= $preferences['dark_mode'] ? 'checked' : '' ?> class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <label for="dark_mode" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Dark Mode by Default</label>
                                </div>
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="dashboard_layout" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dashboard Layout</label>
                                <select id="dashboard_layout" name="dashboard_layout" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                                    <option value="default" <?= $preferences['dashboard_layout'] === 'default' ? 'selected' : '' ?>>Default</option>
                                    <option value="compact" <?= $preferences['dashboard_layout'] === 'compact' ? 'selected' : '' ?>>Compact</option>
                                    <option value="expanded" <?= $preferences['dashboard_layout'] === 'expanded' ? 'selected' : '' ?>>Expanded</option>
                                </select>
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="items_per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Items Per Page</label>
                                <select id="items_per_page" name="items_per_page" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                                    <option value="10" <?= $preferences['items_per_page'] == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="20" <?= $preferences['items_per_page'] == 20 ? 'selected' : '' ?>>20</option>
                                    <option value="50" <?= $preferences['items_per_page'] == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $preferences['items_per_page'] == 100 ? 'selected' : '' ?>>100</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Profile Summary -->
            <div class="space-y-6">
                <!-- Avatar & Basic Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-center">
                        <?php if ($currentUser['avatar']): ?>
                            <img src="<?= htmlspecialchars($currentUser['avatar_url']) ?>" alt="Avatar" class="mx-auto h-20 w-20 rounded-full object-cover">
                        <?php else: ?>
                            <div class="mx-auto h-20 w-20 rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                                <?= substr($currentUser['display_name'] ?: $currentUser['username'], 0, 1) ?>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($currentUser['display_name'] ?: $currentUser['username']) ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($currentUser['email']) ?></p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                Administrator
                            </span>
                        </div>
                        
                        <!-- Avatar Upload -->
                        <div class="mt-4 space-y-2">
                            <form method="POST" enctype="multipart/form-data" class="space-y-2">
                                <input type="hidden" name="action" value="upload_avatar">
                                <div class="flex items-center justify-center">
                                    <label for="avatar" class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <i data-lucide="camera" class="mr-1 h-3 w-3"></i>
                                        Choose Avatar
                                    </label>
                                    <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                                </div>
                            </form>
                            <?php if ($currentUser['avatar']): ?>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="delete_avatar">
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Are you sure you want to delete your avatar?')">
                                        Remove Avatar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Account Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Account Information</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Member Since</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?= date('M j, Y', strtotime($currentUser['created_at'])) ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Last Login</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <?= $currentUser['last_login'] ? date('M j, Y g:i A', strtotime($currentUser['last_login'])) : 'Never' ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Role</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?= ucfirst($currentUser['role']) ?></dd>
                        </div>
                        <?php if ($currentUser['location']): ?>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Location</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($currentUser['location']) ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($currentUser['website']): ?>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Website</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <a href="<?= htmlspecialchars($currentUser['website']) ?>" target="_blank" class="text-purple-600 hover:text-purple-800 dark:text-purple-400">
                                    <?= htmlspecialchars($currentUser['website']) ?>
                                </a>
                            </dd>
                        </div>
                        <?php endif; ?>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Timezone</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($currentUser['timezone'] ?? 'UTC') ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h4>
                    <div class="space-y-2">
                        <a href="/admin/settings.php" class="block w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            <i data-lucide="settings" class="inline-block mr-2 h-4 w-4"></i>
                            System Settings
                        </a>
                        <a href="/admin/logs.php" class="block w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            <i data-lucide="file-text" class="inline-block mr-2 h-4 w-4"></i>
                            View Logs
                        </a>
                        <a href="/admin/user-management.php" class="block w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            <i data-lucide="users" class="inline-block mr-2 h-4 w-4"></i>
                            Manage Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = this.value;
        
        if (newPassword && confirmPassword && newPassword !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/dashboard-footer.php'; ?>