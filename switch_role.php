<?php
// Quick role switcher for development
require_once __DIR__ . '/includes/auth-helper.php';

// Must be logged in to switch roles
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Handle role switch
if (isset($_GET['role']) && in_array($_GET['role'], ['admin', 'user'])) {
    $_SESSION['display_role'] = $_GET['role'];
    
    // Redirect back to where they came from, or dashboard
    $redirect = $_GET['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
    header('Location: ' . $redirect);
    exit();
}

// If no valid role specified, go back
header('Location: dashboard.php');
exit();
?>