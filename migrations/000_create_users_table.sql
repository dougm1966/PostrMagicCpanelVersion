-- Migration 000: Create Users Table
-- This must run first as other tables depend on the users table

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    profile_image_path VARCHAR(500) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    email_verified_at TIMESTAMP NULL,
    verification_token VARCHAR(100) NULL,
    reset_token VARCHAR(100) NULL,
    reset_token_expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
);

-- Insert default admin user (password: admin123 - change this in production!)
-- The password will be hashed using password_hash() in PHP
-- Default password hash for 'admin123' is provided as an example
INSERT INTO users (
    username, 
    email, 
    password_hash, 
    first_name, 
    last_name, 
    role, 
    is_active,
    email_verified_at
) VALUES (
    'admin', 
    'admin@postrmagic.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'Admin', 
    'User', 
    'admin', 
    TRUE,
    NOW()
) 
ON DUPLICATE KEY UPDATE 
    email = VALUES(email),
    first_name = VALUES(first_name),
    last_name = VALUES(last_name),
    is_active = VALUES(is_active),
    updated_at = NOW();
