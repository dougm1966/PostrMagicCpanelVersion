<?php
/**
 * Database Migration Runner for PostrMagic
 * Handles environment detection and database schema migrations
 */

require_once __DIR__ . '/../config/config.php';

class MigrationRunner {
    
    private $pdo;
    private $isMySQL;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->isMySQL = (DB_TYPE === 'mysql');
    }
    
    /**
     * Run all pending migrations
     */
    public function runMigrations() {
        $results = [];
        
        // Create migrations tracking table first
        $this->createMigrationsTable();
        
        // Get list of migration files
        $migrationFiles = $this->getMigrationFiles();
        
        foreach ($migrationFiles as $file) {
            $migrationName = basename($file, '.sql');
            
            if ($this->isMigrationApplied($migrationName)) {
                $results[$migrationName] = ['status' => 'skipped', 'message' => 'Already applied'];
                continue;
            }
            
            try {
                $this->runMigrationFile($file);
                $this->markMigrationAsApplied($migrationName);
                $results[$migrationName] = ['status' => 'success', 'message' => 'Applied successfully'];
            } catch (Exception $e) {
                $results[$migrationName] = ['status' => 'error', 'message' => $e->getMessage()];
                error_log("Migration failed: $migrationName - " . $e->getMessage());
                // Continue with other migrations even if one fails
            }
        }
        
        return $results;
    }
    
    /**
     * Create migrations tracking table
     */
    private function createMigrationsTable() {
        if ($this->isMySQL) {
            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration_name VARCHAR(255) NOT NULL UNIQUE,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration_name TEXT NOT NULL UNIQUE,
                applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )";
        }
        
        $this->pdo->exec($sql);
    }
    
    /**
     * Get list of migration files
     */
    private function getMigrationFiles() {
        $migrationDir = __DIR__ . '/../migrations/';
        $files = glob($migrationDir . '*.sql');
        sort($files); // Ensure they run in order
        return $files;
    }
    
    /**
     * Check if migration has been applied
     */
    private function isMigrationApplied($migrationName) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration_name = ?");
        $stmt->execute([$migrationName]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Mark migration as applied
     */
    private function markMigrationAsApplied($migrationName) {
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration_name) VALUES (?)");
        $stmt->execute([$migrationName]);
    }
    
    /**
     * Run a specific migration file
     */
    private function runMigrationFile($filePath) {
        $sql = file_get_contents($filePath);
        
        if ($sql === false) {
            throw new Exception("Could not read migration file: $filePath");
        }
        
        // Parse SQL based on database type
        $statements = $this->parseSQLFile($sql);
        
        // Execute each statement
        foreach ($statements as $statement) {
            if (trim($statement)) {
                try {
                    $this->pdo->exec($statement);
                } catch (PDOException $e) {
                    throw new Exception("SQL Error in $filePath: " . $e->getMessage() . "\nStatement: " . substr($statement, 0, 200));
                }
            }
        }
    }
    
    /**
     * Parse SQL file based on database type
     */
    private function parseSQLFile($sql) {
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        if ($this->isMySQL) {
            // For MySQL, remove SQLite-specific sections and use MySQL tables
            $lines = explode("\n", $sql);
            $cleanedLines = [];
            $inSqliteSection = false;
            
            foreach ($lines as $line) {
                $trimmedLine = trim($line);
                
                // Skip SQLite section markers
                if (strpos($trimmedLine, '-- SQLite compatible version') !== false ||
                    strpos($trimmedLine, '-- User Media Files Table (SQLite)') !== false ||
                    strpos($trimmedLine, '-- Media Tags Table (SQLite)') !== false ||
                    strpos($trimmedLine, '-- Media Tag Relations Table (SQLite)') !== false ||
                    strpos($trimmedLine, '-- Create indexes for SQLite') !== false) {
                    $inSqliteSection = true;
                    continue;
                }
                
                // Skip lines that are SQLite-specific
                if (strpos($trimmedLine, '_sqlite') !== false) {
                    continue;
                }
                
                // If we're not in a SQLite section, include the line
                if (!$inSqliteSection) {
                    $cleanedLines[] = $line;
                }
            }
            
            $sql = implode("\n", $cleanedLines);
            
        } else {
            // For SQLite, remove MySQL sections and use SQLite tables
            $lines = explode("\n", $sql);
            $cleanedLines = [];
            $inMysqlSection = true; // Start assuming MySQL section
            
            foreach ($lines as $line) {
                $trimmedLine = trim($line);
                
                // When we hit the SQLite section, switch modes
                if (strpos($trimmedLine, '-- SQLite compatible version') !== false) {
                    $inMysqlSection = false;
                    continue;
                }
                
                // Skip MySQL-only syntax in SQLite mode
                if (!$inMysqlSection) {
                    // Include SQLite lines but rename _sqlite tables to regular names
                    if (strpos($trimmedLine, '_sqlite') !== false) {
                        $line = str_replace('_sqlite', '', $line);
                    }
                    $cleanedLines[] = $line;
                } else {
                    // Skip MySQL section entirely for SQLite
                    continue;
                }
            }
            
            $sql = implode("\n", $cleanedLines);
        }
        
        // Split into statements
        $statements = explode(';', $sql);
        
        // Filter out empty statements
        return array_filter($statements, function($stmt) {
            return trim($stmt) !== '';
        });
    }
    
    /**
     * Get applied migrations
     */
    public function getAppliedMigrations() {
        try {
            $stmt = $this->pdo->query("SELECT migration_name, applied_at FROM migrations ORDER BY applied_at");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Rollback a specific migration (if rollback file exists)
     */
    public function rollbackMigration($migrationName) {
        $rollbackFile = __DIR__ . "/../migrations/rollback_{$migrationName}.sql";
        
        if (!file_exists($rollbackFile)) {
            throw new Exception("Rollback file not found for migration: $migrationName");
        }
        
        try {
            $this->runMigrationFile($rollbackFile);
            
            // Remove from migrations table
            $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE migration_name = ?");
            $stmt->execute([$migrationName]);
            
            return ['status' => 'success', 'message' => 'Rollback completed'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Test database connection and environment
     */
    public function testConnection() {
        try {
            $result = [
                'connection' => 'success',
                'database_type' => $this->isMySQL ? 'MySQL' : 'SQLite',
                'tables' => []
            ];
            
            // Test basic query
            if ($this->isMySQL) {
                $stmt = $this->pdo->query("SHOW TABLES");
                $result['tables'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
            } else {
                $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
                $result['tables'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            
            return $result;
        } catch (Exception $e) {
            return [
                'connection' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}

/**
 * Helper function to run migrations
 */
function runDatabaseMigrations() {
    $runner = new MigrationRunner();
    return $runner->runMigrations();
}

/**
 * Helper function to test database connection
 */
function testDatabaseConnection() {
    $runner = new MigrationRunner();
    return $runner->testConnection();
}
?>