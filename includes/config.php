<?php
// Site Configuration
define('SITE_URL', 'https://yourdomain.com'); // No trailing slash
define('SITE_NAME', 'PostrMagic');

// File Uploads
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
