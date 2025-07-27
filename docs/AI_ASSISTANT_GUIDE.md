# PostrMagic AI Assistant Guide
*Comprehensive development guide for AI assistants working on PostrMagic*

**Last Updated:** January 27, 2025  
**System Version:** Current Production State  
**Target Audience:** AI Assistants (Cascade), Automated Agents, Human Developers

---

## 1. Project Overview & Current State

### Project Essence
- **Goal**: Transform uploaded event posters into social media content using AI
- **Current Phase**: Advanced system with full LLM integration, media management, and user profiles
- **Architecture**: Modular PHP system with dual-environment support (SQLite/MySQL)

### Key Features (Implemented)
- Multi-provider LLM system (OpenAI, Anthropic, Gemini)
- Advanced media management with optimization
- User profile system with avatars
- Session-based authentication
- Admin dashboard with analytics
- Cost tracking and usage monitoring
- AI-powered poster analysis
- Automated content generation

---

## 2. System Architecture

### Core Components
```
PostrMagic System Architecture:
├── config/config.php              # Centralized configuration
├── includes/                      # 23 specialized modules
│   ├── DatabaseManager.php        # Database abstraction layer
│   ├── llm-manager.php            # Multi-provider AI system
│   ├── media-manager.php          # Media CRUD operations
│   ├── auth.php                   # Session authentication
│   ├── vision-processor.php       # AI poster analysis
│   ├── content-generator.php      # AI content generation
│   ├── upload-manager.php         # File upload handling
│   ├── image-processor.php        # Image optimization
│   ├── tag-manager.php            # Media tagging
│   └── [14 other specialized modules]
├── admin/                         # 15 admin pages
├── api/                          # API endpoints
├── migrations/                   # Database migrations
├── uploads/                      # User-isolated storage
└── data/                         # SQLite database (local)
```

### Database Architecture
**Dual Environment Support:**
- **Local Development**: SQLite (`data/postrmagic.db`)
- **Production**: MySQL (auto-detected)

**Key Tables:**
```sql
-- Authentication & Users
users (id, username, email, password, role, avatar, profile_fields...)
user_sessions (id, user_id, session_token, ip_address, expires_at...)

-- Media Management
user_media (id, user_id, filename, file_path, file_size, mime_type...)
media_tags (id, user_id, tag_name, usage_count)
media_tag_relations (media_id, tag_id)

-- LLM Integration
llm_providers (id, name, api_endpoint, enabled, priority_order)
llm_configurations (id, provider_id, content_type, model_name...)
llm_usage_logs (id, provider_id, user_id, tokens_used, cost_estimate...)
llm_cost_tracking (id, provider_id, user_id, date_period, total_cost...)

-- Event Management
events (id, user_id, title, description, event_date, category...)
temporary_events (id, poster_path, analysis_data, expires_at...)
```

---

## 3. Code Generation Standards

### PHP Patterns (MANDATORY)
**Always use these patterns when generating PHP code:**

```php
<?php
declare(strict_types=1);

// Standard file header
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';

// Authentication check (for pages)
requireLogin();

// Database operations - ALWAYS use prepared statements
function getUserData(int $userId): array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

// Error handling pattern
try {
    $result = performOperation();
    return ['success' => true, 'data' => $result];
} catch (Exception $e) {
    error_log("Operation failed: " . $e->getMessage());
    return ['success' => false, 'error' => 'Operation failed'];
}

// CSRF protection (all forms)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token mismatch');
    }
}
```

### Database Compatibility Rules
**CRITICAL: All database code must work in both SQLite and MySQL:**

```php
// Dual-environment SQL patterns
if (DB_TYPE === 'sqlite') {
    $stmt = $pdo->prepare("UPDATE users SET updated_at = datetime('now') WHERE id = ?");
} else {
    $stmt = $pdo->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
}

// Migration pattern
CREATE TABLE IF NOT EXISTS new_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT, -- SQLite
    -- id INT AUTO_INCREMENT PRIMARY KEY, -- MySQL (commented)
    user_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### User Isolation (MANDATORY)
**ALL user data operations must include user isolation:**

```php
// ALWAYS filter by user_id for user data
$stmt = $pdo->prepare("SELECT * FROM user_media WHERE user_id = ? AND id = ?");
$stmt->execute([$_SESSION['user_id'], $mediaId]);

// File paths must be user-isolated
$userUploadPath = UPLOAD_DIR . "media/{$userId}/";
if (!is_dir($userUploadPath)) {
    mkdir($userUploadPath, 0755, true);
}
```

---

## 4. Include File Usage Patterns

### Core System Managers
```php
// Database operations
$pdo = getDBConnection(); // Direct PDO connection
$dbManager = DatabaseManager::getInstance(); // Advanced operations

// LLM operations
$llmManager = getLLMManager();
$result = $llmManager->makeAPICall('social_media', $messages, $options);

// Media operations
$mediaManager = getMediaManager();
$uploadResult = $mediaManager->uploadMedia($file, $userId, 'media', $tags);

