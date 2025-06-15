-- Migration 001: Create Media Library Tables
-- Creates tables for user media storage and tagging system

-- User Media Files Table
CREATE TABLE IF NOT EXISTS user_media (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INTEGER NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    width INTEGER NULL,
    height INTEGER NULL,
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
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    tag_name VARCHAR(50) NOT NULL,
    tag_color VARCHAR(7) DEFAULT '#6366f1', -- Hex color for tag display
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usage_count INTEGER DEFAULT 0,
    UNIQUE KEY unique_user_tag (user_id, tag_name),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Media Tag Relations Table (Many-to-Many)
CREATE TABLE IF NOT EXISTS media_tag_relations (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    media_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_media_tag (media_id, tag_id),
    INDEX idx_media_id (media_id),
    INDEX idx_tag_id (tag_id),
    FOREIGN KEY (media_id) REFERENCES user_media(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES media_tags(id) ON DELETE CASCADE
);

-- SQLite compatible version (for local development)
-- Note: Will be automatically detected and used by migration script

-- User Media Files Table (SQLite)
CREATE TABLE IF NOT EXISTS user_media_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    filename TEXT NOT NULL,
    original_filename TEXT NOT NULL,
    file_path TEXT NOT NULL,
    file_size INTEGER NOT NULL,
    mime_type TEXT NOT NULL,
    width INTEGER,
    height INTEGER,
    thumbnail_path TEXT,
    webp_path TEXT,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_accessed DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_optimized INTEGER DEFAULT 0,
    optimization_data TEXT,
    context TEXT DEFAULT 'media' CHECK (context IN ('media', 'poster')),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Media Tags Table (SQLite)
CREATE TABLE IF NOT EXISTS media_tags_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    tag_name TEXT NOT NULL,
    tag_color TEXT DEFAULT '#6366f1',
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    usage_count INTEGER DEFAULT 0,
    UNIQUE (user_id, tag_name),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Media Tag Relations Table (SQLite)
CREATE TABLE IF NOT EXISTS media_tag_relations_sqlite (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    media_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (media_id, tag_id),
    FOREIGN KEY (media_id) REFERENCES user_media(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES media_tags(id) ON DELETE CASCADE
);

-- Create indexes for SQLite
CREATE INDEX IF NOT EXISTS idx_user_media_user_id ON user_media_sqlite(user_id);
CREATE INDEX IF NOT EXISTS idx_user_media_upload_date ON user_media_sqlite(upload_date);
CREATE INDEX IF NOT EXISTS idx_user_media_mime_type ON user_media_sqlite(mime_type);
CREATE INDEX IF NOT EXISTS idx_user_media_context ON user_media_sqlite(context);

CREATE INDEX IF NOT EXISTS idx_media_tags_user_id ON media_tags_sqlite(user_id);

CREATE INDEX IF NOT EXISTS idx_media_tag_relations_media_id ON media_tag_relations_sqlite(media_id);
CREATE INDEX IF NOT EXISTS idx_media_tag_relations_tag_id ON media_tag_relations_sqlite(tag_id);