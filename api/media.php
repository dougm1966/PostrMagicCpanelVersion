<?php
/**
 * Media API Endpoints
 * RESTful API for media management operations
 */

require_once __DIR__ . '/../includes/auth-helper.php';
require_once __DIR__ . '/../includes/media-manager.php';
require_once __DIR__ . '/../includes/tag-manager.php';

// Set JSON response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Ensure user is authenticated for most operations
$publicEndpoints = ['test'];
$endpoint = $_GET['endpoint'] ?? '';

if (!in_array($endpoint, $publicEndpoints)) {
    try {
        // For API endpoints, just check authentication without updating session
        if (!isset($_SESSION['user_id'])) {
            respondWithError('Unauthorized', 401);
        }
        // Don't update session activity for API calls
        $user = ['id' => $_SESSION['user_id'], 'is_admin' => $_SESSION['is_admin'] ?? false];
    } catch (Exception $e) {
        respondWithError('Unauthorized', 401);
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$mediaManager = getMediaManager();
$tagManager = getTagManager();

// Route requests based on endpoint and method
switch ($endpoint) {
    case 'upload':
        if ($method === 'POST') {
            handleUpload();
        } else {
            respondWithError('Method not allowed', 405);
        }
        break;
        
    case 'list':
        if ($method === 'GET') {
            handleList();
        } else {
            respondWithError('Method not allowed', 405);
        }
        break;
        
    case 'get':
        if ($method === 'GET') {
            handleGet();
        } else {
            respondWithError('Method not allowed', 405);
        }
        break;
        
    case 'update':
        if ($method === 'PUT' || $method === 'POST') {
            handleUpdate();
        } else {
            respondWithError('Method not allowed', 405);
        }
        break;
        
    case 'delete':
        if ($method === 'DELETE' || $method === 'POST') {
            handleDelete();
        } else {
            respondWithError('Method not allowed', 405);
        }
        break;
        
    case 'tags':
        handleTags();
        break;
        
    case 'search':
        if ($method === 'GET') {
            handleSearch();
        } else {
            respondWithError('Method not allowed', 405);
        }
        break;
        
    case 'stats':
        if ($method === 'GET') {
            handleStats();
        } else {
            respondWithError('Method not allowed', 405);
        }
        break;
        
    case 'test':
        handleTest();
        break;
        
    default:
        respondWithError('Endpoint not found', 404);
}

/**
 * Handle file upload
 */
function handleUpload() {
    global $mediaManager, $user;
    
    if (!isset($_FILES['file']) && !isset($_FILES['files'])) {
        respondWithError('No files uploaded');
    }
    
    $context = $_POST['context'] ?? 'media';
    $tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
    
    // Clean tags
    $tags = array_filter(array_map('trim', $tags));
    
    $results = [];
    
    // Handle multiple files
    if (isset($_FILES['files']) && is_array($_FILES['files']['name'])) {
        for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
            $file = [
                'name' => $_FILES['files']['name'][$i],
                'type' => $_FILES['files']['type'][$i],
                'tmp_name' => $_FILES['files']['tmp_name'][$i],
                'error' => $_FILES['files']['error'][$i],
                'size' => $_FILES['files']['size'][$i]
            ];
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $result = $mediaManager->uploadMedia($file, $user['id'], $context, $tags);
                $results[] = $result;
            } else {
                $results[] = ['success' => false, 'error' => getUploadError($file['error']), 'filename' => $file['name']];
            }
        }
    } else {
        // Single file upload
        $file = $_FILES['file'] ?? $_FILES['files'];
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            $result = $mediaManager->uploadMedia($file, $user['id'], $context, $tags);
            $results[] = $result;
        } else {
            $results[] = ['success' => false, 'error' => getUploadError($file['error']), 'filename' => $file['name']];
        }
    }
    
    respondWithSuccess($results);
}

function handleList() {
    global $mediaManager, $user;
    
    $options = [
        'page' => max(1, (int) ($_GET['page'] ?? 1)),
        'limit' => min((int) ($_GET['limit'] ?? 20), 100),
        'search' => $_GET['search'] ?? null,
        'context' => $_GET['context'] ?? null,
        'sort' => $_GET['sort'] ?? 'upload_date',
        'order' => strtoupper($_GET['order'] ?? 'DESC')
    ];
    
    $result = $mediaManager->getUserMedia($user['id'], $options);
    respondWithSuccess($result);
}

function handleGet() {
    global $mediaManager, $user;
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        respondWithError('Media ID required');
    }
    
    $result = $mediaManager->getMedia($id, $user['id']);
    
    if ($result['success']) {
        respondWithSuccess($result['media']);
    } else {
        respondWithError($result['error']);
    }
}

function handleUpdate() {
    global $mediaManager, $user;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $id = $input['id'] ?? $_GET['id'] ?? null;
    
    if (!$id) {
        respondWithError('Media ID required');
    }
    
    $updates = [];
    if (isset($input['filename'])) {
        $updates['original_filename'] = $input['filename'];
    }
    if (isset($input['context'])) {
        $updates['context'] = $input['context'];
    }
    
    $result = $mediaManager->updateMedia($id, $user['id'], $updates);
    
    if ($result['success']) {
        respondWithSuccess($result);
    } else {
        respondWithError($result['error']);
    }
}

