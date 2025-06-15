<?php
http_response_code(404);
$page_title = 'Page Not Found';
include_once __DIR__ . '/includes/header.php';
?>

<main class="min-h-[50vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">404</h1>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Page not found
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                The page you're looking for doesn't exist or has been moved.
            </p>
        </div>
        <div class="mt-8">
            <a href="<?= base_url() ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Go back home
            </a>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
