<?php 
include __DIR__ . '/includes/dashboard-header.php';
?>

<main class="flex-1 p-4 lg:p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Event Details: Pool Tournament</h1>
            <a href="event-creation.php?edit=<?= $_GET['id'] ?? '' ?>" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit Event
            </a>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Event Information</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Date & Time</p>
                            <p class="font-medium">Saturday, April 15, 2023 at 2:00 PM - 6:00 PM</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Location</p>
                            <p class="font-medium">Main Street Billiards, 123 Pool Rd, Englewood, FL</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Description</h2>
                    <p class="text-gray-700">Join us for a fun-filled afternoon of pool! Open to all skill levels. Compete for prizes and enjoy specials on drinks. Teams of 2 allowed.</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Social Media Posts</h2>
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fab fa-facebook-f text-white text-xs"></i>
                        </div>
                        <h3 class="font-medium">Facebook</h3>
                    </div>
                    <p class="text-gray-700 mb-3">🎱 Championship vibes! Ready to sink some shots at the Englewood Pool Tournament this Saturday? Prizes, drinks, and good times await. See you there! #EnglewoodPool #TournamentTime</p>
                    <p class="text-xs text-gray-500">Scheduled: Apr 12, 2023 at 10:00 AM</p>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center">
                            <i class="fab fa-instagram text-white text-xs"></i>
                        </div>
                        <h3 class="font-medium">Instagram</h3>
                    </div>
                    <p class="text-gray-700 mb-3">Get ready to rack 'em up! 🎱 Our annual pool tournament is this Saturday at Main Street Billiards. Swipe for details and tag your partner! #PoolTournament #EnglewoodEvents</p>
                    <div class="mb-3">
                        <img src="https://example.com/pool-tournament-poster.jpg" alt="Pool Tournament Poster" class="rounded-lg w-full max-w-xs">
                    </div>
                    <p class="text-xs text-gray-500">Scheduled: Apr 11, 2023 at 3:30 PM</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Analytics</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Total Reach</p>
                    <p class="text-xl font-bold">12,456</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Engagements</p>
                    <p class="text-xl font-bold">1,234</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Clicks</p>
                    <p class="text-xl font-bold">567</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Registrations</p>
                    <p class="text-xl font-bold">89</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/dashboard-footer.php'; ?>
