<?php
// Include the main auth functions
require_once __DIR__ . '/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if current user is an admin
 * @return bool
 */
function isAdmin() {
    // First check if user is logged in
    if (!isLoggedIn()) {
        return false;
    }
    
    // Check for display role override (for development/testing)
    if (isset($_SESSION['display_role'])) {
        return $_SESSION['display_role'] === 'admin';
    }
    
    // Default to actual user role
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Require admin access - redirects to login or 403 if not admin
 */
function requireAdmin() {
    // First require login
    requireLogin();
    
    // Then check admin role
    if (!isAdmin()) {
        header('HTTP/1.0 403 Forbidden');
        echo 'Access Denied - Admin privileges required';
        exit();
    }
}

/**
 * Get the base URL for the application
 * @return string
 */
function base_url($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    
    // Get the document root relative path
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $currentDir = dirname($_SERVER['SCRIPT_FILENAME']);
    
    // Find the relative path from document root to application root
    $relativePath = '';
    if (strpos($currentDir, $documentRoot) === 0) {
        $relativePath = substr($currentDir, strlen($documentRoot));
        // Remove /admin if we're in admin directory to get to app root
        if (strpos($relativePath, '/admin') !== false) {
            $relativePath = str_replace('/admin', '', $relativePath);
        }
    }
    
    // Ensure no double slashes
    if (!empty($path)) {
        $path = '/' . ltrim($path, '/');
    }
    
    $url = rtrim($protocol . $host . $relativePath, '/') . $path;
    return $url;
}

/**
 * Redirect to a URL
 * @param string $path Path to redirect to
 */
function redirect($path) {
    header('Location: ' . base_url($path));
    exit();
}

// For backward compatibility - remove once all files are updated
if (!function_exists('is_admin')) {
    function is_admin() {
        return isAdmin();
    }
}
