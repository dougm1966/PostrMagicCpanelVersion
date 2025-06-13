<?php
require_once 'includes/config.php';

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['poster'];
        
        // Basic file validation
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $file['tmp_name']);
        
        if (!in_array($mime_type, $allowed_types)) {
            $response['message'] = 'Only JPG, PNG, and GIF files are allowed.';
        } elseif ($file['size'] > MAX_UPLOAD_SIZE) {
            $response['message'] = 'File is too large. Maximum size is ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB.';
        } else {
            // Generate unique filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $destination = UPLOAD_DIR . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // File uploaded successfully
                $response = [
                    'success' => true,
                    'message' => 'File uploaded successfully!',
                    'filename' => $filename,
                    'path' => 'uploads/' . $filename
                ];
                
                // Here you would typically process the image and generate social media posts
                // For now, we'll just return a success response
                
            } else {
                $response['message'] = 'Failed to move uploaded file.';
            }
        }
    } else {
        $response['message'] = 'No file uploaded or upload error occurred.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
