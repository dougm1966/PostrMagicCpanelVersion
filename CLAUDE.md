# PostrMagic AI Development Guide

## Project Overview
PostrMagic transforms event posters into social media content using AI. Multi-provider LLM system (OpenAI, Claude, Gemini) with cost tracking, user authentication, media management, and event lifecycle management.

**AI Assistant Instructions**: Use this guide to generate consistent, secure, and well-structured code following established patterns.

## Quick Commands
```bash
php -S localhost:8000                    # Development server
php includes/migration-runner.php        # Run migrations  
php test-full-system.php                # Test system
php view_database.php                   # Inspect database
```

## Code Generation Standards

### PHP Code Patterns
**ALWAYS use these patterns when generating PHP code:**

```php
<?php
declare(strict_types=1);

// File header for includes
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

// Database queries - ALWAYS use prepared statements
function getUserEvents(int $userId): array {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Error handling pattern
try {
    $result = performOperation();
    return ['success' => true, 'data' => $result];
} catch (Exception $e) {
    error_log("Operation failed: " . $e->getMessage());
    return ['success' => false, 'error' => 'Operation failed'];
}

// CSRF protection - include in all forms
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token mismatch');
}
```

### File Structure Rules
**When creating new files, follow these placement rules:**

- **Pages**: Root directory (`dashboard.php`, `user-profile.php`)
- **API Endpoints**: `api/` directory (`api/upload-media.php`)
- **Admin Pages**: `admin/` directory (`admin/user-management.php`)
- **Shared Components**: `includes/` directory (`includes/media-helper.php`)
- **Migrations**: `migrations/` directory (`migrations/006_new_feature.sql`)
- **Tests**: Root with `test-` prefix (`test-new-feature.php`)

### Database Interaction Patterns
**ALWAYS use these patterns for database operations:**

```php
// Connection (already available globally)
$db = getDBConnection();

// Insert with user isolation
$stmt = $db->prepare("INSERT INTO user_media (user_id, filename, file_type) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $filename, $fileType]);

// Dual environment SQL (migrations)
CREATE TABLE IF NOT EXISTS new_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT, -- SQLite
    -- id INT AUTO_INCREMENT PRIMARY KEY, -- MySQL (commented)
    user_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

// User isolation - ALWAYS filter by user_id for user data
$stmt = $db->prepare("SELECT * FROM events WHERE user_id = ? AND id = ?");
$stmt->execute([$_SESSION['user_id'], $eventId]);
```

## UI/Theme Code Patterns
**ALWAYS use these patterns for consistent UI generation:**

```html
<!-- Page structure with theme support -->
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
```

```css
/* Theme-aware component styling - USE THESE VARIABLES */
.component {
    background: var(--bg-primary);           /* Primary background */
    color: var(--text-primary);             /* Primary text */
    border: 1px solid var(--border-color);  /* Border color */
    box-shadow: var(--shadow);              /* Theme-aware shadow */
}

/* Glass effect components */
.glass-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
}

/* Status indicators */
.status-active { color: #10B981; }
.status-pending { color: #F59E0B; }
.status-error { color: #EF4444; }
```

```javascript
// Theme toggle functionality (already available globally)
window.PostrMagicTheme.setTheme('dark'|'light');
window.PostrMagicTheme.toggleTheme();
window.PostrMagicTheme.getCurrentTheme();
```

## Authentication & Security Patterns
**ALWAYS implement these security measures:**

```php
// Page-level authentication check
require_once __DIR__ . '/includes/auth.php';
requireLogin(); // Redirects if not logged in

// Role-based access control
requireRole('admin'); // For admin-only pages
if (!hasRole('admin')) {
    die('Access denied');
}

// CSRF token generation (for forms)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// File upload security
function validateUpload($file, $allowedTypes = ['image/jpeg', 'image/png']) {
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type');
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new Exception('File too large');
    }
    return true;
}

// User isolation for file paths
$userUploadPath = UPLOAD_DIR . "media/{$_SESSION['user_id']}/";
if (!is_dir($userUploadPath)) {
    mkdir($userUploadPath, 0755, true);
}
```

## API Endpoint Patterns
**Use this structure for all API endpoints:**

