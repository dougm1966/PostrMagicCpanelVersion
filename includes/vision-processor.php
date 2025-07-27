<?php
/**
 * Vision Processor Class
 * Handles AI-powered poster analysis and event categorization
 * Implements the two-step flow: Vision Analysis → Category Detection
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/llm-manager.php';
require_once __DIR__ . '/llm-prompt-manager.php';
require_once __DIR__ . '/image-processor.php';

class VisionProcessor {
    
    private $pdo;
    private $isMySQL;
    private $llmManager;
    private $promptManager;
    private $imageProcessor;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->isMySQL = (DB_TYPE === 'mysql');
        $this->llmManager = getLLMManager();
        $this->promptManager = getLLMPromptManager();
        $this->imageProcessor = getImageProcessor();
    }
    
    /**
     * Process poster image through complete AI pipeline
     * Step 1: Vision Analysis - Extract event data
     * Step 2: Category Detection - Determine event category
     */
    public function processPoster($imagePath, $options = []) {
        try {
            $userId = $options['user_id'] ?? null;
            $tempUploadId = $options['temp_upload_id'] ?? null;
            
            // Step 1: Vision Analysis
            $extractedData = $this->extractEventData($imagePath, $options);
            
            if (!$extractedData['success']) {
                return $this->handleProcessingError(
                    'vision_analysis_failed', 
                    $extractedData['error'], 
                    $tempUploadId
                );
            }
            
            // Step 2: Category Detection
            $categoryResult = $this->detectEventCategory($extractedData['data'], $options);
            
            if (!$categoryResult['success']) {
                return $this->handleProcessingError(
                    'category_detection_failed', 
                    $categoryResult['error'], 
                    $tempUploadId
                );
            }
            
            // Check if event fits any category
            if (!$categoryResult['category'] || $categoryResult['confidence'] < 0.7) {
                return $this->handleEventRejection(
                    $imagePath,
                    $extractedData['data'],
                    $categoryResult,
                    $tempUploadId
                );
            }
            
            // Success - return processed data
            return [
                'success' => true,
                'extracted_data' => $extractedData['data'],
                'category' => $categoryResult['category'],
                'confidence' => $categoryResult['confidence'],
                'reasoning' => $categoryResult['reasoning'],
                'processing_details' => [
                    'vision_analysis' => $extractedData['processing_details'],
                    'category_detection' => $categoryResult['processing_details']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Vision processing error: " . $e->getMessage());
            return $this->handleProcessingError(
                'processing_exception', 
                $e->getMessage(), 
                $tempUploadId
            );
        }
    }
    
    /**
     * Step 1: Extract event data from poster using vision AI
     */
    private function extractEventData($imagePath, $options = []) {
        try {
            // Get vision analysis prompt
            $prompt = $this->promptManager->getPrompt('vision_analysis');
            
            if (!$prompt) {
                throw new Exception("Vision analysis prompt not configured");
            }
            
            // Prepare image for AI analysis
            $imageData = $this->prepareImageForAnalysis($imagePath);
            
            // Build prompt data with placeholders
            $promptData = [
                'current_date' => date('Y-m-d'),
                'analysis_instructions' => 'Focus on extracting accurate, structured data from the poster image.'
            ];
            
            // Replace placeholders in prompts
            $systemPrompt = $this->promptManager->replacePlaceholders($prompt['system_prompt'], $promptData);
            $userPrompt = $this->promptManager->replacePlaceholders($prompt['user_prompt'], $promptData);
            
            // Build messages for LLM
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ];
            
            // Prepare LLM options
            $llmOptions = [
                'user_id' => $options['user_id'] ?? null,
                'prompt_id' => $prompt['id'],
                'image_url' => $imageData['url'] ?? null,
                'image_data' => $imageData['base64'] ?? null,
                'image_mime_type' => $imageData['mime_type'] ?? 'image/jpeg'
            ];
            
            // Make AI call
            $result = $this->llmManager->makeAPICall('vision_analysis', $messages, $llmOptions);
            
            if (!$result['success']) {
                throw new Exception("Vision analysis API call failed");
            }
            
            // Parse extracted data
            $extractedData = $this->parseExtractedData($result['content']);
            
            return [
                'success' => true,
                'data' => $extractedData,
                'processing_details' => [
                    'prompt_id' => $prompt['id'],
                    'model_used' => $result['model_used'] ?? 'unknown',
                    'tokens_used' => $result['usage']['total_tokens'] ?? 0,
                    'response_time' => $result['response_time'] ?? 0,
                    'raw_response' => $result['content']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Vision analysis error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Step 2: Detect event category using AI
     */
    private function detectEventCategory($extractedData, $options = []) {
        try {
            // Get category detection prompt
            $prompt = $this->promptManager->getPrompt('category_detection');
            
            if (!$prompt) {
                throw new Exception("Category detection prompt not configured");
            }
            
            // Get available event categories
            $categories = $this->getEventCategories();
            
            // Build prompt data
            $promptData = [
                'extracted_data' => json_encode($extractedData, JSON_PRETTY_PRINT),
                'event_categories' => $this->formatCategoriesForPrompt($categories),
                'category_descriptions' => $this->formatCategoryDescriptions($categories)
            ];
            
            // Replace placeholders
            $systemPrompt = $this->promptManager->replacePlaceholders($prompt['system_prompt'], $promptData);
            $userPrompt = $this->promptManager->replacePlaceholders($prompt['user_prompt'], $promptData);
            
            // Build messages
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ];
            
            // Prepare LLM options
            $llmOptions = [
                'user_id' => $options['user_id'] ?? null,
                'prompt_id' => $prompt['id']
            ];
            
            // Make AI call
            $result = $this->llmManager->makeAPICall('category_detection', $messages, $llmOptions);
            
            if (!$result['success']) {
                throw new Exception("Category detection API call failed");
            }
            
            // Parse category result
            $categoryData = $this->parseCategoryResult($result['content']);
            
            return [
                'success' => true,
                'category' => $categoryData['category'],
                'confidence' => $categoryData['confidence'],
                'reasoning' => $categoryData['reasoning'],
                'processing_details' => [
                    'prompt_id' => $prompt['id'],
                    'model_used' => $result['model_used'] ?? 'unknown',
                    'tokens_used' => $result['usage']['total_tokens'] ?? 0,
                    'response_time' => $result['response_time'] ?? 0,
                    'raw_response' => $result['content'],
                    'available_categories' => array_column($categories, 'category_name')
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Category detection error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Prepare image for AI analysis
     */
    private function prepareImageForAnalysis($imagePath) {
        if (!file_exists($imagePath)) {
            throw new Exception("Image file not found: {$imagePath}");
        }
        
        // Validate image
        $validation = $this->imageProcessor->validateImage($imagePath);
        if (!$validation['valid']) {
            throw new Exception("Invalid image: " . $validation['error']);
        }
        
        // For vision APIs, we need to provide the image in the right format
        $imageData = [
            'mime_type' => $validation['mimeType'],
            'file_size' => filesize($imagePath)
        ];
        
        // Convert to base64 for some providers
        $imageContent = file_get_contents($imagePath);
        $imageData['base64'] = base64_encode($imageContent);
        
        // For URL-based providers, we might need to serve the image temporarily
        // For now, we'll use base64 data
        
        return $imageData;
    }
    
    /**
     * Parse extracted data from AI response
     */
    private function parseExtractedData($aiResponse) {
        // Try to parse as JSON first
        $decoded = json_decode($aiResponse, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $this->sanitizeExtractedData($decoded);
        }
        
        // If not valid JSON, try to extract structured data from text
        return $this->extractDataFromText($aiResponse);
    }
    
    /**
     * Sanitize and validate extracted data
     */
    private function sanitizeExtractedData($data) {
        $sanitized = [];
        
        // Define expected fields and their types
        $expectedFields = [
            'title' => 'string',
            'description' => 'string',
            'date' => 'date',
            'time' => 'string',
            'venue_name' => 'string',
            'venue_address' => 'string',
            'contact_email' => 'email',
            'contact_phone' => 'string',
            'contact_name' => 'string',
            'website_url' => 'url',
            'social_media_links' => 'array',
            'ticket_price' => 'string',
            'ticket_url' => 'url',
            'age_restriction' => 'string'
        ];
        
        foreach ($expectedFields as $field => $type) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $sanitized[$field] = $this->sanitizeField($data[$field], $type);
            } else {
                $sanitized[$field] = null;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize individual field based on type
     */
    private function sanitizeField($value, $type) {
        switch ($type) {
            case 'string':
                return is_string($value) ? trim($value) : (string)$value;
                
            case 'email':
                $email = filter_var($value, FILTER_VALIDATE_EMAIL);
                return $email ? $email : null;
                
            case 'url':
                $url = filter_var($value, FILTER_VALIDATE_URL);
                return $url ? $url : null;
                
            case 'date':
                // Try to parse and reformat date
                $timestamp = strtotime($value);
                return $timestamp ? date('Y-m-d', $timestamp) : null;
                
            case 'array':
                return is_array($value) ? $value : [$value];
                
            default:
                return $value;
        }
    }
    
    /**
     * Extract data from text response (fallback)
     */
    private function extractDataFromText($text) {
        // Basic text parsing as fallback
        $extracted = [];
        
        // Look for common patterns
        if (preg_match('/title[:\s]+(.+)/i', $text, $matches)) {
            $extracted['title'] = trim($matches[1]);
        }
        
        if (preg_match('/email[:\s]+([^\s]+@[^\s]+)/i', $text, $matches)) {
            $extracted['contact_email'] = trim($matches[1]);
        }
        
        if (preg_match('/phone[:\s]+([0-9\-\(\)\s\+]+)/i', $text, $matches)) {
            $extracted['contact_phone'] = trim($matches[1]);
        }
        
        // Add more extraction patterns as needed
        
        return $extracted;
    }
    
    /**
     * Parse category detection result
     */
    private function parseCategoryResult($aiResponse) {
        $decoded = json_decode($aiResponse, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return [
                'category' => $decoded['category'] ?? null,
                'confidence' => (float)($decoded['confidence'] ?? 0.0),
                'reasoning' => $decoded['reasoning'] ?? 'No reasoning provided'
            ];
        }
        
        // Fallback parsing
        return [
            'category' => null,
            'confidence' => 0.0,
            'reasoning' => 'Failed to parse AI response'
        ];
    }
    
    /**
     * Get available event categories
     */
    private function getEventCategories() {
        $table = 'event_categories';
        $sql = "SELECT * FROM {$table} WHERE is_enabled = " . ($this->isMySQL ? 'TRUE' : '1') . " ORDER BY category_name";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Format categories for prompt
     */
    private function formatCategoriesForPrompt($categories) {
        $formatted = [];
        foreach ($categories as $category) {
            $formatted[] = $category['category_name'];
        }
        return implode(', ', $formatted);
    }
    
    /**
     * Format category descriptions for prompt
     */
    private function formatCategoryDescriptions($categories) {
        $descriptions = [];
        foreach ($categories as $category) {
            $descriptions[] = $category['category_name'] . ': ' . $category['description'];
        }
        return implode('\n', $descriptions);
    }
    
    /**
     * Handle processing errors
     */
    private function handleProcessingError($errorType, $errorMessage, $tempUploadId = null) {
        // Log the error
        error_log("Vision processing error [{$errorType}]: {$errorMessage}");
        
        // Update temp upload status if provided
        if ($tempUploadId) {
            $this->updateTempUploadStatus($tempUploadId, 'failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage
            ]);
        }
        
        return [
            'success' => false,
            'error_type' => $errorType,
            'error' => $errorMessage,
            'rejected' => true
        ];
    }
    
    /**
     * Handle event rejection (doesn't fit any category)
     */
    private function handleEventRejection($imagePath, $extractedData, $categoryResult, $tempUploadId = null) {
        try {
            // Store rejected event for admin review
            $this->storeRejectedEvent([
                'temp_file_path' => $imagePath,
                'original_filename' => basename($imagePath),
                'extracted_data' => json_encode($extractedData),
                'rejection_reason' => $categoryResult['reasoning'] ?? 'No suitable category found',
                'suggested_categories' => json_encode($this->suggestNewCategories($extractedData))
            ]);
            
            // Update temp upload status
            if ($tempUploadId) {
                $this->updateTempUploadStatus($tempUploadId, 'rejected', [
                    'rejection_reason' => 'No suitable category',
                    'confidence' => $categoryResult['confidence'],
                    'reasoning' => $categoryResult['reasoning']
                ]);
            }
            
            return [
                'success' => false,
                'rejected' => true,
                'reason' => 'event_category_not_found',
                'message' => 'This event doesn\'t fit any of our current categories.',
                'extracted_data' => $extractedData,
                'category_analysis' => $categoryResult
            ];
            
        } catch (Exception $e) {
            error_log("Error handling event rejection: " . $e->getMessage());
            return $this->handleProcessingError('rejection_handling_failed', $e->getMessage(), $tempUploadId);
        }
    }
    
    /**
     * Store rejected event for admin review
     */
    private function storeRejectedEvent($data) {
        $table = 'rejected_events';
        
        $sql = "INSERT INTO {$table} (
            temp_file_path, original_filename, extracted_data, 
            rejection_reason, suggested_categories
        ) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['temp_file_path'],
            $data['original_filename'],
            $data['extracted_data'],
            $data['rejection_reason'],
            $data['suggested_categories']
        ]);
    }
    
    /**
     * Suggest new categories based on extracted data
     */
    private function suggestNewCategories($extractedData) {
        $suggestions = [];
        
        // Analyze extracted data to suggest potential categories
        $title = strtolower($extractedData['title'] ?? '');
        $description = strtolower($extractedData['description'] ?? '');
        $content = $title . ' ' . $description;
        
        // Look for keywords that might suggest new categories
        $categoryKeywords = [
            'workshop' => ['workshop', 'training', 'seminar', 'class'],
            'charity' => ['charity', 'fundraiser', 'donation', 'benefit'],
            'food' => ['food', 'restaurant', 'dining', 'tasting', 'culinary'],
            'art' => ['art', 'gallery', 'exhibition', 'museum', 'artist'],
            'family' => ['family', 'kids', 'children', 'playground', 'park'],
            'fitness' => ['fitness', 'gym', 'workout', 'yoga', 'health'],
            'technology' => ['tech', 'software', 'digital', 'coding', 'computer']
        ];
        
        foreach ($categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($content, $keyword) !== false) {
                    $suggestions[] = $category;
                    break;
                }
            }
        }
        
        return array_unique($suggestions);
    }
    
    /**
     * Update temporary upload status
     */
    private function updateTempUploadStatus($tempUploadId, $status, $data = []) {
        try {
            $tempManager = getTempUploadManager();
            $tempManager->updateAnalysisResult($tempUploadId, $status, $data);
        } catch (Exception $e) {
            error_log("Error updating temp upload status: " . $e->getMessage());
        }
    }
    
    /**
     * Test vision processing with sample image
     */
    public function testVisionProcessing($imagePath, $userId) {
        $options = [
            'user_id' => $userId,
            'test_mode' => true
        ];
        
        return $this->processPoster($imagePath, $options);
    }
    
    /**
     * Validate extracted data completeness
     */
    public function validateExtractedData($extractedData) {
        $requiredFields = ['title'];
        $recommendedFields = ['date', 'venue_name', 'contact_email'];
        
        $validation = [
            'is_valid' => true,
            'score' => 0,
            'missing_required' => [],
            'missing_recommended' => [],
            'warnings' => []
        ];
        
        // Check required fields
        foreach ($requiredFields as $field) {
            if (empty($extractedData[$field])) {
                $validation['missing_required'][] = $field;
                $validation['is_valid'] = false;
            } else {
                $validation['score'] += 40; // 40 points per required field
            }
        }
        
        // Check recommended fields
        foreach ($recommendedFields as $field) {
            if (empty($extractedData[$field])) {
                $validation['missing_recommended'][] = $field;
            } else {
                $validation['score'] += 20; // 20 points per recommended field
            }
        }
        
        // Additional validation checks
        if (!empty($extractedData['contact_email']) && !filter_var($extractedData['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $validation['warnings'][] = 'Invalid email format';
        }
        
        if (!empty($extractedData['website_url']) && !filter_var($extractedData['website_url'], FILTER_VALIDATE_URL)) {
            $validation['warnings'][] = 'Invalid website URL format';
        }
        
        $validation['score'] = min(100, $validation['score']);
        
        return $validation;
    }
}

/**
 * Global helper function
 */
function getVisionProcessor() {
    static $processor = null;
    if ($processor === null) {
        $processor = new VisionProcessor();
    }
    return $processor;
}
?>
