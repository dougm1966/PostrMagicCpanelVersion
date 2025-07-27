<?php
echo "Step 1: Starting test<br>";

echo "Step 2: Checking if includes/config.php exists: ";
if (file_exists('includes/config.php')) {
    echo "YES<br>";
} else {
    echo "NO<br>";
}

echo "Step 3: Trying to include config.php<br>";
try {
    require_once 'includes/config.php';
    echo "Step 4: Config included successfully<br>";
} catch (Exception $e) {
    echo "Step 4: Config ERROR: " . $e->getMessage() . "<br>";
}

echo "Step 5: Test complete<br>";
?>
