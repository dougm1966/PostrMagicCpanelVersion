<?php
require_once 'includes/migration-runner.php';

echo "=== Database Migration Test ===\n";

try {
    $runner = new MigrationRunner();
    
    // Test database connection first
    echo "Testing database connection...\n";
    $connectionTest = $runner->testConnection();
    
    if ($connectionTest['connection'] === 'success') {
        echo "✅ Database connection successful\n";
        echo "   Database Type: {$connectionTest['database_type']}\n";
        echo "   Existing Tables: " . count($connectionTest['tables']) . " found\n";
        
        if (!empty($connectionTest['tables'])) {
            echo "   Tables: " . implode(', ', $connectionTest['tables']) . "\n";
        }
    } else {
        echo "❌ Database connection failed: {$connectionTest['message']}\n";
        exit(1);
    }
    
    echo "\nRunning migrations...\n";
    $results = $runner->runMigrations();
    
    foreach ($results as $migration => $result) {
        $status = $result['status'];
        $message = $result['message'];
        
        if ($status === 'success') {
            echo "✅ $migration: $message\n";
        } elseif ($status === 'skipped') {
            echo "⏭️  $migration: $message\n";
        } else {
            echo "❌ $migration: $message\n";
        }
    }
    
    echo "\nChecking applied migrations...\n";
    $applied = $runner->getAppliedMigrations();
    
    if (empty($applied)) {
        echo "No migrations have been applied yet.\n";
    } else {
        echo "Applied migrations:\n";
        foreach ($applied as $migration) {
            echo "   - {$migration['migration_name']} (applied: {$migration['applied_at']})\n";
        }
    }
    
    // Test final connection to verify tables were created
    echo "\nVerifying new tables...\n";
    $finalTest = $runner->testConnection();
    if ($finalTest['connection'] === 'success') {
        $newTables = array_diff($finalTest['tables'], $connectionTest['tables']);
        if (!empty($newTables)) {
            echo "✅ New tables created: " . implode(', ', $newTables) . "\n";
        } else {
            echo "ℹ️  No new tables created (they may have existed already)\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Migration test failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Migration Test Complete ===\n";
?>
