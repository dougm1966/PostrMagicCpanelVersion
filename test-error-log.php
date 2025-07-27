<?php
// Test where error_log writes to
echo "Testing error log location...\n";
error_log("TEST LOG ENTRY: " . date('Y-m-d H:i:s'));
echo "error_log() called\n";
echo "ini_get('log_errors'): " . ini_get('log_errors') . "\n";
echo "ini_get('error_log'): " . ini_get('error_log') . "\n";
?>
