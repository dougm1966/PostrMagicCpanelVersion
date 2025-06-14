<?php
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
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
                    <form method="POST" class="p-6 space-y-4">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                                <input type="text" id="username" name="username" value="<?= htmlspecialchars($currentUser['username']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($currentUser['email']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Change Password</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password (optional)</label>
                                        <input type="password" id="new_password" name="new_password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    
                                    <div>
                                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
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
                    <form method="POST" class="p-6 space-y-4">
                        <input type="hidden" name="action" value="update_preferences">
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="email_notifications" name="email_notifications" <?= $preferences['email_notifications'] ? 'checked' : '' ?> class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="email_notifications" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Email Notifications</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="dark_mode" name="dark_mode" <?= $preferences['dark_mode'] ? 'checked' : '' ?> class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="dark_mode" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Dark Mode by Default</label>
                            </div>
                            
                            <div>
                                <label for="dashboard_layout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dashboard Layout</label>
                                <select id="dashboard_layout" name="dashboard_layout" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                    <option value="default" <?= $preferences['dashboard_layout'] === 'default' ? 'selected' : '' ?>>Default</option>
                                    <option value="compact" <?= $preferences['dashboard_layout'] === 'compact' ? 'selected' : '' ?>>Compact</option>
                                    <option value="expanded" <?= $preferences['dashboard_layout'] === 'expanded' ? 'selected' : '' ?>>Expanded</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="items_per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Items Per Page</label>
                                <select id="items_per_page" name="items_per_page" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                    <option value="10" <?= $preferences['items_per_page'] == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="20" <?= $preferences['items_per_page'] == 20 ? 'selected' : '' ?>>20</option>
                                    <option value="50" <?= $preferences['items_per_page'] == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $preferences['items_per_page'] == 100 ? 'selected' : '' ?>>100</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
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
                        <div class="mx-auto h-20 w-20 rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                            <?= substr($currentUser['username'], 0, 1) ?>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($currentUser['username']) ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($currentUser['email']) ?></p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                Administrator
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
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Role</dt>
                            <dd class="text-sm text-gray-900 dark:text-white"><?= ucfirst($currentUser['role']) ?></dd>
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