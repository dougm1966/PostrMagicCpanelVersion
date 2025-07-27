-- Migration 004: Create LLM Management Tables
-- Creates tables for LLM provider management, prompts, and analytics

-- LLM Providers Table
CREATE TABLE IF NOT EXISTS llm_providers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    base_url VARCHAR(255) NULL,
    api_version VARCHAR(20) NULL,
    rate_limit_per_minute INT DEFAULT 60,
    rate_limit_per_hour INT DEFAULT 3600,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- LLM Provider Configurations
CREATE TABLE IF NOT EXISTS llm_configurations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    provider_id INT NOT NULL,
    event_category VARCHAR(100) NULL,
    content_type ENUM('vision_analysis', 'category_detection', 'facebook', 'instagram', 'linkedin') NOT NULL,
    model_name VARCHAR(100) NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    priority_order INT DEFAULT 1,
    max_tokens INT DEFAULT 1000,
    temperature DECIMAL(3,2) DEFAULT 0.7,
    top_p DECIMAL(3,2) DEFAULT 1.0,
    frequency_penalty DECIMAL(3,2) DEFAULT 0.0,
    presence_penalty DECIMAL(3,2) DEFAULT 0.0,
    timeout_seconds INT DEFAULT 30,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider_category (provider_id, event_category),
    INDEX idx_content_type (content_type),
    INDEX idx_priority_order (priority_order),
    FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE
);

-- Event Categories Table
CREATE TABLE IF NOT EXISTS event_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    detection_keywords TEXT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- LLM Prompts Table
CREATE TABLE IF NOT EXISTS llm_prompts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prompt_type ENUM('vision_analysis', 'category_detection', 'content_creation') NOT NULL,
    event_category VARCHAR(100) NULL,
    content_type ENUM('facebook', 'instagram', 'linkedin') NULL,
    system_prompt TEXT NOT NULL,
    user_prompt TEXT NOT NULL,
    assistant_prompt TEXT NULL,
    placeholders TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    version_number INT DEFAULT 1,
    version_notes TEXT NULL,
    created_by_user_id INT NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prompt_type (prompt_type),
    INDEX idx_event_category (event_category),
    INDEX idx_content_type (content_type),
    INDEX idx_is_active (is_active),
    INDEX idx_version (version_number),
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- LLM Prompt Versions
CREATE TABLE IF NOT EXISTS llm_prompt_versions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prompt_id INT NOT NULL,
    version_number INT NOT NULL,
    system_prompt TEXT NOT NULL,
    user_prompt TEXT NOT NULL,
    assistant_prompt TEXT NULL,
    placeholders TEXT NULL,
    version_notes TEXT NULL,
    created_by_user_id INT NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    archived_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prompt_version (prompt_id, version_number),
    FOREIGN KEY (prompt_id) REFERENCES llm_prompts(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- LLM Usage Logs
CREATE TABLE IF NOT EXISTS llm_usage_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    provider_id INT NOT NULL,
    user_id INT NULL,
    event_id INT NULL,
    prompt_id INT NULL,
    prompt_type ENUM('vision_analysis', 'category_detection', 'content_creation') NOT NULL,
    model_name VARCHAR(100) NOT NULL,
    input_tokens INT DEFAULT 0,
    output_tokens INT DEFAULT 0,
    total_tokens INT DEFAULT 0,
    estimated_cost DECIMAL(10,6) DEFAULT 0.000000,
    response_time_ms INT NULL,
    was_successful BOOLEAN DEFAULT TRUE,
    error_message TEXT NULL,
    api_key_source ENUM('admin', 'user') DEFAULT 'admin',
    request_data TEXT NULL,
    response_data TEXT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_provider_date (provider_id, created_date),
    INDEX idx_user_date (user_id, created_date),
    INDEX idx_prompt_type (prompt_type),
    FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    FOREIGN KEY (prompt_id) REFERENCES llm_prompts(id) ON DELETE SET NULL
);

-- LLM Cost Tracking
CREATE TABLE IF NOT EXISTS llm_cost_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    provider_id INT NOT NULL,
    user_id INT NULL,
    event_category VARCHAR(100) NULL,
    content_type VARCHAR(50) NULL,
    date_period DATE NOT NULL,
    total_requests INT DEFAULT 0,
    successful_requests INT DEFAULT 0,
    total_input_tokens INT DEFAULT 0,
    total_output_tokens INT DEFAULT 0,
    total_cost DECIMAL(10,6) DEFAULT 0.000000,
    avg_response_time_ms INT DEFAULT 0,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tracking (provider_id, user_id, event_category, content_type, date_period),
    INDEX idx_date_period (date_period),
    FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Rejected Events
CREATE TABLE IF NOT EXISTS rejected_events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    temp_file_path VARCHAR(500) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    extracted_data TEXT NULL,
    rejection_reason TEXT NOT NULL,
    suggested_categories TEXT NULL,
    admin_reviewed BOOLEAN DEFAULT FALSE,
    review_notes TEXT NULL,
    reviewed_by_user_id INT NULL,
    reviewed_date TIMESTAMP NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_reviewed (admin_reviewed),
    INDEX idx_created_date (created_date),
    FOREIGN KEY (reviewed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- User API Keys
CREATE TABLE IF NOT EXISTS user_api_keys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    provider_id INT NOT NULL,
    api_key_encrypted TEXT NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    last_used_date TIMESTAMP NULL,
    usage_count INT DEFAULT 0,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (user_id, provider_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE
);

-- Insert default providers
INSERT IGNORE INTO llm_providers (name, display_name, is_enabled) VALUES 
('openai', 'OpenAI', 1),
('anthropic', 'Anthropic', 1),
('gemini', 'Google Gemini', 1);

-- Insert default event categories
INSERT IGNORE INTO event_categories (category_name, display_name, description, detection_keywords) VALUES 
('concert', 'Concert/Music Event', 'Live music performances and concerts', '["concert", "music", "live", "band", "singer", "performance", "show"]'),
('festival', 'Festival', 'Music festivals and cultural events', '["festival", "fest", "celebration", "cultural", "outdoor"]'),
('party', 'Party/Club Event', 'Parties, club events, and social gatherings', '["party", "club", "dance", "social", "celebration", "bash"]'),
('sale', 'Sale/Promotion', 'Sales events and promotional activities', '["sale", "discount", "promotion", "offer", "deal", "clearance"]'),
('business', 'Business Event', 'Corporate events and business gatherings', '["business", "corporate", "conference", "meeting", "networking"]'),
('sports', 'Sports Event', 'Sports games and athletic events', '["sports", "game", "match", "tournament", "athletic", "competition"]'),
('community', 'Community Event', 'Community gatherings and local events', '["community", "local", "neighborhood", "public", "civic"]');