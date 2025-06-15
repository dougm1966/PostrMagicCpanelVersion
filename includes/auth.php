<?php
/**
 * Authentication Functions
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Authenticate user with email/username and password
 * @param string $login Email or username
 * @param string $password Plain text password
 * @param bool $remember Whether to set remember token
 * @return array|false User data on success, false on failure
 */
function authenticateUser($login, $password, $remember = false) {
    try {
        $pdo = getDBConnection();
        
        // Check if login is email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND is_active = 1");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login (handle SQLite vs MySQL syntax)
            if (DB_TYPE === 'sqlite') {
                $stmt = $pdo->prepare("UPDATE users SET last_login = datetime('now') WHERE id = ?");
            } else {
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            }
            $stmt->execute([$user['id']]);
            
            // Set up session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            // Handle remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->execute([$token, $user['id']]);
                
                // Set cookie for 30 days
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
            }
            
            // Create session record
            createUserSession($user['id']);
            
            return $user;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create a new user session record
 * @param int $userId
 * @return string Session token
 */
function createUserSession($userId) {
    try {
        $pdo = getDBConnection();
        $sessionToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $sessionToken,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $expiresAt
        ]);
        
        $_SESSION['session_token'] = $sessionToken;
        return $sessionToken;
    } catch (PDOException $e) {
        error_log("Session creation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
        // Verify session is still valid
        try {
            $pdo = getDBConnection();
            if (DB_TYPE === 'sqlite') {
                $stmt = $pdo->prepare("SELECT * FROM user_sessions WHERE session_token = ? AND expires_at > datetime('now')");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM user_sessions WHERE session_token = ? AND expires_at > NOW()");
            }
            $stmt->execute([$_SESSION['session_token']]);
            
            if ($stmt->fetch()) {
                // Update last activity (skip if database is busy)
                try {
                    if (DB_TYPE === 'sqlite') {
                        $stmt = $pdo->prepare("UPDATE user_sessions SET last_activity = datetime('now') WHERE session_token = ?");
                    } else {
                        $stmt = $pdo->prepare("UPDATE user_sessions SET last_activity = NOW() WHERE session_token = ?");
                    }
                    $stmt->execute([$_SESSION['session_token']]);
                } catch (PDOException $updateError) {
                    // Skip update if database is busy - session is still valid
                    error_log("Session update skipped: " . $updateError->getMessage());
                }
                return true;
            }
        } catch (PDOException $e) {
            error_log("Session verification error: " . $e->getMessage());
            // If it's just a lock error, assume session is still valid for this request
            if (strpos($e->getMessage(), 'database is locked') !== false) {
                return true;
            }
        }
    }
    
    // Check remember token
    if (isset($_COOKIE['remember_token'])) {
        return authenticateWithRememberToken($_COOKIE['remember_token']);
    }
    
    return false;
}

/**
 * Authenticate using remember token
 * @param string $token
 * @return bool
 */
function authenticateWithRememberToken($token) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ? AND is_active = 1");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Set up session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            // Create new session
            createUserSession($user['id']);
            
            // Update last login
            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            return true;
        }
    } catch (PDOException $e) {
        error_log("Remember token error: " . $e->getMessage());
    }
    
    return false;
}

/**
 * Logout user
 */
function logout() {
    // Remove session from database
    if (isset($_SESSION['session_token'])) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE session_token = ?");
            $stmt->execute([$_SESSION['session_token']]);
        } catch (PDOException $e) {
            error_log("Session deletion error: " . $e->getMessage());
        }
    }
    
    // Clear remember token
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        
        // Clear from database if user is logged in
        if (isset($_SESSION['user_id'])) {
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
            } catch (PDOException $e) {
                error_log("Remember token clear error: " . $e->getMessage());
            }
        }
    }
    
    // Destroy session
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Require login - redirects to login page if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}

/**
 * Get current user data with full profile information
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, email, username, role, created_at, last_login, 
                              name, avatar, bio, location, website, twitter_handle, phone, 
                              timezone, email_notifications, marketing_emails 
                              FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Ensure we have fallback values for display
        if ($user) {
            $user['display_name'] = $user['name'] ?: $user['username'];
            $user['avatar_url'] = $user['avatar'] ? '/uploads/avatars/' . $user['avatar'] : null;
        }
        
        return $user;
    } catch (PDOException $e) {
        error_log("Get user error: " . $e->getMessage());
        return null;
    }
}

/**
 * Update user profile information
 * @param int $userId User ID
 * @param array $profileData Profile data to update
 * @return bool Success status
 */
function updateUserProfile($userId, $profileData) {
    try {
        $pdo = getDBConnection();
        
        // Build dynamic update query
        $allowedFields = ['name', 'bio', 'location', 'website', 'twitter_handle', 'phone', 
                         'timezone', 'email_notifications', 'marketing_emails'];
        $updateFields = [];
        $values = [];
        
        foreach ($profileData as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updateFields[] = "$field = ?";
                $values[] = $value;
            }
        }
        
        if (empty($updateFields)) {
            return false;
        }
        
        $values[] = $userId; // For WHERE clause
        
        if (DB_TYPE === 'sqlite') {
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . ", updated_at = datetime('now') WHERE id = ?";
        } else {
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ?";
        }
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($values);
        
        // Update session data for immediate effect
        if ($result && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
            if (isset($profileData['name'])) {
                $_SESSION['display_name'] = $profileData['name'];
            }
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("Profile update error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update user avatar
 * @param int $userId User ID
 * @param string $avatarFilename Avatar filename
 * @return bool Success status
 */
function updateUserAvatar($userId, $avatarFilename) {
    try {
        $pdo = getDBConnection();
        
        if (DB_TYPE === 'sqlite') {
            $stmt = $pdo->prepare("UPDATE users SET avatar = ?, updated_at = datetime('now') WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
        }
        
        return $stmt->execute([$avatarFilename, $userId]);
    } catch (PDOException $e) {
        error_log("Avatar update error: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate profile data
 * @param array $data Profile data to validate
 * @param int $currentUserId Current user ID (for unique checks)
 * @return array Validation errors
 */
function validateProfileData($data, $currentUserId = null) {
    $errors = [];
    
    // Name validation
    if (isset($data['name']) && strlen($data['name']) > 255) {
        $errors['name'] = 'Name must be less than 255 characters';
    }
    
    // Bio validation
    if (isset($data['bio']) && strlen($data['bio']) > 1000) {
        $errors['bio'] = 'Bio must be less than 1000 characters';
    }
    
    // Website validation
    if (isset($data['website']) && !empty($data['website'])) {
        if (!filter_var($data['website'], FILTER_VALIDATE_URL)) {
            $errors['website'] = 'Please enter a valid website URL';
        }
    }
    
    // Phone validation (basic)
    if (isset($data['phone']) && !empty($data['phone'])) {
        if (!preg_match('/^[\+]?[0-9\s\-\(\)]{10,20}$/', $data['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number';
        }
    }
    
    // Twitter handle validation
    if (isset($data['twitter_handle']) && !empty($data['twitter_handle'])) {
        $handle = str_replace('@', '', $data['twitter_handle']);
        if (!preg_match('/^[A-Za-z0-9_]{1,15}$/', $handle)) {
            $errors['twitter_handle'] = 'Twitter handle must be 1-15 characters, letters, numbers, and underscores only';
        }
        $data['twitter_handle'] = $handle; // Remove @ if present
    }
    
    return $errors;
}