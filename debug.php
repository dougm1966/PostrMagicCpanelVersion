<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>PHP Debug Information</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test if we can start a session
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        echo "<p>✅ Session started successfully</p>";
    } else {
        echo "<p>✅ Session already active</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Session error: " . $e->getMessage() . "</p>";
}

// Test if includes directory exists
if (is_dir('includes')) {
    echo "<p>✅ includes/ directory exists</p>";
    
    // Check specific files
    $files = ['dashboard-header.php', 'config.php', 'sidebar-user.php', 'sidebar-admin.php'];
    foreach ($files as $file) {
        if (file_exists("includes/$file")) {
            echo "<p>✅ includes/$file exists</p>";
        } else {
            echo "<p>❌ includes/$file NOT FOUND</p>";
        }
    }
} else {
    echo "<p>❌ includes/ directory NOT FOUND</p>";
}

// Test basic PHP features
echo "<h2>PHP Feature Tests</h2>";

// Test null coalescing operator (PHP 7.0+)
$test = $_GET['test'] ?? 'default';
echo "<p>✅ Null coalescing operator works: $test</p>";

// Test array destructuring
$array = ['a', 'b', 'c'];
[$first, $second, $third] = $array;
echo "<p>✅ Array destructuring works: $first, $second, $third</p>";

// Test arrow function alternative (regular anonymous function)
$numbers = [1, 2, 3, 4, 5];
$doubled = array_map(function($n) { return $n * 2; }, $numbers);
echo "<p>✅ Anonymous functions work: " . implode(', ', $doubled) . "</p>";

echo "<h2>Trying to include dashboard-header.php</h2>";

// Try to include the header
$page_title = 'Debug Test';
try {
    ob_start();
    require_once 'includes/dashboard-header.php';
    $header_output = ob_get_clean();
    echo "<p>✅ dashboard-header.php included successfully</p>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; background: #f9f9f9;'>";
    echo "<h3>Header Output Preview:</h3>";
    echo htmlspecialchars(substr($header_output, 0, 500)) . "...";
    echo "</div>";
    echo "<a href='admin-dashboard.php' class='group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-900 hover:bg-purple-50 hover:text-purple-700 dark:text-gray-200 dark:hover:bg-purple-900 dark:hover:text-white'>Admin Dashboard</a>";
} catch (Exception $e) {
    echo "<p>❌ Error including dashboard-header.php: " . $e->getMessage() . "</p>";
}

echo "<h2>Server Information</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p>Current Working Directory: " . getcwd() . "</p>";

phpinfo();
?>