// Vision processing
$visionProcessor = getVisionProcessor();
$analysisResult = $visionProcessor->processPoster($imagePath, $options);

// Authentication
requireLogin(); // Redirect if not logged in
requireRole('admin'); // Admin-only access
$user = getCurrentUser(); // Get current user data
```

### File Upload Pattern
```php
require_once __DIR__ . '/includes/upload-manager.php';
require_once __DIR__ . '/includes/file-validator.php';
require_once __DIR__ . '/includes/image-processor.php';

// Complete upload workflow
$uploadManager = new UploadManager();
$fileValidator = new FileValidator();
$imageProcessor = new ImageProcessor();

// 1. Validate
$validation = $fileValidator->validateFile($tmpPath, 'media', $filename);
if (!$validation['valid']) {
    return ['error' => $validation['error']];
}

// 2. Move to user directory
$moveResult = $uploadManager->moveUploadedFile($tmpPath, $filename, 'media', $userId);

// 3. Process if image
if ($validation['isImage']) {
    $optimization = $imageProcessor->optimizeImage($path, $optimizedPath, $options);
}
```

---

## 5. LLM Integration Patterns

### Making AI Calls
```php
// Get LLM manager
$llmManager = getLLMManager();

// Vision analysis
$visionResult = $llmManager->makeAPICall('vision_analysis', [
    ['role' => 'user', 'content' => 'Analyze this poster image'],
    ['role' => 'user', 'content' => ['type' => 'image_url', 'image_url' => $imageUrl]]
], [
    'user_id' => $userId,
    'event_category' => 'music'
]);

// Content generation
$contentResult = $llmManager->makeAPICall('social_media', [
    ['role' => 'system', 'content' => 'Generate social media content'],
    ['role' => 'user', 'content' => json_encode($eventData)]
], [
    'user_id' => $userId,
    'platform' => 'instagram'
]);

// ALWAYS log usage (automatic in LLMManager)
// Cost tracking is handled automatically
```

### Prompt Management
```php
$promptManager = getLLMPromptManager();

// Get configured prompt
$prompt = $promptManager->getPrompt('vision_analysis');
$processedPrompt = $promptManager->processPrompt($prompt, $placeholderData);

// Available prompt types:
// - vision_analysis
// - category_detection
// - social_media_instagram
// - social_media_facebook
// - social_media_twitter
```

---

## 6. UI/Theme Patterns

### Page Structure
```html
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title - PostrMagic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="theme-aware-bg theme-aware-text">
    <?php require_once __DIR__ . '/includes/dashboard-header.php'; ?>
    
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
        <?php require_once __DIR__ . '/includes/sidebar-user.php'; ?>
        
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Page content -->
        </main>
    </div>
    
    <?php require_once __DIR__ . '/includes/dashboard-footer.php'; ?>
</body>
</html>
```

### Theme-Aware Styling
```css
/* Use these CSS variables for consistent theming */
.component {
    background: var(--bg-primary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow);
}

/* Status indicators */
.status-active { color: #10B981; }
.status-pending { color: #F59E0B; }
.status-error { color: #EF4444; }
```

---

## 7. Security Implementation

### Authentication Patterns
```php
// Page-level security
require_once __DIR__ . '/includes/auth.php';
requireLogin();

// Role-based access
requireRole('admin'); // For admin pages

// API endpoint security
header('Content-Type: application/json');
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
        http_response_code(403);
        echo json_encode(['error' => 'CSRF token mismatch']);
        exit;
    }
}
```

### File Security
```php
// File validation
$fileValidator = new FileValidator();
$validation = $fileValidator->validateFile($tmpPath, 'media', $filename);

// User isolation
$userPath = UPLOAD_DIR . "media/{$userId}/";
$filename = basename($userInput); // Prevent path traversal

// Allowed types
if (!in_array($mimeType, ALLOWED_MEDIA_TYPES)) {
    throw new Exception('Invalid file type');
}
```

---

## 8. API Endpoint Patterns

### Standard API Structure
```php
<?php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $result = handleGet();
            break;
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            validateCSRF($input['csrf_token'] ?? '');
            $result = handlePost($input);
            break;
        default:
            throw new Exception('Method not allowed');
    }
    
    echo json_encode(['success' => true, 'data' => $result]);
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Operation failed']);
}
```

---

## 9. Testing Patterns

### System Testing
```php
// Test database connection
php test-full-system.php

// Test LLM integration
$llmManager = getLLMManager();
$result = $llmManager->makeAPICall('test', [['role' => 'user', 'content' => 'Hello']]);

// Test media upload
$mediaManager = getMediaManager();
$result = $mediaManager->uploadMedia($testFile, $userId);

// Test vision processing
$visionProcessor = getVisionProcessor();
$result = $visionProcessor->testVisionProcessing($imagePath, $userId);
```

---

## 10. Environment Configuration

### Local Development
```php
// Automatic detection in config/config.php
$is_local = (
    (isset($_SERVER['HTTP_HOST']) && (
        $_SERVER['HTTP_HOST'] === 'localhost' ||
        str_ends_with($_SERVER['HTTP_HOST'], '.localhost')
    )) ||
    (isset($_SERVER['SERVER_PORT']) && in_array($_SERVER['SERVER_PORT'], [8000, 8080]))
);

