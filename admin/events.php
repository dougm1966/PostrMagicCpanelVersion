<?php
$page_title = 'Event Management';

// Force admin view
$_SESSION['is_admin'] = true;

// Mock data for admin event management
$events = [
    [
        'id' => 1,
        'title' => 'Summer Music Festival 2024',
        'date' => '2024-07-15',
        'location' => 'Central Park, NYC',
        'status' => 'active',
        'thumbnail' => '../Test_posters/CMF-lineup-poster.jpg',
        'posts_generated' => 5,
        'last_modified' => '2 hours ago',
        'creator' => 'John Doe',
        'creator_email' => 'john@example.com',
        'flagged' => false,
        'views' => 1250,
        'engagement' => 8.5
    ],
    [
        'id' => 2,
        'title' => 'Tech Conference 2024',
        'date' => '2024-08-22',
        'location' => 'Convention Center',
        'status' => 'pending',
        'thumbnail' => '../Test_posters/Community-Fest-Poster.png',
        'posts_generated' => 0,
        'last_modified' => '1 day ago',
        'creator' => 'Jane Smith',
        'creator_email' => 'jane@example.com',
        'flagged' => false,
        'views' => 0,
        'engagement' => 0
    ],
    [
        'id' => 3,
        'title' => 'Pool Tournament Championship',
        'date' => '2024-06-10',
        'location' => 'Sports Club',
        'status' => 'active',
        'thumbnail' => '../Test_posters/Pool tournament.jpg',
        'posts_generated' => 8,
        'last_modified' => '2 weeks ago',
        'creator' => 'Mike Johnson',
        'creator_email' => 'mike@example.com',
        'flagged' => true,
        'views' => 890,
        'engagement' => 6.2
    ],
    [
        'id' => 4,
        'title' => 'Community Festival',
        'date' => '2024-09-05',
        'location' => 'Town Square',
        'status' => 'active',
        'thumbnail' => '../Test_posters/FLYER-EED-Visitors-FINAL-2024.jpg',
        'posts_generated' => 3,
        'last_modified' => '3 days ago',
        'creator' => 'Sarah Wilson',
        'creator_email' => 'sarah@example.com',
        'flagged' => false,
        'views' => 2100,
        'engagement' => 12.3
    ]
];

// Filter events based on status parameter
$filter = $_GET['filter'] ?? 'all';
$filtered_events = $events;

switch ($filter) {
    case 'pending':
        $filtered_events = array_filter($events, function($e) { return $e['status'] === 'pending'; });
        break;
    case 'flagged':
        $filtered_events = array_filter($events, function($e) { return $e['flagged'] === true; });
        break;
    case 'active':
        $filtered_events = array_filter($events, function($e) { return $e['status'] === 'active'; });
        break;
}

// Count events by filter
$filter_counts = [
    'all' => count($events),
    'active' => count(array_filter($events, function($e) { return $e['status'] === 'active'; })),
    'pending' => count(array_filter($events, function($e) { return $e['status'] === 'pending'; })),
    'flagged' => count(array_filter($events, function($e) { return $e['flagged'] === true; }))
];

