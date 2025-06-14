<?php 
include __DIR__ . '/includes/dashboard-header.php';

// Mock user data (would come from database in production)
$user_profile = [
    'name' => 'Alex Johnson',
    'email' => 'alex@example.com',
    'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg',
    'joined' => 'January 2023',
    'bio' => 'Event organizer and marketing specialist with a passion for creating memorable experiences.',
    'location' => 'Denver, CO',
    'website' => 'alexjohnson.com',
    'twitter' => '@alexjohnson',
    'timezone' => 'America/Denver',
    'notifications' => true,
    'marketing_emails' => true
];
?>

<main class="flex-1 p-4 lg:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <!-- Cover Photo -->
            <div class="h-40 bg-gradient-to-r from-primary to-secondary">
                <!-- Cover photo upload button -->
                <div class="flex justify-end p-4">
                    <button class="bg-white/90 text-gray-700 hover:bg-white px-3 py-1.5 rounded-full text-sm font-medium shadow-sm transition-all duration-200 flex items-center">
                        <i class="fas fa-camera mr-1.5"></i> Update Cover
                    </button>
                </div>
            </div>
            
            <!-- Profile Info -->
            <div class="px-6 pb-6 -mt-16 relative">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between">
                    <div class="flex items-end">
                        <div class="relative group">
                            <img src="<?= htmlspecialchars($user_profile['avatar']) ?>" alt="Profile" class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-lg" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user_profile['name']) ?>&background=5B73F0&color=fff'">
                            <button class="absolute bottom-2 right-2 bg-white p-2 rounded-full shadow-md text-gray-700 hover:bg-gray-100 transition-all duration-200 opacity-0 group-hover:opacity-100">
                                <i class="fas fa-camera text-sm"></i>
                            </button>
                        </div>
                        <div class="ml-6 mb-2">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($user_profile['name']) ?></h1>
                            <p class="text-gray-600 dark:text-gray-300">Member since <?= $user_profile['joined'] ?></p>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 font-medium">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h2>
                        <button class="text-sm text-primary hover:text-primary-dark font-medium">Edit</button>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="fullName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                <input type="text" id="fullName" value="<?= htmlspecialchars($user_profile['name']) ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                <input type="email" id="email" value="<?= htmlspecialchars($user_profile['email']) ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bio</label>
                            <textarea id="bio" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($user_profile['bio']) ?></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">A short bio about yourself</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                                <input type="text" id="location" value="<?= htmlspecialchars($user_profile['location']) ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Website</label>
                                <input type="url" id="website" value="<?= htmlspecialchars($user_profile['website']) ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Zone</label>
                            <select id="timezone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-white">
                                <?php 
                                $timezones = DateTimeZone::listIdentifiers(DateTimeZone::AMERICA);
                                foreach($timezones as $tz): 
                                    $selected = ($tz === $user_profile['timezone']) ? 'selected' : '';
                                    $tz_display = str_replace('_', ' ', $tz);
                                    echo "<option value=\"$tz\" $selected>$tz_display</option>";
                                endforeach; 
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Social Profiles -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Social Profiles</h2>
                        <button class="text-sm text-primary hover:text-primary-dark font-medium">Add New</button>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <i class="fab fa-twitter"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Twitter</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($user_profile['twitter']) ?></p>
                            </div>
                            <button class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-center bg-gray-50 dark:bg-gray-700 p-3 rounded-lg opacity-50">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                <i class="fab fa-facebook-f"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-500">Facebook</p>
                                <p class="text-sm text-gray-400">Not connected</p>
                            </div>
                            <button class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-center bg-gray-50 dark:bg-gray-700 p-3 rounded-lg opacity-50">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-pink-500 to-yellow-500 text-white flex items-center justify-center">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-500">Instagram</p>
                                <p class="text-sm text-gray-400">Not connected</p>
                            </div>
                            <button class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Security -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Security</h2>
                    
                    <div class="space-y-4">
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">To update your password, please use the "Forgot Password" link on the login page.</p>
                            <a href="forgot-password.php" class="text-primary hover:text-primary-dark font-medium text-sm">
                                Reset Password <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Account Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Settings</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Email Notifications</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Receive email notifications</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer" <?= $user_profile['notifications'] ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:peer-focus:ring-primary/80 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Marketing Emails</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Receive marketing emails</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer" <?= $user_profile['marketing_emails'] ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 dark:peer-focus:ring-primary/80 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                            </label>
                        </div>
                        
                        <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                            <button class="w-full px-4 py-2 border border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200 font-medium">
                                Delete Account
                            </button>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Once you delete your account, there is no going back. Please be certain.</p>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/dashboard-footer.php'; ?>
