# PostrMagic — AI Assistant Project Training Guide

This document is a **single-source cheat-sheet** for PostrMagic's AI assistant (Cascade) or any automated agent contributing to the codebase. It distills the *why*, *what* and *how* so that future automation remains aligned with product, tech and quality standards.

---
## 1. Project Essence
* **Goal**: Turn uploaded event posters into ready-to-publish social-media content and deliver it to event organisers through a freemium → subscription upsell.
* **Current Phase**: UI first (static & interactive screens), DB & backend logic after UI is complete.

---
## 2. Definitive Tech Stack
| Concern | Mandatory Choice | Notes |
|---------|------------------|-------|
| Front-end | HTML5 / CSS3 / Vanilla JS / TailwindCSS | No frameworks; mobile-first styling with TailwindCSS utility classes. |
| Back-end | PHP 8.1+ | Use built-in server for local dev: `php -S localhost:8000` |
| Database | MySQL (via cPanel phpMyAdmin) | **Do NOT** initialise until UI milestone passes. |
| AI | OpenAI GPT-4V for image analysis, GPT-4 for text generation | Use `callOpenAI()` wrapper in `/api/*.php`. |
| SMS | Telnyx.com REST API | Server-side via PHP SDK. |
| Email | PHP `mail()` or PHPMailer | Prefer PHPMailer for reliability. |
| Payments | Stripe API (Subscriptions) | Webhooks handled in `api/stripe-webhook.php`. |
| Hosting | cPanel shared hosting | Keep writable paths inside `assets/uploads`. |
| Scraping | Simple HTML DOM Parser | Lightweight venue research. |

---
## 3. File/Directory Blueprint
```
/public_html
├── index.php              # Landing page (hero, CTA)
├── index_v2.php           # Alternative landing page with new design
├── upload.php             # Poster upload form & preview
├── claim.php              # Event claiming screen
├── dashboard.php          # User dashboard layout with sidebar
├── event-detail.php       # Event details and management
├── media-library.php      # Media asset management
├── analytics-dashboard.php # Analytics and metrics view
├── user-profile.php       # User profile and settings
├── settings.php           # System and account settings
├── user-management.php    # Admin user management interface
├── includes/              # Reusable PHP fragments
│   ├── header.php         # Public site header
│   ├── footer.php         # Public site footer
│   ├── dashboard-header.php # Logged-in user header
│   ├── sidebar-user.php   # Navigation for regular users
│   └── sidebar-admin.php  # Navigation for admin users
├── assets/
│   ├── css/style.css      # Core styles
│   ├── js/main.js         # Core JavaScript
│   ├── js/dashboard.js    # Dashboard-specific JavaScript
│   └── uploads/           # Poster images & media library
├── api/                   # Stateless AJAX endpoints
├── cron/                  # Scheduled scripts
└── docs/                  # Documentation (this file, ADRs, etc.)
```
*Keep all secrets in a non-web-root `config.php`.*

---
## 4. Coding & Architecture Standards (TL;DR)
1. **Domain-oriented structure** over technical silos.
2. Strongly-typed PHP (`declare(strict_types=1)`).
3. No circular imports; small single-responsibility files.
4. Mobile-first CSS, centralised theme variables (`:root`).
5. Use CSS `clamp()` for responsive typography instead of breakpoints.
6. Prepared statements for all SQL; never trust user input.
7. Document every public function with **why**-focused comments.
8. Use CSRF tokens for all forms and API endpoints.

(Full standards live in `MEMORY[user_global]` and must be mirrored.)

---
## 5. Build Sequence
1. **UI Foundation**   *(current)*
   * Header/footer, hero, nav toggle.
   * Dashboard layout with user/admin sidebars.
   * Poster upload page with drag-&-drop preview.
   * Event detail page with tabs for content, analytics, and settings.
   * Media library interface.
   * Analytics dashboard with visualizations.
   * User profile and settings pages.
   * Admin user management interface.
2. **AI Integration**  
   * `api/analyze-poster.php` — send base64 image → GPT-4V, parse JSON.
   * `api/generate-content.php` — feed extracted JSON → GPT-4.
3. **Notification Layer**
   * `api/send-notifications.php` (Twilio, PHPMailer).
4. **Claim & Auth** (simple email/SMS verification).
5. **Stripe Monetisation** — packages & webhook listener.
6. **Media Library & Stakeholder Research**.
7. **Polish, testing, docs, deployment**.

Automations must respect this order unless the product owner explicitly reprioritises.

---
## 6. Key Helper Functions
* `callOpenAI($prompt, $imageData = null)` — central OpenAI invoker.
* `sendEmail($to, $eventData, $uploader)` — wrapper around Sendmail.
* `sendSMS($phone, $eventData, $uploader)` — Telnyx.com helper.

Place shared helpers in `includes/functions.php` with namespaced functions or a static `Utils` class.

---
## 7. Security & Compliance Quick-check
* Validate & sanitise *every* file upload (`mime`, `size`, `extension`).
* Store API keys outside web root; reference via ENV or `config.php` (non-committed).
* Implement CSRF tokens for forms, rate-limit uploads.
* Apply HTTPS redirects (`.htaccess`).

---
## 8. Testing & Quality Gates
* Use PHPUnit for backend logic tests (e.g., JSON parsers).
* Cypress or Playwright for crucial UI flows (upload → preview).
* Pre-commit: PHPCS, Prettier, ESLint (JS only).

---
## 8.5. Local Development Server Setup
For previewing PHP pages locally:
```bash
# Start PHP built-in server (requires PHP installed)
php -S localhost:8000

# Alternative: Python HTTP server for static files only
python3 -m http.server 8000

# Access pages at:
# http://localhost:8000/index.php
# http://localhost:8000/index_v2.php
# http://localhost:8000/index_v2%20copy.php (for files with spaces)
```

---
## 9. Deployment Checklist (cPanel)
1. Upload files under `/public_html` except `config.php`.
2. Set correct file & folder permissions (644/755).
3. Create MySQL DB & run schema SQL scripts (after UI completion).
4. Add cron jobs for cleanup & reminders.
5. Add Stripe & Telnyx.com webhooks via cPanel URL mapping.

---
## 10. Glossary
* **Unclaimed Event** — newly uploaded, organiser not yet verified.
* **Content Package** — paid plan (1-, 2-, 3-week post schedule).
* **Stakeholder** — venue, sponsor, media partner related to events.

---
---
## 11. UI Layout Structure

### Dashboard Layout
* **Logged-in Header** - Contains user information, notifications, and global actions
* **Sidebar Navigation** - Different versions for regular users and administrators
* **Main Content Area** - Changes dynamically based on navigation selection

### Responsive Design Approach
* **Typography**: Use CSS `clamp()` for fluid typography scaling
  ```css
  /* Example: */
  .heading { font-size: clamp(1.5rem, 5vw, 2.5rem); }
  ```
* **Layout**: Combine fluid sizing with selective media queries for major layout shifts
* **Components**: Design elements should adapt smoothly across viewport sizes

### Navigation Structure
* **User Sidebar**: Events, Media Library, Analytics, Account Settings
* **Admin Sidebar**: Additional sections for User Management, System Configuration

---
*Last updated: 2025-06-13*
