<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
define('BASE_URL', $base_url);

// Include config
require_once __DIR__ . '/config.php';

// Set page title
$page_title = isset($page_title) ? $page_title . ' - PostrMagic' : 'PostrMagic';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
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
    
    <!-- Custom CSS for performance improvements -->
    <style>
      /* CSS for better scrolling performance */
      html {
        scroll-behavior: smooth;
      }
      
      body {
        overflow-x: hidden;
      }
      
      /* Improve canvas performance */
      canvas {
        will-change: transform;
        transform: translateZ(0);
        backface-visibility: hidden;
        perspective: 1000px;
      }
      
      /* Optimize animation performance */
      section {
        will-change: opacity;
        contain: content;
      }
      
      /* Avoid flickering during page load */
      .no-fouc {
        opacity: 0;
        transition: opacity 0.5s ease-in;
      }
      
      /* Fix for stuck scrolling */
      main {
        overflow-anchor: none;
      }
    </style>
    
    <script>
      // Fix FOUC (Flash of Unstyled Content)
      document.documentElement.classList.add('no-fouc');
      window.addEventListener('load', function() {
        document.documentElement.classList.remove('no-fouc');
      });
    </script>
    <!-- Original CSS will be gradually migrated -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css" />
    <script src="<?= BASE_URL ?>assets/js/main.js" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-sans overflow-x-hidden">
    <header class="w-full py-4 bg-white shadow-sm z-50">
        <div class="container mx-auto px-6 sm:px-12 max-w-6xl flex items-center justify-between">
            <h1 class="text-primary font-bold text-2xl"><a href="<?= BASE_URL ?>">PostrMagic</a></h1>
            <div class="flex items-center gap-4">
                <button class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-200" aria-label="Toggle dark mode" title="Toggle theme">
                    <i class="fa fa-moon text-gray-700" id="theme-icon"></i>
                </button>
                <!-- Hidden on desktop, shown on mobile -->
                <button class="md:hidden p-2 rounded-full hover:bg-gray-100 transition-colors duration-200" aria-label="Toggle navigation" id="nav-toggle">
                    <i class="fa fa-bars text-gray-700"></i>
                </button>
                <!-- Main Navigation -->
                <nav class="hidden md:block">
                    <ul class="flex gap-6">
                        <li><a href="<?= BASE_URL ?>upload.php" class="font-medium text-gray-700 hover:text-primary transition-colors duration-200">Upload Poster</a></li>
                        <li><a href="#" class="font-medium text-gray-700 hover:text-primary transition-colors duration-200">Dashboard</a></li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <!-- Mobile Menu (Hidden by default) -->
        <div class="hidden" id="mobile-menu">
            <div class="container mx-auto px-6 py-4 border-t border-gray-100">
                <ul class="space-y-3">
                    <li><a href="<?= BASE_URL ?>upload.php" class="block font-medium text-gray-700 hover:text-primary transition-colors duration-200">Upload Poster</a></li>
                    <li><a href="#" class="block font-medium text-gray-700 hover:text-primary transition-colors duration-200">Dashboard</a></li>
                </ul>
            </div>
        </div>
    </header>
    <main>
