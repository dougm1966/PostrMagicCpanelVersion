# Complete Profile Connection & Update System Implementation Plan

## Current State Analysis:
- **Admin Profile**: `/admin/profile.php` - Has profile update functionality but limited
- **User Profile**: `user-profile.php` - Static mock data, no update functionality  
- **Settings Pages**: Both admin and user have settings pages with profile update forms
- **Auth System**: Strong authentication with `getCurrentUser()` function available

## Sequential Implementation Plan with Testing:

### Step 1: Update getCurrentUser() Function
- **File**: `/includes/auth.php`
- **Action**: Expand `getCurrentUser()` to include additional profile fields needed for display
- **Add Fields**: `name`, `avatar`, `bio`, `location`, `website`, `phone`, `timezone`
- **Purpose**: Ensure all profile data is available in session

**Testing for Step 1:**
- Test `getCurrentUser()` returns all new fields
- Test function works for both admin and regular users
- Test null handling for missing fields
- Test database connection error handling

### Step 2: Create User Profile Database Fields
- **File**: `/includes/auth.php` or migration script  
- **Action**: Add database fields for extended profile information
- **New Fields**: 
  - `name` (full display name)
  - `avatar` (profile image URL)
  - `bio` (user biography) 
  - `location` (user location)
  - `website` (personal website)
  - `phone` (contact number)
  - `timezone` (user timezone)
- **Method**: Create table migration or ALTER statements

**Testing for Step 2:**
- Test database schema changes applied successfully
- Test existing users still work after migration
- Test new fields accept NULL values initially
- Test field length constraints work properly
- Test database rollback if migration fails

### Step 3: Update Admin Profile Page (`/admin/profile.php`)
- **Current**: Basic profile editing (username, email, password)
- **Enhance**: Add extended profile fields (name, bio, location, website, phone)
- **Form Updates**: Add new form sections for extended profile data
- **Avatar Upload**: Implement avatar upload functionality
- **Data Binding**: Connect form to database using `getCurrentUser()`

**Testing for Step 3:**
- Test admin can view current profile data in form
- Test admin can update each field individually
- Test admin can update multiple fields simultaneously
- Test form validation for each field type
- Test password change functionality still works
- Test unauthorized access prevention
- Test form displays error messages properly
- Test success messages appear after updates

### Step 4: Transform User Profile Page (`user-profile.php`)
- **Current**: Static mock data display
- **Transform**: Convert to dynamic data from `getCurrentUser()`
- **Add Edit Mode**: Implement edit functionality similar to admin profile
- **Form Handling**: Add POST handling for profile updates
- **Layout**: Maintain existing beautiful design while adding edit capabilities

**Testing for Step 4:**
- Test profile displays real user data instead of mock data
- Test edit mode toggle functionality
- Test user can update profile fields
- Test form validation works for user profile
- Test beautiful design is maintained
- Test unauthorized users cannot access
- Test user cannot access admin-only features
- Test profile updates persist to database

### Step 5: Update Sidebar Profile Links
- **Admin Sidebar**: Ensure "Admin Profile" link goes to `/admin/profile.php`
- **User Sidebar**: Ensure "My Profile" link goes to `user-profile.php`
- **Settings Links**: Keep existing settings page links for preferences

**Testing for Step 5:**
- Test admin sidebar links to correct admin profile page
- Test user sidebar links to correct user profile page
- Test settings links still work for both user types
- Test links work in different browser states
- Test navigation breadcrumbs are correct

### Step 6: Implement Avatar Upload System
- **Create**: `/includes/upload-handler.php`
- **Features**: 
  - Secure file upload
  - Image validation and resizing
  - File storage in `/uploads/avatars/`
  - Database URL storage
- **Integration**: Add to both admin and user profile pages

**Testing for Step 6:**
- Test avatar upload accepts valid image formats (JPG, PNG, GIF)
- Test avatar upload rejects non-image files
- Test file size limits are enforced
- Test image resizing works correctly
- Test file permissions are secure
- Test upload directory creation if it doesn't exist
- Test database stores correct avatar URL
- Test old avatar files are cleaned up
- Test avatar displays correctly after upload
- Test upload works for both admin and regular users

