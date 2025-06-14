<?php
$page_title = 'My Events';

// Mock data for demonstration
$events = [
    [
        'id' => 1,
        'title' => 'Summer Music Festival 2024',
        'date' => '2024-07-15',
        'location' => 'Central Park, NYC',
        'status' => 'active',
        'thumbnail' => 'Test_posters/CMF-lineup-poster.jpg',
        'posts_generated' => 5,
        'last_modified' => '2 hours ago',
        'description' => 'Annual summer music festival featuring top artists'
    ],
    [
        'id' => 2,
        'title' => 'Tech Conference 2024',
        'date' => '2024-08-22',
        'location' => 'Convention Center',
        'status' => 'draft',
        'thumbnail' => 'Test_posters/Community-Fest-Poster.png',
        'posts_generated' => 0,
        'last_modified' => '1 day ago',
        'description' => 'Technology conference for developers and entrepreneurs'
    ],
    [
        'id' => 3,
        'title' => 'Pool Tournament Championship',
        'date' => '2024-06-10',
        'location' => 'Sports Club',
        'status' => 'past',
        'thumbnail' => 'Test_posters/Pool tournament.jpg',
        'posts_generated' => 8,
        'last_modified' => '2 weeks ago',
        'description' => 'Annual pool tournament with cash prizes'
    ],
    [
        'id' => 4,
        'title' => 'Community Festival',
        'date' => '2024-09-05',
        'location' => 'Town Square',
        'status' => 'active',
        'thumbnail' => 'Test_posters/FLYER-EED-Visitors-FINAL-2024.jpg',
        'posts_generated' => 3,
        'last_modified' => '3 days ago',
        'description' => 'Local community celebration with food and entertainment'
    ]
];

// Filter events based on status parameter
$filter = $_GET['status'] ?? 'all';
$filtered_events = $events;
if ($filter !== 'all') {
    $filtered_events = array_filter($events, function($event) use ($filter) {
        return $event['status'] === $filter;
    });
}

// Count events by status
$status_counts = [
    'all' => count($events),
    'active' => count(array_filter($events, function($e) { return $e['status'] === 'active'; })),
    'draft' => count(array_filter($events, function($e) { return $e['status'] === 'draft'; })),
    'past' => count(array_filter($events, function($e) { return $e['status'] === 'past'; }))
];

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'active': return 'bg-green-100 text-green-800';
        case 'draft': return 'bg-yellow-100 text-yellow-800';
        case 'past': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Include dashboard header (includes sidebars and opens main tag)
require_once 'includes/dashboard-header.php';
?>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Events</h1>
            <p class="text-gray-600 mt-1">Manage and track your event campaigns</p>
        </div>
        <a href="event-creation.php" 
           class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            Create Event
        </a>
    </div>
</div>

<!-- Filter Tabs -->
<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="?status=all" 
               class="<?= $filter === 'all' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                All Events (<?= $status_counts['all'] ?>)
            </a>
            <a href="?status=active" 
               class="<?= $filter === 'active' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Active (<?= $status_counts['active'] ?>)
            </a>
            <a href="?status=draft" 
               class="<?= $filter === 'draft' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Draft (<?= $status_counts['draft'] ?>)
            </a>
            <a href="?status=past" 
               class="<?= $filter === 'past' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Past (<?= $status_counts['past'] ?>)
            </a>
        </nav>
    </div>
</div>

<!-- Search and Sort -->
<div class="mb-6 flex flex-col sm:flex-row gap-4">
    <div class="flex-1">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" 
                   placeholder="Search events..." 
                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary focus:border-primary">
        </div>
    </div>
    <div class="flex items-center gap-2">
        <label for="sort" class="text-sm font-medium text-gray-700">Sort by:</label>
        <select id="sort" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
            <option>Date Created</option>
            <option>Event Date</option>
            <option>Title (A-Z)</option>
            <option>Status</option>
        </select>
    </div>
</div>

<?php if (empty($filtered_events)): ?>
<!-- Empty State -->
<div class="text-center py-12">
    <div class="mx-auto h-12 w-12 text-gray-400">
        <i class="fas fa-calendar-plus text-4xl"></i>
    </div>
    <h3 class="mt-2 text-sm font-medium text-gray-900">No events found</h3>
    <p class="mt-1 text-sm text-gray-500">
        <?php if ($filter === 'all'): ?>
            Get started by creating your first event.
        <?php else: ?>
            No <?= $filter ?> events found. Try a different filter.
        <?php endif; ?>
    </p>
    <?php if ($filter === 'all'): ?>
    <div class="mt-6">
        <a href="event-creation.php" 
           class="inline-flex items-center px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            Create Your First Event
        </a>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<!-- Events Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($filtered_events as $event): ?>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
        <!-- Event Thumbnail -->
        <div class="aspect-video bg-gray-100 relative">
            <img src="<?= htmlspecialchars($event['thumbnail']) ?>" 
                 alt="<?= htmlspecialchars($event['title']) ?>"
                 class="w-full h-full object-cover">
            <div class="absolute top-2 right-2">
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium <?= getStatusBadgeClass($event['status']) ?>">
                    <?= ucfirst($event['status']) ?>
                </span>
            </div>
        </div>
        
        <!-- Event Content -->
        <div class="p-4">
            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                <?= htmlspecialchars($event['title']) ?>
            </h3>
            
            <div class="space-y-2 text-sm text-gray-600 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt w-4 text-center mr-2"></i>
                    <?= date('M j, Y', strtotime($event['date'])) ?>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt w-4 text-center mr-2"></i>
                    <?= htmlspecialchars($event['location']) ?>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-share-alt w-4 text-center mr-2"></i>
                    <?= $event['posts_generated'] ?> posts generated
                </div>
            </div>
            
            <div class="text-xs text-gray-500 mb-4">
                Updated <?= $event['last_modified'] ?>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                <a href="event-details.php?id=<?= $event['id'] ?>" 
                   class="flex-1 text-center px-3 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary/90 transition-colors duration-200">
                    View Details
                </a>
                <a href="event-creation.php?edit=<?= $event['id'] ?>" 
                   class="px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors duration-200"
                   title="Edit Event">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors duration-200"
                        title="View Analytics">
                    <i class="fas fa-chart-bar"></i>
                </button>
                <button class="px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors duration-200"
                        title="Share">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<div class="mt-8 flex items-center justify-between">
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
<?php endif; ?>

<script>
// Search functionality
document.querySelector('input[type="text"]').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const eventCards = document.querySelectorAll('.grid > div');
    
    eventCards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const location = card.querySelector('.fa-map-marker-alt').parentElement.textContent.toLowerCase();
        
        if (title.includes(searchTerm) || location.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Sort functionality
document.getElementById('sort').addEventListener('change', function(e) {
    const sortBy = e.target.value;
    console.log('Sorting by:', sortBy);
});
</script>

<?php
// Include dashboard footer to close all tags
require_once 'includes/dashboard-footer.php';
?>