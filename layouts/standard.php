<?php
/**
 * Standard Page Layout
 * 
 * This layout is used for all standard website pages like About, Contact, etc.
 * It includes the standard header and footer with the page content in between.
 */

// Start output buffering
ob_start();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? SITE_NAME) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description ?? 'A page from ' . SITE_NAME) ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="/assets/css/main.css" rel="stylesheet">
    
    <!-- Dark mode support -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                            <?= SITE_NAME ?>
                        </a>
                    </div>
                    <nav class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="/" class="border-indigo-500 text-gray-900 dark:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Home
                        </a>
                        <a href="/about" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            About
                        </a>
                        <a href="/contact" class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Contact
                        </a>
                    </nav>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <?php if (isLoggedIn()): ?>
                        <a href="/dashboard" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="/login" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white px-3 py-2 text-sm font-medium">
                            Log in
                        </a>
                        <a href="/register" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign up
                        </a>
                    <?php endif; ?>
                    
                    <!-- Dark mode toggle -->
                    <button id="theme-toggle" type="button" class="ml-3 p-2 rounded-full text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                    </button>
                </div>
                
                <!-- Mobile menu button -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="/" class="bg-indigo-50 border-indigo-500 text-indigo-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Home</a>
                <a href="/about" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">About</a>
                <a href="/contact" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Contact</a>
                <?php if (isLoggedIn()): ?>
                    <a href="/dashboard" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Dashboard</a>
                <?php else: ?>
                    <a href="/login" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Log in</a>
                    <a href="/register" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Sign up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <?php 
        // Content will be injected here
        echo $content; 
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 overflow-hidden sm:px-6 lg:px-8">
            <nav class="-mx-5 -my-2 flex flex-wrap justify-center" aria-label="Footer">
                <div class="px-5 py-2">
                    <a href="/about" class="text-base text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">About</a>
                </div>
                <div class="px-5 py-2">
                    <a href="/blog" class="text-base text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Blog</a>
                </div>
                <div class="px-5 py-2">
                    <a href="/contact" class="text-base text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Contact</a>
                </div>
                <div class="px-5 py-2">
                    <a href="/privacy" class="text-base text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Privacy Policy</a>
                </div>
                <div class="px-5 py-2">
                    <a href="/terms" class="text-base text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Terms of Service</a>
                </div>
            </nav>
            <p class="mt-8 text-center text-base text-gray-400">
                &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('button[aria-controls="mobile-menu"]');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Theme toggle
            const themeToggleBtn = document.getElementById('theme-toggle');
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    // Toggle the dark class on the html element
                    document.documentElement.classList.toggle('dark');
                    
                    // Save the preference to localStorage
                    if (document.documentElement.classList.contains('dark')) {
                        localStorage.theme = 'dark';
                    } else {
                        localStorage.theme = 'light';
                    }
                });
            }
        });
    </script>
</body>
</html>
<?php
// Get the buffer and clean it
$output = ob_get_clean();
echo $output;