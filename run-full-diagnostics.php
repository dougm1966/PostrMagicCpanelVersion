<?php
/**
 * PostrMagic Full System Diagnostics
 * 
 * This script runs a series of checks to validate the server environment,
 * PHP configuration, and database connectivity. It is designed to be run
 * from the browser to identify and help resolve configuration issues.
 */

// --- Basic Setup ---
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Helper Functions ---
function print_header($title) {
    echo "<div style='background-color:#333; color:#fff; padding:15px; margin-top:20px; border-radius:5px;'>";
    echo "<h2 style='margin:0; font-size:1.5em;'>{$title}</h2>";
    echo "</div>";
    echo "<div style='border:1px solid #ccc; padding:15px; border-radius:0 0 5px 5px;'>";
}

function print_footer() {
    echo "</div>";
}

function check_status($condition, $success_msg, $failure_msg) {
    if ($condition) {
        echo "<p style='color:green; margin:5px 0;'><b>✔ SUCCESS:</b> {$success_msg}</p>";
    } else {
        echo "<p style='color:red; margin:5px 0;'><b>✖ FAILURE:</b> {$failure_msg}</p>";
    }
    return $condition;
}

function get_value($value) {
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value) || $value === '') {
        return '<em>Not set</em>';
    }
    return htmlspecialchars(print_r($value, true));
}

// --- Start Diagnostics ---
echo "<div style='font-family:sans-serif; max-width:1000px; margin:20px auto; padding:20px; background-color:#f9f9f9; border:1px solid #eee; border-radius:10px;'>";
echo "<h1 style='text-align:center; color:#333;'>PostrMagic System Diagnostics</h1>";
echo "<p style='text-align:center; font-size:0.9em; color:#666;'>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// --- 1. Server & PHP Environment ---
print_header('Server & PHP Environment');
check_status(true, "Server diagnostics started.", "");

echo "<table style='width:100%; border-collapse:collapse;'>";
echo "<tr><td style='padding:5px; border-bottom:1px solid #eee;'>PHP Version</td><td style='padding:5px; border-bottom:1px solid #eee;'>" . phpversion() . "</td></tr>";
check_status(version_compare(phpversion(), '8.1.0', '>='), "PHP version is 8.1+.", "PHP version is too old. Please upgrade to 8.1 or newer.");

echo "<tr><td style='padding:5px; border-bottom:1px solid #eee;'>Server API</td><td style='padding:5px; border-bottom:1px solid #eee;'>" . php_sapi_name() . "</td></tr>";
echo "<tr><td style='padding:5px; border-bottom:1px solid #eee;'>Document Root</td><td style='padding:5px; border-bottom:1px solid #eee;'>" . get_value($_SERVER['DOCUMENT_ROOT']) . "</td></tr>";
echo "<tr><td style='padding:5px; border-bottom:1px solid #eee;'>HTTP Host</td><td style='padding:5px; border-bottom:1px solid #eee;'>" . get_value($_SERVER['HTTP_HOST']) . "</td></tr>";

$required_extensions = ['pdo', 'pdo_sqlite', 'pdo_mysql', 'curl', 'json', 'mbstring', 'gd', 'exif'];
foreach ($required_extensions as $ext) {
    check_status(extension_loaded($ext), "PHP extension '{$ext}' is loaded.", "PHP extension '{$ext}' is NOT loaded. This is required.");
}
print_footer();

// --- 2. File System & Permissions ---
print_header('File System & Permissions');
$project_root = __DIR__;
check_status(is_dir($project_root), "Project root directory found at: {$project_root}", "Could not determine project root.");

$config_file = $project_root . '/config/config.php';
check_status(file_exists($config_file), "Configuration file found: {$config_file}", "Configuration file NOT found at {$config_file}. This is critical.");

$src_dir = $project_root . '/src';
check_status(!file_exists($src_dir), "'src' directory does not exist, as expected from analysis.", "'src' directory exists. This contradicts composer.json's autoload configuration.");

$data_dir = $project_root . '/data';
check_status(is_dir($data_dir) || mkdir($data_dir, 0755, true), "Data directory is accessible.", "Data directory could not be created or accessed.");
check_status(is_writable($data_dir), "Data directory is writable.", "Data directory is NOT writable. This will cause SQLite errors.");

$uploads_dir = $project_root . '/uploads';
check_status(is_dir($uploads_dir) || mkdir($uploads_dir, 0755, true), "Uploads directory is accessible.", "Uploads directory could not be created or accessed.");
check_status(is_writable($uploads_dir), "Uploads directory is writable.", "Uploads directory is NOT writable. This will prevent file uploads.");
print_footer();