```php
<?php
// api/example-endpoint.php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';

// Authenticate request
requireLogin();

// CSRF check for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
        http_response_code(403);
        echo json_encode(['error' => 'CSRF token mismatch']);
        exit;
    }
}

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $result = handleGet();
            break;
        case 'POST':
            $result = handlePost($input);
            break;
        default:
            throw new Exception('Method not allowed');
    }
    
    echo json_encode(['success' => true, 'data' => $result]);
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Operation failed']);
}
```

## Database Schema Reference
**Use these table structures and relationships:**

```sql
-- Users and Authentication
users (id, username, email, password_hash, role, created_at)
user_sessions (id, user_id, token, ip_address, expires_at)

-- Event Management  
events (id, user_id, title, description, event_date, location, category, status, ai_analysis_data)
temporary_events (id, poster_path, analysis_data, expires_at, claim_token)
event_claims (id, temporary_event_id, contact_info, verification_status)

-- Media Management
user_media (id, user_id, filename, original_name, file_type, file_size, tags, created_at)

-- AI Integration
llm_providers (id, name, api_endpoint, enabled, priority_order)
llm_configurations (id, provider_id, content_type, model_name, settings_json)
llm_usage_logs (id, provider_id, user_id, tokens_used, cost_estimate, operation_type)
```

## Common Implementation Tasks
**Follow these patterns for typical development tasks:**

### Adding a New User Dashboard Page
1. Create file in root: `new-feature.php`
2. Use dashboard layout pattern:

```php
<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = "New Feature";
include __DIR__ . '/includes/dashboard-header.php';
?>

<div class="flex h-screen bg-gray-50">
    <?php include __DIR__ . '/includes/sidebar-user.php'; ?>
    
    <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-2xl font-bold mb-6 theme-aware-text"><?= htmlspecialchars($pageTitle) ?></h1>
            
            <!-- Content here -->
        </div>
    </main>
</div>

<?php include __DIR__ . '/includes/dashboard-footer.php'; ?>
```

### Adding Database Migration
1. Create: `migrations/007_description.sql`
2. Include both SQLite and MySQL syntax
3. Run: `php includes/migration-runner.php`

### File Upload Implementation
```php
// Handle file upload with user isolation
function handleFileUpload($file, $userId, $uploadType = 'media') {
    validateUpload($file);
    
    $userDir = UPLOAD_DIR . "{$uploadType}/{$userId}/";
    if (!is_dir($userDir)) {
        mkdir($userDir, 0755, true);
    }
    
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $userDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Upload failed');
    }
    
    // Save to database
    $db = getDBConnection();
    $stmt = $db->prepare("INSERT INTO user_media (user_id, filename, original_name, file_type, file_size) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $filename, $file['name'], $file['type'], $file['size']]);
    
    return $filename;
}
```

### AI Integration Pattern
```php
// Use existing AI manager
require_once __DIR__ . '/includes/llm-manager.php';

$llmManager = getLLMManager();
$result = $llmManager->generateContent('social_media', [
    'event_title' => $eventTitle,
    'event_description' => $description,
    'platform' => 'instagram'
]);

if ($result['success']) {
    $generatedContent = $result['content'];
    // Log usage for cost tracking
    logLLMUsage($result['provider'], $result['tokens_used'], $result['cost_estimate']);
}
```

## Form Patterns
**Use these patterns for all forms:**

```html
<!-- Standard form with CSRF and theme support -->
<form method="POST" class="space-y-4" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <div class="form-group">
        <label class="block text-sm font-medium theme-aware-text mb-2">Field Label</label>
        <input type="text" name="field_name" 
               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 theme-aware-bg theme-aware-border"
               required>
    </div>
    
    <button type="submit" 
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
        Submit
    </button>
</form>
```

## Available Helper Functions
**Use these existing functions (already loaded):**

```php
// Authentication
requireLogin()                           // Redirect if not logged in
requireRole('admin')                     // Require specific role
hasRole('admin')                         // Check role (boolean)
getCurrentUser()                         // Get current user data

// Database
getDBConnection()                        // Get PDO connection

// File handling
validateUpload($file, $allowedTypes)     // Validate uploaded file
optimizeImage($filepath)                 // Optimize image file

// AI Integration
getLLMManager()                          // Get AI manager instance
logLLMUsage($provider, $tokens, $cost)   // Log AI usage

// Utilities
generateCSRFToken()                      // Generate CSRF token
sanitizeInput($input)                    // Sanitize user input
formatFileSize($bytes)                   // Format file size for display
```

## Error Handling Requirements
**Always implement proper error handling:**