function handleDelete() {
    global $mediaManager, $user;
    
    $id = $_GET['id'] ?? $_POST['id'] ?? null;
    
    if (!$id) {
        respondWithError('Media ID required');
    }
    
    $result = $mediaManager->deleteMedia($id, $user['id']);
    
    if ($result['success']) {
        respondWithSuccess($result);
    } else {
        respondWithError($result['error']);
    }
}

function handleTags() {
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGetTags();
            break;
        case 'POST':
            handleCreateTag();
            break;
        case 'PUT':
            handleUpdateTag();
            break;
        case 'DELETE':
            handleDeleteTag();
            break;
        default:
            respondWithError('Method not allowed', 405);
    }
}

function handleGetTags() {
    global $tagManager, $user;
    
    $limit = min((int) ($_GET['limit'] ?? 50), 100);
    $tags = $tagManager->getUserTags($user['id'], $limit);
    respondWithSuccess(['tags' => $tags]);
}

function handleCreateTag() {
    global $tagManager, $user;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $name = $input['name'] ?? null;
    $color = $input['color'] ?? null;
    
    if (!$name) {
        respondWithError('Tag name required');
    }
    
    $result = $tagManager->createTag($user['id'], $name, $color);
    
    if ($result['success']) {
        respondWithSuccess($result);
    } else {
        respondWithError($result['error']);
    }
}

function handleUpdateTag() {
    global $tagManager, $user;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $tagId = $input['id'] ?? null;
    $name = $input['name'] ?? null;
    $color = $input['color'] ?? null;
    
    if (!$tagId) {
        respondWithError('Tag ID required');
    }
    
    $updates = [];
    if ($name) $updates['name'] = $name;
    if ($color) $updates['color'] = $color;
    
    $result = $tagManager->updateTag($tagId, $user['id'], $updates);
    
    if ($result['success']) {
        respondWithSuccess($result);
    } else {
        respondWithError($result['error']);
    }
}

function handleDeleteTag() {
    global $tagManager, $user;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $tagId = $input['id'] ?? $_GET['id'] ?? null;
    
    if (!$tagId) {
        respondWithError('Tag ID required');
    }
    
    $result = $tagManager->deleteTag($tagId, $user['id']);
    
    if ($result['success']) {
        respondWithSuccess($result);
    } else {
        respondWithError($result['error']);
    }
}

/**
 * Handle search operations
 */
function handleSearch() {
    global $mediaManager, $tagManager, $user;
    
    $type = $_GET['type'] ?? 'media';
    $query = $_GET['q'] ?? '';
    $limit = min((int) ($_GET['limit'] ?? 20), 100);
    
    switch ($type) {
        case 'media':
            $options = [
                'search' => $query,
                'limit' => $limit,
                'page' => 1
            ];
            $result = $mediaManager->getUserMedia($user['id'], $options);
            respondWithSuccess($result);
            break;
            
        case 'tags':
            $tags = $tagManager->searchTags($user['id'], $query, $limit);
            respondWithSuccess(['tags' => $tags]);
            break;
            
        default:
            respondWithError('Invalid search type');
    }
}

/**
 * Handle statistics
 */
function handleStats() {
    global $tagManager, $user;
    
    $type = $_GET['type'] ?? 'tags';
    
    switch ($type) {
        case 'tags':
            $stats = $tagManager->getTagStats($user['id']);
            respondWithSuccess(['stats' => $stats]);
            break;
            
        case 'media':
            // Media stats implementation would go here
            respondWithSuccess(['stats' => ['message' => 'Media stats not implemented yet']]);
            break;
            
        default:
            respondWithError('Invalid stats type');
    }
}

/**
 * Handle API test
 */
function handleTest() {
    respondWithSuccess([
        'message' => 'Media API is working',
        'timestamp' => time(),
        'version' => '1.0.0',
        'endpoints' => [
            'upload' => 'POST /api/media.php?endpoint=upload',
            'list' => 'GET /api/media.php?endpoint=list',
            'get' => 'GET /api/media.php?endpoint=get&id={id}',
            'update' => 'PUT /api/media.php?endpoint=update',
            'delete' => 'DELETE /api/media.php?endpoint=delete',
            'tags' => 'GET|POST|PUT|DELETE /api/media.php?endpoint=tags',
            'search' => 'GET /api/media.php?endpoint=search&type={media|tags}&q={query}',
            'stats' => 'GET /api/media.php?endpoint=stats&type={tags|media}',
        ]
    ]);
}

/**
 * Utility functions
 */

function respondWithSuccess($data = [], $message = 'Success', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => time()
    ]);
    exit;
}

function respondWithError($message, $code = 400, $details = null) {
    http_response_code($code);
    $response = [
        'success' => false,
        'error' => $message,
        'timestamp' => time()
    ];
    
    if ($details) {
        $response['details'] = $details;
    }
    
    echo json_encode($response);
    exit;
}

function getUploadError($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return 'File size exceeds server limit';
        case UPLOAD_ERR_FORM_SIZE:
            return 'File size exceeds form limit';
        case UPLOAD_ERR_PARTIAL:
            return 'File upload was interrupted';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing temporary upload directory';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'Upload blocked by extension';
        default:
            return 'Unknown upload error';
    }
}
?>