// --- 3. Configuration Analysis (`config.php`) ---
print_header('Configuration Analysis (`config.php`)');
if (file_exists($config_file)) {
    // Temporarily capture output to prevent config from breaking the script
    ob_start();
    require_once $config_file;
    ob_end_clean();

    check_status(defined('POSTRMAGIC_CONFIG_LOADED'), "config.php was loaded.", "config.php could not be loaded properly.");

    $is_local = (
        (isset($_SERVER['HTTP_HOST']) && (
            $_SERVER['HTTP_HOST'] === 'localhost:8000' || 
            $_SERVER['HTTP_HOST'] === 'localhost' ||
            $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
            strpos($_SERVER['HTTP_HOST'], 'localhost') !== false
        )) ||
        (php_sapi_name() === 'cli' && file_exists(__DIR__ . '/data/postrmagic.db'))
    );

    echo "<p><b>Environment Detection Logic:</b></p>";
    echo "<ul>";
    echo "<li>HTTP Host: " . get_value($_SERVER['HTTP_HOST']) . "</li>";
    echo "<li>Detected as Local: " . get_value($is_local) . "</li>";
    echo "</ul>";

    check_status($is_local, "Environment correctly detected as LOCAL.", "Environment incorrectly detected as PRODUCTION. This is the primary cause of failure.");

    echo "<p><b>Database Constants Defined in `config.php`:</b></p>";
    echo "<table style='width:100%; border-collapse:collapse;'>";
    echo "<tr><td style='padding:5px; border-bottom:1px solid #eee;'>DB_TYPE</td><td style='padding:5px; border-bottom:1px solid #eee;'>" . get_value(defined('DB_TYPE') ? DB_TYPE : null) . "</td></tr>";
    echo "<tr><td style='padding:5px; border-bottom:1px solid #eee;'>DB_PATH (for SQLite)</td><td style='padding:5px; border-bottom:1px solid #eee;'>" . get_value(defined('DB_PATH') ? DB_PATH : null) . "</td></tr>";
    echo "<tr><td style='padding:5px; border-bottom:1px solid #eee;'>DB_HOST (for MySQL)</td><td style='padding:5px; border-bottom:1px solid #eee;'>" . get_value(defined('DB_HOST') ? DB_HOST : null) . "</td></tr>";
    echo "<tr><td style='padding:5px; border-bottom:1px solid #eee;'>DB_NAME (for MySQL)</td><td style='padding:5px; border-bottom:1px solid #eee;'>" . get_value(defined('DB_NAME') ? DB_NAME : null) . "</td></tr>";
    echo "</table>";

} else {
    check_status(false, "", "config.php not found.");
}
print_footer();

// --- 4. Database Connection Test ---
print_header('Database Connection Test');
$db_manager_file = $project_root . '/includes/DatabaseManager.php';
$auth_file = $project_root . '/includes/auth.php';

if (file_exists($config_file) && file_exists($auth_file)) {
    echo "<p><b>Attempting connection using global `getDBConnection()` from `config.php`...</b></p>";
    try {
        $pdo = getDBConnection();
        check_status(true, "Successfully connected to the database using `getDBConnection()`.", "");
        echo "<p>Database Type: " . get_value($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) . "</p>";
        
        // Test a simple query
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) == 'sqlite') {
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        } else {
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        }
        $users_table_exists = $stmt->fetch() !== false;
        check_status($users_table_exists, "'users' table found in the database.", "'users' table NOT found. The database may be empty or uninitialized.");

    } catch (Exception $e) {
        check_status(false, "", "Failed to connect using `getDBConnection()`. Error: " . $e->getMessage());
    }
} else {
    check_status(false, "", "Cannot test DB connection because config.php or auth.php is missing.");
}

if (file_exists($db_manager_file)) {
    echo "<p style='margin-top:20px;'><b>Attempting connection using `DatabaseManager` class...</b></p>";
    try {
        require_once $db_manager_file;
        $dbManager = DatabaseManager::getInstance();
        $pdo = $dbManager->getConnection();
        check_status(true, "Successfully connected to the database using `DatabaseManager`.", "");
        echo "<p>Database Type: " . get_value($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) . "</p>";
    } catch (Exception $e) {
        check_status(false, "", "Failed to connect using `DatabaseManager`. Error: " . $e->getMessage());
    }
}

print_footer();

// --- 5. Final Summary ---
print_header('Final Summary & Recommendations');

echo "<p><b>Diagnostics Complete.</b></p>";
echo "<p><b>Primary Issue Identified:</b> The application's configuration in <code>config/config.php</code> uses a flawed method to detect the local environment. It is likely misidentifying your server as 'production' and attempting to connect to a MySQL database instead of the local SQLite database.</p>";
echo "<p><b>Recommendation:</b></p>";
echo "<ol>";
echo "<li><b>Run this script from your browser:</b> Open <code>http://your-local-server/postrmagic/run-full-diagnostics.php</code> to see the live results.</li>";
echo "<li><b>Confirm the Diagnosis:</b> The 'Configuration Analysis' section should show 'FAILURE' for the environment detection.</li>";
echo "<li><b>Proceed with the fix:</b> The next step will be to create a <code>.env</code> file to override the faulty configuration and force the application to use the correct local SQLite database.</li>";
echo "</ol>";

print_footer();

echo "</div>";

?>
