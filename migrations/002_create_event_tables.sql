-- Migration 002: Create Event Management Tables
-- Creates tables for event system including temporary events and claiming

-- Events Table
CREATE TABLE IF NOT EXISTS events (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NULL, -- NULL for unclaimed events
    poster_media_id INTEGER NULL, -- Link to the poster in user_media
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    event_date DATE NULL,
    event_time TIME NULL,
    venue_name VARCHAR(255) NULL,
    venue_address TEXT NULL,
    contact_email VARCHAR(255) NULL,
    contact_phone VARCHAR(20) NULL,
    contact_name VARCHAR(255) NULL,
    website_url VARCHAR(500) NULL,
    social_media_links TEXT NULL, -- JSON array of social links
    event_type VARCHAR(100) NULL, -- 'concert', 'festival', 'party', etc.
    ticket_price VARCHAR(100) NULL,
    ticket_url VARCHAR(500) NULL,
    age_restriction VARCHAR(50) NULL,
    status ENUM('temporary', 'active', 'draft', 'past', 'cancelled') DEFAULT 'temporary',
    is_public BOOLEAN DEFAULT TRUE,
    ai_extracted_data TEXT NULL, -- JSON of AI analysis results
    ai_confidence_score DECIMAL(3,2) NULL, -- 0.00 to 1.00
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    claim_deadline TIMESTAMP NULL, -- For temporary events
    published_date TIMESTAMP NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_event_date (event_date),
    INDEX idx_contact_email (contact_email),
    INDEX idx_contact_phone (contact_phone),
    INDEX idx_is_public (is_public),
    INDEX idx_claim_deadline (claim_deadline),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (poster_media_id) REFERENCES user_media(id) ON DELETE SET NULL
);

-- Temporary Events Table (for anonymous uploads)
CREATE TABLE IF NOT EXISTS temporary_events (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    temp_file_path VARCHAR(500) NOT NULL,
    extracted_email VARCHAR(255) NULL,
    extracted_phone VARCHAR(20) NULL,
    extracted_name VARCHAR(255) NULL,
    ai_analysis_data TEXT NULL, -- Full JSON of AI analysis
    upload_ip VARCHAR(45) NULL,
    upload_user_agent TEXT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL, -- 48 hours from creation
    is_processed BOOLEAN DEFAULT FALSE,
    event_id INTEGER NULL, -- Link to events table once processed
    status ENUM('pending', 'processing', 'processed', 'expired', 'claimed') DEFAULT 'pending',
    INDEX idx_extracted_email (extracted_email),
    INDEX idx_extracted_phone (extracted_phone),
    INDEX idx_expires_at (expires_at),
    INDEX idx_status (status),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
);

-- Event Claims Table
CREATE TABLE IF NOT EXISTS event_claims (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    event_id INTEGER NOT NULL,
    temporary_event_id INTEGER NULL,
    claim_token VARCHAR(64) NOT NULL UNIQUE,
    contact_email VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(20) NULL,
    contact_method ENUM('email', 'sms', 'both') DEFAULT 'email',
    verification_code VARCHAR(10) NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    claimed_by_user_id INTEGER NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL, -- 48 hours from creation
    claimed_at TIMESTAMP NULL,
    notification_sent_at TIMESTAMP NULL,
    reminder_sent_at TIMESTAMP NULL,
    INDEX idx_claim_token (claim_token),
    INDEX idx_contact_email (contact_email),
    INDEX idx_expires_at (expires_at),
    INDEX idx_event_id (event_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (temporary_event_id) REFERENCES temporary_events(id) ON DELETE SET NULL,
    FOREIGN KEY (claimed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Social Media Posts Table
CREATE TABLE IF NOT EXISTS social_media_posts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    event_id INTEGER NOT NULL,
    platform ENUM('facebook', 'instagram', 'twitter', 'linkedin') NOT NULL,
    post_content TEXT NOT NULL,
    hashtags TEXT NULL, -- JSON array of hashtags
    post_image_path VARCHAR(500) NULL,
    generated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_approved BOOLEAN DEFAULT FALSE,
    approval_date TIMESTAMP NULL,
    scheduled_date TIMESTAMP NULL,
    posted_date TIMESTAMP NULL,
    post_url VARCHAR(500) NULL,
    engagement_data TEXT NULL, -- JSON of likes, shares, comments
    INDEX idx_event_id (event_id),
    INDEX idx_platform (platform),
    INDEX idx_generated_date (generated_date),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Link Shortener Table
CREATE TABLE IF NOT EXISTS short_links (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    short_code VARCHAR(10) NOT NULL UNIQUE,
    original_url VARCHAR(1000) NOT NULL,
    link_type ENUM('claim', 'event', 'media', 'general') DEFAULT 'general',
    created_by_user_id INTEGER NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    click_count INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    metadata TEXT NULL, -- JSON for additional data
    INDEX idx_short_code (short_code),
    INDEX idx_link_type (link_type),
    INDEX idx_created_date (created_date),
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Rate Limiting Table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    identifier VARCHAR(255) NOT NULL, -- email, phone, or IP
    identifier_type ENUM('email', 'phone', 'ip') NOT NULL,
    action_type ENUM('poster_upload', 'claim_request', 'api_call') NOT NULL,
    attempt_count INTEGER DEFAULT 1,
    first_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    block_until TIMESTAMP NULL,
    is_blocked BOOLEAN DEFAULT FALSE,
    UNIQUE KEY unique_rate_limit (identifier, identifier_type, action_type),
    INDEX idx_identifier (identifier),
    INDEX idx_block_until (block_until),
    INDEX idx_last_attempt (last_attempt)
);

-- SQLite compatible versions

-- Events Table (SQLite)
CREATE TABLE IF NOT EXISTS events_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    poster_media_id INTEGER,
    title TEXT NOT NULL,
    description TEXT,
    event_date DATE,
    event_time TIME,
    venue_name TEXT,
    venue_address TEXT,
    contact_email TEXT,
    contact_phone TEXT,
    contact_name TEXT,
    website_url TEXT,
    social_media_links TEXT,
    event_type TEXT,
    ticket_price TEXT,
    ticket_url TEXT,
    age_restriction TEXT,
    status TEXT DEFAULT 'temporary' CHECK (status IN ('temporary', 'active', 'draft', 'past', 'cancelled')),
    is_public INTEGER DEFAULT 1,
    ai_extracted_data TEXT,
    ai_confidence_score REAL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    claim_deadline DATETIME,
    published_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (poster_media_id) REFERENCES user_media(id) ON DELETE SET NULL
);

-- Temporary Events Table (SQLite)
CREATE TABLE IF NOT EXISTS temporary_events_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    temp_file_path TEXT NOT NULL,
    extracted_email TEXT,
    extracted_phone TEXT,
    extracted_name TEXT,
    ai_analysis_data TEXT,
    upload_ip TEXT,
    upload_user_agent TEXT,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    is_processed INTEGER DEFAULT 0,
    event_id INTEGER,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'processing', 'processed', 'expired', 'claimed')),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
);

