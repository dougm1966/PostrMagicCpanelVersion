<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL if not already defined
if (!defined('BASE_URL')) {
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
    define('BASE_URL', $base_url);
}

// Include config and auth helper
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth-helper.php';

// Set page title
$page_title = isset($page_title) ? $page_title . ' - PostrMagic' : 'PostrMagic Dashboard';

// Determine if user is admin using our auth helper
$is_admin = isAdmin();

// Get current user data
$current_user = getCurrentUser();
$user = [
    'name' => $current_user['username'] ?? 'User',
    'email' => $current_user['email'] ?? '',
    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($current_user['username'] ?? 'U') . '&background=6366f1&color=fff',
    'notifications' => 0
];
?>
<?php
// Check for dark mode preference from cookie or user settings
$dark_mode = isset($_COOKIE['dark_mode']) ? $_COOKIE['dark_mode'] === 'true' : true; // Default to dark mode if no preference set
?>
<!DOCTYPE html>
<html lang="en" class="<?= $dark_mode ? 'dark' : '' ?>">
<head>
    <script>
        // Prevent flash of light mode by immediately setting the theme before any content loads
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              primary: '#5B73F0',
              secondary: '#54E8CC',
              accent: '#54E8CC',
              dark: '#0F172A',
            },
            fontFamily: {
              sans: ['Poppins', 'sans-serif'],
            }
          }
        }
      }
    </script>
    
    <!-- Custom CSS including clamp for responsive typography -->
    <style>
      /* CSS for better scrolling performance */
      html {
        scroll-behavior: smooth;
        transition: background-color 0.3s ease, color 0.3s ease;
      }
      
      body {
        overflow-x: hidden;
        transition: background-color 0.3s ease, color 0.3s ease;
      }
      
      /* Dark mode styles */
      .dark body {
        background-color: #0f172a;
        color: #e2e8f0;
      }
      
      .dark .bg-white {
        background-color: #1e293b !important;
      }
      
      .dark .text-gray-900 {
        color: #e2e8f0 !important;
      }
      
      .dark .text-gray-700 {
        color: #cbd5e1 !important;
      }
      
      .dark .text-gray-600 {
        color: #94a3b8 !important;
      }
      
      .dark .text-gray-500 {
        color: #64748b !important;
      }
      
      .dark .border-gray-200 {
        border-color: #374151 !important;
      }
      
      .dark .bg-gray-100 {
        background-color: #374151 !important;
      }
      
      .dark .bg-gray-50 {
        background-color: #1f2937 !important;
      }
      
      /* Dark mode for borders and shadows */
      .dark .border-gray-100 {
        border-color: #374151 !important;
      }
      
      .dark .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3) !important;
      }
      
      /* Dark mode for hover states */
      .dark .hover\:bg-gray-50:hover {
        background-color: #374151 !important;
      }
      
      .dark tr:hover {
        background-color: #374151 !important;
      }
      
      /* Dark mode for status badges */
      .dark .bg-yellow-100 {
        background-color: #451a03 !important;
      }
      
      .dark .text-yellow-800 {
        color: #fbbf24 !important;
      }
      
      .dark .bg-green-100 {
        background-color: #064e3b !important;
      }
      
      .dark .text-green-800 {
        color: #34d399 !important;
      }
      
      .dark .bg-gray-100.text-gray-800 {
        background-color: #374151 !important;
        color: #d1d5db !important;
      }
      
      /* Dark mode for cards and containers */
      .dark .bg-white.rounded-lg {
        background-color: #1e293b !important;
        border-color: #374151 !important;
      }
      
      /* Dark mode for sidebar profile button */
      .dark #sidebar .hover\:bg-gray-100:hover {
        background-color: #374151 !important;
      }
      
      /* Dark mode for user dropdown menu */
      .dark .bg-white.rounded-md.shadow-lg {
        background-color: #1e293b !important;
        border-color: #374151 !important;
      }
      
      .dark .hover\:bg-gray-100:hover {
        background-color: #374151 !important;
      }
      
      .dark .hover\:bg-red-50:hover {
        background-color: #7f1d1d !important;
      }
      
      /* Dark mode for quick action descriptions */
      .dark .text-xs.text-gray-500 {
        color: #94a3b8 !important;
      }
      
      /* Keep Quick Action titles dark in dark mode for contrast */
      .dark .bg-blue-50 h3.text-sm.font-medium.text-gray-900,
      .dark .bg-amber-50 h3.text-sm.font-medium.text-gray-900,
      .dark .bg-green-50 h3.text-sm.font-medium.text-gray-900,
      .dark .bg-purple-50 h3.text-sm.font-medium.text-gray-900 {
        color: #111827 !important;
      }
      
      /* Implement fluid typography with clamp() */
      h1 {
        font-size: clamp(1.5rem, 5vw, 2.25rem);
      }
      
      h2 {
        font-size: clamp(1.25rem, 3vw, 1.75rem);
      }
      
      h3 {
        font-size: clamp(1.1rem, 2vw, 1.5rem);
      }
      
      p, .text-base {
        font-size: clamp(0.9rem, 1.5vw, 1rem);
      }
      
      .text-sm {
        font-size: clamp(0.8rem, 1.2vw, 0.875rem);
      }
      
      /* Sidebar styling */
      .sidebar {
        width: 280px;
        transition: all 0.3s ease;
      }
      
      @media (max-width: 1024px) {
        .sidebar {
          width: 240px;
        }
      }
      
      @media (max-width: 768px) {
        .sidebar {
          width: 100%;
          position: fixed;
          z-index: 40;
          transform: translateX(-100%);
        }
        
        .sidebar.open {
          transform: translateX(0);
        }
      }
      
      /* Beautiful shadow effect */
      .beautiful-shadow {
        box-shadow: 0px 0px 0px 1px rgba(0,0,0,0.06), 
                    0px 1px 1px -0.5px rgba(0,0,0,0.06), 
                    0px 3px 3px -1.5px rgba(0,0,0,0.06), 
                    0px 6px 6px -3px rgba(0,0,0,0.06), 
                    0px 12px 12px -6px rgba(0,0,0,0.06), 
                    0px 24px 24px -12px rgba(0,0,0,0.06);
      }
    </style>
    
    <!-- Original CSS will be gradually migrated -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css" />
    <script src="<?= BASE_URL ?>assets/js/main.js" defer></script>
    <script src="<?= BASE_URL ?>assets/js/dashboard.js" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="font-sans bg-gray-100 overflow-x-hidden">
    <!-- Dashboard Header -->
    <header class="w-full bg-white shadow-sm z-40 sticky top-0">
        <div class="w-full px-4 py-3 flex items-center justify-between">
            <!-- Left section with logo and toggle -->
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-200 lg:hidden" aria-label="Toggle sidebar">
                    <i class="fas fa-bars text-gray-700"></i>
                </button>
                <h1 class="text-primary font-bold text-xl"><a href="<?= BASE_URL ?>">PostrMagic</a></h1>
            </div>
            
            <!-- Right section with notifications and search -->
            <div class="flex items-center gap-4">
                
                <!-- Search button -->
                <button class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-200" aria-label="Search" title="Search">
                    <i class="fas fa-search text-gray-700"></i>
                </button>
                
                <!-- Theme Toggle -->
                <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-200" aria-label="Toggle theme" title="Toggle theme">
                    <i class="fas fa-moon text-gray-700 dark:hidden"></i>
                    <i class="fas fa-sun text-yellow-500 hidden dark:block"></i>
                </button>
                
                <!-- Quick Role Switch (Development) -->
                <?php if (APP_DEBUG): ?>
                <div class="flex items-center gap-1 text-xs bg-blue-50 px-2 py-1 rounded border">
                    <span class="text-gray-600">View:</span>
                    <?php 
                    $current_role = $_SESSION['display_role'] ?? $_SESSION['user_role'] ?? 'user';
                    $switch_role = $current_role === 'admin' ? 'user' : 'admin';
                    ?>
                    <a href="/switch_role.php?role=<?= $switch_role ?>" 
                       class="font-medium text-blue-600 hover:text-blue-800 transition-colors">
                        <?= ucfirst($current_role) ?> → <?= ucfirst($switch_role) ?>
                    </a>
                </div>
                <?php endif; ?>
                
                <!-- Notifications -->
                <div class="relative">
                    <button class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-200 relative" aria-label="Notifications" title="Notifications">
                        <i class="fas fa-bell text-gray-700"></i>
                        <?php if ($user['notifications'] > 0): ?>
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full"><?= $user['notifications'] ?></span>
                        <?php endif; ?>
                    </button>
                </div>
                
                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @mouseenter="open = true" class="flex items-center gap-2 p-1 pr-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200" aria-label="User menu">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-white text-sm font-bold">
                            <?= substr($user['name'], 0, 1) ?>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                         x-cloak
                         @mouseenter="open = true"
                         @mouseleave="open = false"
                         role="menu"
                         aria-orientation="vertical"
                         aria-labelledby="user-menu-button"
                         tabindex="-1"
                         x-ref="dropdown"
                         @keydown.escape.window="open = false">
                        <!-- Token Count -->
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Available Tokens</span>
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-primary dark:text-primary-300">1,250</span>
                                    <button class="ml-2 p-1 text-xs text-white bg-primary hover:bg-primary/90 rounded-full w-5 h-5 flex items-center justify-center">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                <div class="bg-primary h-1.5 rounded-full" style="width: 75%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span>25% used</span>
                                <a href="#" class="text-primary hover:underline">Get more</a>
                            </div>
                        </div>
                        
                        <div class="py-1">
                            <!-- User Profile -->
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-white text-sm font-bold">
                                        <?= substr($user['name'], 0, 1) ?>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($user['name']) ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-user mr-2 w-4 text-center"></i> View Profile
                            </a>
                            <button id="theme-menu-toggle" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-moon mr-2 w-4 text-center"></i> Dark Mode
                            </button>
                            <a href="help.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-question-circle mr-2 w-4 text-center"></i> Help & Support
                            </a>
                            <a href="#" id="feedback-btn" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-purple-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-comment-dots mr-2 w-4 text-center"></i> Give Feedback
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2 w-4 text-center"></i> Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Dashboard Layout Container -->
    <div class="flex min-h-screen w-full">
        <?php 
        // Include appropriate sidebar based on user role
        if ($is_admin) {
            include_once 'sidebar-admin.php';
        } else {
            include_once 'sidebar-user.php';
        }
        ?>
        
        <!-- Main Content Area -->
        <main class="flex-1 p-4 lg:p-8">
