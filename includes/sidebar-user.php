<!-- Sidebar Navigation for Regular Users -->
<aside class="sidebar bg-white w-64 min-h-screen border-r border-gray-200 beautiful-shadow flex flex-col" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header p-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-white font-bold shadow-sm">
                    <span class="text-sm">PM</span>
                </div>
            </div>
            <div class="flex flex-col justify-center flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900 tracking-tight">Dashboard</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-sm">
                        Active
                    </span>
                </div>
                <p class="text-xs text-gray-500 truncate">Welcome back, <?= htmlspecialchars($user['name']) ?></p>
            </div>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="p-4 space-y-1 flex-grow overflow-y-auto">
        <!-- Dashboard -->
        <a href="dashboard.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 hover:bg-primary/10 hover:text-primary">
            <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-gray-500 group-hover:text-primary"></i>
            Dashboard
        </a>

        <!-- My Events -->
        <div class="nav-group">
            <button class="group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 hover:bg-primary/10 hover:text-primary justify-between">
                <div class="flex items-center">
                    <i data-lucide="calendar" class="mr-3 h-5 w-5 text-gray-500 group-hover:text-primary"></i>
                    My Events
                </div>
                <i data-lucide="chevron-down" class="h-4 w-4 text-gray-500"></i>
            </button>
            <div class="pl-10 space-y-1 mt-1">
                <a href="events.php?status=active" class="group flex items-center px-3 py-2 text-sm text-gray-700 rounded-md hover:bg-primary/10 hover:text-primary">
                    Active Events
                    <span class="ml-auto inline-block py-0.5 px-2 text-xs rounded-full bg-green-100 text-green-800">
                        3
                    </span>
                </a>
                <a href="events.php?status=draft" class="group flex items-center px-3 py-2 text-sm text-gray-700 rounded-md hover:bg-primary/10 hover:text-primary">
                    Draft Events
                    <span class="ml-auto inline-block py-0.5 px-2 text-xs rounded-full bg-gray-100 text-gray-800">
                        1
                    </span>
                </a>
                <a href="events.php?status=past" class="group flex items-center px-3 py-2 text-sm text-gray-700 rounded-md hover:bg-primary/10 hover:text-primary">
                    Past Events
                </a>
            </div>
        </div>

        <!-- Event Creation -->
        <a href="event-creation.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 hover:bg-primary/10 hover:text-primary">
            <i data-lucide="plus-circle" class="mr-3 h-5 w-5 text-gray-500 group-hover:text-primary"></i>
            Create Event
        </a>

        <!-- Media Library -->
        <a href="media-library.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 hover:bg-primary/10 hover:text-primary">
            <i data-lucide="image" class="mr-3 h-5 w-5 text-gray-500 group-hover:text-primary"></i>
            Media Library
        </a>

        <!-- Analytics Dashboard -->
        <a href="analytics-dashboard.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 hover:bg-primary/10 hover:text-primary">
            <i data-lucide="bar-chart" class="mr-3 h-5 w-5 text-gray-500 group-hover:text-primary"></i>
            Analytics
        </a>

        <!-- Divider -->
        <hr class="my-4 border-gray-200">
    </nav>

    <!-- Sidebar Footer with User Profile Popup -->
    <div class="px-4 py-4 border-t border-gray-200 relative sticky bottom-0 bg-white dark:bg-gray-900 z-10">
        <!-- User Profile Button -->
        <div class="relative">
            <button id="profile-menu-button" class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200" 
                    onclick="toggleProfileMenu()" 
                    aria-expanded="false" 
                    aria-controls="profile-menu">
                <div class="flex items-center">
                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Profile" class="h-8 w-8 rounded-full object-cover border border-gray-200 mr-2" 
                         onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=5B73F0&color=fff'">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($user['name']) ?></h3>
                        <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>
                <i data-lucide="chevron-up" class="h-4 w-4 text-gray-500 transition-transform duration-200" id="profile-chevron"></i>
            </button>
            
            <!-- User Profile Popup (Hidden by default) -->
            <div id="profile-menu" 
                 class="hidden absolute bottom-full left-2 right-2 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-lg border border-gray-300 dark:border-gray-500 mb-2 z-50">
            <!-- Token Count Section -->
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Available Tokens</span>
                    <button class="p-1 text-xs text-white bg-primary hover:bg-primary/90 rounded-full w-5 h-5 flex items-center justify-center">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-1">
                    <div class="bg-primary h-2 rounded-full" style="width: 75%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                    <span>1,250 tokens</span>
                    <a href="#" class="text-primary hover:underline">Get more</a>
                </div>
            </div>
            <div class="p-2">
                <a href="user-profile.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-primary/10 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <i data-lucide="user" class="inline-block mr-2 h-4 w-4 align-text-bottom"></i> My Profile
                </a>
                <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-primary/10 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <i data-lucide="settings" class="inline-block mr-2 h-4 w-4 align-text-bottom"></i> Settings
                </a>
                <a href="help.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-primary/10 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <i data-lucide="help-circle" class="inline-block mr-2 h-4 w-4 align-text-bottom"></i> Help & Support
                </a>
                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                <a href="<?= BASE_URL ?>logout.php" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-primary/10 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <i data-lucide="log-out" class="inline-block mr-2 h-4 w-4 align-text-bottom"></i> Sign Out
                </a>
            </div>
        </div>
    </div>
</aside>

<!-- inline dashboard script moved to assets/js/dashboard.js
    // Initialize Lucide icons
    lucide.createIcons();

    // Initialize dropdown toggles for the sidebar
    document.querySelectorAll('.nav-group button').forEach(button => {
        button.addEventListener('click', (e) => {
            const dropdown = button.nextElementSibling;
            const chevron = button.querySelector('[data-lucide="chevron-down"]');
            
            dropdown.classList.toggle('hidden');
            
            if (chevron) {
                chevron.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        });
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        
        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target) && window.innerWidth < 1024) {
            sidebar.classList.add('hidden');
        }
    });

    // Toggle profile menu on click
    function toggleProfileMenu() {
        const profileMenu = document.getElementById('profile-menu');
        const isOpen = !profileMenu.classList.contains('hidden');
        
        // Close all other open menus first
        document.querySelectorAll('.profile-menu').forEach(menu => {
            if (menu !== profileMenu) menu.classList.add('hidden');
        });
        
        // Toggle current menu
        profileMenu.classList.toggle('hidden');
        const chevron = document.getElementById('profile-chevron');
        chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        
        // Update aria-expanded
        const button = document.getElementById('profile-menu-button');
        button.setAttribute('aria-expanded', !isOpen);
    }
    
    
    // Close profile menu
    function closeProfileMenu() {
        const profileMenu = document.getElementById('profile-menu');
        profileMenu.classList.add('hidden');
        document.getElementById('profile-chevron').style.transform = 'rotate(0deg)';
        document.getElementById('profile-menu-button').setAttribute('aria-expanded', 'false');
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const profileMenu = document.getElementById('profile-menu');
        const profileButton = document.getElementById('profile-menu-button');
        
        if (!profileMenu.contains(event.target) && !profileButton.contains(event.target)) {
            closeProfileMenu();
        }
    });
    
    // Close menu when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeProfileMenu();
        }
    });
-->
