<!-- Sidebar Navigation for Admin Users -->
<aside class="sidebar bg-white dark:bg-gray-800 w-64 min-h-screen border-r border-gray-200 dark:border-gray-700 beautiful-shadow flex flex-col" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-sm">
                    <span class="text-sm">PM</span>
                </div>
            </div>
            <div class="flex flex-col justify-center flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white tracking-tight">Dashboard</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-100 shadow-sm">
                        Admin
                    </span>
                </div>
                <p class="text-xs text-gray-500 truncate">Welcome back, <?= htmlspecialchars($user['name']) ?></p>
            </div>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="p-4 space-y-1 flex-grow overflow-y-auto">
        <!-- Dashboard -->
        <a href="/admin/dashboard.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
            <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-gray-500 dark:text-gray-400 group-hover:text-purple-700 dark:group-hover:text-purple-400"></i>
            Admin Dashboard
        </a>

        <!-- User Management -->
        <div class="nav-group">
            <button class="group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400 justify-between">
                <div class="flex items-center">
                    <i data-lucide="users" class="mr-3 h-5 w-5 text-gray-500 dark:text-gray-400 group-hover:text-purple-700 dark:group-hover:text-purple-400"></i>
                    User Management
                </div>
                <i data-lucide="chevron-down" class="h-4 w-4 text-gray-500 dark:text-gray-400"></i>
            </button>
            <div class="pl-10 space-y-1 mt-1">
                <a href="/admin/user-management.php" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    View All Users
                </a>
                <a href="/admin/user-management.php?activity=1" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    Activity Logs
                </a>
            </div>
        </div>

        <!-- Event Management -->
        <div class="nav-group">
            <button class="group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400 justify-between">
                <div class="flex items-center">
                    <i data-lucide="calendar" class="mr-3 h-5 w-5 text-gray-500 dark:text-gray-400 group-hover:text-purple-700 dark:group-hover:text-purple-400"></i>
                    Event Management
                </div>
                <i data-lucide="chevron-down" class="h-4 w-4 text-gray-500 dark:text-gray-400"></i>
            </button>
            <div class="pl-10 space-y-1 mt-1">
                <a href="/admin/events.php" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    All Events
                    <span class="ml-auto inline-block py-0.5 px-2 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        42
                    </span>
                </a>
                <a href="/admin/events.php?status=claimed" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    Claimed Events
                    <span class="ml-auto inline-block py-0.5 px-2 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                        35
                    </span>
                </a>
                <a href="/admin/events.php?status=unclaimed" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    Unclaimed Events
                    <span class="ml-auto inline-block py-0.5 px-2 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                        7
                    </span>
                </a>
            </div>
        </div>

        <!-- Content Management -->
        <div class="nav-group">
            <button class="group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400 justify-between">
                <div class="flex items-center">
                    <i data-lucide="file-text" class="mr-3 h-5 w-5 text-gray-500 dark:text-gray-400 group-hover:text-purple-700 dark:group-hover:text-purple-400"></i>
                    Content
                </div>
                <i data-lucide="chevron-down" class="h-4 w-4 text-gray-500 dark:text-gray-400"></i>
            </button>
            <div class="pl-10 space-y-1 mt-1">
                <a href="/admin-media.php" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    Media Library
                </a>
                <a href="/admin-templates.php" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    Email Templates
                </a>
                <a href="/admin-pages.php" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    Static Pages
                </a>
            </div>
        </div>

        <!-- Analytics & Reports -->
        <a href="/admin-analytics.php" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
            <i data-lucide="bar-chart-2" class="mr-3 h-5 w-5 text-gray-500 dark:text-gray-400 group-hover:text-purple-700 dark:group-hover:text-purple-400"></i>
            Analytics & Reports
        </a>

        <!-- System Settings -->
        <div class="nav-group">
            <button class="group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400 justify-between">
                <div class="flex items-center">
                    <i data-lucide="settings" class="mr-3 h-5 w-5 text-gray-500 dark:text-gray-400 group-hover:text-purple-700 dark:group-hover:text-purple-400"></i>
                    System Settings
                </div>
                <i data-lucide="chevron-down" class="h-4 w-4 text-gray-500 dark:text-gray-400"></i>
            </button>
            <div class="pl-10 space-y-1 mt-1">
                <a href="/admin-settings.php?general=1" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    General Settings
                </a>
                <a href="/admin-settings.php?api=1" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    API Configuration
                </a>
                <a href="/admin-settings.php?email=1" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    Email Settings
                </a>
                <a href="/admin-settings.php?integrations=1" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    Integrations
                </a>
                <a href="/admin-settings.php?security=1" class="group flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400">
                    Security Settings
                </a>
            </div>
        </div>

        <!-- Divider -->

    </nav>

    <!-- Sidebar Footer with User Profile Popup -->
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 relative sticky bottom-0 bg-white dark:bg-gray-800 z-10">
        <!-- System Status -->
        <div class="mb-3 px-3">
            <div class="flex justify-between items-center mb-1">
                <a href="/system-overview.php" class="text-xs font-medium text-gray-700 hover:text-purple-600 hover:underline">System Status</a>
                <span class="text-xs font-medium text-green-600">Healthy</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: 95%"></div>
            </div>
        </div>
        
        <!-- Admin Profile Button -->
        <button id="profile-menu-button" class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
            <div class="flex items-center">
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Profile" class="h-8 w-8 rounded-full object-cover border border-gray-200 mr-2" 
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=5B73F0&color=fff'">
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($user['name']) ?></h3>
                    <p class="text-xs text-gray-500 truncate">Administrator</p>
                </div>
            </div>
            <i data-lucide="chevron-up" class="h-4 w-4 text-gray-500 transition-transform duration-200" id="profile-chevron"></i>
        </button>
        
        <!-- Admin Profile Popup (Hidden by default) -->
        <div id="profile-menu" class="hidden absolute bottom-full left-0 w-full bg-white rounded-lg shadow-lg border border-gray-100 mb-2 z-50">
            <div class="p-2">
                <a href="/admin-profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-md">
                    <i data-lucide="user" class="inline-block mr-2 h-4 w-4 align-text-bottom"></i> Admin Profile
                </a>
                <a href="/admin-settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-md">
                    <i data-lucide="settings" class="inline-block mr-2 h-4 w-4 align-text-bottom"></i> Settings
                </a>
                <a href="/admin-logs.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-md">
                    <i data-lucide="file-text" class="inline-block mr-2 h-4 w-4 align-text-bottom"></i> System Logs
                </a>
                <div class="border-t border-gray-200 my-2"></div>
                <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md">
                    <i data-lucide="log-out" class="inline-block mr-2 h-4 w-4 align-text-bottom"></i> Sign Out
                </a>
            </div>
        </div>
    </div>
</aside>

<script>
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
    
    // Show/hide sidebar on mobile
    document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        
        if (window.innerWidth < 768 && 
            sidebar && 
            sidebarToggle && 
            !sidebar.contains(e.target) && 
            !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
    
    // Toggle profile menu dropdown
    document.getElementById('profile-menu-button')?.addEventListener('click', () => {
        const profileMenu = document.getElementById('profile-menu');
        const chevron = document.getElementById('profile-chevron');
        
        profileMenu.classList.toggle('hidden');
        
        if (chevron) {
            chevron.style.transform = profileMenu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    });
    
    // Close profile menu when clicking elsewhere
    document.addEventListener('click', (e) => {
        const profileMenu = document.getElementById('profile-menu');
        const profileMenuButton = document.getElementById('profile-menu-button');
        
        if (profileMenu && 
            profileMenuButton && 
            !profileMenu.contains(e.target) && 
            !profileMenuButton.contains(e.target)) {
            profileMenu.classList.add('hidden');
            document.getElementById('profile-chevron').style.transform = 'rotate(0deg)';
        }
    });
</script>
