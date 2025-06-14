<?php
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
requireAdmin();

// Set page title
$page_title = "System Overview";

// Include dashboard header (includes sidebars)
require_once __DIR__ . '/../includes/dashboard-header.php';

// Include mock data
require_once __DIR__ . '/../includes/mock-data.php';

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
    'max_execution_time' => ini_get('max_execution_time') . 's',
    'memory_limit' => ini_get('memory_limit'),
];

// Mock database info
$dbInfo = [
    'status' => 'Connected',
    'version' => 'MySQL 8.0.26',
    'tables' => 24,
    'size' => '45.8 MB',
    'last_backup' => '2023-11-05 02:00:00',
];
?>

<main class="main-content" id="main-content">
    <!-- Page Title (Replacing Top Bar) -->
    <div class="px-4 pt-6 pb-2 md:px-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">System Overview</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Server health and performance monitoring</p>
    </div>

    <div class="p-4 md:p-6 space-y-6">
        <!-- System Status Alert -->
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="h-5 w-5 text-green-600 dark:text-green-400 mr-3"></i>
                <div>
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">All systems operational</h3>
                    <p class="text-sm text-green-700 dark:text-green-300">Your application is running smoothly with no issues detected.</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- CPU Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">CPU Usage</h3>
                    <div class="p-1.5 rounded-full bg-green-100 dark:bg-green-900/30">
                        <i data-lucide="cpu" class="h-4 w-4 text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">42%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">4.2 GHz / 10 GHz</p>
                    </div>
                    <div class="w-16 h-16">
                        <canvas id="cpuChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Memory</h3>
                    <div class="p-1.5 rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <i data-lucide="memory-stick" class="h-4 w-4 text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">6.2 GB</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">62% of 10 GB</p>
                    </div>
                    <div class="w-16 h-16">
                        <canvas id="memoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Disk Space -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Disk Space</h3>
                    <div class="p-1.5 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                        <i data-lucide="hard-drive" class="h-4 w-4 text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">128 GB</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">64% of 200 GB</p>
                    </div>
                    <div class="w-16 h-16">
                        <canvas id="diskChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Uptime -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Uptime</h3>
                    <div class="p-1.5 rounded-full bg-purple-100 dark:bg-purple-900/30">
                        <i data-lucide="clock" class="h-4 w-4 text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">24d 6h</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Last reboot: 2023-10-12</p>
                    </div>
                    <div class="w-16 h-16">
                        <canvas id="uptimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Server & Database Info -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Server Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Server Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">PHP Version</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($serverInfo['php_version']) ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Server Software</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($serverInfo['server_software']) ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Server OS</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($serverInfo['server_os']) ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Server Time</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($serverInfo['server_time']) ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Timezone</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($serverInfo['timezone']) ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Max Upload Size</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($serverInfo['max_upload_size']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Database Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Database Status</h3>
                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-full text-xs font-medium">
                        <?= htmlspecialchars($dbInfo['status']) ?>
                    </span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Database Version</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($dbInfo['version']) ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Tables</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $dbInfo['tables'] ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Database Size</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $dbInfo['size'] ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Last Backup</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white"><?= $dbInfo['last_backup'] ?></span>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <i data-lucide="database-backup" class="mr-1.5 h-3.5 w-3.5"></i>
                        Backup Now
                    </button>
                    <button type="button" class="ml-2 inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <i data-lucide="refresh-cw" class="mr-1.5 h-3.5 w-3.5"></i>
                        Optimize
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Error Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Error Logs</h3>
                <div class="flex space-x-2">
                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i data-lucide="trash-2" class="mr-1.5 h-3.5 w-3.5"></i>
                        Clear Logs
                    </button>
                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <i data-lucide="download" class="mr-1.5 h-3.5 w-3.5"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Message</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($errorLogs as $log): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($log['time']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= 
                                    $log['type'] === 'Error' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                    ($log['type'] === 'Warning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400')
                                ?>">
                                    <?= htmlspecialchars($log['type']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                <div class="truncate max-w-xs"><?= htmlspecialchars($log['message']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div class="truncate max-w-xs"><?= htmlspecialchars($log['file']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">24</span> entries
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
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Initialize Lucide icons
lucide.createIcons();

// Initialize charts when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    // CPU Usage Gauge
    const cpuCtx = document.getElementById('cpuChart').getContext('2d');
    new Chart(cpuCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [42, 58],
                backgroundColor: ['#10B981', '#E5E7EB'],
                borderWidth: 0,
                cutout: '70%',
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 70,
            rotation: -90,
            circumference: 180,
            legend: { display: false },
            tooltips: { enabled: false },
            animation: { animateScale: true, animateRotate: true }
        }
    });

    // Similar chart initializations for Memory, Disk, and Uptime would go here
    // Memory Chart
    // Disk Chart
    // Uptime Chart
    
    // For brevity, I'm omitting the other chart initializations
    // They would follow the same pattern as the CPU chart above
});
</script>

<?php 
// Include dashboard footer
require_once __DIR__ . '/../includes/dashboard-footer.php'; 
?>
