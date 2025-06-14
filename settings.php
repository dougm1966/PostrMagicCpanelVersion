<?php
// Include auth helper and require login
require_once __DIR__ . '/includes/auth-helper.php';
requireLogin();

// Set page title
$page_title = "Account Settings";

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = getDBConnection();
        $userId = $_SESSION['user_id'];
        
        if ($action === 'update_profile') {
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
            // Create user_preferences table if it doesn't exist
            $createTable = "
                CREATE TABLE IF NOT EXISTS user_preferences (
                    user_id INTEGER PRIMARY KEY,
                    email_notifications BOOLEAN DEFAULT 1,
                    event_reminders BOOLEAN DEFAULT 1,
                    dark_mode BOOLEAN DEFAULT 0,
                    timezone VARCHAR(100) DEFAULT 'UTC',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )";
            
            if (DB_TYPE === 'mysql') {
                $createTable = str_replace('INTEGER PRIMARY KEY', 'INT PRIMARY KEY', $createTable);
                $createTable = str_replace('DATETIME DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT NOW()', $createTable);
            }
            
            $pdo->exec($createTable);
            
            $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
            $event_reminders = isset($_POST['event_reminders']) ? 1 : 0;
            $dark_mode = isset($_POST['dark_mode']) ? 1 : 0;
            $timezone = $_POST['timezone'] ?? 'UTC';
            
            // Insert or update preferences
            $stmt = $pdo->prepare("
                INSERT INTO user_preferences (user_id, email_notifications, event_reminders, dark_mode, timezone)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                email_notifications = VALUES(email_notifications),
                event_reminders = VALUES(event_reminders),
                dark_mode = VALUES(dark_mode),
                timezone = VALUES(timezone),
                updated_at = CURRENT_TIMESTAMP
            ");
            
            if (DB_TYPE === 'sqlite') {
                $stmt = $pdo->prepare("
                    INSERT OR REPLACE INTO user_preferences (user_id, email_notifications, event_reminders, dark_mode, timezone)
                    VALUES (?, ?, ?, ?, ?)
                ");
            }
            
            $stmt->execute([$userId, $email_notifications, $event_reminders, $dark_mode, $timezone]);
            
            $message = 'Preferences updated successfully.';
        } elseif ($action === 'delete_account') {
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Get current user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (!password_verify($confirm_password, $currentUser['password'])) {
                throw new Exception('Password is incorrect.');
            }
            
            // Soft delete - mark as inactive instead of actually deleting
            $stmt = $pdo->prepare("UPDATE users SET is_active = 0, email = CONCAT('deleted_', id, '_', email) WHERE id = ?");
            $stmt->execute([$userId]);
            
            // Log out and redirect
            logout();
            header('Location: login.php?message=account_deleted');
            exit();
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
    $stmt = $pdo->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Preferences might not exist yet
}

// Default preferences
if (!$preferences) {
    $preferences = [
        'email_notifications' => 1,
        'event_reminders' => 1,
        'dark_mode' => 0,
        'timezone' => 'UTC'
    ];
}

// Include dashboard header
require_once __DIR__ . '/includes/dashboard-header.php';
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
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Account Settings</h1>
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
            <!-- Main Settings -->
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
                                <input type="text" id="username" name="username" value="<?= htmlspecialchars($currentUser['username']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:text-white px-4 py-3">
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($currentUser['email']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:text-white px-4 py-3">
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-6">Change Password</h4>
                            
                            <div class="space-y-6">
                                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:text-white px-4 py-3">
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password (optional)</label>
                                        <input type="password" id="new_password" name="new_password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:text-white px-4 py-3">
                                    </div>
                                    
                                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:text-white px-4 py-3">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-primary text-white px-6 py-3 rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Preferences -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Preferences</h3>
                    </div>
                    <form method="POST" class="p-6 space-y-8">
                        <input type="hidden" name="action" value="update_preferences">
                        
                        <div class="space-y-6">
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" id="email_notifications" name="email_notifications" <?= $preferences['email_notifications'] ? 'checked' : '' ?> class="h-5 w-5 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="email_notifications" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Email Notifications</label>
                                </div>
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" id="event_reminders" name="event_reminders" <?= $preferences['event_reminders'] ? 'checked' : '' ?> class="h-5 w-5 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="event_reminders" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Event Reminders</label>
                                </div>
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" id="dark_mode" name="dark_mode" <?= $preferences['dark_mode'] ? 'checked' : '' ?> class="h-5 w-5 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="dark_mode" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Dark Mode by Default</label>
                                </div>
                            </div>
                            
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                                <select id="timezone" name="timezone" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:text-white px-4 py-3">
                                    <option value="UTC" <?= $preferences['timezone'] === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                    <option value="America/New_York" <?= $preferences['timezone'] === 'America/New_York' ? 'selected' : '' ?>>Eastern Time</option>
                                    <option value="America/Chicago" <?= $preferences['timezone'] === 'America/Chicago' ? 'selected' : '' ?>>Central Time</option>
                                    <option value="America/Denver" <?= $preferences['timezone'] === 'America/Denver' ? 'selected' : '' ?>>Mountain Time</option>
                                    <option value="America/Los_Angeles" <?= $preferences['timezone'] === 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific Time</option>
                                    <option value="Europe/London" <?= $preferences['timezone'] === 'Europe/London' ? 'selected' : '' ?>>London</option>
                                    <option value="Europe/Paris" <?= $preferences['timezone'] === 'Europe/Paris' ? 'selected' : '' ?>>Paris</option>
                                    <option value="Asia/Tokyo" <?= $preferences['timezone'] === 'Asia/Tokyo' ? 'selected' : '' ?>>Tokyo</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-primary text-white px-6 py-3 rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary">
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-red-200 dark:border-red-800">
                    <div class="px-6 py-4 border-b border-red-200 dark:border-red-800">
                        <h3 class="text-lg font-medium text-red-900 dark:text-red-300">Danger Zone</h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4 mb-4">
                            <h4 class="text-sm font-medium text-red-900 dark:text-red-300">Delete Account</h4>
                            <p class="mt-1 text-sm text-red-700 dark:text-red-400">Once you delete your account, there is no going back. Please be certain.</p>
                        </div>
                        <button onclick="showDeleteModal()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete My Account
                        </button>
                    </div>
                </div>
            </div>

            <!-- Profile Summary -->
            <div class="space-y-6">
                <!-- Avatar & Basic Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="text-center">
                        <div class="mx-auto h-20 w-20 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-white text-2xl font-bold">
                            <?= substr($currentUser['username'], 0, 1) ?>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($currentUser['username']) ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($currentUser['email']) ?></p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                <?= ucfirst($currentUser['role']) ?>
                            </span>
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
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Account Status</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">Active</dd>
                        </div>
                    </dl>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h4>
                    <div class="space-y-2">
                        <a href="user-profile.php" class="block w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary rounded-md">
                            <i data-lucide="user" class="inline-block mr-2 h-4 w-4"></i>
                            View Profile
                        </a>
                        <a href="events.php" class="block w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary rounded-md">
                            <i data-lucide="calendar" class="inline-block mr-2 h-4 w-4"></i>
                            My Events
                        </a>
                        <a href="media-library.php" class="block w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary rounded-md">
                            <i data-lucide="image" class="inline-block mr-2 h-4 w-4"></i>
                            Media Library
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Delete Account Modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-red-900 dark:text-red-300 mb-4">Delete Account</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">This action cannot be undone. This will permanently delete your account and remove all associated data.</p>
            <form method="POST">
                <input type="hidden" name="action" value="delete_account">
                <div class="mb-4">
                    <label for="delete_confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm with your password</label>
                    <input type="password" id="delete_confirm_password" name="confirm_password" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideDeleteModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    
    function showDeleteModal() {
        document.getElementById('delete-modal').classList.remove('hidden');
    }
    
    function hideDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('delete-modal');
        if (event.target === modal) {
            hideDeleteModal();
        }
    }
</script>

<?php require_once __DIR__ . '/includes/dashboard-footer.php'; ?>