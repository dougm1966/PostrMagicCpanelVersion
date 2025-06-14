<?php
// Set page title
$page_title = "Event Details";

// Include header
require_once 'includes/header.php';

// Mock data for event (would normally come from database)
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$event = [
    'id' => $event_id,
    'title' => 'Summer Music Festival 2024',
    'date' => '08/14/2024',
    'time' => '7:00 PM - 11:00 PM',
    'location' => '123 Music Ave, Downtown, CA 90210',
    'description' => 'Join us for an unforgettable night of live music featuring local and international artists in the heart of downtown.',
    'status' => 'active',
    'organizer' => 'City Events LLC',
    'contact' => '+1 (555) 123-4567',
    'thumbnail' => 'assets/images/event-placeholder.jpg',
    'views' => 1254,
    'shares' => 87,
    'engagement' => '32%'
];

// Ensure we have a valid CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!-- Event Detail Page Container -->
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Back Navigation -->
    <div class="mb-6">
        <a href="javascript:history.back()" class="inline-flex items-center text-primary hover:underline">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Back to Events</span>
        </a>
    </div>
    
    <!-- Event Header Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <!-- Event Main Info -->
        <div class="md:flex">
            <!-- Event Image -->
            <div class="md:w-1/3 bg-gray-100 flex items-center justify-center p-4" style="min-height: 200px;">
                <img src="<?= htmlspecialchars($event['thumbnail']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" 
                     class="w-full object-cover rounded" onerror="this.src='assets/images/event-placeholder.jpg'">
            </div>
            
            <!-- Event Info -->
            <div class="md:w-2/3 p-6">
                <div class="flex justify-between items-start">
                    <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($event['title']) ?></h1>
                    <span class="px-3 py-1 rounded-full text-sm font-medium <?= $event['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                        <?= ucfirst(htmlspecialchars($event['status'])) ?>
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="flex items-center text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-primary"></i>
                            <span><?= htmlspecialchars($event['date']) ?></span>
                        </div>
                        <div class="flex items-center text-gray-700 mb-2">
                            <i class="fas fa-clock mr-2 text-primary"></i>
                            <span><?= htmlspecialchars($event['time']) ?></span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-map-marker-alt mr-2 text-primary"></i>
                            <span><?= htmlspecialchars($event['location']) ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-primary"></i>
                            <span>Organizer: <?= htmlspecialchars($event['organizer']) ?></span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-phone mr-2 text-primary"></i>
                            <span><?= htmlspecialchars($event['contact']) ?></span>
                        </div>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-4"><?= htmlspecialchars($event['description']) ?></p>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <button class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center">
                        <i class="fas fa-share-alt mr-2"></i> Share Event
                    </button>
                    <button class="bg-white border border-primary hover:bg-gray-50 text-primary px-4 py-2 rounded flex items-center">
                        <i class="fas fa-calendar-plus mr-2"></i> Add to Calendar
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="border-t border-gray-200 py-4 px-6 bg-gray-50">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-xl font-semibold"><?= number_format($event['views']) ?></div>
                    <div class="text-sm text-gray-500">Views</div>
                </div>
                <div>
                    <div class="text-xl font-semibold"><?= number_format($event['shares']) ?></div>
                    <div class="text-sm text-gray-500">Shares</div>
                </div>
                <div>
                    <div class="text-xl font-semibold"><?= htmlspecialchars($event['engagement']) ?></div>
                    <div class="text-sm text-gray-500">Engagement</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs Navigation -->
    <div class="mb-8">
        <div class="border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="tab-button active py-2 px-4 font-medium text-primary border-b-2 border-primary" 
                            id="content-tab" data-tab="content" role="tab">
                        <i class="fas fa-file-alt mr-2"></i>Content
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="tab-button py-2 px-4 font-medium text-gray-500 hover:text-primary border-b-2 border-transparent" 
                            id="analytics-tab" data-tab="analytics" role="tab">
                        <i class="fas fa-chart-bar mr-2"></i>Analytics
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="tab-button py-2 px-4 font-medium text-gray-500 hover:text-primary border-b-2 border-transparent" 
                            id="settings-tab" data-tab="settings" role="tab">
                        <i class="fas fa-cog mr-2"></i>Settings
                    </button>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Tab Content -->
    <div class="tab-content-container">
        <!-- Content Tab (visible by default) -->
        <div class="tab-content active" id="content-content">
            <div class="bg-white rounded-lg shadow-md overflow-hidden p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Generated Content</h2>
                <p class="text-gray-600 mb-4">Content will be displayed here.</p>
                <div class="bg-gray-100 p-4 rounded-lg text-center">
                    <p class="text-gray-500">Loading content...</p>
                </div>
            </div>
        </div>
        
        <!-- Analytics Tab (hidden by default) -->
        <div class="tab-content hidden" id="analytics-content">
            <div class="bg-white rounded-lg shadow-md overflow-hidden p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Event Analytics</h2>
                <p class="text-gray-600 mb-4">Analytics data will be displayed here.</p>
                <div class="bg-gray-100 p-4 rounded-lg text-center">
                    <p class="text-gray-500">Loading analytics...</p>
                </div>
            </div>
        </div>
        
        <!-- Settings Tab (hidden by default) -->
        <div class="tab-content hidden" id="settings-content">
            <div class="bg-white rounded-lg shadow-md overflow-hidden p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Event Settings</h2>
                <p class="text-gray-600 mb-4">Settings will be displayed here.</p>
                <div class="bg-gray-100 p-4 rounded-lg text-center">
                    <p class="text-gray-500">Loading settings...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
