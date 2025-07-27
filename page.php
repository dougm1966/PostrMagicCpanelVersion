<?php
// Include required files
require_once __DIR__ . '/includes/auth-helper.php';
require_once __DIR__ . '/includes/config.php';

// Get the requested slug from the URL
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$slug = '';

// Extract the slug from the URL
if (preg_match('#^/page/([^/]+)/?$#', $request_uri, $matches)) {
    $slug = $matches[1];
}

// If no slug found, redirect to home
if (empty($slug)) {
    header('Location: ' . base_url());
    exit();
}

try {
    // Connect to database
    $pdo = getDBConnection();
    
    // Get the page from database
    $stmt = $pdo->prepare("SELECT * FROM static_pages WHERE slug = ? AND is_published = 1");
    $stmt->execute([$slug]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If page not found or not published, show 404
    if (!$page) {
        http_response_code(404);
        $page_title = 'Page Not Found';
        $content = '<div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl">Page Not Found</h1>
            <p class="mt-6 text-base text-gray-500">The page you\'re looking for doesn\'t exist or has been moved.</p>
            <div class="mt-10 flex justify-center">
                <a href="<?= BASE_URL ?>" class="text-base font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    Go back home<span aria-hidden="true"> &rarr;</span>
                </a>
            </div>
        </div>';
        include __DIR__ . '/layouts/standard.php';
        exit();
    }
    
    // Set up page variables for the layout
    $page_title = htmlspecialchars($page['title']);
    $meta_description = !empty($page['meta_description']) ? 
        htmlspecialchars($page['meta_description']) : 
        'A page from ' . SITE_NAME;
    
    // Prepare the content
    $content = '<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="prose dark:prose-invert max-w-none">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6">' . 
                htmlspecialchars($page['title']) . 
            '</h1>
            <div class="content">' . 
                $page['content'] . 
            '</div>
        </div>
    </div>';
    
    // Include the layout
    include __DIR__ . '/layouts/standard.php';
    
} catch (Exception $e) {
    // Log the error
    error_log('Error loading page: ' . $e->getMessage());
    
    // Show error page
    http_response_code(500);
    $page_title = 'Server Error';
    $content = '<div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl">Server Error</h1>
        <p class="mt-6 text-base text-gray-500">Sorry, something went wrong on our end. Please try again later.</p>
        <div class="mt-10 flex justify-center">
            <a href="<?= BASE_URL ?>" class="text-base font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                Go back home<span aria-hidden="true"> &rarr;</span>
            </a>
        </div>
    </div>';
    include __DIR__ . '/layouts/standard.php';
    exit();
}
