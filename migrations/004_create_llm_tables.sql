-- Migration 004: Create LLM Management Tables
-- Creates tables for LLM provider management, prompts, and analytics

-- LLM Providers Table
CREATE TABLE IF NOT EXISTS llm_providers (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE, -- 'openai', 'anthropic', 'gemini'
    display_name VARCHAR(100) NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    base_url VARCHAR(255) NULL, -- Custom API endpoint if needed
    api_version VARCHAR(20) NULL,
    rate_limit_per_minute INTEGER DEFAULT 60,
    rate_limit_per_hour INTEGER DEFAULT 3600,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- LLM Provider Configurations (global and per-category settings)
CREATE TABLE IF NOT EXISTS llm_configurations (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    provider_id INTEGER NOT NULL,
    event_category VARCHAR(100) NULL, -- NULL for global config
    content_type ENUM('vision_analysis', 'category_detection', 'facebook', 'instagram', 'linkedin') NOT NULL,
    model_name VARCHAR(100) NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    priority_order INTEGER DEFAULT 1, -- For fallback ordering
    max_tokens INTEGER DEFAULT 1000,
    temperature DECIMAL(3,2) DEFAULT 0.7,
    top_p DECIMAL(3,2) DEFAULT 1.0,
    frequency_penalty DECIMAL(3,2) DEFAULT 0.0,
    presence_penalty DECIMAL(3,2) DEFAULT 0.0,
    timeout_seconds INTEGER DEFAULT 30,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_provider_category (provider_id, event_category),
    INDEX idx_content_type (content_type),
    INDEX idx_priority_order (priority_order),
    FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE
);

-- Event Categories Table
CREATE TABLE IF NOT EXISTS event_categories (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    detection_keywords TEXT NULL, -- JSON array of keywords for detection
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- LLM Prompts Table (with versioning)
CREATE TABLE IF NOT EXISTS llm_prompts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    prompt_type ENUM('vision_analysis', 'category_detection', 'content_creation') NOT NULL,
    event_category VARCHAR(100) NULL, -- NULL for global prompts
    content_type ENUM('facebook', 'instagram', 'linkedin') NULL, -- Only for content_creation type
    system_prompt TEXT NOT NULL,
    user_prompt TEXT NOT NULL,
    assistant_prompt TEXT NULL,
    placeholders TEXT NULL, -- JSON array of available placeholders
    is_active BOOLEAN DEFAULT TRUE,
    version_number INTEGER DEFAULT 1,
    version_notes TEXT NULL,
    created_by_user_id INTEGER NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prompt_type (prompt_type),
    INDEX idx_event_category (event_category),
    INDEX idx_content_type (content_type),
    INDEX idx_is_active (is_active),
    INDEX idx_version (version_number),
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- LLM Prompt Versions (stores up to 4 previous versions)
CREATE TABLE IF NOT EXISTS llm_prompt_versions (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    prompt_id INTEGER NOT NULL,
    version_number INTEGER NOT NULL,
    system_prompt TEXT NOT NULL,
    user_prompt TEXT NOT NULL,
    assistant_prompt TEXT NULL,
    placeholders TEXT NULL,
    version_notes TEXT NULL,
    created_by_user_id INTEGER NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    archived_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prompt_version (prompt_id, version_number),
    FOREIGN KEY (prompt_id) REFERENCES llm_prompts(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- LLM Usage Logs (for cost tracking and analytics)
CREATE TABLE IF NOT EXISTS llm_usage_logs (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    provider_id INTEGER NOT NULL,
    user_id INTEGER NULL, -- NULL for system usage
    event_id INTEGER NULL,
    prompt_id INTEGER NULL,
    prompt_type ENUM('vision_analysis', 'category_detection', 'content_creation') NOT NULL,
    model_name VARCHAR(100) NOT NULL,
    input_tokens INTEGER DEFAULT 0,
    output_tokens INTEGER DEFAULT 0,
    total_tokens INTEGER DEFAULT 0,
    estimated_cost DECIMAL(10,6) DEFAULT 0.000000, -- Cost in USD
    response_time_ms INTEGER NULL,
    was_successful BOOLEAN DEFAULT TRUE,
    error_message TEXT NULL,
    api_key_source ENUM('admin', 'user') DEFAULT 'admin',
    request_data TEXT NULL, -- JSON of request parameters
    response_data TEXT NULL, -- JSON of response data
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_provider_date (provider_id, created_date),
    INDEX idx_user_date (user_id, created_date),
    INDEX idx_event_id (event_id),
    INDEX idx_prompt_type (prompt_type),
    INDEX idx_cost_tracking (user_id, provider_id, created_date),
    FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    FOREIGN KEY (prompt_id) REFERENCES llm_prompts(id) ON DELETE SET NULL
);

-- LLM Cost Tracking Summary (for faster analytics)
CREATE TABLE IF NOT EXISTS llm_cost_tracking (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    provider_id INTEGER NOT NULL,
    user_id INTEGER NULL,
    event_category VARCHAR(100) NULL,
    content_type VARCHAR(50) NULL,
    date_period DATE NOT NULL, -- Daily aggregation
    total_requests INTEGER DEFAULT 0,
    successful_requests INTEGER DEFAULT 0,
    total_input_tokens INTEGER DEFAULT 0,
    total_output_tokens INTEGER DEFAULT 0,
    total_cost DECIMAL(10,6) DEFAULT 0.000000,
    avg_response_time_ms INTEGER DEFAULT 0,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_cost_tracking (provider_id, user_id, event_category, content_type, date_period),
    INDEX idx_provider_period (provider_id, date_period),
    INDEX idx_user_period (user_id, date_period),
    INDEX idx_category_period (event_category, date_period),
    FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Rejected Events Table (for tracking events that don't fit categories)
CREATE TABLE IF NOT EXISTS rejected_events (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    temp_file_path VARCHAR(500) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    extracted_data TEXT NULL, -- JSON of what was extracted
    rejection_reason TEXT NOT NULL,
    suggested_categories TEXT NULL, -- JSON array of suggested new categories
    admin_reviewed BOOLEAN DEFAULT FALSE,
    review_notes TEXT NULL,
    reviewed_by_user_id INTEGER NULL,
    reviewed_date TIMESTAMP NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_reviewed (admin_reviewed),
    INDEX idx_created_date (created_date),
    FOREIGN KEY (reviewed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- User API Keys Table (for when users provide their own keys)
CREATE TABLE IF NOT EXISTS user_api_keys (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    provider_id INTEGER NOT NULL,
    api_key_encrypted TEXT NOT NULL, -- Encrypted API key
    is_enabled BOOLEAN DEFAULT TRUE,
    last_used_date TIMESTAMP NULL,
    usage_count INTEGER DEFAULT 0,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_provider (user_id, provider_id),
    INDEX idx_user_id (user_id),
    INDEX idx_provider_id (provider_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES llm_providers(id) ON DELETE CASCADE
);

-- SQLite compatible versions

-- LLM Providers Table (SQLite)
CREATE TABLE IF NOT EXISTS llm_providers_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
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

-- LLM Provider Configurations (SQLite)
CREATE TABLE IF NOT EXISTS llm_configurations_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    provider_id INTEGER NOT NULL,
    event_category TEXT,
    content_type TEXT NOT NULL CHECK (content_type IN ('vision_analysis', 'category_detection', 'facebook', 'instagram', 'linkedin')),
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
    FOREIGN KEY (provider_id) REFERENCES llm_providers_sqlite(id) ON DELETE CASCADE
);

-- Event Categories Table (SQLite)
CREATE TABLE IF NOT EXISTS event_categories_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_name TEXT NOT NULL UNIQUE,
    display_name TEXT NOT NULL,
    description TEXT,
    is_enabled INTEGER DEFAULT 1,
    detection_keywords TEXT,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- LLM Prompts Table (SQLite)
CREATE TABLE IF NOT EXISTS llm_prompts_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prompt_type TEXT NOT NULL CHECK (prompt_type IN ('vision_analysis', 'category_detection', 'content_creation')),
    event_category TEXT,
    content_type TEXT CHECK (content_type IN ('facebook', 'instagram', 'linkedin')),
    system_prompt TEXT NOT NULL,
    user_prompt TEXT NOT NULL,
    assistant_prompt TEXT,
    placeholders TEXT,
    is_active INTEGER DEFAULT 1,
    version_number INTEGER DEFAULT 1,
    version_notes TEXT,
    created_by_user_id INTEGER NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- LLM Prompt Versions (SQLite)
CREATE TABLE IF NOT EXISTS llm_prompt_versions_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
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
    FOREIGN KEY (prompt_id) REFERENCES llm_prompts_sqlite(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- LLM Usage Logs (SQLite)
CREATE TABLE IF NOT EXISTS llm_usage_logs_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    provider_id INTEGER NOT NULL,
    user_id INTEGER,
    event_id INTEGER,
    prompt_id INTEGER,
    prompt_type TEXT NOT NULL CHECK (prompt_type IN ('vision_analysis', 'category_detection', 'content_creation')),
    model_name TEXT NOT NULL,
    input_tokens INTEGER DEFAULT 0,
    output_tokens INTEGER DEFAULT 0,
    total_tokens INTEGER DEFAULT 0,
    estimated_cost REAL DEFAULT 0.0,
    response_time_ms INTEGER,
    was_successful INTEGER DEFAULT 1,
    error_message TEXT,
    api_key_source TEXT DEFAULT 'admin' CHECK (api_key_source IN ('admin', 'user')),
    request_data TEXT,
    response_data TEXT,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES llm_providers_sqlite(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (event_id) REFERENCES events_sqlite(id) ON DELETE SET NULL,
    FOREIGN KEY (prompt_id) REFERENCES llm_prompts_sqlite(id) ON DELETE SET NULL
);

-- LLM Cost Tracking Summary (SQLite)
CREATE TABLE IF NOT EXISTS llm_cost_tracking_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
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
    UNIQUE (provider_id, user_id, event_category, content_type, date_period),
    FOREIGN KEY (provider_id) REFERENCES llm_providers_sqlite(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Rejected Events Table (SQLite)
CREATE TABLE IF NOT EXISTS rejected_events_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    temp_file_path TEXT NOT NULL,
    original_filename TEXT NOT NULL,
    extracted_data TEXT,
    rejection_reason TEXT NOT NULL,
    suggested_categories TEXT,
    admin_reviewed INTEGER DEFAULT 0,
    review_notes TEXT,
    reviewed_by_user_id INTEGER,
    reviewed_date DATETIME,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- User API Keys Table (SQLite)
CREATE TABLE IF NOT EXISTS user_api_keys_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    provider_id INTEGER NOT NULL,
    api_key_encrypted TEXT NOT NULL,
    is_enabled INTEGER DEFAULT 1,
    last_used_date DATETIME,
    usage_count INTEGER DEFAULT 0,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, provider_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES llm_providers_sqlite(id) ON DELETE CASCADE
);

-- Create indexes for SQLite tables
CREATE INDEX IF NOT EXISTS idx_llm_configs_provider_category ON llm_configurations_sqlite(provider_id, event_category);
CREATE INDEX IF NOT EXISTS idx_llm_configs_content_type ON llm_configurations_sqlite(content_type);

CREATE INDEX IF NOT EXISTS idx_llm_prompts_type ON llm_prompts_sqlite(prompt_type);
CREATE INDEX IF NOT EXISTS idx_llm_prompts_category ON llm_prompts_sqlite(event_category);
CREATE INDEX IF NOT EXISTS idx_llm_prompts_content_type ON llm_prompts_sqlite(content_type);

CREATE INDEX IF NOT EXISTS idx_llm_usage_provider_date ON llm_usage_logs_sqlite(provider_id, created_date);
CREATE INDEX IF NOT EXISTS idx_llm_usage_user_date ON llm_usage_logs_sqlite(user_id, created_date);
CREATE INDEX IF NOT EXISTS idx_llm_usage_prompt_type ON llm_usage_logs_sqlite(prompt_type);

CREATE INDEX IF NOT EXISTS idx_llm_cost_provider_period ON llm_cost_tracking_sqlite(provider_id, date_period);
CREATE INDEX IF NOT EXISTS idx_llm_cost_user_period ON llm_cost_tracking_sqlite(user_id, date_period);

CREATE INDEX IF NOT EXISTS idx_rejected_events_reviewed ON rejected_events_sqlite(admin_reviewed);
CREATE INDEX IF NOT EXISTS idx_rejected_events_date ON rejected_events_sqlite(created_date);

-- Insert default providers
INSERT OR IGNORE INTO llm_providers_sqlite (name, display_name, is_enabled) VALUES 
('openai', 'OpenAI', 1),
('anthropic', 'Anthropic', 1),
('gemini', 'Google Gemini', 1);

INSERT IGNORE INTO llm_providers (name, display_name, is_enabled) VALUES 
('openai', 'OpenAI', 1),
('anthropic', 'Anthropic', 1),
('gemini', 'Google Gemini', 1);

-- Insert default event categories
INSERT OR IGNORE INTO event_categories_sqlite (category_name, display_name, description, detection_keywords) VALUES 
('concert', 'Concert/Music Event', 'Live music performances and concerts', '["concert", "music", "live", "band", "singer", "performance", "show"]'),
('festival', 'Festival', 'Music festivals and cultural events', '["festival", "fest", "celebration", "cultural", "outdoor"]'),
('party', 'Party/Club Event', 'Parties, club events, and social gatherings', '["party", "club", "dance", "social", "celebration", "bash"]'),
('sale', 'Sale/Promotion', 'Sales events and promotional activities', '["sale", "discount", "promotion", "offer", "deal", "clearance"]'),
('business', 'Business Event', 'Corporate events and business gatherings', '["business", "corporate", "conference", "meeting", "networking"]'),
('sports', 'Sports Event', 'Sports games and athletic events', '["sports", "game", "match", "tournament", "athletic", "competition"]'),
('community', 'Community Event', 'Community gatherings and local events', '["community", "local", "neighborhood", "public", "civic"]');

INSERT IGNORE INTO event_categories (category_name, display_name, description, detection_keywords) VALUES 
('concert', 'Concert/Music Event', 'Live music performances and concerts', '["concert", "music", "live", "band", "singer", "performance", "show"]'),
('festival', 'Festival', 'Music festivals and cultural events', '["festival", "fest", "celebration", "cultural", "outdoor"]'),
('party', 'Party/Club Event', 'Parties, club events, and social gatherings', '["party", "club", "dance", "social", "celebration", "bash"]'),
('sale', 'Sale/Promotion', 'Sales events and promotional activities', '["sale", "discount", "promotion", "offer", "deal", "clearance"]'),
('business', 'Business Event', 'Corporate events and business gatherings', '["business", "corporate", "conference", "meeting", "networking"]'),
('sports', 'Sports Event', 'Sports games and athletic events', '["sports", "game", "match", "tournament", "athletic", "competition"]'),
('community', 'Community Event', 'Community gatherings and local events', '["community", "local", "neighborhood", "public", "civic"]');