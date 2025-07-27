<?php
require_once __DIR__ . '/includes/auth.php';

// Log out the user
logout();

// Redirect to login page
header('Location: login.php?logout=1');
exit();
