<?php 
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
requireAdmin();

// Set page title
$page_title = "Analytics Dashboard";

// Include dashboard header (includes sidebars)
require_once __DIR__ . '/../includes/dashboard-header.php';
?>

<main class="main-content" id="main-content">
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
                        <i data-lucide="calendar" class="mr-2 h-4 w-4"></i>
                        <span>Last 30 days</span>
                        <i data-lucide="chevron-down" class="ml-2 h-3 w-3"></i>
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
                <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <i data-lucide="download" class="mr-2 h-4 w-4"></i>
                    Export
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Views -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <i data-lucide="eye" class="h-6 w-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Views</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">24,568</p>
                        <p class="text-sm text-green-600 dark:text-green-400">
                            <i data-lucide="trending-up" class="h-4 w-4 inline-block mr-1"></i>
                            12.5% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Unique Visitors -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <i data-lucide="users" class="h-6 w-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unique Visitors</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">8,742</p>
                        <p class="text-sm text-green-600 dark:text-green-400">
                            <i data-lucide="trending-up" class="h-4 w-4 inline-block mr-1"></i>
                            8.3% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bounce Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                        <i data-lucide="corner-up-left" class="h-6 w-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bounce Rate</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">42.3%</p>
                        <p class="text-sm text-red-600 dark:text-red-400">
                            <i data-lucide="trending-down" class="h-4 w-4 inline-block mr-1"></i>
                            2.1% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Avg. Session Duration -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                        <i data-lucide="clock" class="h-6 w-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg. Session</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">4m 32s</p>
                        <p class="text-sm text-green-600 dark:text-green-400">
                            <i data-lucide="trending-up" class="h-4 w-4 inline-block mr-1"></i>
                            15.7% from last period
                        </p>
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
                        <button type="button" class="px-3 py-1 text-xs font-medium rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">Daily</button>
                        <button type="button" class="px-3 py-1 text-xs font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Weekly</button>
                        <button type="button" class="px-3 py-1 text-xs font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Monthly</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="viewsChart"></canvas>
                </div>
            </div>

            <!-- Top Pages -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Top Pages</h3>
                    <button type="button" class="text-sm font-medium text-primary hover:text-primary-dark">View All</button>
                </div>
                <div class="space-y-4">
                    <?php
                    $topPages = [
                        ['url' => '/events/summer-festival', 'views' => 4523, 'change' => 12.5],
                        ['url' => '/events/tech-conference', 'views' => 3876, 'change' => 8.2],
                        ['url' => '/events/music-awards', 'views' => 3210, 'change' => -3.4],
                        ['url' => '/events/food-fair', 'views' => 2876, 'change' => 5.7],
                        ['url' => '/events/art-exhibition', 'views' => 2543, 'change' => 15.2],
                    ];
                    
                    foreach ($topPages as $page):
                        $isPositive = $page['change'] >= 0;
                    ?>
                    <div class="flex items-center justify-between">
                        <div class="truncate pr-2">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= htmlspecialchars($page['url']) ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?= number_format($page['views']) ?> views</p>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xs font-medium <?= $isPositive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' ?>">
                                <?= $isPositive ? '+' : '' ?><?= $page['change'] ?>%
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Traffic Sources -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Traffic Sources</h3>
                <div class="h-64">
                    <canvas id="trafficSourcesChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Devices</h3>
                <div class="h-64">
                    <canvas id="devicesChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php
                $activities = [
                    ['user' => 'John Doe', 'action' => 'created a new event', 'time' => '2 minutes ago', 'icon' => 'plus-circle', 'color' => 'green'],
                    ['user' => 'Jane Smith', 'action' => 'updated event details', 'time' => '1 hour ago', 'icon' => 'edit-2', 'color' => 'blue'],
                    ['user' => 'Alex Johnson', 'action' => 'uploaded media', 'time' => '3 hours ago', 'icon' => 'image', 'color' => 'purple'],
                    ['user' => 'Sam Wilson', 'action' => 'commented on an event', 'time' => '5 hours ago', 'icon' => 'message-square', 'color' => 'yellow'],
                    ['user' => 'Taylor Swift', 'action' => 'registered for an event', 'time' => '1 day ago', 'icon' => 'user-plus', 'color' => 'pink'],
                ];
                
                $colors = [
                    'blue' => 'text-blue-500',
                    'green' => 'text-green-500',
                    'yellow' => 'text-yellow-500',
                    'red' => 'text-red-500',
                    'purple' => 'text-purple-500',
                    'pink' => 'text-pink-500',
                    'indigo' => 'text-indigo-500',
                ];
                
                foreach ($activities as $activity):
                    $colorClass = $colors[$activity['color']] ?? 'text-gray-500';
                ?>
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-600 flex items-center justify-center">
                                <i data-lucide="<?= $activity['icon'] ?>" class="h-5 w-5 <?= $colorClass ?>"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($activity['user']) ?> <span class="text-gray-500 dark:text-gray-400 font-normal"><?= $activity['action'] ?></span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= $activity['time'] ?></p>
                            </div>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                <?php if (isset($activity['event'])): ?>
                                <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded"><?= $activity['event'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right border-t border-gray-200 dark:border-gray-600">
                <a href="#" class="text-sm font-medium text-primary hover:text-primary-dark">View all activity</a>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Views Over Time Chart
    const viewsCtx = document.getElementById('viewsChart').getContext('2d');
    new Chart(viewsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: '2023',
                data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#4F46E5',
                pointBorderColor: '#fff',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#4F46E5',
                pointHoverBorderColor: '#fff',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1F2937',
                    titleFont: { size: 12, family: 'Inter' },
                    bodyFont: { size: 12, family: 'Inter', weight: '500' },
                    padding: 10,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.parsed.y.toLocaleString() + ' views';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 12
                        },
                        color: '#6B7280'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#E5E7EB',
                        borderDash: [3, 3],
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 12
                        },
                        color: '#6B7280',
                        callback: function(value) {
                            return (value / 1000) + 'k';
                        }
                    }
                }
            }
        }
    });
    
    // Traffic Sources Chart
    const trafficCtx = document.getElementById('trafficSourcesChart').getContext('2d');
    new Chart(trafficCtx, {
        type: 'doughnut',
        data: {
            labels: ['Direct', 'Organic Search', 'Social', 'Email', 'Referral'],
            datasets: [{
                data: [35, 30, 15, 12, 8],
                backgroundColor: [
                    '#4F46E5',
                    '#10B981',
                    '#F59E0B',
                    '#EC4899',
                    '#8B5CF6'
                ],
                borderWidth: 0,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: {
                            family: 'Inter',
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#1F2937',
                    titleFont: { size: 12, family: 'Inter' },
                    bodyFont: { size: 12, family: 'Inter', weight: '500' },
                    padding: 10,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return ` ${label}: ${percentage}% (${value.toLocaleString()})`;
                        }
                    }
                }
            }
        }
    });
    
    // Devices Chart
    const devicesCtx = document.getElementById('devicesChart').getContext('2d');
    new Chart(devicesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Desktop', 'Mobile', 'Tablet'],
            datasets: [{
                data: [60, 30, 10],
                backgroundColor: [
                    '#4F46E5',
                    '#10B981',
                    '#F59E0B'
                ],
                borderWidth: 0,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: {
                            family: 'Inter',
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#1F2937',
                    titleFont: { size: 12, family: 'Inter' },
                    bodyFont: { size: 12, family: 'Inter', weight: '500' },
                    padding: 10,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            return ` ${label}: ${value}%`;
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php 
// Include dashboard footer
require_once __DIR__ . '/../includes/dashboard-footer.php'; 
?>
