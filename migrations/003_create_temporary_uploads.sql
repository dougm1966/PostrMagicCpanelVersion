-- Migration: Create Temporary Uploads Table
-- Description: Stores temporary poster uploads before AI analysis and conversion to permanent events

-- MySQL Version
CREATE TABLE IF NOT EXISTS temporary_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temp_filename VARCHAR(255) NOT NULL UNIQUE,
    original_filename VARCHAR(255) NOT NULL,
    temp_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    analysis_status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    analysis_result JSON,
    contact_info VARCHAR(255),
    additional_notes TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_temp_filename (temp_filename),
    INDEX idx_expires_at (expires_at),
    INDEX idx_analysis_status (analysis_status),
    INDEX idx_upload_time (upload_time)
);

-- SQLite Version (for development)
-- Note: SQLite doesn't support ENUM, JSON, or some advanced features
-- This version uses TEXT and simplified structure for compatibility

-- CREATE TABLE IF NOT EXISTS temporary_uploads (
--     id INTEGER PRIMARY KEY AUTOINCREMENT,
--     temp_filename TEXT NOT NULL UNIQUE,
--     original_filename TEXT NOT NULL,
--     temp_path TEXT NOT NULL,
--     file_size INTEGER NOT NULL,
--     mime_type TEXT NOT NULL,
--     upload_time DATETIME DEFAULT CURRENT_TIMESTAMP,
--     expires_at DATETIME NOT NULL,
--     analysis_status TEXT DEFAULT 'pending' CHECK(analysis_status IN ('pending', 'processing', 'completed', 'failed')),
--     analysis_result TEXT, -- JSON stored as TEXT
--     contact_info TEXT,
--     additional_notes TEXT,
--     ip_address TEXT,
--     user_agent TEXT,
--     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
--     updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
-- );

-- CREATE INDEX IF NOT EXISTS idx_temp_filename ON temporary_uploads (temp_filename);
-- CREATE INDEX IF NOT EXISTS idx_expires_at ON temporary_uploads (expires_at);
-- CREATE INDEX IF NOT EXISTS idx_analysis_status ON temporary_uploads (analysis_status);
-- CREATE INDEX IF NOT EXISTS idx_upload_time ON temporary_uploads (upload_time);