```php
// Page-level error handling
try {
    // Operation code
    $result = performAction();
    $_SESSION['success_message'] = 'Operation completed successfully';
} catch (Exception $e) {
    error_log("Error in " . __FILE__ . ": " . $e->getMessage());
    $_SESSION['error_message'] = 'Operation failed. Please try again.';
}

// API error responses
catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Operation failed', 'details' => APP_DEBUG ? $e->getMessage() : null]);
}
```

## Testing & Validation Patterns
**When implementing features, verify functionality:**

```php
// Test file pattern for new features
// test-{feature-name}.php
<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';

echo "Testing {Feature Name}...\n";

try {
    // Test implementation
    $result = testFeature();
    echo "✓ Test passed\n";
} catch (Exception $e) {
    echo "✗ Test failed: " . $e->getMessage() . "\n";
}
```

### Environment Detection in Code
```php
// Use these patterns for environment-specific logic
if (IS_LOCAL) {
    // Local development behavior
    ini_set('display_errors', '1');
} else {
    // Production behavior
    ini_set('display_errors', '0');
}

// Database environment detection (automatic)
$dbType = (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false) ? 'sqlite' : 'mysql';
```

## AI Processing Implementation Patterns
**Implement multi-provider AI features using these patterns:**

```php
// Vision analysis pattern
function analyzeEventPoster($imagePath, $userId) {
    $llmManager = getLLMManager();
    
    // Use vision endpoint with automatic provider selection
    $result = $llmManager->analyzeImage('vision', [
        'image_path' => $imagePath,
        'analysis_type' => 'event_extraction'
    ]);
    
    if ($result['success']) {
        // Store analysis results
        $db = getDBConnection();
        $stmt = $db->prepare("
            INSERT INTO events (user_id, title, description, event_date, location, category, ai_analysis_data) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $result['data']['title'],
            $result['data']['description'],
            $result['data']['event_date'],
            $result['data']['location'],
            $result['data']['category'],
            json_encode($result['data'])
        ]);
        
        // Log AI usage for cost tracking
        logLLMUsage($result['provider'], $result['tokens_used'], $result['cost_estimate']);
    }
    
    return $result;
}

// Content generation with fallback
function generateSocialContent($eventId, $platform) {
    $llmManager = getLLMManager();
    
    // Automatic provider failover handled internally
    return $llmManager->generateContent('social_media', [
        'event_id' => $eventId,
        'platform' => $platform,
        'tone' => 'engaging',
        'hashtags' => true
    ]);
}
```

### Security Implementation Checklist
**ALWAYS include these security measures in every feature:**

```php
// Page security template
require_once __DIR__ . '/includes/auth-helper.php';
requireLogin();

// Form security
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// File operations security
$userPath = UPLOAD_DIR . "media/{$_SESSION['user_id']}/";
if (!is_dir($userPath)) {
    mkdir($userPath, 0755, true);
}

// Database operations security
$stmt = $db->prepare("SELECT * FROM table WHERE user_id = ? AND id = ?");
$stmt->execute([$_SESSION['user_id'], $id]);

// Output security
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// Session security
session_regenerate_id(true); // On login/privilege change
```

## Implementation Patterns for Common Extensions

### Adding New Event Category
```php
// In includes/llm-manager.php, add to CATEGORIES array
private const CATEGORIES = [
    'music' => 'Music & Concerts',
    'sports' => 'Sports & Fitness',
    'academic' => 'Academic & Educational',
    'social' => 'Social & Networking',
    'your_new_category' => 'Display Name' // Add here
];

// Update category detection prompt
private function getCategoryPrompt() {
    return "Categorize into: " . implode(', ', array_keys(self::CATEGORIES));
}
```

### Adding New User Role
```php
// In includes/auth-helper.php
function hasRole($requiredRole) {
    $roleHierarchy = [
        'user' => 1,
        'moderator' => 2,  // New role
        'admin' => 3
    ];
    
    $userRole = $_SESSION['user_role'] ?? 'user';
    return ($roleHierarchy[$userRole] ?? 0) >= ($roleHierarchy[$requiredRole] ?? 0);
}
```

### Adding New File Type Support
```php
// In config/config.php
define('ALLOWED_MEDIA_TYPES', [
    'image/jpeg' => '.jpg',
    'image/png' => '.png',
    'image/gif' => '.gif',
    'image/svg+xml' => '.svg',
    'application/pdf' => '.pdf',
    'video/mp4' => '.mp4'  // New type
]);

// Update upload handler
if (isset(ALLOWED_MEDIA_TYPES[$file['type']])) {
    // Process based on type
    if (str_starts_with($file['type'], 'video/')) {
        // Video-specific processing
    }
}
```

