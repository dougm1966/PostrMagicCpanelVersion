<?php
/**
 * DatabaseManager.php
 * 
 * Database abstraction layer for PostrMagic
 * Supports both SQLite (development) and MySQL (production)
 * Implements connection pooling and factory pattern
 */

class DatabaseManager {
    private static $instance = null;
    private $pdo = null;
    private $config = [];
    
    private function __construct() {
        $this->loadConfig();
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DatabaseManager();
        }
        return self::$instance;
    }
    
    private function loadConfig() {
        // Load configuration from environment variables or config files
        $this->config = [
            'type' => defined('DB_TYPE') ? DB_TYPE : (getenv('DB_TYPE') ?: 'sqlite'),
            'host' => defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost'),
            'port' => defined('DB_PORT') ? DB_PORT : (getenv('DB_PORT') ?: '3306'),
            'database' => defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'postrmagic'),
            'username' => defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'postrmagic_user'),
            'password' => defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: ''),
            'path' => defined('DB_PATH') ? DB_PATH : (getenv('DB_PATH') ?: __DIR__ . '/../data/postrmagic.db'),
            'charset' => defined('DB_CHARSET') ? DB_CHARSET : (getenv('DB_CHARSET') ?: 'utf8mb4')
        ];
    }
    
    private function connect() {
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 30,
            ];
            
            if ($this->config['type'] === 'sqlite') {
                // SQLite for local development
                $data_dir = dirname($this->config['path']);
                if (!is_dir($data_dir)) {
                    mkdir($data_dir, 0755, true);
                }
                $dsn = "sqlite:" . $this->config['path'];
                $this->pdo = new PDO($dsn, null, null, $options);
                
                // SQLite settings to prevent locks
                $this->pdo->exec("PRAGMA journal_mode = DELETE");
                $this->pdo->exec("PRAGMA synchronous = NORMAL");
                $this->pdo->exec("PRAGMA cache_size = 10000");
                $this->pdo->exec("PRAGMA temp_store = MEMORY");
                $this->pdo->exec("PRAGMA mmap_size = 0");
                $this->pdo->exec("PRAGMA busy_timeout = 30000");
            } else {
                // MySQL for production
                $dsn = "mysql:host=" . $this->config['host'] . 
                       ";port=" . $this->config['port'] . 
                       ";dbname=" . $this->config['database'] . 
                       ";charset=" . $this->config['charset'];
                $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
            }
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    public function commit() {
        return $this->pdo->commit();
    }
    
    public function rollback() {
        return $this->pdo->rollback();
    }
    
    public function inTransaction() {
        return $this->pdo->inTransaction();
    }
}

// Backward compatibility function
if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        return DatabaseManager::getInstance()->getConnection();
    }
}

?>
