<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Test if session can start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "Session started successfully<br>";
}

// Check if includes work
if (file_exists('includes/dashboard-header.php')) {
    echo "dashboard-header.php exists<br>";
} else {
    echo "ERROR: dashboard-header.php NOT FOUND<br>";
}
?>