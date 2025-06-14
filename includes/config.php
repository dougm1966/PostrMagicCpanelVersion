<?php
// Include main configuration file
require_once __DIR__ . '/../config/config.php';

// Site Configuration (if not already defined in main config)
if (!defined('SITE_URL')) {
    define('SITE_URL', 'https://yourdomain.com'); // No trailing slash
}
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'PostrMagic');
}

// Use the centralized database connection from main config
$pdo = getDBConnection();
