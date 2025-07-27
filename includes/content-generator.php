<?php
/**
 * Content Generator Class
 * Generates social media content using extracted event data and AI prompts
 * Supports Facebook, Instagram, and LinkedIn with category-specific prompts
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/llm-manager.php';
require_once __DIR__ . '/llm-prompt-manager.php';

class ContentGenerator {
    
    private $pdo;
    private $isMySQL;
    private $llmManager;
    private $promptManager;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->isMySQL = (DB_TYPE === 'mysql');
        $this->llmManager = getLLMManager();
        $this->promptManager = getLLMPromptManager();
    }
    
    /**
     * Generate social media content for an event
     */
    public function generateContent($eventData, $contentType, $options = []) {
        try {
            $userId = $options['user_id'] ?? null;
            $eventId = $options['event_id'] ?? null;
            $eventCategory = $eventData['category'] ?? null;
            
            // Validate content type
            if (!in_array($contentType, ['facebook', 'instagram', 'linkedin'])) {
                throw new Exception("Invalid content type: {$contentType}");
            }
            
            // Get content creation prompt
            $prompt = $this->promptManager->getPrompt('content_creation', $eventCategory, $contentType);
            
            if (!$prompt) {
                // Fall back to global prompt if category-specific doesn't exist
                $prompt = $this->promptManager->getPrompt('content_creation', null, $contentType);
            }
            
            if (!$prompt) {
                throw new Exception("No content creation prompt configured for {$contentType}");
            }
            
            // Prepare prompt data with event information
            $promptData = $this->buildPromptData($eventData, $contentType);
            
            // Replace placeholders in prompts
            $systemPrompt = $this->promptManager->replacePlaceholders($prompt['system_prompt'], $promptData);
            $userPrompt = $this->promptManager->replacePlaceholders($prompt['user_prompt'], $promptData);
            $assistantPrompt = $prompt['assistant_prompt'] ? 
                $this->promptManager->replacePlaceholders($prompt['assistant_prompt'], $promptData) : null;
            
            // Build messages for LLM
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ];
            
            if ($assistantPrompt) {
                $messages[] = ['role' => 'assistant', 'content' => $assistantPrompt];
            }
            
            // Prepare LLM options
            $llmOptions = [
                'user_id' => $userId,
                'event_id' => $eventId,
                'event_category' => $eventCategory,
                'prompt_id' => $prompt['id']
            ];
            
            // Make AI call for content generation
            $result = $this->llmManager->makeAPICall('content_creation', $messages, $llmOptions);
            
            if (!$result['success']) {
                throw new Exception("Content generation API call failed");
            }
            
            // Process and validate generated content
            $generatedContent = $this->processGeneratedContent($result['content'], $contentType);
            
            // Store generated content
            $contentId = $this->storeGeneratedContent([
                'user_id' => $userId,
                'event_id' => $eventId,
                'event_category' => $eventCategory,
                'content_type' => $contentType,
                'prompt_id' => $prompt['id'],
                'generated_content' => $generatedContent['content'],
                'content_metadata' => json_encode($generatedContent['metadata']),
                'raw_ai_response' => $result['content'],
                'processing_details' => json_encode([
                    'model_used' => $result['model_used'] ?? 'unknown',
                    'tokens_used' => $result['usage']['total_tokens'] ?? 0,
                    'response_time' => $result['response_time'] ?? 0
                ])
            ]);
            
            return [
                'success' => true,
                'content_id' => $contentId,
                'content' => $generatedContent['content'],
                'metadata' => $generatedContent['metadata'],
                'content_type' => $contentType,
                'processing_details' => [
                    'prompt_id' => $prompt['id'],
                    'model_used' => $result['model_used'] ?? 'unknown',
                    'tokens_used' => $result['usage']['total_tokens'] ?? 0,
                    'response_time' => $result['response_time'] ?? 0,
                    'raw_response' => $result['content']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Content generation error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'content_type' => $contentType
            ];
        }
    }
    
    /**
     * Generate content for multiple platforms
     */
    public function generateMultiPlatformContent($eventData, $platforms = ['facebook', 'instagram', 'linkedin'], $options = []) {
        $results = [];
        
        foreach ($platforms as $platform) {
            $result = $this->generateContent($eventData, $platform, $options);
            $results[$platform] = $result;
            
            // Add small delay between requests to avoid rate limits
            if (count($platforms) > 1) {
                usleep(500000); // 0.5 second delay
            }
        }
        
        return [
            'success' => !empty(array_filter($results, function($r) { return $r['success']; })),
            'results' => $results,
            'platforms' => $platforms
        ];
    }
    
    /**
     * Build prompt data from event information
     */
    private function buildPromptData($eventData, $contentType) {
        $extractedData = $eventData['extracted_data'] ?? [];
        
        return [
            'event_title' => $extractedData['title'] ?? 'Event',
            'event_description' => $extractedData['description'] ?? '',
            'event_date' => $this->formatDate($extractedData['date'] ?? null),
            'event_time' => $extractedData['time'] ?? '',
            'venue_name' => $extractedData['venue_name'] ?? '',
            'venue_address' => $extractedData['venue_address'] ?? '',
            'contact_email' => $extractedData['contact_email'] ?? '',
            'contact_phone' => $extractedData['contact_phone'] ?? '',
            'contact_name' => $extractedData['contact_name'] ?? '',
            'contact_info' => $this->buildContactInfo($extractedData),
            'ticket_price' => $extractedData['ticket_price'] ?? '',
            'ticket_url' => $extractedData['ticket_url'] ?? '',
            'website_url' => $extractedData['website_url'] ?? '',
            'social_media_links' => $this->formatSocialLinks($extractedData['social_media_links'] ?? []),
            'age_restriction' => $extractedData['age_restriction'] ?? '',
            'event_category' => $eventData['category'] ?? '',
            'business_name' => $this->extractBusinessName($extractedData),
            'current_date' => date('Y-m-d'),
            'platform_type' => $contentType
        ];
    }
    
    /**
     * Process and validate generated content
     */
    private function processGeneratedContent($rawContent, $contentType) {
        $processed = [
            'content' => trim($rawContent),
            'metadata' => [
                'character_count' => mb_strlen($rawContent),
                'hashtags' => $this->extractHashtags($rawContent),
                'mentions' => $this->extractMentions($rawContent),
                'platform_specific' => $this->analyzePlatformSpecifics($rawContent, $contentType)
            ]
        ];
        
        // Platform-specific validation and formatting
        switch ($contentType) {
            case 'facebook':
                $processed['metadata']['platform_specific']['recommended_length'] = 'Under 400 characters for optimal engagement';
                $processed['metadata']['platform_specific']['optimal_posting_times'] = ['9am-10am', '2pm-4pm', '7pm-9pm'];
                break;
                
            case 'instagram':
                $processed['metadata']['platform_specific']['recommended_length'] = 'Under 2,200 characters';
                $processed['metadata']['platform_specific']['hashtag_count'] = count($processed['metadata']['hashtags']);
                $processed['metadata']['platform_specific']['optimal_hashtags'] = '8-12 hashtags recommended';
                break;
                
            case 'linkedin':
                $processed['metadata']['platform_specific']['recommended_length'] = 'Under 1,300 characters for optimal engagement';
                $processed['metadata']['platform_specific']['professional_tone'] = $this->assessProfessionalTone($rawContent);
                break;
        }
        
        return $processed;
    }
    
    /**
     * Store generated content in database
     */
    private function storeGeneratedContent($data) {
        $table = 'generated_content';
        
        // Check if table exists, create if not
        $this->ensureContentTableExists();
        
        $sql = "INSERT INTO {$table} (
            user_id, event_id, event_category, content_type, prompt_id,
            generated_content, content_metadata, raw_ai_response, processing_details
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['user_id'],
            $data['event_id'],
            $data['event_category'],
            $data['content_type'],
            $data['prompt_id'],
            $data['generated_content'],
            $data['content_metadata'],
            $data['raw_ai_response'],
            $data['processing_details']
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Ensure content storage table exists (handled by unified migration)
     */
    private function ensureContentTableExists() {
        // Table creation handled by unified migration script
        // No action needed - table should already exist
    }
    
    /**
     * Helper functions for content processing
     */
    private function formatDate($date) {
        if (!$date) return '';
        
        $timestamp = strtotime($date);
        if ($timestamp) {
            return date('l, F j, Y', $timestamp); // e.g., "Friday, March 15, 2024"
        }
        
        return $date;
    }
    
    private function buildContactInfo($extractedData) {
        $contact = [];
        
        if (!empty($extractedData['contact_name'])) {
            $contact[] = $extractedData['contact_name'];
        }
        if (!empty($extractedData['contact_email'])) {
            $contact[] = $extractedData['contact_email'];
        }
        if (!empty($extractedData['contact_phone'])) {
            $contact[] = $extractedData['contact_phone'];
        }
        
        return implode(' | ', $contact);
    }
    
    private function formatSocialLinks($links) {
        if (!is_array($links)) return '';
        
        return implode(', ', array_filter($links));
    }
    
    private function extractBusinessName($extractedData) {
        // Try to extract business name from various fields
        $candidates = [
            $extractedData['contact_name'] ?? '',
            $extractedData['venue_name'] ?? '',
            // Could extract from email domain or other fields
        ];
        
        foreach ($candidates as $candidate) {
            if (!empty($candidate) && strlen($candidate) > 2) {
                return $candidate;
            }
        }
        
        return 'Event Organizer';
    }
    
    private function extractHashtags($content) {
        preg_match_all('/#[\w]+/', $content, $matches);
        return $matches[0] ?? [];
    }
    
    private function extractMentions($content) {
        preg_match_all('/@[\w]+/', $content, $matches);
        return $matches[0] ?? [];
    }
    
    private function analyzePlatformSpecifics($content, $platform) {
        $analysis = [
            'call_to_action_present' => $this->hasCallToAction($content),
            'emoji_count' => $this->countEmojis($content),
            'url_count' => $this->countUrls($content)
        ];
        
        return $analysis;
    }
    
    private function assessProfessionalTone($content) {
        $professionalKeywords = ['networking', 'professional', 'business', 'career', 'industry', 'opportunity'];
        $casualKeywords = ['awesome', 'cool', 'amazing', 'fun', 'party'];
        
        $professionalCount = 0;
        $casualCount = 0;
        
        foreach ($professionalKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $professionalCount++;
            }
        }
        
        foreach ($casualKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $casualCount++;
            }
        }
        
        if ($professionalCount > $casualCount) {
            return 'Professional';
        } elseif ($casualCount > $professionalCount) {
            return 'Casual';
        } else {
            return 'Neutral';
        }
    }
    
    private function hasCallToAction($content) {
        $ctaPatterns = [
            '/register now/i',
            '/book now/i',
            '/buy tickets/i',
            '/learn more/i',
            '/visit us/i',
            '/join us/i',
            '/don\'t miss/i',
            '/limited time/i'
        ];
        
        foreach ($ctaPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function countEmojis($content) {
        // Simple emoji counting - could be enhanced with proper Unicode handling
        return preg_match_all('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]/u', $content);
    }
    
    private function countUrls($content) {
        return preg_match_all('/https?:\/\/[^\s]+/', $content);
    }
    
    /**
     * Get user's generated content history
     */
    public function getUserContentHistory($userId, $limit = 50, $contentType = null) {
        try {
            $table = 'generated_content';
            
            $sql = "SELECT * FROM {$table} WHERE user_id = ?";
            $params = [$userId];
            
            if ($contentType) {
                $sql .= " AND content_type = ?";
                $params[] = $contentType;
            }
            
            $sql .= " ORDER BY created_date DESC LIMIT ?";
            $params[] = $limit;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            
            // Parse JSON metadata
            foreach ($results as &$result) {
                if ($result['content_metadata']) {
                    $result['content_metadata'] = json_decode($result['content_metadata'], true);
                }
                if ($result['processing_details']) {
                    $result['processing_details'] = json_decode($result['processing_details'], true);
                }
            }
            
            return $results;
            
        } catch (Exception $e) {
            error_log("Error getting user content history: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Test content generation with sample data
     */
    public function testContentGeneration($contentType, $sampleEventData, $userId) {
        $options = [
            'user_id' => $userId,
            'test_mode' => true
        ];
        
        return $this->generateContent($sampleEventData, $contentType, $options);
    }
    
    /**
     * Get content generation analytics
     */
    public function getContentAnalytics($userId = null, $dateRange = 30) {
        try {
            $table = 'generated_content';
            
            $sql = "SELECT 
                        content_type,
                        event_category,
                        COUNT(*) as total_generated,
                        COUNT(CASE WHEN is_published = " . ($this->isMySQL ? 'TRUE' : '1') . " THEN 1 END) as published_count,
                        AVG(JSON_EXTRACT(content_metadata, '$.character_count')) as avg_character_count
                    FROM {$table} 
                    WHERE created_date >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            
            $params = [$dateRange];
            
            if ($userId) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " GROUP BY content_type, event_category ORDER BY total_generated DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting content analytics: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Global helper function
 */
function getContentGenerator() {
    static $generator = null;
    if ($generator === null) {
        $generator = new ContentGenerator();
    }
    return $generator;
}
?>