## Critical File Patterns & Modifications

### Configuration Updates
```php
// When modifying config/config.php
define('NEW_CONSTANT', 'value'); // Add new constants at end of file

// Environment-specific config
if (IS_LOCAL) {
    define('NEW_FEATURE_ENABLED', true);
} else {
    define('NEW_FEATURE_ENABLED', false);
}
```

### Authentication Extensions
```php
// Adding to includes/auth-helper.php
function requirePermission($permission) {
    if (!hasPermission($permission)) {
        http_response_code(403);
        die('Access denied');
    }
}
```

### LLM Manager Extensions
```php
// Adding new AI capability to includes/llm-manager.php
public function newAIFeature($type, $params) {
    // Follow existing pattern
    return $this->callProvider('endpoint', [
        'model' => $this->getModelForType($type),
        'parameters' => $params
    ]);
}
```

## Production Configuration Patterns

```php
// Production config/config.php settings
define('APP_ENV', 'production');
define('APP_DEBUG', false);
define('SECURE_COOKIES', true);

// Production database configuration
if (!IS_LOCAL) {
    define('DB_HOST', getenv('DB_HOST'));
    define('DB_NAME', getenv('DB_NAME'));
    define('DB_USER', getenv('DB_USER'));
    define('DB_PASS', getenv('DB_PASS'));
}

// Production session configuration
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');

// Cron job patterns for cleanup
// crontab entry: 0 */4 * * * php /path/to/cleanup-temporary-events.php
```

### Security Headers Implementation
```php
// Add to includes/security-headers.php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' https://cdn.tailwindcss.com https://unpkg.com; style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com; font-src \'self\' https://fonts.gstatic.com;');
```

## Migration Patterns

### Creating Database Migrations
```sql
-- migrations/007_add_new_feature.sql
-- Dual-environment migration pattern

-- Create table (works in both SQLite and MySQL)
CREATE TABLE IF NOT EXISTS new_feature (
    id INTEGER PRIMARY KEY AUTOINCREMENT, -- SQLite
    -- id INT AUTO_INCREMENT PRIMARY KEY, -- MySQL
    user_id INTEGER NOT NULL,
    feature_data TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add column (compatible syntax)
ALTER TABLE existing_table ADD COLUMN new_column VARCHAR(255);

-- Create index
CREATE INDEX idx_new_feature_user ON new_feature(user_id);
```

### Debugging Patterns
```php
// Debug output pattern (only in development)
if (APP_DEBUG) {
    error_log("Debug: " . print_r($data, true));
    echo "<!-- Debug: " . htmlspecialchars(json_encode($data)) . " -->";
}

// Exception handling with debug info
try {
    $result = performOperation();
} catch (Exception $e) {
    error_log("Error in " . __FILE__ . ": " . $e->getMessage());
    if (APP_DEBUG) {
        throw $e; // Re-throw in debug mode
    }
    // User-friendly error in production
    die('An error occurred. Please try again.');
}
```

## Critical Implementation Rules

### Database Compatibility Rules
```php
// ALWAYS write migrations that work in both environments
// Use INTEGER for IDs (works in both SQLite/MySQL)
// Use DATETIME not TIMESTAMP
// Comment MySQL-specific syntax for production deployment
```

### File Storage Rules
```php
// ALWAYS isolate user files
$userPath = UPLOAD_DIR . "media/{$_SESSION['user_id']}/";
// NEVER allow path traversal
$filename = basename($userInput); // Strip directory components
// ALWAYS validate file types
if (!in_array($mimeType, ALLOWED_MEDIA_TYPES)) {
    throw new Exception('Invalid file type');
}
```

### API Key Security
```php
// NEVER hardcode API keys
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY'));
// ALWAYS check key exists before use
if (!OPENAI_API_KEY) {
    throw new Exception('API key not configured');
}
```

### Cost Tracking Requirements
```php
// ALWAYS log AI usage after operations
logLLMUsage($provider, $tokensUsed, $estimatedCost);
// Include operation context
$db->prepare("INSERT INTO llm_usage_logs (provider_id, user_id, operation_type, tokens_used, cost_estimate) VALUES (?, ?, ?, ?, ?)");
```