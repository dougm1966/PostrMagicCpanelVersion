<?php 
include __DIR__ . '/includes/dashboard-header.php';
?>

<main class="flex-1 p-4 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track your event performance and audience engagement</p>
            </div>
            <div class="flex items-center space-x-3 mt-4 sm:mt-0">
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span>Last 30 days</span>
                        <i class="fas fa-chevron-down ml-2 text-xs"></i>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10" style="display: none;">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Last 7 days</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Last 30 days</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Last 90 days</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">This year</a>
                        </div>
                    </div>
                </div>
                <button class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Views -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <i class="fas fa-eye text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Views</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">24,589</p>
                        <p class="text-sm text-green-600 dark:text-green-400 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i> 12.5% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Engagement Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                        <i class="fas fa-hand-pointer text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Engagement Rate</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">3.8%</p>
                        <p class="text-sm text-green-600 dark:text-green-400 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i> 2.1% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Click-Through Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <i class="fas fa-mouse-pointer text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Click-Through Rate</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">2.4%</p>
                        <p class="text-sm text-green-600 dark:text-green-400 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i> 0.8% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Events -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Events</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">12</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">3 upcoming this week</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Views Over Time -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Views Over Time</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-xs font-medium rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">Daily</button>
                        <button class="px-3 py-1 text-xs font-medium rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">Weekly</button>
                        <button class="px-3 py-1 text-xs font-medium rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">Monthly</button>
                    </div>
                </div>
                <div class="h-64">
                    <!-- Chart will be rendered here -->
                    <div class="flex items-center justify-center h-full bg-gray-50 dark:bg-gray-700/30 rounded-md">
                        <p class="text-gray-400 dark:text-gray-500">Views over time chart</p>
                    </div>
                </div>
            </div>

            <!-- Top Performing Events -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Top Performing Events</h3>
                    <a href="events.php" class="text-sm font-medium text-primary hover:text-primary-dark">View All</a>
                </div>
                <div class="space-y-4">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 font-medium">
                                <?= $i ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Event Title <?= $i ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= rand(100, 500) ?> views</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?= rand(5, 15) ?>%</p>
                            <p class="text-xs text-green-600 dark:text-green-400">+<?= rand(1, 5) ?>%</p>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Traffic Sources -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Traffic Sources</h3>
                <div class="h-64">
                    <!-- Chart will be rendered here -->
                    <div class="flex items-center justify-center h-full bg-gray-50 dark:bg-gray-700/30 rounded-md">
                        <p class="text-gray-400 dark:text-gray-500">Traffic sources chart</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    <?php 
                    $activities = [
                        ['icon' => 'eye', 'color' => 'text-blue-500', 'text' => 'New high of 1,248 views on "Summer Music Festival"', 'time' => '2 hours ago'],
                        ['icon' => 'share', 'color' => 'text-green-500', 'text' => 'Event shared 24 times on social media', 'time' => '5 hours ago'],
                        ['icon' => 'calendar-plus', 'color' => 'text-purple-500', 'text' => 'New event "Art Exhibition" created', 'time' => '1 day ago'],
                        ['icon' => 'users', 'color' => 'text-yellow-500', 'text' => '42 new users registered', 'time' => '1 day ago'],
                        ['icon' => 'chart-line', 'color' => 'text-red-500', 'text' => 'Engagement rate increased by 15%', 'time' => '2 days ago']
                    ];
                    
                    foreach($activities as $activity): ?>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-<?= $activity['icon'] ?> <?= $activity['color'] ?>"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?= $activity['text'] ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?= $activity['time'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="#" class="text-sm font-medium text-primary hover:text-primary-dark">View all activity</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/dashboard-footer.php'; ?>
