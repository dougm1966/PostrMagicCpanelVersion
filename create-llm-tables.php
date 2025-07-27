<?php
require_once 'config/config.php';

try {
    $pdo = getDBConnection();
    
    echo "Creating LLM management tables...\n";
    
    // Create unified tables for both MySQL and SQLite
    $tables = [
        'llm_providers' => "
            CREATE TABLE IF NOT EXISTS llm_providers (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                name TEXT NOT NULL UNIQUE,
                display_name TEXT NOT NULL,
                is_enabled INTEGER DEFAULT 1,
                base_url TEXT,
                api_version TEXT,
                rate_limit_per_minute INTEGER DEFAULT 60,
                rate_limit_per_hour INTEGER DEFAULT 3600,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_date DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'llm_configurations' => "
            CREATE TABLE IF NOT EXISTS llm_configurations (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                provider_id INTEGER NOT NULL,
                event_category TEXT,
                content_type TEXT NOT NULL,
                model_name TEXT NOT NULL,
                is_enabled INTEGER DEFAULT 1,
                priority_order INTEGER DEFAULT 1,
                max_tokens INTEGER DEFAULT 1000,
                temperature REAL DEFAULT 0.7,
                top_p REAL DEFAULT 1.0,
                frequency_penalty REAL DEFAULT 0.0,
                presence_penalty REAL DEFAULT 0.0,
                timeout_seconds INTEGER DEFAULT 30,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE
            );
        ",
        
        'event_categories' => "
            CREATE TABLE IF NOT EXISTS event_categories (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                category_name TEXT NOT NULL UNIQUE,
                display_name TEXT NOT NULL,
                description TEXT,
                is_enabled INTEGER DEFAULT 1,
                detection_keywords TEXT,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_date DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'llm_prompts' => "
            CREATE TABLE IF NOT EXISTS llm_prompts (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                prompt_type TEXT NOT NULL,
                event_category TEXT,
                content_type TEXT,
                system_prompt TEXT NOT NULL,
                user_prompt TEXT NOT NULL,
                assistant_prompt TEXT,
                placeholders TEXT,
                is_active INTEGER DEFAULT 1,
                version_number INTEGER DEFAULT 1,
                version_notes TEXT,
                created_by_user_id INTEGER NOT NULL,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'llm_prompt_versions' => "
            CREATE TABLE IF NOT EXISTS llm_prompt_versions (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                prompt_id INTEGER NOT NULL,
                version_number INTEGER NOT NULL,
                system_prompt TEXT NOT NULL,
                user_prompt TEXT NOT NULL,
                assistant_prompt TEXT,
                placeholders TEXT,
                version_notes TEXT,
                created_by_user_id INTEGER NOT NULL,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                archived_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (prompt_id) REFERENCES llm_prompts(id) ON DELETE CASCADE
            );
        ",
        
        'llm_usage_logs' => "
            CREATE TABLE IF NOT EXISTS llm_usage_logs (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                provider_id INTEGER NOT NULL,
                user_id INTEGER,
                event_id INTEGER,
                prompt_id INTEGER,
                prompt_type TEXT NOT NULL,
                model_name TEXT NOT NULL,
                input_tokens INTEGER DEFAULT 0,
                output_tokens INTEGER DEFAULT 0,
                total_tokens INTEGER DEFAULT 0,
                estimated_cost REAL DEFAULT 0.0,
                response_time_ms INTEGER,
                was_successful INTEGER DEFAULT 1,
                error_message TEXT,
                api_key_source TEXT DEFAULT 'admin',
                request_data TEXT,
                response_data TEXT,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE RESTRICT
            );
        ",
        
        'llm_cost_tracking' => "
            CREATE TABLE IF NOT EXISTS llm_cost_tracking (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                provider_id INTEGER NOT NULL,
                user_id INTEGER,
                event_category TEXT,
                content_type TEXT,
                date_period DATE NOT NULL,
                total_requests INTEGER DEFAULT 0,
                successful_requests INTEGER DEFAULT 0,
                total_input_tokens INTEGER DEFAULT 0,
                total_output_tokens INTEGER DEFAULT 0,
                total_cost REAL DEFAULT 0.0,
                avg_response_time_ms INTEGER DEFAULT 0,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE
            );
        ",
        
        'rejected_events' => "
            CREATE TABLE IF NOT EXISTS rejected_events (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                temp_file_path TEXT NOT NULL,
                original_filename TEXT NOT NULL,
                extracted_data TEXT,
                rejection_reason TEXT NOT NULL,
                suggested_categories TEXT,
                admin_reviewed INTEGER DEFAULT 0,
                review_notes TEXT,
                reviewed_by_user_id INTEGER,
                reviewed_date DATETIME,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'user_api_keys' => "
            CREATE TABLE IF NOT EXISTS user_api_keys (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                user_id INTEGER NOT NULL,
                provider_id INTEGER NOT NULL,
                api_key_encrypted TEXT NOT NULL,
                is_enabled INTEGER DEFAULT 1,
                last_used_date DATETIME,
                usage_count INTEGER DEFAULT 0,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE
            );
        ",
        
        'generated_content' => "
            CREATE TABLE IF NOT EXISTS generated_content (
                id INTEGER PRIMARY KEY " . (DB_TYPE === 'mysql' ? 'AUTO_INCREMENT' : 'AUTOINCREMENT') . ",
                user_id INTEGER,
                event_id INTEGER,
                prompt_id INTEGER,
                content_type TEXT NOT NULL,
                event_category TEXT,
                generated_content TEXT NOT NULL,
                metadata TEXT,
                is_approved INTEGER DEFAULT 0,
                approval_notes TEXT,
                usage_count INTEGER DEFAULT 0,
                last_used_date DATETIME,
                created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_date DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        "
    ];
    
    foreach ($tables as $tableName => $sql) {
        try {
            $pdo->exec($sql);
            echo "✓ Created table: $tableName\n";
        } catch (PDOException $e) {
            echo "✗ Error creating $tableName: " . $e->getMessage() . "\n";
        }
    }
    
    // Insert default data
    echo "\nInserting default data...\n";
    
    // Default providers
    $providers = [
        ['openai', 'OpenAI'],
        ['anthropic', 'Anthropic'],
        ['gemini', 'Google Gemini']
    ];
    
    $insertType = (DB_TYPE === 'mysql') ? 'INSERT IGNORE' : 'INSERT OR IGNORE';
    $stmt = $pdo->prepare("$insertType INTO llm_providers (name, display_name, is_enabled) VALUES (?, ?, 1)");
    foreach ($providers as $provider) {
        $stmt->execute($provider);
        echo "✓ Added provider: {$provider[1]}\n";
    }
    
    // Default event categories
    $categories = [
        ['concert', 'Concert/Music Event', 'Live music performances and concerts', '["concert", "music", "live", "band", "singer", "performance", "show"]'],
        ['festival', 'Festival', 'Music festivals and cultural events', '["festival", "fest", "celebration", "cultural", "outdoor"]'],
        ['party', 'Party/Club Event', 'Parties, club events, and social gatherings', '["party", "club", "dance", "social", "celebration", "bash"]'],
        ['sale', 'Sale/Promotion', 'Sales events and promotional activities', '["sale", "discount", "promotion", "offer", "deal", "clearance"]'],
        ['business', 'Business Event', 'Corporate events and business gatherings', '["business", "corporate", "conference", "meeting", "networking"]'],
        ['sports', 'Sports Event', 'Sports games and athletic events', '["sports", "game", "match", "tournament", "athletic", "competition"]'],
        ['community', 'Community Event', 'Community gatherings and local events', '["community", "local", "neighborhood", "public", "civic"]']
    ];
    
    $stmt = $pdo->prepare("$insertType INTO event_categories (category_name, display_name, description, detection_keywords) VALUES (?, ?, ?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
        echo "✓ Added category: {$category[1]}\n";
    }
    
    echo "\n✅ LLM management tables created successfully!\n";
    echo "Database type: " . DB_TYPE . "\n";
    echo "You can now access the AI/LLM settings tab.\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
?>
