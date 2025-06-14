# PostrMagic UI Implementation Prompts

This document contains complete prompts for implementing the remaining UI pages for PostrMagic. Each prompt includes specific requirements and guidance to ensure consistency with the existing light theme styling.

## Table of Contents

1. [Event Detail Page](#1-event-detail-page)
2. [Media Library Detail Page](#2-media-library-detail-page)
3. [Analytics Dashboard](#3-analytics-dashboard)
4. [User Profile Settings](#4-user-profile-settings)
5. [Settings/Configuration Pages](#5-settingsconfiguration-pages)
6. [User Management (Admin)](#6-user-management-admin)

---

## 1. Event Detail Page

```
Please implement the Event Detail Page for PostrMagic following our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Follow the overall layout structure from the Event Claim Page screenshot (Image 1) which shows a clean two-column layout with event details on the left and generated content on the right
- Use the card and status indicator styling from the My Events page (Image 5) for consistency

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern, optimized JavaScript libraries for UI interactions
- Follow the component patterns in index_v2.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Implement proper loading states during API calls
- Handle empty states gracefully when data is not available

The page should include:
- Event header with event title, date, location, and status indicator
  * Status should use the same styling as existing status indicators
  * Include event thumbnail/image with proper aspect ratio handling
- Key metrics summary (views, engagement, shares)
  * Use card components similar to the dashboard stats section
  * Include subtle visual indicators for metrics trends
- Main tabbed interface with sections for:
  * Content: Display all generated social media posts with platform indicators
    - Follow the existing social post card design
    - Include platform-specific styling for each post type
    - Add copy/share functionality for each post
  * Analytics: Performance metrics with simple visualizations
    - Implement chart components using an optimized library
    - Show engagement over time with date filtering
  * Settings: Event configuration options
    - Use consistent form patterns from existing pages
    - Include validation and success/error states
- Action buttons for managing the event (Edit, Delete, Share)
  * Add confirmation dialogs for destructive actions
  * Position actions based on existing UX patterns
- Responsive design using CSS clamp() for fluid sizing

Integration Requirements:
- Connect to backend using the callOpenAI() function for content generation
- Utilize the helpers in includes/functions.php for common operations
- Implement proper error handling for API failures
- Support real-time updates when content status changes
```

## 2. Media Library Detail Page

```
Please implement the Media Library Detail Page for PostrMagic using our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Use the grid layout approach seen in the My Events page (Image 5) for displaying media items
- Reference the card component styling from index_v2.php (lines 230-240)

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern, optimized JavaScript libraries for UI interactions
- For drag-and-drop functionality, use the same pattern as seen in our upload components
- Follow the card component pattern found in index_v2.php (lines 230-240) for media items
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Ensure all components handle empty states gracefully (e.g., no files uploaded yet)

The page should include:
- Upload area with drag-and-drop functionality and multi-file support
  * Reference existing upload UI patterns in the application
  * Support for image formats (JPG, PNG, SVG) and PDF files
  * Progress indicators during upload
- Toggle between grid and list views for media assets
  * Grid view should use the card component styling from index_v2.php
  * List view should include detailed metadata (filename, size, date)
- Folder organization system with create/rename/delete capabilities
  * Use consistent modal dialogs for these operations
- Tagging system and search functionality
  * Implement tag selection similar to other form components
- Filtering options by media type, date uploaded, and event
  * Use the existing filter component patterns
- Preview functionality for media items (images, videos)
  * Lightbox-style preview with navigation controls
- Bulk selection with actions (delete, move, tag)
  * Include confirmation dialogs for destructive actions
- Responsive design using CSS clamp() for fluid sizing

Ensure the page integrates with our backend storage system and follows our existing styling conventions. No media editing capabilities are needed at this stage.

Implement appropriate loading states and error handling for API operations.
```

## 3. Analytics Dashboard

```
Please implement the Analytics Dashboard for PostrMagic using our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Follow the dashboard layout structure from the User Dashboard (Image 3) and Admin Dashboard (Image 4)
- Use the same stats card components seen in these dashboards for metrics display

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use a modern, optimized JavaScript charting library that prioritizes performance
- Implement data caching strategy to minimize API calls
- Follow component patterns in index_v2.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Ensure all visualizations render properly across all modern browsers

The page should include:
- Overview section with summary statistics and KPIs
  * Use card components similar to the dashboard stats section
  * Include period-over-period comparison indicators
  * Implement skeleton loaders during data fetching
- Date range selector for filtering data (last 7 days, 30 days, custom)
  * Use consistent date picker pattern from existing components
  * Implement instant filtering without page reload (AJAX)
  * Persist selected date range in session
- Visual charts for key metrics (engagement, views, clicks, conversions)
  * Create responsive chart components that maintain readability
  * Use appropriate chart types for different metrics (line, bar, pie)
  * Include tooltips with detailed information on hover
  * Implement proper empty states when data is insufficient
- Platform-specific breakdowns (Instagram, Facebook, Twitter, etc.)
  * Use platform icons consistent with our existing UI
  * Color-code metrics by platform using our theme palette
- Content performance section showing top-performing posts
  * Use the same card component from the content display sections
  * Include sorting options (by engagement, recency, etc.)
  * Link to the full content view for each post
- Export functionality for reports (CSV, PDF)
  * Implement progress indicator during export generation
  * Allow customization of export parameters
- Responsive data visualizations using CSS clamp() for fluid sizing
  * Charts should adapt their display strategy on smaller screens
  * Consider alternative visualization methods for mobile

Integration Requirements:
- Fetch analytics data using existing API endpoints
- Implement efficient data processing on the client side
- Use proper error handling with user-friendly error states
- Consider implementing data refresh without full page reload
```

## 4. User Profile Settings

```
Please implement the User Profile Settings page for PostrMagic using our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- For form layouts, follow the structure and styling from the Event Claim Page (Image 1) form section
- Use the same input field styles, spacing, and button treatments

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Follow secure coding practices for handling user data
- Implement proper input validation and sanitization
- Follow component patterns in index_v2.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Ensure all form submissions include CSRF protection

The page should include:
- Account information section with editable fields for:
  * Personal details (name, email, profile picture)
    - Use the same form component styles as existing forms
    - Add client-side validation for fields
    - Include image upload with preview for profile picture
    - Support image cropping for profile photos
  * Password change functionality
    - Implement strong password requirements
    - Include password strength indicator
    - Require current password verification
  * Account recovery options
    - Add backup email and/or phone number options
    - Include verification process for new recovery methods
- Notification preferences with toggle switches for different notification types
  * Use the same toggle switch component as in existing forms
  * Group notifications by category (events, system, marketing)
  * Include explanatory text for each preference
- Connected accounts section for social media platforms
  * Use platform icons consistent with our existing UI
  * Show connection status and last authorized date
  * Include connect/disconnect actions with confirmation
- Email preferences for marketing and system notifications
  * Use consistent checkbox/radio components
  * Include frequency options for marketing emails
- Form validation and error handling
  * Implement inline validation with immediate feedback
  * Show field-specific error messages
  * Handle API errors with user-friendly messages
- Success confirmation messages for saved changes
  * Use toast notifications for success messages
  * Include visual confirmation on the form itself
- Responsive design using CSS clamp() for fluid sizing

Security Requirements:
- Implement proper input sanitization for all form fields
- Store sensitive information securely following best practices
- Use HTTPS for all API communications
- Implement rate limiting for sensitive operations
- Add login verification for critical changes

Connect to our existing user management backend using the appropriate helper functions in includes/functions.php.
```

## 5. Settings/Configuration Pages

```
Please implement the Settings/Configuration pages for PostrMagic using our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Use the navigation sidebar structure from the dashboard pages (Images 3 and 4)
- For settings sections, reference the clean two-column layout from the Event Claim Page (Image 1)

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern JavaScript for interactive elements
- Implement proper access control with role-based restrictions
- Follow component patterns in index_v2.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Ensure settings persist properly in the database

The pages should include:
- Main settings dashboard with navigation to specific setting categories
  * Use the same navigation component style as in the admin panel
  * Include visual indicators for sections with pending changes
  * Implement breadcrumbs for deeper navigation levels
- System preferences section for application behavior
  * Group related settings logically with clear section headers
  * Use tooltips to explain technical options
  * Implement toggles for feature flags
- Integration settings for third-party services with API key management
  * Mask API key inputs with reveal option
  * Include connection test functionality
  * Display connection status indicators
  * Store API keys securely with proper encryption
- Appearance configuration options (if applicable)
  * Use color pickers with hex/RGB input options
  * Include theme preview functionality
  * Implement theme settings that respect accessibility guidelines
- Language and region settings
  * Use standard language/locale selectors
  * Include date/time format previews
  * Implement number and currency format options
- Data management options (backup, export, delete)
  * Add progress indicators for long-running operations
  * Include confirmation steps for destructive actions
  * Implement proper error handling with recovery options
- Admin-specific settings with proper access control
  * Hide/disable options based on user permissions
  * Include audit logging for sensitive setting changes
  * Add detailed descriptions for critical settings
- Save/cancel buttons with confirmation dialogs for sensitive changes
  * Use consistent button styling and positioning
  * Implement "unsaved changes" warnings
  * Add clear success/error feedback after changes
- Responsive design using CSS clamp() for fluid sizing

Security Requirements:
- Validate and sanitize all user inputs
- Implement CSRF protection for all forms
- Log all critical setting changes for audit purposes
- Use proper authorization checks before applying changes
- Add confirmation requirements for security-related settings

Follow our existing UI patterns and ensure proper data validation and error handling.
```

## 6. User Management (Admin)

```
Please implement the User Management page for PostrMagic admins using our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- For the user listing, follow the card-based approach seen in the Available Events page (Image 2)
- Use the admin navigation structure shown in the Admin Dashboard (Image 4)

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern JavaScript for data tables and interactive elements
- Implement proper access control (admin-only access)
- Follow component patterns in index_v2.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Optimize for performance with large user datasets

The page should include:
- User listing with filterable and searchable interface
  * Use data tables with sortable columns
  * Implement real-time search with debouncing
  * Include advanced filtering options (role, status, date range)
  * Show essential user information in the list view
  * Add status indicators for active/inactive/pending users
- User detail view showing profile information and activity
  * Create modal or dedicated page for detailed user view
  * Include user stats and activity timeline
  * Show connected devices and session information
  * Display membership/subscription details if applicable
- Role management system with predefined roles (Admin, Editor, User)
  * Use consistent dropdown/select components
  * Include role descriptions and capability summaries
  * Add confirmation when changing critical roles
  * Prevent self-demotion for admin users
- Permission matrix for granular access control
  * Create toggle grid for individual permissions
  * Group permissions by feature/section
  * Include inherited permissions display
  * Add bulk permission assignment options
- Activity logs with filtering by user, action, and date
  * Implement date range filtering
  * Include action type categorization
  * Add export functionality for logs
  * Show IP addresses and device information
- User creation/invitation workflow
  * Multi-step form with validation
  * Email verification integration
  * Initial role and permission assignment
  * Welcome email customization options
- Batch actions for managing multiple users
  * Use checkbox selection for multiple users
  * Implement confirm dialogs for bulk actions
  * Show progress for long-running operations
  * Display results summary after completion
- Pagination for large user lists
  * Use standard pagination controls
  * Allow page size customization
  * Consider infinite scroll for certain views
- Responsive design using CSS clamp() for fluid sizing
  * Adapt table view for mobile devices
  * Use collapsible sections on smaller screens

Security Requirements:
- Implement proper authorization checks for all actions
- Add audit logging for user management operations
- Use CSRF protection for all forms
- Sanitize all user inputs
- Implement rate limiting for sensitive operations

Connect to our backend user management system using the helper functions in includes/functions.php. Ensure proper error handling and user feedback throughout the interface.
```

## 7. My Events Page (events.php)

```
Please implement the My Events page for PostrMagic following our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Follow the card-based layout approach from the Available Events page (Image 2) for displaying events
- Use the dashboard layout structure from the User Dashboard (Image 3) for overall page organization
- Reference the status indicators and filtering patterns from existing UI components

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern JavaScript for filtering, sorting, and search functionality
- Follow component patterns in index_v2.php and dashboard-header.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Implement proper loading states during data fetching
- Handle empty states gracefully when no events exist

The page should include:
- Page header with title "My Events" and create event button
  * Use consistent button styling from existing pages
  * Position create button prominently for easy access
- Filter tabs for event status (All, Active, Draft, Past Events)
  * Implement active tab styling consistent with existing UI
  * Include event counts in each tab (e.g., "Active (3)")
  * Use URL parameters to maintain filter state on refresh
- Search and sort functionality
  * Real-time search with debouncing for performance
  * Sort options: Date Created, Event Date, Title (A-Z), Status
  * Implement search highlighting in results
- Event cards grid layout showing:
  * Event thumbnail/poster image with proper aspect ratio
  * Event title, date, and location
  * Status indicator (Active, Draft, Past) with color coding
  * Quick action buttons (Edit, View Analytics, Share, Delete)
  * Generated content count indicator (e.g., "5 posts generated")
  * Last modified timestamp
- Pagination or infinite scroll for large event lists
  * Use consistent pagination controls if implementing pagination
  * Include page size options (12, 24, 48 events per page)
- Bulk actions for managing multiple events
  * Checkbox selection for multiple events
  * Bulk actions: Delete, Change Status, Export
  * Confirmation dialogs for destructive actions
- Empty state design for new users
  * Friendly illustration or icon
  * Clear call-to-action to create first event
  * Brief explanation of the event creation process
- Responsive grid layout using CSS clamp() for fluid sizing
  * 1 column on mobile, 2-3 on tablet, 3-4 on desktop
  * Maintain card readability across all screen sizes

Event Card Component Design:
- Hover effects with subtle elevation changes
- Status badges using consistent color scheme (green for active, orange for draft, gray for past)
- Quick preview of generated social media posts on hover
- Direct links to event detail page and analytics
- Last activity indicator (e.g., "Updated 2 hours ago")

Integration Requirements:
- Connect to backend API for event data fetching
- Implement real-time updates when events are modified
- Use existing authentication and session management
- Follow error handling patterns from existing pages
- Implement proper CSRF protection for all actions

Performance Considerations:
- Implement client-side caching for event data
- Use lazy loading for event thumbnails
- Optimize database queries for large event collections
- Consider implementing virtual scrolling for very large lists
```

## 8. Event Creation Page (event-creation.php)

```
Please implement the Event Creation page for PostrMagic following our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Follow the form layout structure from the Event Claim Page (Image 1) for the main form design
- Use the clean two-column layout pattern for organizing form sections
- Reference the upload UI patterns visible in existing components

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern JavaScript for form validation, file uploads, and dynamic interactions
- Follow component patterns in index_v2.php and dashboard-header.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Implement progressive form saving (auto-save drafts)
- Handle file uploads with progress indicators and error handling

The page should include:
- Multi-step form wizard or single-page form with clear sections
  * Step indicators if using wizard approach (Event Details → Media Upload → Review)
  * Progress saving between steps with visual feedback
  * Ability to navigate between steps without losing data
- Event details section with fields for:
  * Event title (required, with character count)
  * Event description (rich text editor with formatting options)
  * Event date and time (date/time picker components)
  * Event location (with optional map integration)
  * Event category/type (dropdown with predefined options)
  * Event website/URL (optional, with URL validation)
  * Contact information (email, phone - optional)
- Media upload section with drag-and-drop functionality
  * Support for poster/flyer images (JPG, PNG, PDF)
  * Image preview with crop/resize options
  * Multiple file support with progress indicators
  * File size and format validation with clear error messages
  * Option to select from existing media library
- Social media generation preferences
  * Platform selection (Instagram, Facebook, Twitter, LinkedIn)
  * Content tone/style options (Professional, Casual, Energetic, etc.)
  * Hashtag suggestions with custom hashtag input
  * Target audience selection (General, Young Adults, Professionals, etc.)
- Advanced options (collapsible section)
  * Custom AI prompts for content generation
  * Scheduling options for social media posting
  * Privacy settings (Public, Private, Organization Only)
  * Collaboration settings (who can edit/view)
- Form validation and error handling
  * Real-time validation with inline error messages
  * Required field indicators and validation
  * Form submission with loading states
  * Comprehensive error handling with recovery suggestions
- Save actions with multiple options
  * Save as Draft button (allows incomplete forms)
  * Save and Generate Content button (validates required fields)
  * Save and Schedule button (for future content generation)
- Responsive design using CSS clamp() for fluid sizing
  * Single column on mobile with collapsible sections
  * Two-column layout on larger screens
  * Maintain form usability across all devices

AI Integration Features:
- Smart suggestion system for event titles and descriptions
- Auto-detection of event details from uploaded posters (OCR)
- Dynamic hashtag recommendations based on event content
- Content preview generation before final submission
- Real-time character count for platform-specific limits

User Experience Enhancements:
- Auto-save functionality every 30 seconds
- Form persistence across browser sessions
- Keyboard shortcuts for common actions
- Contextual help tooltips for complex fields
- Preview mode to see how content will appear
- Integration with existing media library for asset reuse

Integration Requirements:
- Connect to backend API for event creation and media upload
- Integrate with AI content generation system
- Use existing file upload and processing workflows
- Implement proper CSRF protection and input sanitization
- Follow authentication and authorization patterns from existing pages
```

## 9. Media Library Page (media-library.php)

```
Please implement the Media Library page for PostrMagic following our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Use the grid layout approach from the My Events page for displaying media items in card format
- Follow the dashboard layout structure for overall page organization
- Reference the upload UI patterns from the event creation workflow

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern JavaScript for file management, drag-and-drop, and filtering
- Follow component patterns in index_v2.php and dashboard-header.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Implement efficient loading for large media collections
- Handle file operations with proper progress feedback and error handling

The page should include:
- Page header with title "Media Library" and upload button
  * Prominent upload button with consistent styling
  * Quick stats showing total files, storage used, and available space
- Upload area with drag-and-drop functionality
  * Full-page drop zone that appears when files are dragged over browser
  * Support for multiple file selection and batch uploads
  * Progress indicators for individual files and overall upload progress
  * File type validation with clear error messages
  * File size limits with user-friendly warnings
- View toggle between grid and list layouts
  * Grid view: thumbnail cards with hover previews
  * List view: detailed table with filename, size, date, type, usage count
  * Maintain view preference in user session
- Advanced filtering and search system
  * Filter by file type (Images, PDFs, Documents, Videos)
  * Filter by upload date (Today, This Week, This Month, Custom Range)
  * Filter by usage status (Used in Events, Unused, Recently Used)
  * Real-time search with filename and tag matching
  * Combined filters with clear filter state display
- Folder organization system
  * Create, rename, and delete folders with confirmation dialogs
  * Drag-and-drop files between folders
  * Breadcrumb navigation for folder hierarchy
  * Folder sharing and permission settings
- Media item cards (grid view) showing:
  * Thumbnail preview with appropriate aspect ratio handling
  * Filename with truncation for long names
  * File size and upload date
  * Usage indicator (number of events using this file)
  * Quick action buttons (Preview, Download, Delete, Share)
  * File type icon overlay for non-image files
- Detailed media information panel
  * Full-size preview for images and PDFs
  * Metadata display (dimensions, file size, upload date, last modified)
  * Usage tracking (which events use this file)
  * Tagging system for better organization
  * Direct links and embed codes for sharing
- Bulk selection and batch operations
  * Checkbox selection for multiple files
  * Bulk actions: Delete, Move to Folder, Add Tags, Download as ZIP
  * Select all/none functionality with keyboard shortcuts
  * Confirmation dialogs for destructive batch operations
- Advanced preview functionality
  * Lightbox-style preview with navigation between files
  * Zoom and pan for high-resolution images
  * PDF page navigation for multi-page documents
  * Download and share options within preview
- Storage management features
  * Visual storage usage indicator with color coding
  * Automatic duplicate detection with merge options
  * File optimization suggestions (compress large images)
  * Archive old files functionality
- Responsive design using CSS clamp() for fluid sizing
  * 1-2 columns on mobile, 3-4 on tablet, 4-6 on desktop for grid view
  * Horizontal scrolling table on mobile for list view
  * Touch-friendly interactions for mobile devices

Integration with Event System:
- Show which events are using each media file
- Direct links to edit events that use specific media
- Suggestions for unused media files (cleanup recommendations)
- Integration with event creation workflow for asset selection
- Automatic tagging based on event associations

Performance Optimizations:
- Lazy loading for thumbnails and previews
- Virtual scrolling for large media collections
- Image optimization and multiple size variants
- Client-side caching for frequently accessed files
- Progressive loading with skeleton screens

Integration Requirements:
- Connect to backend file storage system (local or cloud)
- Implement proper file security and access controls
- Use existing authentication and session management
- Follow CSRF protection patterns for all file operations
- Integrate with existing upload processing workflows
```

## 10. Analytics Dashboard Page (analytics-dashboard.php)

```
Please implement the Analytics Dashboard page for PostrMagic following our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Follow the dashboard layout structure from existing dashboard pages for metric cards and charts
- Use the same stats card components seen in the user dashboard for KPI display
- Reference the clean layout patterns from existing pages for chart organization

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use a lightweight, modern JavaScript charting library (Chart.js or similar)
- Implement efficient data fetching with caching to minimize server load
- Follow component patterns in index_v2.php and dashboard-header.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Ensure charts are accessible and responsive across all devices

The page should include:
- Page header with title "Analytics" and date range selector
  * Prominent date range picker with presets (Last 7 days, 30 days, 90 days, Custom)
  * Export button for generating reports
  * Real-time data refresh toggle option
- Key performance indicators (KPI) cards section
  * Total Events Created with period-over-period comparison
  * Total Social Media Posts Generated with trend indicator
  * Average Engagement Rate across all platforms
  * Top Performing Event with link to event details
  * Total Reach/Impressions with percentage change
  * Content Generation Success Rate
- Platform performance breakdown
  * Individual cards or chart sections for each platform (Instagram, Facebook, Twitter, LinkedIn)
  * Platform-specific metrics (likes, shares, comments, reach)
  * Color-coded performance indicators using platform brand colors
  * Best posting times recommendations for each platform
- Content performance analytics
  * Top performing posts with thumbnails and engagement metrics
  * Content type performance (image posts vs. text vs. video)
  * Hashtag performance analysis with trending tags
  * Optimal content length analysis
- Event analytics section
  * Most successful events by engagement
  * Event category performance comparison
  * Geographic performance if location data available
  * Seasonal trends and patterns
- Interactive charts and visualizations
  * Engagement over time (line chart with multiple platform lines)
  * Content type distribution (pie or donut chart)
  * Platform reach comparison (horizontal bar chart)
  * Monthly event creation trends (column chart)
  * User engagement patterns (heatmap by day/time)
- Insights and recommendations panel
  * AI-generated insights about performance patterns
  * Recommendations for improving engagement
  * Best times to post for optimal reach
  * Content suggestions based on trending topics
- Advanced filtering options
  * Filter by event status (active, past, draft)
  * Filter by content type (image, video, text)
  * Filter by platform performance
  * Filter by engagement thresholds
- Export and reporting functionality
  * Generate PDF reports with charts and insights
  * Export data to CSV for external analysis
  * Schedule automated weekly/monthly reports
  * Custom report builder with metric selection
- Real-time updates and notifications
  * Live data refresh without page reload
  * Notifications for significant performance changes
  * Alert system for unusually high or low engagement
- Responsive design using CSS clamp() for fluid sizing
  * Single column layout on mobile with collapsible chart sections
  * Two-column layout on tablet with responsive charts
  * Multi-column dashboard layout on desktop
  * Charts that adapt scale and detail level based on screen size

Chart Implementation Details:
- Use consistent color scheme matching existing UI theme
- Implement hover tooltips with detailed information
- Add click-through functionality to drill down into data
- Include loading states and empty data handling
- Ensure charts are keyboard accessible
- Implement zoom and pan functionality for detailed analysis

Data Integration:
- Connect to analytics API endpoints for real-time data
- Implement data caching strategy to improve performance
- Handle API rate limits gracefully with user feedback
- Provide graceful degradation when analytics data is unavailable
- Include sample data for demonstration purposes

Performance Considerations:
- Lazy load charts that are not immediately visible
- Implement data pagination for large datasets
- Use efficient chart rendering techniques
- Cache frequently accessed analytics data
- Optimize database queries for analytics calculations
```

## 11. User Profile Settings Page (user-profile.php)

```
Please implement the User Profile Settings page for PostrMagic following our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Follow the form layout structure from existing forms for consistent input styling
- Use the dashboard layout structure for overall page organization
- Reference the settings patterns from existing configuration interfaces

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern JavaScript for form validation, image uploads, and interactive elements
- Follow component patterns in index_v2.php and dashboard-header.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Implement proper security measures for sensitive user data handling
- Handle form submissions with comprehensive validation and error handling

The page should include:
- Page header with title "Profile Settings" and save status indicator
  * Clear indication of unsaved changes with warning before navigation
  * Auto-save functionality with visual feedback
  * Last saved timestamp display
- Profile picture section
  * Current profile image display with placeholder for new users
  * Upload area with drag-and-drop functionality for new profile pictures
  * Image cropping interface with circular preview
  * Support for JPG, PNG formats with size validation
  * Option to remove current profile picture
- Personal information section
  * Full name (first name, last name) with real-time validation
  * Email address with verification status indicator
  * Phone number (optional) with format validation
  * Timezone selection with automatic detection option
  * Language preference dropdown
  * Date format preference (MM/DD/YYYY, DD/MM/YYYY, YYYY-MM-DD)
- Account security section
  * Current password field for verification of changes
  * New password field with strength indicator
  * Confirm new password with real-time matching validation
  * Two-factor authentication (2FA) setup toggle
  * Session management (view active sessions, remote logout)
  * Account recovery email setup
- Notification preferences
  * Email notifications toggle for different categories:
    - Event updates and reminders
    - Social media performance alerts
    - Weekly analytics summaries
    - Product updates and announcements
    - Marketing emails and promotions
  * In-app notification preferences:
    - Push notifications for mobile
    - Browser notifications for desktop
    - Sound notifications toggle
  * Notification frequency settings (Immediate, Daily Digest, Weekly Summary)
- Connected accounts section
  * Social media platform connections (Instagram, Facebook, Twitter, LinkedIn)
  * Connection status indicators (Connected, Expired, Error)
  * Connect/disconnect buttons with OAuth flow handling
  * Last authorization date display
  * Permissions summary for each connected account
- Privacy settings
  * Profile visibility options (Public, Private, Organization Only)
  * Data sharing preferences with third-party integrations
  * Analytics data retention settings
  * Account deletion request option with confirmation process
- Subscription and billing information (if applicable)
  * Current plan display with upgrade/downgrade options
  * Billing cycle and next payment date
  * Payment method management
  * Usage statistics against plan limits
  * Download billing history and invoices
- Form validation and user experience
  * Real-time validation with inline error messages
  * Field-specific validation (email format, password strength, phone format)
  * Bulk validation on form submission
  * Success messages for completed updates
  * Undo functionality for recent changes
- Advanced settings (collapsible section)
  * API key management for advanced users
  * Data export options (profile data, events, analytics)
  * Account migration tools
  * Developer settings and webhooks
- Responsive design using CSS clamp() for fluid sizing
  * Single column layout on mobile with logical section grouping
  * Two-column layout on larger screens with related settings grouped
  * Maintain form usability across all device sizes

Security Implementation:
- CSRF protection for all form submissions
- Input sanitization and validation on both client and server side
- Password hashing using secure algorithms (bcrypt, Argon2)
- Session invalidation for security-related changes
- Audit logging for profile modifications
- Rate limiting for sensitive operations (password changes, email updates)

User Experience Enhancements:
- Progressive disclosure for advanced settings
- Contextual help tooltips for complex options
- Keyboard navigation support throughout the form
- Screen reader accessibility compliance
- Confirmation dialogs for destructive actions
- Smart defaults based on user behavior and location

Integration Requirements:
- Connect to user management API for profile updates
- Integrate with authentication system for password changes
- Use existing session management and security protocols
- Connect to billing system for subscription management
- Integrate with notification delivery systems
- Follow existing error handling and logging patterns

Data Management:
- Implement proper data validation and sanitization
- Handle profile picture uploads with resizing and optimization
- Manage user preferences with proper defaults
- Sync settings across multiple devices/sessions
- Backup critical profile data before major changes
```

## 12. Admin Dashboard Page (admin-dashboard.php)

```
Please implement the Admin Dashboard page for PostrMagic following our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Follow the dashboard layout structure from existing dashboard pages for metric cards and organization
- Use the same stats card components for displaying system metrics
- Reference the admin navigation patterns for consistent administrative interface design

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern JavaScript for real-time data updates and interactive elements
- Implement proper admin access control and role verification
- Follow component patterns in index_v2.php and dashboard-header.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Ensure secure handling of sensitive administrative data

The page should include:
- Admin dashboard header with system status indicator
  * Overall system health status (Operational, Warning, Critical)
  * Current server load and performance metrics
  * Active user count and concurrent sessions
  * Quick access to system maintenance tools
- Key system metrics cards
  * Total registered users with growth percentage
  * Total events created (all time and recent)
  * AI content generation usage and success rate
  * Storage usage and capacity monitoring
  * API usage statistics and rate limiting status
  * Revenue metrics (if applicable)
- Recent activity feed
  * Recent user registrations and activations
  * Recent event creations and major updates
  * System alerts and error notifications
  * Content moderation queue items
  * Payment and subscription updates
- User management quick actions
  * Recently registered users requiring approval
  * User accounts with issues (suspended, payment failed)
  * Support tickets and user reports
  * User activity anomalies and security alerts
- Content oversight section
  * Recently created events requiring review
  * Flagged content awaiting moderation
  * AI-generated content that needs verification
  * DMCA or copyright reports
- System monitoring charts
  * User growth over time (line chart)
  * Event creation trends (column chart)
  * System performance metrics (CPU, memory, storage)
  * API usage patterns and peak times
  * Error rates and system stability indicators
- Quick action buttons for common admin tasks
  * Send system announcement to all users
  * Create maintenance window notification
  * Export system reports (CSV, PDF)
  * Access system logs and audit trails
  * Emergency system lockdown controls
- Administrative notifications panel
  * Critical system alerts requiring immediate attention
  * Scheduled maintenance reminders
  * Security incident reports
  * Performance degradation warnings
  * Backup status and data integrity checks
- Platform-specific analytics summary
  * Social media API status for each platform
  * Content generation success rates by platform
  * Popular hashtags and trending content
  * Geographic usage distribution
- Responsive design using CSS clamp() for fluid sizing
  * Single column layout on mobile with priority information first
  * Multi-column layout on larger screens with logical grouping
  * Collapsible sections for detailed information

Security and Access Control:
- Admin role verification on page load
- Audit logging for all administrative actions
- Secure handling of sensitive system information
- Rate limiting for administrative API calls
- CSRF protection for all admin operations
- Session timeout controls for admin sessions

Real-time Features:
- Live system metrics updates without page refresh
- Real-time notifications for critical system events
- WebSocket connections for instant admin alerts
- Auto-refresh for activity feeds and monitoring data
- Push notifications for urgent administrative matters

Integration Requirements:
- Connect to system monitoring APIs for real-time metrics
- Integrate with user management system for user statistics
- Use existing authentication and authorization systems
- Connect to logging and audit trail systems
- Integrate with notification delivery systems
```

## 13. User Management Admin Page (user-management.php)

```
Please implement the User Management admin page for PostrMagic following our existing light theme styling. Create all necessary PHP, HTML, CSS, and JavaScript files.

Reference Images:
- Follow the card-based layout approach for displaying user information
- Use the admin dashboard layout structure for overall page organization
- Reference the table and filtering patterns from existing admin interfaces

Technical Requirements:
- PHP 8.1+ compatibility for all server-side code
- Use modern JavaScript for advanced filtering, search, and batch operations
- Implement proper admin access control with role-based permissions
- Follow component patterns in index_v2.php and dashboard-header.php for consistent styling
- Use CSS clamp() for responsive sizing instead of fixed breakpoints
- Optimize for handling large user datasets efficiently

The page should include:
- Page header with title "User Management" and user creation button
  * Quick stats: Total Users, Active Users, New This Month, Pending Approval
  * Export users button with format options (CSV, Excel)
  * Bulk import users functionality with template download
- Advanced search and filtering system
  * Real-time search by name, email, or user ID
  * Filter by user status (Active, Inactive, Suspended, Pending)
  * Filter by user role (Admin, Editor, User, Custom Roles)
  * Filter by registration date range
  * Filter by last activity date
  * Filter by subscription status (if applicable)
  * Combined filters with clear filter state display
- User listing with sortable columns
  * User avatar (thumbnail) and full name
  * Email address with verification status
  * User role with role change dropdown
  * Registration date and last login
  * Account status with color-coded indicators
  * Quick action buttons (Edit, Suspend, Delete, Login As)
- User detail modal/panel with comprehensive information
  * Complete profile information and avatar
  * Account creation and verification details
  * Activity timeline with login history and major actions
  * Created events and content generation statistics
  * Subscription and billing information (if applicable)
  * Connected social media accounts
  * Security information (2FA status, password changes)
- Role and permission management
  * Predefined roles (Super Admin, Admin, Editor, User)
  * Custom role creation with granular permissions
  * Permission matrix for different system features
  * Bulk role assignment with confirmation
  * Role inheritance and permission conflict resolution
- Bulk operations interface
  * Checkbox selection for multiple users
  * Bulk actions: Change Role, Suspend, Activate, Delete, Export
  * Progress indicators for long-running bulk operations
  * Detailed results summary after bulk operations complete
  * Undo functionality for reversible bulk actions
- User activity monitoring
  * Activity logs with filtering by user and action type
  * Login patterns and security anomaly detection
  * Content creation and modification tracking
  * API usage and rate limiting violations
  * Suspicious activity alerts and flagging
- User creation and invitation workflow
  * Manual user creation form with role assignment
  * Bulk user invitation via email with CSV upload
  * Custom welcome email templates
  * Account activation and verification process
  * Integration with external user directories (LDAP, SSO)
- Advanced user management features
  * Account impersonation for support purposes (with audit logging)
  * Password reset and account recovery tools
  * Account merging for duplicate users
  * Data export for individual users (GDPR compliance)
  * Account deletion with data retention options
- Pagination and performance optimization
  * Efficient pagination for large user lists
  * Configurable page sizes (25, 50, 100, 200 users)
  * Virtual scrolling for very large datasets
  * Search result optimization and caching
- Responsive design using CSS clamp() for fluid sizing
  * Card-based layout on mobile devices
  * Table layout on larger screens with horizontal scrolling
  * Touch-friendly interactions for mobile administration

Security and Compliance:
- Admin permission verification for all operations
- Comprehensive audit logging for user management actions
- GDPR compliance features (data export, deletion, consent tracking)
- SOX compliance for financial data handling
- Rate limiting for bulk operations
- Secure handling of personal and sensitive information

Integration Requirements:
- Connect to user database with optimized queries
- Integrate with authentication and authorization systems
- Use existing email notification systems
- Connect to audit logging and compliance systems
- Integrate with billing and subscription management
```

## Implementation Notes

- All implementations should follow the existing light theme styling visible in current PostrMagic pages
- Focus on the layout and elements, not the specific color palette that may appear in reference images
- Maintain responsive design across all pages for mobile, tablet, and desktop viewports
- Use existing helper functions from includes/functions.php where appropriate
- Implement one page at a time in the order presented here
