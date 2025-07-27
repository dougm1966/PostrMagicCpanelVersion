-- Migration 001: Create Media Library Tables
-- Creates tables for user media storage and tagging system

-- User Media Files Table
CREATE TABLE IF NOT EXISTS user_media (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    width INT NULL,
    height INT NULL,
    thumbnail_path VARCHAR(500) NULL,
    webp_path VARCHAR(500) NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_accessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_optimized BOOLEAN DEFAULT FALSE,
    optimization_data TEXT NULL, -- JSON data about optimization results
    context ENUM('media', 'poster') DEFAULT 'media',
    INDEX idx_user_id (user_id),
    INDEX idx_upload_date (upload_date),
    INDEX idx_mime_type (mime_type),
    INDEX idx_context (context),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Media Tags Table  
CREATE TABLE IF NOT EXISTS media_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    tag_name VARCHAR(50) NOT NULL,
    tag_color VARCHAR(7) DEFAULT '#6366f1', -- Hex color for tag display
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usage_count INT DEFAULT 0,
    UNIQUE KEY unique_user_tag (user_id, tag_name),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Media Tag Relations Table (Many-to-Many)
CREATE TABLE IF NOT EXISTS media_tag_relations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    media_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_media_tag (media_id, tag_id),
    INDEX idx_media_id (media_id),
    INDEX idx_tag_id (tag_id),
    FOREIGN KEY (media_id) REFERENCES user_media(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES media_tags(id) ON DELETE CASCADE
);

/* SQLite compatible versions removed - we're using MySQL */