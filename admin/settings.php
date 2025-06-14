<?php
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
requireAdmin();

// Set page title
$page_title = "System Settings";

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tab = $_POST['tab'] ?? 'general';
    
    try {
        $pdo = getDBConnection();
        
        switch ($tab) {
            case 'general':
                // Handle general settings
                $site_name = $_POST['site_name'] ?? '';
                $site_description = $_POST['site_description'] ?? '';
                $timezone = $_POST['timezone'] ?? 'UTC';
                
                // Save general settings (you would implement proper settings storage)
                $message = 'General settings saved successfully.';
                break;
                
            case 'api':
                // Handle API settings
                $api_enabled = isset($_POST['api_enabled']) ? 1 : 0;
                $rate_limit = intval($_POST['rate_limit'] ?? 1000);
                
                $message = 'API settings saved successfully.';
                break;
                
            case 'email':
                // Handle email settings
                $smtp_host = $_POST['smtp_host'] ?? '';
                $smtp_port = $_POST['smtp_port'] ?? '';
                $smtp_username = $_POST['smtp_username'] ?? '';
                
                $message = 'Email settings saved successfully.';
                break;
                
            case 'integrations':
                // Handle integration settings
                $message = 'Integration settings saved successfully.';
                break;
                
            case 'security':
                // Handle security settings
                $two_factor_required = isset($_POST['two_factor_required']) ? 1 : 0;
                $session_timeout = intval($_POST['session_timeout'] ?? 30);
                
                $message = 'Security settings saved successfully.';
                break;
        }
    } catch (Exception $e) {
        $error = 'Error saving settings: ' . $e->getMessage();
    }
}

// Get current tab
$current_tab = $_GET['tab'] ?? $_POST['tab'] ?? 'general';

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
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">System Settings</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure system-wide settings and preferences</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Content -->
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

        <!-- Settings Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <a href="?tab=general" class="<?= $current_tab === 'general' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <i data-lucide="settings" class="inline-block mr-2 h-4 w-4"></i>
                        General
                    </a>
                    <a href="?tab=api" class="<?= $current_tab === 'api' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <i data-lucide="code" class="inline-block mr-2 h-4 w-4"></i>
                        API
                    </a>
                    <a href="?tab=email" class="<?= $current_tab === 'email' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <i data-lucide="mail" class="inline-block mr-2 h-4 w-4"></i>
                        Email
                    </a>
                    <a href="?tab=integrations" class="<?= $current_tab === 'integrations' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <i data-lucide="plug" class="inline-block mr-2 h-4 w-4"></i>
                        Integrations
                    </a>
                    <a href="?tab=security" class="<?= $current_tab === 'security' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <i data-lucide="shield" class="inline-block mr-2 h-4 w-4"></i>
                        Security
                    </a>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <?php if ($current_tab === 'general'): ?>
                <!-- General Settings -->
                <form method="POST" class="space-y-8">
                    <input type="hidden" name="tab" value="general">
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Name</label>
                        <input type="text" id="site_name" name="site_name" value="PostrMagic" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="site_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Description</label>
                        <textarea id="site_description" name="site_description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">Create amazing event posters and manage your events with ease.</textarea>
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Timezone</label>
                        <select id="timezone" name="timezone" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time</option>
                            <option value="America/Chicago">Central Time</option>
                            <option value="America/Denver">Mountain Time</option>
                            <option value="America/Los_Angeles">Pacific Time</option>
                        </select>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Save General Settings
                        </button>
                    </div>
                </form>

                <?php elseif ($current_tab === 'api'): ?>
                <!-- API Settings -->
                <form method="POST" class="space-y-8">
                    <input type="hidden" name="tab" value="api">
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <div class="flex items-center">
                            <input type="checkbox" id="api_enabled" name="api_enabled" checked class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="api_enabled" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Enable API Access</label>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="rate_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rate Limit (requests per hour)</label>
                        <input type="number" id="rate_limit" name="rate_limit" value="1000" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                    </div>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-6">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-300">API Documentation</h4>
                        <p class="mt-2 text-sm text-blue-700 dark:text-blue-400">API endpoints are available at <code>/api/v1/</code>. Generate API keys in user profiles.</p>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Save API Settings
                        </button>
                    </div>
                </form>

                <?php elseif ($current_tab === 'email'): ?>
                <!-- Email Settings -->
                <form method="POST" class="space-y-8">
                    <input type="hidden" name="tab" value="email">
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="smtp_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Host</label>
                        <input type="text" id="smtp_host" name="smtp_host" placeholder="smtp.gmail.com" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="smtp_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Port</label>
                        <input type="number" id="smtp_port" name="smtp_port" value="587" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="smtp_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Username</label>
                        <input type="text" id="smtp_username" name="smtp_username" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="smtp_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Password</label>
                        <input type="password" id="smtp_password" name="smtp_password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Save Email Settings
                        </button>
                    </div>
                </form>

                <?php elseif ($current_tab === 'integrations'): ?>
                <!-- Integrations Settings -->
                <form method="POST" class="space-y-8">
                    <input type="hidden" name="tab" value="integrations">
                    
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Third-party Integrations</h3>
                        
                        <!-- Social Media Integrations -->
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-6">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-4">Social Media</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Facebook Integration</span>
                                    <button type="button" class="text-sm text-purple-600 hover:text-purple-700 px-3 py-1 rounded border border-purple-300 hover:bg-purple-50">Configure</button>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Twitter Integration</span>
                                    <button type="button" class="text-sm text-purple-600 hover:text-purple-700 px-3 py-1 rounded border border-purple-300 hover:bg-purple-50">Configure</button>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Instagram Integration</span>
                                    <button type="button" class="text-sm text-purple-600 hover:text-purple-700 px-3 py-1 rounded border border-purple-300 hover:bg-purple-50">Configure</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Integrations -->
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-6">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-4">Payment Processing</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Stripe</span>
                                    <button type="button" class="text-sm text-purple-600 hover:text-purple-700 px-3 py-1 rounded border border-purple-300 hover:bg-purple-50">Configure</button>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">PayPal</span>
                                    <button type="button" class="text-sm text-purple-600 hover:text-purple-700 px-3 py-1 rounded border border-purple-300 hover:bg-purple-50">Configure</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Save Integration Settings
                        </button>
                    </div>
                </form>

                <?php elseif ($current_tab === 'security'): ?>
                <!-- Security Settings -->
                <form method="POST" class="space-y-8">
                    <input type="hidden" name="tab" value="security">
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <div class="flex items-center">
                            <input type="checkbox" id="two_factor_required" name="two_factor_required" class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="two_factor_required" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Require Two-Factor Authentication</label>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="session_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Session Timeout (minutes)</label>
                        <input type="number" id="session_timeout" name="session_timeout" value="30" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                    </div>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-6">
                        <h4 class="text-sm font-medium text-yellow-900 dark:text-yellow-300">Security Notice</h4>
                        <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">Changes to security settings will affect all users. Make sure to communicate changes to your team.</p>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Save Security Settings
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>

<?php require_once __DIR__ . '/../includes/dashboard-footer.php'; ?>