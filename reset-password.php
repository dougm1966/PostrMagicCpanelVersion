<?php
require_once 'config/config.php';
$db = getDBConnection();
$hash = password_hash('bob', PASSWORD_DEFAULT);
$stmt = $db->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
$result = $stmt->execute([$hash, 'bob@bob.com']);
echo $result ? 'Password updated for bob@bob.com' : 'Update failed';
?>