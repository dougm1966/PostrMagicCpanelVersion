<?php
// Set page title
$page_title = "Dashboard";

// Mock session data for testing (would come from actual auth system)
// Set to true to see admin sidebar, false for regular user sidebar
$_SESSION['is_admin'] = false;

// Include dashboard header (includes sidebars)
require_once 'includes/dashboard-header.php';

// Generate CSRF token for security
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Mock data for dashboard
$recent_events = [
    [
        'id' => 1,
        'title' => 'Summer Music Festival',
        'date' => '2025-07-15',
        'status' => 'active',
        'views' => 1250,
        'engagement' => '23%',
    ],
    [
        'id' => 2,
        'title' => 'Tech Conference 2025',
        'date' => '2025-08-22',
        'status' => 'active',
        'views' => 879,
        'engagement' => '18%',
    ],
    [
        'id' => 3,
        'title' => 'Art Exhibition Opening',
        'date' => '2025-06-30',
        'status' => 'draft',
        'views' => 0,
        'engagement' => '0%',
    ]
];

$stats = [
    'total_events' => 5,
    'active_events' => 3,
    'total_views' => 4875,
    'engagement_rate' => '21%',
    'media_count' => 27
];
?>

<!-- Dashboard Content Starts Here -->
<div class="py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
            <div>
                <a href="event-creation.php" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-md flex items-center gap-2">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    <span>Create Event</span>
                </a>
            </div>
        </div>
        <p class="text-gray-600">Welcome back! Here's an overview of your events and activity.</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Events -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Events</p>
                    <h3 class="text-2xl font-semibold text-gray-900"><?= $stats['total_events'] ?></h3>
                    <p class="text-sm text-gray-500 mt-2"><?= $stats['active_events'] ?> active</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <i data-lucide="calendar" class="h-6 w-6 text-blue-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Views -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Views</p>
                    <h3 class="text-2xl font-semibold text-gray-900"><?= number_format($stats['total_views']) ?></h3>
                    <p class="text-sm text-gray-500 mt-2">+12% from last month</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <i data-lucide="eye" class="h-6 w-6 text-green-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Engagement Rate -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Engagement Rate</p>
                    <h3 class="text-2xl font-semibold text-gray-900"><?= $stats['engagement_rate'] ?></h3>
                    <p class="text-sm text-gray-500 mt-2">+3% from last month</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-full">
                    <i data-lucide="thumbs-up" class="h-6 w-6 text-purple-500"></i>
                </div>
            </div>
        </div>
        
        <!-- Media Items -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Media Items</p>
                    <h3 class="text-2xl font-semibold text-gray-900"><?= $stats['media_count'] ?></h3>
                    <p class="text-sm text-gray-500 mt-2">2.1 GB used</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-full">
                    <i data-lucide="image" class="h-6 w-6 text-amber-500"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Events & Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Events -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-medium text-gray-900">Recent Events</h2>
                <a href="events.php" class="text-sm text-primary hover:text-primary/80">View all</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="pb-3 pr-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="pb-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="pb-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="pb-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                            <th class="pb-3 pl-6 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_events as $event): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 pr-6">
                                <a href="event-detail.php?id=<?= $event['id'] ?>" class="text-gray-900 font-medium hover:text-primary">
                                    <?= htmlspecialchars($event['title']) ?>
                                </a>
                            </td>
                            <td class="py-4 px-6 text-gray-500">
                                <?= date('M d, Y', strtotime($event['date'])) ?>
                            </td>
                            <td class="py-4 px-6">
                                <?php if ($event['status'] == 'active'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 text-gray-500">
                                <?= number_format($event['views']) ?>
                            </td>
                            <td class="py-4 pl-6 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="event-detail.php?id=<?= $event['id'] ?>" class="text-gray-500 hover:text-primary" title="View Details">
                                        <i data-lucide="eye" class="h-5 w-5"></i>
                                    </a>
                                    <a href="event-detail.php?id=<?= $event['id'] ?>&tab=edit" class="text-gray-500 hover:text-primary" title="Edit Event">
                                        <i data-lucide="edit" class="h-5 w-5"></i>
                                    </a>
                                    <button type="button" class="text-gray-500 hover:text-red-500" title="Delete Event">
                                        <i data-lucide="trash-2" class="h-5 w-5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <h2 class="text-lg font-medium text-gray-900 mb-6">Quick Actions</h2>
            
            <div class="space-y-4">
                <a href="event-creation.php" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-150">
                    <div class="p-3 mr-4 bg-blue-100 rounded-full">
                        <i data-lucide="plus-circle" class="h-6 w-6 text-blue-500"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Create New Event</h3>
                        <p class="text-xs text-gray-500">Upload poster and generate content</p>
                    </div>
                </a>
                
                <a href="media-library.php" class="flex items-center p-4 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors duration-150">
                    <div class="p-3 mr-4 bg-amber-100 rounded-full">
                        <i data-lucide="image" class="h-6 w-6 text-amber-500"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Media Library</h3>
                        <p class="text-xs text-gray-500">Manage your uploaded media</p>
                    </div>
                </a>
                
                <a href="analytics-dashboard.php" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-150">
                    <div class="p-3 mr-4 bg-green-100 rounded-full">
                        <i data-lucide="bar-chart" class="h-6 w-6 text-green-500"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Analytics Dashboard</h3>
                        <p class="text-xs text-gray-500">View performance metrics</p>
                    </div>
                </a>
                
                <a href="user-profile.php" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-150">
                    <div class="p-3 mr-4 bg-purple-100 rounded-full">
                        <i data-lucide="settings" class="h-6 w-6 text-purple-500"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Account Settings</h3>
                        <p class="text-xs text-gray-500">Update your profile and preferences</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Visit Admin Dashboard (only visible for admins) -->
    <?php if ($is_admin): ?>
    <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-lg mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-indigo-900">Administrator Tools</h3>
                <p class="text-indigo-700 text-sm">Access additional administrative features and system settings</p>
            </div>
            <a href="admin-dashboard.php" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md flex items-center gap-2">
                <i data-lucide="shield" class="h-4 w-4"></i>
                <span>Admin Dashboard</span>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Example of a chart that could be added later with a JS library
    // (placeholder for future implementation)
    const addCharts = () => {
        console.log('Charts would be initialized here using a library of choice');
        // This is where we'd initialize charts when that library is chosen
    };
    
    // Call this when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        // Open dropdowns in the sidebar for current section
        const currentPageUrl = window.location.pathname;
        const navLinks = document.querySelectorAll('#sidebar a');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPageUrl.split('/').pop()) {
                link.classList.add('bg-primary/10', 'text-primary');
                
                // If it's in a dropdown, open the dropdown
                const parentDropdown = link.closest('.nav-group');
                if (parentDropdown) {
                    const dropdownButton = parentDropdown.querySelector('button');
                    const dropdownContent = link.parentElement;
                    
                    if (dropdownContent.classList.contains('hidden')) {
                        dropdownContent.classList.remove('hidden');
                    }
                    
                    const chevron = dropdownButton?.querySelector('[data-lucide="chevron-down"]');
                    if (chevron) {
                        chevron.style.transform = 'rotate(180deg)';
                    }
                }
            }
        });
    });
</script>

<?php
// Include dashboard footer to close all tags
require_once 'includes/dashboard-footer.php';
?>
