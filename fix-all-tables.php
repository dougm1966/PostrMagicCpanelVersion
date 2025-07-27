<?php
/**
 * Unified Database Fix Script
 * Creates all missing tables for both Media and LLM systems
 * Works on both SQLite (local) and MySQL (cPanel)
 */

require_once 'config/config.php';

try {
    $pdo = getDBConnection();
    $isMySQL = (DB_TYPE === 'mysql');
    
    echo "<h2>Fixing All Database Tables</h2>\n";
    echo "<p>Environment: " . DB_TYPE . "</p>\n";
    
    // Define all tables with environment-specific syntax
    $autoIncrement = $isMySQL ? 'AUTO_INCREMENT' : 'AUTOINCREMENT';
    $textType = $isMySQL ? 'VARCHAR(255)' : 'TEXT';
    $boolType = $isMySQL ? 'BOOLEAN' : 'INTEGER';
    $enumType = $isMySQL ? "ENUM('media', 'poster')" : "TEXT CHECK (context IN ('media', 'poster'))";
    
    $tables = [
        // Media Tables
        'user_media' => "
            CREATE TABLE IF NOT EXISTS user_media (
                id INTEGER PRIMARY KEY $autoIncrement,
                user_id INTEGER NOT NULL,
                filename $textType NOT NULL,
                original_filename $textType NOT NULL,
                file_path TEXT NOT NULL,
                file_size INTEGER NOT NULL,
                mime_type VARCHAR(100) NOT NULL,
                width INTEGER NULL,
                height INTEGER NULL,
                thumbnail_path TEXT NULL,
                webp_path TEXT NULL,
                upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_accessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_optimized $boolType DEFAULT " . ($isMySQL ? 'FALSE' : '0') . ",
                optimization_data TEXT NULL,
                context $enumType DEFAULT 'media'
            );
        ",
        
        'media_tags' => "
            CREATE TABLE IF NOT EXISTS media_tags (
                id INTEGER PRIMARY KEY $autoIncrement,
                user_id INTEGER NOT NULL,
                tag_name $textType NOT NULL,
                usage_count INTEGER DEFAULT 0,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'media_tag_relations' => "
            CREATE TABLE IF NOT EXISTS media_tag_relations (
                id INTEGER PRIMARY KEY $autoIncrement,
                media_id INTEGER NOT NULL,
                tag_id INTEGER NOT NULL,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        // LLM Tables
        'llm_providers' => "
            CREATE TABLE IF NOT EXISTS llm_providers (
                id INTEGER PRIMARY KEY $autoIncrement,
                name $textType NOT NULL UNIQUE,
                display_name $textType NOT NULL,
                is_enabled $boolType DEFAULT " . ($isMySQL ? 'TRUE' : '1') . ",
                base_url TEXT NULL,
                api_version VARCHAR(20) NULL,
                rate_limit_per_minute INTEGER DEFAULT 60,
                rate_limit_per_hour INTEGER DEFAULT 3600,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'llm_configurations' => "
            CREATE TABLE IF NOT EXISTS llm_configurations (
                id INTEGER PRIMARY KEY $autoIncrement,
                provider_id INTEGER NOT NULL,
                event_category TEXT NULL,
                content_type TEXT NOT NULL,
                model_name $textType NOT NULL,
                is_enabled $boolType DEFAULT " . ($isMySQL ? 'TRUE' : '1') . ",
                priority_order INTEGER DEFAULT 1,
                max_tokens INTEGER DEFAULT 1000,
                temperature DECIMAL(3,2) DEFAULT 0.7,
                top_p DECIMAL(3,2) DEFAULT 1.0,
                frequency_penalty DECIMAL(3,2) DEFAULT 0.0,
                presence_penalty DECIMAL(3,2) DEFAULT 0.0,
                timeout_seconds INTEGER DEFAULT 30,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'event_categories' => "
            CREATE TABLE IF NOT EXISTS event_categories (
                id INTEGER PRIMARY KEY $autoIncrement,
                category_name $textType NOT NULL UNIQUE,
                display_name $textType NOT NULL,
                description TEXT NULL,
                is_enabled $boolType DEFAULT " . ($isMySQL ? 'TRUE' : '1') . ",
                detection_keywords TEXT NULL,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'llm_prompts' => "
            CREATE TABLE IF NOT EXISTS llm_prompts (
                id INTEGER PRIMARY KEY $autoIncrement,
                prompt_type TEXT NOT NULL,
                event_category TEXT NULL,
                content_type TEXT NULL,
                system_prompt TEXT NOT NULL,
                user_prompt TEXT NOT NULL,
                assistant_prompt TEXT NULL,
                placeholders TEXT NULL,
                is_active $boolType DEFAULT " . ($isMySQL ? 'TRUE' : '1') . ",
                version_number INTEGER DEFAULT 1,
                version_notes TEXT NULL,
                created_by_user_id INTEGER NOT NULL,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'llm_prompt_versions' => "
            CREATE TABLE IF NOT EXISTS llm_prompt_versions (
                id INTEGER PRIMARY KEY $autoIncrement,
                prompt_id INTEGER NOT NULL,
                version_number INTEGER NOT NULL,
                system_prompt TEXT NOT NULL,
                user_prompt TEXT NOT NULL,
                assistant_prompt TEXT NULL,
                placeholders TEXT NULL,
                version_notes TEXT NULL,
                created_by_user_id INTEGER NOT NULL,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                archived_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'llm_usage_logs' => "
            CREATE TABLE IF NOT EXISTS llm_usage_logs (
                id INTEGER PRIMARY KEY $autoIncrement,
                provider_id INTEGER NOT NULL,
                user_id INTEGER NULL,
                event_id INTEGER NULL,
                prompt_id INTEGER NULL,
                prompt_type TEXT NOT NULL,
                model_name $textType NOT NULL,
                input_tokens INTEGER DEFAULT 0,
                output_tokens INTEGER DEFAULT 0,
                total_tokens INTEGER DEFAULT 0,
                estimated_cost DECIMAL(10,4) DEFAULT 0.0,
                response_time_ms INTEGER NULL,
                was_successful $boolType DEFAULT " . ($isMySQL ? 'TRUE' : '1') . ",
                error_message TEXT NULL,
                api_key_source TEXT DEFAULT 'admin',
                request_data TEXT NULL,
                response_data TEXT NULL,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'llm_cost_tracking' => "
            CREATE TABLE IF NOT EXISTS llm_cost_tracking (
                id INTEGER PRIMARY KEY $autoIncrement,
                provider_id INTEGER NOT NULL,
                user_id INTEGER NULL,
                event_category TEXT NULL,
                content_type TEXT NULL,
                date_period DATE NOT NULL,
                total_requests INTEGER DEFAULT 0,
                successful_requests INTEGER DEFAULT 0,
                total_input_tokens INTEGER DEFAULT 0,
                total_output_tokens INTEGER DEFAULT 0,
                total_cost DECIMAL(10,4) DEFAULT 0.0,
                avg_response_time_ms INTEGER DEFAULT 0,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'rejected_events' => "
            CREATE TABLE IF NOT EXISTS rejected_events (
                id INTEGER PRIMARY KEY $autoIncrement,
                temp_file_path TEXT NOT NULL,
                original_filename $textType NOT NULL,
                extracted_data TEXT NULL,
                rejection_reason TEXT NOT NULL,
                suggested_categories TEXT NULL,
                admin_reviewed $boolType DEFAULT " . ($isMySQL ? 'FALSE' : '0') . ",
                review_notes TEXT NULL,
                reviewed_by_user_id INTEGER NULL,
                reviewed_date TIMESTAMP NULL,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'user_api_keys' => "
            CREATE TABLE IF NOT EXISTS user_api_keys (
                id INTEGER PRIMARY KEY $autoIncrement,
                user_id INTEGER NOT NULL,
                provider_id INTEGER NOT NULL,
                api_key_encrypted TEXT NOT NULL,
                is_enabled $boolType DEFAULT " . ($isMySQL ? 'TRUE' : '1') . ",
                last_used_date TIMESTAMP NULL,
                usage_count INTEGER DEFAULT 0,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'generated_content' => "
            CREATE TABLE IF NOT EXISTS generated_content (
                id INTEGER PRIMARY KEY $autoIncrement,
                user_id INTEGER NULL,
                event_id INTEGER NULL,
                prompt_id INTEGER NULL,
                content_type TEXT NOT NULL,
                event_category TEXT NULL,
                generated_content TEXT NOT NULL,
                metadata TEXT NULL,
                is_approved $boolType DEFAULT " . ($isMySQL ? 'FALSE' : '0') . ",
                approval_notes TEXT NULL,
                usage_count INTEGER DEFAULT 0,
                last_used_date TIMESTAMP NULL,
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ",
        
        'temporary_uploads' => "
            CREATE TABLE IF NOT EXISTS temporary_uploads (
                id INTEGER PRIMARY KEY $autoIncrement,
                temp_filename $textType NOT NULL,
                original_filename $textType NOT NULL,
                file_path TEXT NOT NULL,
                file_size INTEGER NOT NULL,
                mime_type VARCHAR(100) NOT NULL,
                contact_info TEXT NULL,
                additional_notes TEXT NULL,
                upload_time INTEGER NOT NULL,
                expires_at INTEGER NOT NULL,
                is_processed $boolType DEFAULT " . ($isMySQL ? 'FALSE' : '0') . ",
                processing_status TEXT DEFAULT 'pending',
                created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        "
    ];
    
    echo "<h3>Creating Tables...</h3>\n";
    foreach ($tables as $tableName => $sql) {
        try {
            $pdo->exec($sql);
            echo "✓ Created/verified table: <strong>$tableName</strong><br>\n";
        } catch (PDOException $e) {
            echo "✗ Error with $tableName: " . $e->getMessage() . "<br>\n";
        }
    }
    
    // Insert default data
    echo "<h3>Inserting Default Data...</h3>\n";
    
    // LLM Providers
    $insertCmd = $isMySQL ? 'INSERT IGNORE' : 'INSERT OR IGNORE';
    
    $providers = [
        ['openai', 'OpenAI'],
        ['anthropic', 'Anthropic'],
        ['gemini', 'Google Gemini']
    ];
    
    $stmt = $pdo->prepare("$insertCmd INTO llm_providers (name, display_name, is_enabled) VALUES (?, ?, " . ($isMySQL ? 'TRUE' : '1') . ")");
    foreach ($providers as $provider) {
        try {
            $stmt->execute($provider);
            echo "✓ Added provider: <strong>{$provider[1]}</strong><br>\n";
        } catch (PDOException $e) {
            echo "- Provider {$provider[1]} already exists<br>\n";
        }
    }
    
    // Event Categories
    $categories = [
        ['concert', 'Concert/Music Event', 'Live music performances and concerts'],
        ['festival', 'Festival', 'Music festivals and cultural events'],
        ['party', 'Party/Club Event', 'Parties, club events, and social gatherings'],
        ['sale', 'Sale/Promotion', 'Sales events and promotional activities'],
        ['business', 'Business Event', 'Corporate events and business gatherings'],
        ['sports', 'Sports Event', 'Sports games and athletic events'],
        ['community', 'Community Event', 'Community gatherings and local events']
    ];
    
    $stmt = $pdo->prepare("$insertCmd INTO event_categories (category_name, display_name, description) VALUES (?, ?, ?)");
    foreach ($categories as $category) {
        try {
            $stmt->execute($category);
            echo "✓ Added category: <strong>{$category[1]}</strong><br>\n";
        } catch (PDOException $e) {
            echo "- Category {$category[1]} already exists<br>\n";
        }
    }
    
    echo "<h2>✅ Database Setup Complete!</h2>\n";
    echo "<p><strong>All tables created with unified naming for both SQLite and MySQL compatibility.</strong></p>\n";
    echo "<p>Ready for cPanel deployment - same table names will work on both environments.</p>\n";
    
} catch (Exception $e) {
    echo "<h2>❌ Database Setup Failed</h2>\n";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}
?>
