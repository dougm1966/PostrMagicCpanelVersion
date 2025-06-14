<?php
$page_title = "Test";
try {
    require_once 'includes/dashboard-header.php';
    echo "Header loaded successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>