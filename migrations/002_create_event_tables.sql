-- Migration 002: Create Event Management Tables
-- Creates tables for event system including temporary events and claiming

-- Events Table
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL, -- NULL for unclaimed events
    poster_media_id INT NULL, -- Link to the poster in user_media
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
    id INT PRIMARY KEY AUTO_INCREMENT,
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
    event_id INT NULL, -- Link to events table once processed
    status ENUM('pending', 'processing', 'processed', 'expired', 'claimed') DEFAULT 'pending',
    INDEX idx_extracted_email (extracted_email),
    INDEX idx_extracted_phone (extracted_phone),
    INDEX idx_expires_at (expires_at),
    INDEX idx_status (status),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
);

-- Event Claims Table
CREATE TABLE IF NOT EXISTS event_claims (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    temporary_event_id INT NULL,
    claim_token VARCHAR(64) NOT NULL UNIQUE,
    contact_email VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(20) NULL,
    contact_method ENUM('email', 'sms', 'both') DEFAULT 'email',
    verification_code VARCHAR(10) NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    claimed_by_user_id INT NULL,
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
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    platform ENUM('facebook', 'instagram', 'twitter', 'linkedin') NOT NULL,
    post_content TEXT NOT NULL,
    hashtags TEXT NULL,
    post_image_path VARCHAR(500) NULL,
    generated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_approved BOOLEAN DEFAULT FALSE,
    approval_date TIMESTAMP NULL,
    scheduled_date TIMESTAMP NULL,
    posted_date TIMESTAMP NULL,
    post_url VARCHAR(500) NULL,
    engagement_data TEXT NULL, -- JSON data with metrics
    INDEX idx_event_id (event_id),
    INDEX idx_platform (platform),
    INDEX idx_generated_date (generated_date),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Link Shortener Table
CREATE TABLE IF NOT EXISTS short_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    short_code VARCHAR(10) NOT NULL UNIQUE,
    original_url VARCHAR(1000) NOT NULL,
    link_type ENUM('claim', 'event', 'media', 'general') DEFAULT 'general',
    created_by_user_id INT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    click_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    metadata TEXT NULL, -- JSON data with link information
    INDEX idx_short_code (short_code),
    INDEX idx_link_type (link_type),
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Rate Limiting Table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    identifier VARCHAR(255) NOT NULL,
    identifier_type ENUM('email', 'phone', 'ip') NOT NULL,
    action_type ENUM('poster_upload', 'claim_request', 'api_call') NOT NULL,
    attempt_count INT DEFAULT 1,
    first_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    block_until TIMESTAMP NULL,
    is_blocked BOOLEAN DEFAULT FALSE,
    UNIQUE KEY unique_rate_limit (identifier, identifier_type, action_type),
    INDEX idx_identifier (identifier),
    INDEX idx_block_until (block_until),
    INDEX idx_last_attempt (last_attempt)
);

/* SQLite compatible versions removed - we're using MySQL */