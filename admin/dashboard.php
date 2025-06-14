<?php
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
requireAdmin();

// Set page title
$page_title = "Admin Dashboard";

// Include dashboard header (includes sidebars)
require_once __DIR__ . '/../includes/dashboard-header.php';

// Include mock data
require_once __DIR__ . '/../includes/mock-data.php';

// Get mock data
$stats = getMockSystemStats();
$recentActivity = getMockActivityLogs();
$users = getMockUsers();
$events = getMockAdminEvents();

// Calculate some additional stats
$activeUsersPercent = round(($stats['active_users'] / $stats['total_users']) * 100);
$pendingEventsPercent = round(($stats['pending_events'] / $stats['total_events']) * 100);
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
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Admin Dashboard</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">System overview and management</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 relative">
                    <i data-lucide="bell" class="h-5 w-5 text-gray-600 dark:text-gray-300"></i>
                    <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="p-4 md:p-6 space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Users -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= number_format($stats['total_users']) ?></p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">
                            <i data-lucide="trending-up" class="inline-block h-4 w-4"></i>
                            +<?= $stats['new_users_today'] ?> today
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <i data-lucide="users" class="h-6 w-6 text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
            </div>

            <!-- Active Events -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Events</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= number_format($stats['active_events']) ?></p>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2">
                            <i data-lucide="alert-circle" class="inline-block h-4 w-4"></i>
                            <?= $stats['pending_events'] ?> pending
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <i data-lucide="calendar" class="h-6 w-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Monthly Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">$<?= number_format($stats['revenue_this_month'] ?? 0) ?></p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-2">
                            <i data-lucide="trending-up" class="inline-block h-4 w-4"></i>
                            <?php
                            // Calculate revenue growth percentage if not set
                            $revenueGrowth = $stats['revenue_growth'] ?? 0;
                            if ($revenueGrowth > 0) {
                                echo '+' . $revenueGrowth . '%';
                            } elseif ($revenueGrowth < 0) {
                                echo $revenueGrowth . '%';
                            } else {
                                echo '0%';
                            }
                            ?> this month
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <i data-lucide="dollar-sign" class="h-6 w-6 text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">System Health</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">Excellent</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            All systems operational
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <i data-lucide="heart" class="h-6 w-6 text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="user-management.php" class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i data-lucide="users" class="h-8 w-8 text-purple-600 dark:text-purple-400 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Manage Users</span>
                </a>
                <a href="events.php" class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i data-lucide="calendar" class="h-8 w-8 text-blue-600 dark:text-blue-400 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">View Events</span>
                </a>
                <a href="analytics.php" class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i data-lucide="bar-chart-2" class="h-8 w-8 text-green-600 dark:text-green-400 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">View Reports</span>
                </a>
                <a href="settings.php" class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i data-lucide="settings" class="h-8 w-8 text-gray-600 dark:text-gray-400 mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Settings</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
                    <a href="logs.php" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">View all</a>
                </div>
                <div class="space-y-3">
                    <?php foreach (array_slice($recentActivity, 0, 5) as $activity): ?>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                <i data-lucide="<?= $activity['icon'] ?>" class="h-4 w-4 text-purple-600 dark:text-purple-400"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($activity['description']) ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?= $activity['time'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- System Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">System Resources</h2>
                    <a href="../system-overview.php" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">Details</a>
                </div>
                <div class="space-y-4">
                    <!-- CPU Usage -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">CPU Usage</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400"><?= $stats['cpu_usage'] ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $stats['cpu_usage'] ?>%"></div>
                        </div>
                    </div>
                    
                    <!-- Memory Usage -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Memory Usage</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400"><?= $stats['memory_usage'] ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: <?= $stats['memory_usage'] ?>%"></div>
                        </div>
                    </div>
                    
                    <!-- Disk Usage -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Disk Usage</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400"><?= $stats['disk_usage'] ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: <?= $stats['disk_usage'] ?>%"></div>
                        </div>
                    </div>

                    <!-- API Calls -->
                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">API Calls Today</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white"><?= number_format($stats['api_calls_today']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Items Alert -->
        <?php if ($stats['pending_events'] > 0 || $stats['flagged_events'] > 0): ?>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-center">
                <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mr-3"></i>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Attention Required</h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        You have <?= $stats['pending_events'] ?> events pending review and <?= $stats['flagged_events'] ?> flagged items.
                        <a href="events.php?pending=1" class="underline ml-1">Review now</a>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>

</body>
</html>
