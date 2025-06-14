<?php
// Include config first to ensure session is started properly
require_once __DIR__ . '/includes/config.php';

// Debug information
$debug = [
    'session_id' => session_id(),
    'session_status' => session_status(),
    'current_role' => $_SESSION['user_role'] ?? 'not set',
    'requested_role' => $_GET['role'] ?? 'not specified'
];

// Set default role if not set
if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = 'user';
}

// Handle role switching
if (isset($_GET['role'])) {
    $valid_roles = ['user', 'admin'];
    if (in_array($_GET['role'], $valid_roles)) {
        $_SESSION['user_role'] = $_GET['role'];
        // Force session write and close before redirect
        session_write_close();
        // Redirect to remove the role parameter from URL
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Role Manager</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl">
        <h1 class="text-2xl font-bold mb-6 text-center">Development Role Manager</h1>
        
        <!-- Current Role Display -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <p class="text-center">
                <span class="font-medium">Current Role:</span>
                <span class="ml-2 px-3 py-1 rounded-full text-sm font-medium 
                    <?= ($_SESSION['user_role'] ?? '') === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                    <?= ucfirst($_SESSION['user_role'] ?? 'not set') ?>
                </span>
            </p>
        </div>
        
        <!-- Debug Information (visible on page) -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg text-sm">
            <h3 class="font-semibold mb-2">Debug Information:</h3>
            <pre class="bg-white p-3 rounded text-xs overflow-auto"><?= htmlspecialchars(print_r($debug, true)) ?></pre>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <a href="?role=admin" class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i data-lucide="shield" class="w-5 h-5 mr-2"></i>
                Switch to Admin
            </a>
            <a href="?role=user" class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                Switch to User
            </a>
            <a href="dashboard.php" class="mt-4 text-center text-blue-600 hover:underline">
                Go to Dashboard
            </a>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