// Local settings
if ($is_local) {
    define('DB_TYPE', 'sqlite');
    define('DB_PATH', __DIR__ . '/../data/postrmagic.db');
    define('APP_DEBUG', true);
}
```

### Production Deployment
```php
// Production settings
if (!$is_local) {
    define('DB_TYPE', 'mysql');
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'postrmagic_production');
    define('APP_DEBUG', false);
}

// Security headers (automatic in config.php)
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
```

---

## 11. Migration Patterns

### Creating Migrations
```sql
-- migrations/008_new_feature.sql
-- Dual-environment migration

CREATE TABLE IF NOT EXISTS new_feature (
    id INTEGER PRIMARY KEY AUTOINCREMENT, -- SQLite
    -- id INT AUTO_INCREMENT PRIMARY KEY, -- MySQL
    user_id INTEGER NOT NULL,
    feature_data TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add index
CREATE INDEX idx_new_feature_user ON new_feature(user_id);
```

### Running Migrations
```php
// Run all pending migrations
php includes/migration-runner.php

// In code
require_once __DIR__ . '/includes/migration-runner.php';
$migrationRunner = new MigrationRunner();
$migrationRunner->runPendingMigrations();
```

---

## 12. Quick Reference Commands

### Development Commands
```bash
# Start development server
php -S localhost:8000

# Run system tests
php test-full-system.php

# Run migrations
php includes/migration-runner.php

# View database
php view_database.php

# Test specific components
php test-media-api.php
php test-image-processing.php
```

### Common Helper Functions
```php
// Database
getDBConnection()                    // Get PDO connection
DatabaseManager::getInstance()       // Get database manager

// Authentication
requireLogin()                       // Require authentication
requireRole($role)                   // Require specific role
getCurrentUser()                     // Get current user data
isLoggedIn()                        // Check login status

// Media
getMediaManager()                    // Get media manager
getUploadManager()                   // Get upload manager
getImageProcessor()                  // Get image processor

// LLM
getLLMManager()                      // Get LLM manager
getLLMPromptManager()               // Get prompt manager
getVisionProcessor()                // Get vision processor

// Utilities
formatFileSize($bytes)              // Format file size
generateCSRFToken()                 // Generate CSRF token
```

---

## 13. Critical Implementation Rules

### NEVER Do These:
- Hardcode API keys (use environment variables)
- Skip user isolation in database queries
- Ignore CSRF protection
- Mix SQLite and MySQL specific syntax
- Skip file validation
- Use direct SQL without prepared statements

### ALWAYS Do These:
- Include user_id in all user data queries
- Validate and sanitize all inputs
- Log LLM usage for cost tracking
- Use prepared statements
- Check authentication before operations
- Handle both SQLite and MySQL environments
- Include error handling and logging

---

## 14. Troubleshooting

### Common Issues
```php
// Database lock (SQLite)
$pdo->exec("PRAGMA busy_timeout = 30000");

// Memory issues with large files
ini_set('memory_limit', '256M');
ini_set('upload_max_filesize', '10M');

// LLM API failures
// Automatic failover is handled by LLMManager

// File permission issues
chmod($uploadDir, 0755);
```

### Debug Patterns
```php
if (APP_DEBUG) {
    error_log("Debug: " . print_r($data, true));
    echo "<!-- Debug: " . htmlspecialchars(json_encode($data)) . " -->";
}
```

---

## 15. Deployment Checklist (cPanel)

### Production Deployment Steps
1. Upload files under `/public_html` except `config.php`
2. Set correct file & folder permissions (644/755)
3. Create MySQL database & import schema
4. Configure environment variables or update config
5. Add cron jobs for cleanup & maintenance
6. Set up webhooks for external services (Stripe, etc.)
7. Test all critical functionality

### Post-Deployment Verification
```bash
# Test database connection
php test-db-connection.php

# Verify file permissions
ls -la uploads/

# Test LLM integration
php test-llm-integration.php
```

---

## 16. Project Glossary

### Business Terms
- **Unclaimed Event**: Newly uploaded poster, organizer not yet verified
- **Content Package**: Paid subscription plan (1-, 2-, 3-week post schedule)
- **Stakeholder**: Venue, sponsor, media partner related to events
- **Event Category**: Classification system for different event types

### Technical Terms
- **Vision Processing**: AI-powered poster analysis pipeline
- **LLM Provider**: AI service (OpenAI, Anthropic, Gemini)
- **User Isolation**: Security pattern ensuring data separation
- **Dual Environment**: SQLite (local) + MySQL (production) support
- **Cost Tracking**: AI usage monitoring and billing system

---

*This guide reflects the actual current state of PostrMagic as of January 2025. Always refer to the actual codebase for the most up-to-date implementation details.*
