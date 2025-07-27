<?php
/**
 * Test Media API Endpoints
 * This script tests the media library API endpoints to verify they work correctly
 * without causing database locking issues.
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up test environment
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';

// Test function to make API calls
function testApiCall($url, $method = 'GET', $data = []) {
    $ch = curl_init();
    
    // Set URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Set method and data
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error
    ];
}

// Test concurrent API calls
function testConcurrentCalls($url, $count = 5) {
    $results = [];
    $start = microtime(true);
    
    // Make multiple concurrent calls
    for ($i = 0; $i < $count; $i++) {
        $results[] = testApiCall($url);
    }
    
    $end = microtime(true);
    $duration = $end - $start;
    
    return [
        'results' => $results,
        'duration' => $duration
    ];
}

// Test user media library API
function testUserMediaApi() {
    echo "Testing User Media Library API...\n";
    
    // Test media list
    $result = testApiCall('http://localhost/media-library-backend.php?api=1&action=list');
    echo "Media list API - HTTP Code: {$result['http_code']}\n";
    
    if ($result['error']) {
        echo "Error: {$result['error']}\n";
    } else {
        $data = json_decode($result['response'], true);
        if ($data && $data['success']) {
            echo "Media list API - Success\n";
        } else {
            echo "Media list API - Failed: " . ($data['error'] ?? 'Unknown error') . "\n";
        }
    }
    
    echo "\n";
}

// Test admin media library API
function testAdminMediaApi() {
    echo "Testing Admin Media Library API...\n";
    
    // Test media list
    $result = testApiCall('http://localhost/admin/media-backend.php?api=1&action=media');
    echo "Media list API - HTTP Code: {$result['http_code']}\n";
    
    if ($result['error']) {
        echo "Error: {$result['error']}\n";
    } else {
        $data = json_decode($result['response'], true);
        if ($data && $data['success']) {
            echo "Media list API - Success\n";
        } else {
            echo "Media list API - Failed: " . ($data['error'] ?? 'Unknown error') . "\n";
        }
    }
    
    echo "\n";
}

// Test API media endpoint
function testApiMediaEndpoint() {
    echo "Testing API Media Endpoint...\n";
    
    // Test media list
    $result = testApiCall('http://localhost/api/media.php?api=1&action=list');
    echo "Media list API - HTTP Code: {$result['http_code']}\n";
    
    if ($result['error']) {
        echo "Error: {$result['error']}\n";
    } else {
        $data = json_decode($result['response'], true);
        if ($data && $data['success']) {
            echo "Media list API - Success\n";
        } else {
            echo "Media list API - Failed: " . ($data['error'] ?? 'Unknown error') . "\n";
        }
    }
    
    echo "\n";
}

// Test concurrent calls to check for locking
function testConcurrentAccess() {
    echo "Testing Concurrent API Access...\n";
    
    // Test concurrent calls to user media API
    $result = testConcurrentCalls('http://localhost/media-library-backend.php?api=1&action=list', 3);
    echo "Concurrent User Media API Calls - Duration: " . number_format($result['duration'], 4) . " seconds\n";
    
    // Check results
    $successCount = 0;
    foreach ($result['results'] as $res) {
        $data = json_decode($res['response'], true);
        if ($data && $data['success']) {
            $successCount++;
        }
    }
    echo "Concurrent User Media API Calls - Success: $successCount/3\n";
    
    // Test concurrent calls to admin media API
    $result = testConcurrentCalls('http://localhost/admin/media-backend.php?api=1&action=media', 3);
    echo "Concurrent Admin Media API Calls - Duration: " . number_format($result['duration'], 4) . " seconds\n";
    
    // Check results
    $successCount = 0;
    foreach ($result['results'] as $res) {
        $data = json_decode($res['response'], true);
        if ($data && $data['success']) {
            $successCount++;
        }
    }
    echo "Concurrent Admin Media API Calls - Success: $successCount/3\n";
    
    echo "\n";
}

// Run tests
try {
    echo "Media Library API Test Script\n";
    echo "==========================\n\n";
    
    // Test each API endpoint
    testUserMediaApi();
    testAdminMediaApi();
    testApiMediaEndpoint();
    
    // Test concurrent access
    testConcurrentAccess();
    
    echo "All tests completed.\n";
    
} catch (Exception $e) {
    echo "Test error: " . $e->getMessage() . "\n";
}
?>