function getAdminStatusBadgeClass($status) {
    switch ($status) {
        case 'active': return 'bg-green-100 text-green-800';
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'suspended': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Include dashboard header (includes admin sidebar and opens main tag)
require_once '../includes/dashboard-header.php';
?>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Event Management</h1>
            <p class="text-gray-600 mt-1">Monitor and manage all user events</p>
        </div>
        <div class="flex gap-2">
            <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i class="fas fa-download mr-2"></i>
                Export Events
            </button>
            <button class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors duration-200">
                <i class="fas fa-cog mr-2"></i>
                Settings
            </button>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Events</p>
                <p class="text-3xl font-bold text-gray-900"><?= count($events) ?></p>
            </div>
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-calendar-alt text-blue-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Pending Review</p>
                <p class="text-3xl font-bold text-yellow-600"><?= $filter_counts['pending'] ?></p>
            </div>
            <div class="p-3 bg-yellow-100 rounded-lg">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Flagged Content</p>
                <p class="text-3xl font-bold text-red-600"><?= $filter_counts['flagged'] ?></p>
            </div>
            <div class="p-3 bg-red-100 rounded-lg">
                <i class="fas fa-flag text-red-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Active Events</p>
                <p class="text-3xl font-bold text-green-600"><?= $filter_counts['active'] ?></p>
            </div>
            <div class="p-3 bg-green-100 rounded-lg">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="?filter=all" 
               class="<?= $filter === 'all' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                All Events (<?= $filter_counts['all'] ?>)
            </a>
            <a href="?filter=pending" 
               class="<?= $filter === 'pending' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Pending Review (<?= $filter_counts['pending'] ?>)
            </a>
            <a href="?filter=flagged" 
               class="<?= $filter === 'flagged' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Flagged Content (<?= $filter_counts['flagged'] ?>)
            </a>
            <a href="?filter=active" 
               class="<?= $filter === 'active' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Active (<?= $filter_counts['active'] ?>)
            </a>
        </nav>
    </div>
</div>

<!-- Search and Actions -->
<div class="mb-6 flex flex-col sm:flex-row gap-4">
    <div class="flex-1">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" 
                   placeholder="Search events, creators, or content..." 
                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary focus:border-primary">
        </div>
    </div>
    <div class="flex items-center gap-2">
        <select class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
            <option>Sort by Date</option>
            <option>Sort by Creator</option>
            <option>Sort by Status</option>
            <option>Sort by Engagement</option>
        </select>
        <button class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-colors duration-200">
            Bulk Actions
        </button>
    </div>
</div>

<!-- Events Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Modified</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($filtered_events as $event): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                <img class="h-12 w-12 rounded-lg object-cover" src="<?= htmlspecialchars($event['thumbnail']) ?>" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($event['title']) ?></div>
                                <div class="text-sm text-gray-500"><?= date('M j, Y', strtotime($event['date'])) ?> • <?= htmlspecialchars($event['location']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= htmlspecialchars($event['creator']) ?></div>
                        <div class="text-sm text-gray-500"><?= htmlspecialchars($event['creator_email']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium <?= getAdminStatusBadgeClass($event['status']) ?>">
                                <?= ucfirst($event['status']) ?>
                            </span>
                            <?php if ($event['flagged']): ?>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-flag mr-1"></i>Flagged
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div><?= number_format($event['views']) ?> views</div>
                        <div class="text-gray-500"><?= $event['engagement'] ?>% engagement</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= $event['last_modified'] ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <button class="text-primary hover:text-primary/80" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($event['status'] === 'pending'): ?>
                            <button class="text-green-600 hover:text-green-800" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <?php endif; ?>
                            <button class="text-red-600 hover:text-red-800" title="Suspend">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6 flex items-center justify-between">
    <div class="text-sm text-gray-700">
        Showing <span class="font-medium"><?= count($filtered_events) ?></span> of <span class="font-medium"><?= count($events) ?></span> events
    </div>
    <div class="flex items-center space-x-2">
        <button class="px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors duration-200" disabled>
            Previous
        </button>
        <button class="px-3 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 transition-colors duration-200">
            1
        </button>
        <button class="px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors duration-200" disabled>
            Next
        </button>
    </div>
</div>

<script>
// Search functionality
document.querySelector('input[type="text"]').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        const eventTitle = row.querySelector('.text-sm.font-medium').textContent.toLowerCase();
        const creatorName = row.querySelector('td:nth-child(3) .text-sm.text-gray-900').textContent.toLowerCase();
        
        if (eventTitle.includes(searchTerm) || creatorName.includes(searchTerm)) {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    });
});

// Bulk checkbox functionality
document.querySelector('thead input[type="checkbox"]').addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = e.target.checked;
    });
});
</script>

<?php
// Include dashboard footer to close all tags
require_once '../includes/dashboard-footer.php';
?>