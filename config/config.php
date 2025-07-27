<?php
/**
 * PostrMagic Configuration File
 * 
 * This file contains sensitive information and should be kept secure.
 * Never commit this file to version control.
 */

// Prevent multiple loads
if (defined('POSTRMAGIC_CONFIG_LOADED')) {
    return;
}
define('POSTRMAGIC_CONFIG_LOADED', true);

// Application Settings
define('APP_NAME', 'PostrMagic');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // 'development', 'staging', or 'production'
define('APP_DEBUG', true);

// Base URL - Update this to your actual domain in production
define('BASE_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000') . '/postrmagic/');

// Database Configuration
// Auto-detect environment - use SQLite for local development, MySQL for production
$is_local = (
    // Check for common local hostnames
    (isset($_SERVER['HTTP_HOST']) && (
        $_SERVER['HTTP_HOST'] === 'localhost' ||
        $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
        str_ends_with($_SERVER['HTTP_HOST'], '.localhost') || // Covers domains like myapp.localhost
        str_ends_with($_SERVER['HTTP_HOST'], '.local') || // Covers domains like myapp.local
        str_ends_with($_SERVER['HTTP_HOST'], '.test') // Covers domains like myapp.test
    )) ||
    // Check for non-standard local server ports
    (isset($_SERVER['SERVER_PORT']) && in_array($_SERVER['SERVER_PORT'], [8000, 8080, 3000])) ||
    // Check for CLI execution (like running scripts)
    (php_sapi_name() === 'cli') ||
    // Check for a specific local-only file marker
    (file_exists(__DIR__ . '/.local_env'))
);

if ($is_local) {
    // Local development - use SQLite
    define('DB_TYPE', 'sqlite');
    define('DB_PATH', __DIR__ . '/../data/postrmagic.db');
    define('DB_HOST', ''); // Not used for SQLite
    define('DB_NAME', ''); // Not used for SQLite
    define('DB_USER', ''); // Not used for SQLite
    define('DB_PASS', ''); // Not used for SQLite
} else {
    // Production/cPanel - use MySQL
    define('DB_TYPE', 'mysql');
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'postrmagic_gititon');
    define('DB_USER', 'postrmagic_gititon');
    define('DB_PASS', 'b6gwej8^X');
}
define('DB_CHARSET', 'utf8mb4');

// File Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB

// Media Library File Types (NO GIF per requirements)
define('ALLOWED_MEDIA_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// Poster-specific file types (includes PDF for poster uploads)
define('ALLOWED_POSTER_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'application/pdf']);

// Legacy support for existing code
define('ALLOWED_FILE_TYPES', ALLOWED_MEDIA_TYPES);

// LLM API Configuration
define('OPENAI_API_KEY', 'your_openai_api_key_here');
define('OPENAI_MODEL', 'gpt-4');

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    session_start();
}

// Error Reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('UTC');

// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Database Connection Function
function getDBConnection() {
    try {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        if (DB_TYPE === 'sqlite') {
            // SQLite for local development
            $data_dir = dirname(DB_PATH);
            if (!is_dir($data_dir)) {
                mkdir($data_dir, 0755, true);
            }
            $dsn = "sqlite:" . DB_PATH;
            return new PDO($dsn, null, null, $options);
        } else {
            // MySQL for production
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        }
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        if (APP_DEBUG) {
            die("Database connection failed: " . $e->getMessage() . 
                "<br><br>Environment: " . (DB_TYPE === 'sqlite' ? 'Local (SQLite)' : 'Production (MySQL)') .
                "<br>Database Type: " . DB_TYPE);
        } else {
            die("A database error occurred. Please try again later.");
        }
    }
}

// Helper function to get environment variable with fallback
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Set error and exception handlers
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr in $errfile on line $errline");
    if (APP_DEBUG) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    return true;
});

set_exception_handler(function($e) {
    error_log("Uncaught Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    if (APP_DEBUG) {
        if (!headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        if (!headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>An error occurred. Please try again later.</p>";
    }
    exit(1);
});
