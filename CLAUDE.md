# PostrMagic Project Guide

## Project Overview
PostrMagic is a PHP 8.1+ web application that transforms event posters into social media content using AI. It features anonymous poster uploads, AI-powered event data extraction, user event claiming, and multi-provider LLM integration with comprehensive cost tracking.

## Tech Stack
- **Backend**: PHP 8.1+ with strict typing
- **Database**: SQLite (local) / MySQL (production) with dual compatibility
- **Frontend**: HTML/CSS with Tailwind CSS, Lucide icons, vanilla JavaScript
- **AI Integration**: Multi-provider LLM system (OpenAI, Anthropic Claude, Google Gemini)
- **Dependencies**: Composer with minimal external packages

## Core Architecture
- **Traditional PHP** with modern patterns (Factory, Strategy, Service Layer)
- **Database abstraction** with auto-environment detection
- **User-isolated file storage** with security boundaries
- **Multi-provider AI system** with automatic fallback
- **Event lifecycle management** from anonymous upload to user claiming

## Key Directories
- `admin/` - Admin-only pages (user management, LLM settings, analytics)
- `api/` - AJAX endpoints (media operations, profile updates)
- `assets/` - Static files (CSS, JS, UI components)
- `config/` - Configuration files
- `includes/` - Core system components and utilities
- `layouts/` - Page templates
- `migrations/` - Database schema files (dual MySQL/SQLite)
- `uploads/` - User-isolated file storage by type and user ID
- `data/` - SQLite database (local development only)

## Database Schema
**Core Tables:**
- `users` - User accounts with roles (`user`, `admin`)
- `user_sessions` - Session management with token tracking
- `user_media` - Media library with tagging and optimization metadata
- `events` - Event management with AI analysis data
- `temporary_events` - Anonymous uploads (48-hour expiry)
- `event_claims` - Event claiming system with verification
- `llm_providers` - AI provider configurations
- `llm_configurations` - Provider settings per content type
- `llm_usage_logs` - Cost tracking and analytics

## User System
**Roles:**
- `user` - Create events, manage media, claim events
- `admin` - Full system access, LLM settings, user management

**Authentication:**
- Password hashing with `password_hash()`
- Database-stored session tokens
- Remember me functionality
- IP and user agent tracking
- Profile system with avatars and contact info

## AI Integration
**Multi-Provider LLM System:**
- **Providers**: OpenAI GPT-4, Anthropic Claude, Google Gemini
- **Automatic Fallback**: Provider switching on failures
- **Cost Tracking**: Token usage and cost estimation
- **Content Types**: Vision analysis, category detection, social media content
- **Prompt Management**: Versioned prompts with placeholder replacement

**AI Processing Pipeline:**
1. **Vision Analysis** - Extract event data from poster images
2. **Category Detection** - Classify into predefined categories
3. **Content Generation** - Create platform-specific posts
4. **Rejection Handling** - Store unclassifiable events for admin review

## File Upload System
**Structure:**
```
/uploads/
├── media/{user_id}/     # User media files
├── posters/{user_id}/   # Event posters  
├── avatars/             # User avatars
├── thumbnails/          # Generated thumbnails
├── webp/               # WebP optimized versions
├── temp/               # Temporary uploads (48hr cleanup)
└── optimized/          # Optimized originals
```

**Features:**
- **User Isolation**: Files organized by user ID
- **Automatic Processing**: Image optimization and WebP conversion
- **Thumbnail Generation**: 300px width thumbnails
- **File Types**: JPEG, PNG, WebP (NO GIF), PDF for posters
- **Size Limit**: 10MB default
- **Security**: MIME validation, file type checking

## Event Management
**Event Lifecycle:**
1. **Anonymous Upload** - Anyone can upload poster
2. **AI Processing** - Automatic data extraction
3. **Temporary Storage** - 48-hour claiming window
4. **Event Claiming** - Email/SMS verification
5. **User Association** - Link to user account
6. **Publishing** - Public searchable events

**Event Categories:**
- Concert/Music Event, Festival, Party/Club Event
- Sale/Promotion, Business Event, Sports Event, Community Event

## Development Commands
**Development Server:**
```bash
php -S localhost:8000
```

**Database Setup:**
```bash
php setup_database.php
```

**Run Migrations:**
```bash
php includes/migration-runner.php
```

**Testing:**
```bash
# System tests (no formal framework)
php test-full-system.php
php test-migrations.php
php test-image-processing.php
```

## Environment Detection
**Local Development:**
- Auto-detected via hostname (`localhost`, `127.0.0.1`)
- SQLite database at `data/postrmagic.db`
- Debug mode enabled

**Production:**
- MySQL with cPanel credentials
- Error reporting disabled
- Security headers enabled

## Security Features  
- CSRF token protection
- SQL injection prevention (PDO prepared statements)
- XSS protection headers
- File upload validation and MIME checking
- User-isolated file storage
- Session hijacking protection
- Rate limiting for uploads

## Common Tasks
**Add New Event Category:**
- Update category arrays in `includes/llm-manager.php`

**Modify AI Prompts:**
- Edit prompt templates in `includes/llm-prompt-manager.php`

**Add New User Role:**
- Modify role checks in `includes/auth.php`

**Configure New LLM Provider:**
- Add provider config in admin LLM settings
- Update `includes/llm-manager.php` if needed

**Modify Upload Types:**
- Update `ALLOWED_MEDIA_TYPES` in `config/config.php`

## Key Files
- `config/config.php` - Main configuration and database connection
- `includes/auth.php` - Authentication and session management
- `includes/media-manager.php` - File upload and media processing
- `includes/llm-manager.php` - AI integration and multi-provider handling
- `includes/migration-runner.php` - Database schema management
- `dashboard.php` - Main user interface
- `admin-dashboard.php` - Admin control panel

## Production Deployment
1. Update database credentials in `config/config.php`
2. Set `APP_ENV='production'` and `APP_DEBUG=false`
3. Configure LLM provider API keys in admin panel
4. Set up file permissions for uploads directory
5. Configure cleanup cron jobs for temporary files
6. Enable security headers and HTTPS

## Notes
- No formal testing framework (test files are manual)
- No build process - pure PHP application
- Composer dev dependencies available but not actively used
- Database migrations support both MySQL and SQLite in same files
- AI cost tracking provides detailed analytics per provider