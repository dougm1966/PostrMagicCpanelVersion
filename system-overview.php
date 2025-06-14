<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Force admin mode for this page
$_SESSION['is_admin'] = true;

// Set page title
$page_title = "System Overview";

// Include dashboard header (includes sidebars)
require_once 'includes/dashboard-header.php';

// Include mock data
require_once 'includes/mock-data.php';

// Get mock data
$stats = getMockSystemStats();
$errorLogs = getMockErrorLogs();

// Mock server info
$serverInfo = [
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/2.4.41',
    'server_os' => PHP_OS,
    'server_time' => date('Y-m-d H:i:s'),
    'timezone' => date_default_timezone_get(),
    'max_upload_size' => ini_get('upload_max_filesize'),
    'memory_limit' => ini_get('memory_limit'),
    'execution_time' => ini_get('max_execution_time') . 's'
];

// Mock database info
$dbInfo = [
    'status' => 'Connected',
    'host' => 'localhost',
    'database' => 'postrmagic_db',
    'tables' => 15,
    'size' => '124.5 MB',
    'connections' => 8,
    'max_connections' => 100
];
?>

<!-- Main Content -->
<main class="main-content" id="main-content">
    <!-- Page Title (Replacing Top Bar) -->
    <div class="px-4 pt-6 pb-2 md:px-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">System Overview</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Server health and performance monitoring</p>
    </div>

    <!-- System Overview Content -->
    <div class="p-4 md:p-6 space-y-6">
        <!-- System Status Alert -->
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="h-5 w-5 text-green-600 dark:text-green-400 mr-3"></i>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">All Systems Operational</h3>
                    <p class="text-sm text-green-700 dark:text-green-300">Last checked: <?= date('Y-m-d H:i:s') ?></p>
                </div>
                <span class="px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-lg text-sm font-medium">
                    Healthy
                </span>
                <button class="ml-2 px-3 py-1.5 text-sm bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg flex items-center">
                    <i data-lucide="refresh-cw" class="inline-block h-4 w-4 mr-1"></i>
                    Auto-refresh: ON
                </button>
            </div>
        </div>

        <!-- Resource Usage -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- CPU Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">CPU Usage</h3>
                    <i data-lucide="cpu" class="h-5 w-5 text-gray-400"></i>
                </div>
                <div class="relative h-32 flex items-center justify-center mb-4">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-blue-600 dark:text-blue-400"><?= $stats['cpu_usage'] ?>%</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Current Load</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Processes</span>
                        <span class="text-gray-900 dark:text-white">127</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Threads</span>
                        <span class="text-gray-900 dark:text-white">512</span>
                    </div>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Memory Usage</h3>
                    <i data-lucide="memory-stick" class="h-5 w-5 text-gray-400"></i>
                </div>
                <div class="relative h-32 flex items-center justify-center mb-4">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-purple-600 dark:text-purple-400"><?= $stats['memory_usage'] ?>%</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">4.2 GB / 8 GB</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Available</span>
                        <span class="text-gray-900 dark:text-white">3.8 GB</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Cache</span>
                        <span class="text-gray-900 dark:text-white">1.2 GB</span>
                    </div>
                </div>
            </div>

            <!-- Disk Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Disk Usage</h3>
                    <i data-lucide="hard-drive" class="h-5 w-5 text-gray-400"></i>
                </div>
                <div class="relative h-32 flex items-center justify-center mb-4">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-green-600 dark:text-green-400"><?= $stats['disk_usage'] ?>%</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">38 GB / 100 GB</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Free Space</span>
                        <span class="text-gray-900 dark:text-white">62 GB</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">I/O Rate</span>
                        <span class="text-gray-900 dark:text-white">125 MB/s</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Server Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Server Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">PHP Version</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $serverInfo['php_version'] ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Server Software</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $serverInfo['server_software'] ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Operating System</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $serverInfo['server_os'] ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Server Time</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $serverInfo['server_time'] ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Timezone</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $serverInfo['timezone'] ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Max Upload Size</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $serverInfo['max_upload_size'] ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Memory Limit</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $serverInfo['memory_limit'] ?></span>
                    </div>
                </div>
            </div>

            <!-- Database Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Database Status</h3>
                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-full text-xs font-medium">
                        <?= $dbInfo['status'] ?>
                    </span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Host</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $dbInfo['host'] ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Database</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $dbInfo['database'] ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Tables</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $dbInfo['tables'] ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Size</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $dbInfo['size'] ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Active Connections</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $dbInfo['connections'] ?> / <?= $dbInfo['max_connections'] ?></span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: <?= ($dbInfo['connections'] / $dbInfo['max_connections']) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Error Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Error Logs</h3>
                <a href="admin-logs.php" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">View all logs</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Level</th>
                            <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Message</th>
                            <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">File</th>
                            <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($errorLogs as $log): ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="py-2 px-3">
                                <?php if ($log['level'] === 'error'): ?>
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 rounded text-xs font-medium">ERROR</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 rounded text-xs font-medium">WARNING</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 px-3 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($log['message']) ?></td>
                            <td class="py-2 px-3 text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($log['file']) ?>:<?= $log['line'] ?></td>
                            <td class="py-2 px-3 text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($log['timestamp']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Response Time</h4>
                    <i data-lucide="zap" class="h-4 w-4 text-gray-400"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">125ms</p>
                <p class="text-sm text-green-600 dark:text-green-400 mt-1">-12% from last hour</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Uptime</h4>
                    <i data-lucide="clock" class="h-4 w-4 text-gray-400"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">99.98%</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">45 days, 12 hours</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">SSL Certificate</h4>
                    <i data-lucide="shield-check" class="h-4 w-4 text-gray-400"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">Valid</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Expires in 87 days</p>
            </div>
        </div>
    </div>
</main>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Auto-refresh simulation (would use AJAX in production)
    setInterval(function() {
        console.log('Refreshing system stats...');
        // In production, this would fetch new data via AJAX
    }, 30000); // Refresh every 30 seconds
</script>

</body>
</html>