### Step 7: Add Profile Update Validation
- **Validation Rules**:
  - Email uniqueness check
  - Username uniqueness check  
  - Phone format validation
  - Website URL validation
  - Bio length limits
- **Error Handling**: Consistent error messaging across both profile pages

**Testing for Step 7:**
- Test email uniqueness validation (should reject existing emails)
- Test username uniqueness validation (should reject existing usernames)
- Test phone format validation (various phone number formats)
- Test website URL validation (valid/invalid URLs)
- Test bio length limits (character count enforcement)
- Test validation messages are user-friendly
- Test validation works on both client and server side
- Test form retains valid data when validation fails

### Step 8: Update Session Management
- **Purpose**: Ensure profile changes are reflected in session immediately
- **Action**: Update session variables when profile is updated
- **Files**: Both admin and user profile pages
- **Sync**: Keep `$_SESSION` data in sync with database changes

**Testing for Step 8:**
- Test session updates immediately after profile change
- Test sidebar displays updated user name after change
- Test session data matches database data
- Test session persists across page reloads
- Test session updates work for both user types
- Test concurrent session handling

### Step 9: Create Profile Update API Endpoints (Optional)
- **File**: `/api/profile-update.php`
- **Purpose**: AJAX profile updates without page reload
- **Features**: Separate endpoints for different profile sections
- **Security**: Proper authentication and CSRF protection

**Testing for Step 9:**
- Test API endpoints require authentication
- Test CSRF protection works
- Test API returns proper JSON responses
- Test API handles validation errors correctly
- Test API updates database correctly
- Test API rate limiting works
- Test API error handling for malformed requests

### Step 10: Cross-Integration Testing
- **Admin Testing**: Test all admin profile functionality
- **User Testing**: Test all user profile functionality  
- **Cross-Browser**: Ensure avatar upload works across browsers
- **Security Testing**: Validate file upload security
- **Database Testing**: Ensure all data persists correctly

**Testing for Step 10:**
- Test admin login → navigate to profile → update profile → verify changes
- Test user login → navigate to profile → update profile → verify changes
- Test profile updates in Chrome, Firefox, Safari, Edge
- Test mobile responsiveness of profile pages
- Test avatar upload in different browsers
- Test large file upload handling
- Test malicious file upload attempts
- Test SQL injection attempts on profile fields
- Test XSS attempts on profile fields
- Test concurrent users updating profiles
- Test database transaction rollbacks on errors

### Step 11: Performance Testing
- **Load Testing**: Test profile pages under load
- **Avatar Upload**: Test multiple simultaneous avatar uploads
- **Database**: Test profile queries with large user datasets

**Testing for Step 11:**
- Test profile page load times with 100+ concurrent users
- Test avatar upload performance with multiple files
- Test database query performance with 10,000+ users
- Test memory usage during profile operations
- Test file storage performance and disk space usage

### Step 12: User Acceptance Testing
- **Admin Users**: Full admin profile management workflow
- **Regular Users**: Full user profile management workflow
- **Edge Cases**: Test boundary conditions and error scenarios

**Testing for Step 12:**
- Test complete admin user journey from login to profile update
- Test complete regular user journey from login to profile update
- Test profile update with empty fields
- Test profile update with maximum length fields
- Test profile update with special characters
- Test undo/cancel profile changes
- Test profile update confirmation workflows

## File Changes Summary:
1. `/includes/auth.php` - Expand getCurrentUser(), add migration
2. `/admin/profile.php` - Add extended profile fields and avatar upload
3. `/user-profile.php` - Convert to dynamic with edit functionality
4. `/includes/upload-handler.php` - New file for avatar uploads
5. `/includes/sidebar-admin.php` - Verify profile link
6. `/includes/sidebar-user.php` - Verify profile link
7. `/api/profile-update.php` - Optional AJAX endpoints

## Success Criteria:
- Admin users can fully edit their profile from admin dashboard
- Regular users can fully edit their profile from user dashboard  
- Avatar uploads work securely for both user types
- All profile data persists to database
- Session data stays synchronized with profile changes
- Both profile pages maintain their distinct design aesthetics
- All tests pass with 100% success rate
- System handles edge cases gracefully
- Performance meets acceptable standards