-- Event Claims Table (SQLite)
CREATE TABLE IF NOT EXISTS event_claims_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_id INTEGER NOT NULL,
    temporary_event_id INTEGER,
    claim_token TEXT NOT NULL UNIQUE,
    contact_email TEXT NOT NULL,
    contact_phone TEXT,
    contact_method TEXT DEFAULT 'email' CHECK (contact_method IN ('email', 'sms', 'both')),
    verification_code TEXT,
    is_verified INTEGER DEFAULT 0,
    claimed_by_user_id INTEGER,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    claimed_at DATETIME,
    notification_sent_at DATETIME,
    reminder_sent_at DATETIME,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (temporary_event_id) REFERENCES temporary_events(id) ON DELETE SET NULL,
    FOREIGN KEY (claimed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Social Media Posts Table (SQLite)
CREATE TABLE IF NOT EXISTS social_media_posts_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_id INTEGER NOT NULL,
    platform TEXT NOT NULL CHECK (platform IN ('facebook', 'instagram', 'twitter', 'linkedin')),
    post_content TEXT NOT NULL,
    hashtags TEXT,
    post_image_path TEXT,
    generated_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_approved INTEGER DEFAULT 0,
    approval_date DATETIME,
    scheduled_date DATETIME,
    posted_date DATETIME,
    post_url TEXT,
    engagement_data TEXT,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Link Shortener Table (SQLite)
CREATE TABLE IF NOT EXISTS short_links_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    short_code TEXT NOT NULL UNIQUE,
    original_url TEXT NOT NULL,
    link_type TEXT DEFAULT 'general' CHECK (link_type IN ('claim', 'event', 'media', 'general')),
    created_by_user_id INTEGER,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME,
    click_count INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    metadata TEXT,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Rate Limiting Table (SQLite)
CREATE TABLE IF NOT EXISTS rate_limits_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    identifier TEXT NOT NULL,
    identifier_type TEXT NOT NULL CHECK (identifier_type IN ('email', 'phone', 'ip')),
    action_type TEXT NOT NULL CHECK (action_type IN ('poster_upload', 'claim_request', 'api_call')),
    attempt_count INTEGER DEFAULT 1,
    first_attempt DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP,
    block_until DATETIME,
    is_blocked INTEGER DEFAULT 0,
    UNIQUE (identifier, identifier_type, action_type)
);

-- Create indexes for SQLite tables
CREATE INDEX IF NOT EXISTS idx_events_user_id ON events_sqlite(user_id);
CREATE INDEX IF NOT EXISTS idx_events_status ON events_sqlite(status);
CREATE INDEX IF NOT EXISTS idx_events_event_date ON events_sqlite(event_date);
CREATE INDEX IF NOT EXISTS idx_events_contact_email ON events_sqlite(contact_email);
CREATE INDEX IF NOT EXISTS idx_events_is_public ON events_sqlite(is_public);

CREATE INDEX IF NOT EXISTS idx_temp_events_email ON temporary_events_sqlite(extracted_email);
CREATE INDEX IF NOT EXISTS idx_temp_events_expires ON temporary_events_sqlite(expires_at);
CREATE INDEX IF NOT EXISTS idx_temp_events_status ON temporary_events_sqlite(status);

CREATE INDEX IF NOT EXISTS idx_claims_token ON event_claims_sqlite(claim_token);
CREATE INDEX IF NOT EXISTS idx_claims_email ON event_claims_sqlite(contact_email);
CREATE INDEX IF NOT EXISTS idx_claims_expires ON event_claims_sqlite(expires_at);

CREATE INDEX IF NOT EXISTS idx_social_posts_event ON social_media_posts_sqlite(event_id);
CREATE INDEX IF NOT EXISTS idx_social_posts_platform ON social_media_posts_sqlite(platform);

CREATE INDEX IF NOT EXISTS idx_short_links_code ON short_links_sqlite(short_code);
CREATE INDEX IF NOT EXISTS idx_short_links_type ON short_links_sqlite(link_type);

CREATE INDEX IF NOT EXISTS idx_rate_limits_identifier ON rate_limits_sqlite(identifier);
CREATE INDEX IF NOT EXISTS idx_rate_limits_block_until ON rate_limits_sqlite(block_until);