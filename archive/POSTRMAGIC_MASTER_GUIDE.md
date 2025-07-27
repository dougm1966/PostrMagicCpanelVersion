##  accurate handoff summary so we can make the updatesPostrMagic - Master Development Guide

This document is the single-source-of-truth for PostrMagic's development. It is for the AI assistant (Cascade) and any automated agent or human developer contributing to the codebase. It distills the *why*, *what*, and *how* so that all contributions remain aligned with product, technical, and quality standards.

---

## 1. Project Essence
* **Goal**: Turn uploaded event posters into ready-to-publish social-media content and deliver it to event organisers through a freemium → subscription upsell.
* **Current Phase**: UI first (static & interactive screens), DB & backend logic after UI is complete.

---

## 2. Definitive Tech Stack
| Concern | Mandatory Choice | Notes |
|---|---|---|
| Front-end | HTML5 / CSS3 / Vanilla JS / TailwindCSS | No frameworks; mobile-first styling with TailwindCSS utility classes. |
| Back-end | PHP 8.1+ | Use built-in server for local dev: `php -S localhost:8000` |
| Database | MySQL (production), SQLite (local) | Dual-environment support is required. |
| AI | OpenAI, Claude, Gemini | Multi-provider system with cost tracking. |
| Hosting | cPanel shared hosting | Keep writable paths inside `assets/uploads`. |

---

## 3. File/Directory Blueprint
/public_html ├── index.php # Landing page (hero, CTA) ├── dashboard.php # User dashboard layout with sidebar ├── admin/ # Admin-only pages and logic │ ├── dashboard.php # Admin dashboard │ └── user-management.php # Admin user management ├── includes/ # Reusable PHP fragments │ ├── dashboard-header.php # Logged-in user header │ ├── sidebar-user.php # Navigation for regular users │ └── sidebar-admin.php # Navigation for admin users ├── assets/ │ ├── css/style.css # Core styles │ ├── js/main.js # Core JavaScript │ └── uploads/ # User-specific media uploads ├── api/ # Stateless AJAX endpoints ├── migrations/ # Database migration scripts └── docs/ # Documentation (this file, etc.)

*Keep all secrets in a non-web-root [config.php](cci:7://file:///c:/xampp/htdocs/postrmagic/config/config.php:0:0-0:0).*

---

## 4. Build Sequence
1. **UI Foundation** *(current)*
   * Dashboard layout with user/admin sidebars.
   * Poster upload page with drag-&-drop preview.
   * Event detail page with tabs for content, analytics, and settings.
2. **AI Integration**
   * `api/analyze-poster.php` — send image, parse JSON.
   * `api/generate-content.php` — feed extracted data to LLM.
3. **Authentication & Database**
   * User login, registration, and session management.
   * Database schema implementation and migrations.
4. **Stripe Monetisation**
   * Subscription packages & webhook listener.

---

## 5. Code Generation Standards

### PHP Code Patterns
**ALWAYS use these patterns when generating PHP code:**
```php
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
Database Interaction Patterns
ALWAYS use these patterns for database operations:

php
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
Database Schema Reference
Use these table structures and relationships:

sql
-- Users and Authentication
users (id, username, email, password_hash, role, created_at)
user_sessions (id, user_id, token, ip_address, expires_at)

-- Event Management
events (id, user_id, title, description, event_date, location, category, status, ai_analysis_data)

-- Media Management
user_media (id, user_id, filename, original_name, file_type, file_size, tags, created_at)

-- AI Integration
llm_providers (id, name, api_endpoint, enabled, priority_order)
llm_configurations (id, provider_id, content_type, model_name, settings_json)
llm_usage_logs (id, provider_id, user_id, tokens_used, cost_estimate, operation_type)
Authentication & Security Patterns
ALWAYS implement these security measures:

php
// Page-level authentication check
require_once __DIR__ . '/includes/auth.php';
requireLogin(); // Redirects if not logged in

// Role-based access control
requireRole('admin'); // For admin-only pages

// CSRF token generation (for forms)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// File upload security
function validateUpload($file, $allowedTypes = ['image/jpeg', 'image/png']) { /* ... */ }

// User isolation for file paths
$userUploadPath = UPLOAD_DIR . "media/{$_SESSION['user_id']}/";
API Endpoint Patterns
Use this structure for all API endpoints:

php
<?php
// api/example-endpoint.php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

// CSRF check for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') { /* ... */ }

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
    http_response_code(500);
    echo json_encode(['error' => 'Operation failed']);
}
Critical Implementation Rules
Database Compatibility: ALWAYS write migrations that work in both SQLite and MySQL.
File Storage: ALWAYS isolate user files into user-specific directories and validate all uploads.
API Keys: NEVER hardcode API keys. Use environment variables or a non-committed config file.
Cost Tracking: ALWAYS log AI usage after an operation to track costs.