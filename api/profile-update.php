<?php
/**
 * Profile Update API Endpoint
 * Handles AJAX profile updates with JSON responses
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$action = $input['action'] ?? '';
$userId = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'update_profile':
            // Handle profile update
            $allowedFields = ['name', 'bio', 'location', 'website', 'twitter_handle', 'phone', 
                             'timezone', 'email_notifications', 'marketing_emails'];
            
            $profileData = [];
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $profileData[$field] = $input[$field];
                }
            }
            
            // Validate profile data
            $validationErrors = validateProfileData($profileData, $userId);
            if (!empty($validationErrors)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Validation failed',
                    'errors' => $validationErrors
                ]);
                exit;
            }
            
            // Update profile
            if (updateUserProfile($userId, $profileData)) {
                // Get updated user data
                $updatedUser = getCurrentUser();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'user' => [
                        'name' => $updatedUser['name'],
                        'display_name' => $updatedUser['display_name'],
                        'bio' => $updatedUser['bio'],
                        'location' => $updatedUser['location'],
                        'website' => $updatedUser['website'],
                        'twitter_handle' => $updatedUser['twitter_handle'],
                        'phone' => $updatedUser['phone'],
                        'timezone' => $updatedUser['timezone'],
                        'avatar_url' => $updatedUser['avatar_url']
                    ]
                ]);
            } else {
                throw new Exception('Failed to update profile');
            }
            break;
            
        case 'update_field':
            // Handle single field update
            $fieldName = $input['field'] ?? '';
            $fieldValue = $input['value'] ?? '';
            
            if (!$fieldName) {
                throw new Exception('Field name is required');
            }
            
            $allowedFields = ['name', 'bio', 'location', 'website', 'twitter_handle', 'phone', 
                             'timezone', 'email_notifications', 'marketing_emails'];
            
            if (!in_array($fieldName, $allowedFields)) {
                throw new Exception('Invalid field name');
            }
            
            $profileData = [$fieldName => $fieldValue];
            
            // Validate the specific field
            $validationErrors = validateProfileData($profileData, $userId);
            if (!empty($validationErrors)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validationErrors
                ]);
                exit;
            }
            
            // Update the field
            if (updateUserProfile($userId, $profileData)) {
                echo json_encode([
                    'success' => true,
                    'message' => ucfirst($fieldName) . ' updated successfully',
                    'field' => $fieldName,
                    'value' => $fieldValue
                ]);
            } else {
                throw new Exception('Failed to update ' . $fieldName);
            }
            break;
            
        case 'get_profile':
            // Return current profile data
            $currentUser = getCurrentUser();
            if ($currentUser) {
                echo json_encode([
                    'success' => true,
                    'user' => [
                        'id' => $currentUser['id'],
                        'username' => $currentUser['username'],
                        'email' => $currentUser['email'],
                        'name' => $currentUser['name'],
                        'display_name' => $currentUser['display_name'],
                        'bio' => $currentUser['bio'],
                        'location' => $currentUser['location'],
                        'website' => $currentUser['website'],
                        'twitter_handle' => $currentUser['twitter_handle'],
                        'phone' => $currentUser['phone'],
                        'timezone' => $currentUser['timezone'],
                        'email_notifications' => (bool)$currentUser['email_notifications'],
                        'marketing_emails' => (bool)$currentUser['marketing_emails'],
                        'avatar_url' => $currentUser['avatar_url'],
                        'role' => $currentUser['role'],
                        'created_at' => $currentUser['created_at']
                    ]
                ]);
            } else {
                throw new Exception('User not found');
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
