<?php
declare(strict_types=1);

echo "Testing requireLogin behavior...\n";

require_once __DIR__ . '/includes/auth.php';

echo "Before requireLogin() call\n";
requireLogin();
echo "After requireLogin() call - THIS SHOULD NOT APPEAR IF NOT LOGGED IN\n";
?>
