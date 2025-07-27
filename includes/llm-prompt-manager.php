<?php
/**
 * LLM Prompt Manager Class
 * Handles prompt management, versioning, and testing functionality
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/llm-manager.php';

class LLMPromptManager {
    
    private $pdo;
    private $isMySQL;
    private $llmManager;
    private $maxVersions = 4; // Keep only 4 previous versions
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->isMySQL = (DB_TYPE === 'mysql');
        $this->llmManager = getLLMManager();
    }
    
    /**
     * Create or update a prompt with versioning
     */
    public function savePrompt($promptData) {
        try {
            $this->pdo->beginTransaction();
            
            $existingPrompt = $this->getPrompt(
                $promptData['prompt_type'],
                $promptData['event_category'] ?? null,
                $promptData['content_type'] ?? null
            );
            
            if ($existingPrompt) {
                // Archive current version before updating
                $this->archivePromptVersion($existingPrompt);
                $newVersion = $existingPrompt['version_number'] + 1;
                $promptId = $existingPrompt['id'];
                
                // Update existing prompt
                $this->updatePrompt($promptId, $promptData, $newVersion);
            } else {
                // Create new prompt
                $promptId = $this->createPrompt($promptData);
            }
            
            // Clean up old versions (keep only last 4)
            $this->cleanupOldVersions($promptId);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'prompt_id' => $promptId,
                'message' => $existingPrompt ? 'Prompt updated successfully' : 'Prompt created successfully'
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error saving prompt: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to save prompt: ' . $e->getMessage()];
        }
    }
    
    /**
     * Create new prompt
     */
    private function createPrompt($promptData) {
        $table = 'llm_prompts';
        
        $sql = "INSERT INTO {$table} (
            prompt_type, event_category, content_type, system_prompt, user_prompt,
            assistant_prompt, placeholders, created_by_user_id, version_notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $promptData['prompt_type'],
            $promptData['event_category'] ?? null,
            $promptData['content_type'] ?? null,
            $promptData['system_prompt'],
            $promptData['user_prompt'],
            $promptData['assistant_prompt'] ?? null,
            isset($promptData['placeholders']) ? json_encode($promptData['placeholders']) : null,
            $promptData['created_by_user_id'],
            $promptData['version_notes'] ?? null
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Update existing prompt
     */
    private function updatePrompt($promptId, $promptData, $newVersion) {
        $table = 'llm_prompts';
        
        $sql = "UPDATE {$table} SET 
                system_prompt = ?, user_prompt = ?, assistant_prompt = ?,
                placeholders = ?, version_number = ?, version_notes = ?,
                created_by_user_id = ?, created_date = CURRENT_TIMESTAMP
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $promptData['system_prompt'],
            $promptData['user_prompt'],
            $promptData['assistant_prompt'] ?? null,
            isset($promptData['placeholders']) ? json_encode($promptData['placeholders']) : null,
            $newVersion,
            $promptData['version_notes'] ?? null,
            $promptData['created_by_user_id'],
            $promptId
        ]);
    }
    
    /**
     * Archive current prompt version
     */
    private function archivePromptVersion($prompt) {
        $table = 'llm_prompt_versions';
        
        $sql = "INSERT INTO {$table} (
            prompt_id, version_number, system_prompt, user_prompt, assistant_prompt,
            placeholders, version_notes, created_by_user_id, created_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $prompt['id'],
            $prompt['version_number'],
            $prompt['system_prompt'],
            $prompt['user_prompt'],
            $prompt['assistant_prompt'],
            $prompt['placeholders'],
            $prompt['version_notes'],
            $prompt['created_by_user_id'],
            $prompt['created_date']
        ]);
    }
    
    /**
     * Clean up old versions (keep only last 4)
     */
    private function cleanupOldVersions($promptId) {
        $table = 'llm_prompt_versions';
        
        // Get count of versions
        $countSql = "SELECT COUNT(*) FROM {$table} WHERE prompt_id = ?";
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute([$promptId]);
        $count = $stmt->fetchColumn();
        
        if ($count > $this->maxVersions) {
            // Delete oldest versions
            $deleteCount = $count - $this->maxVersions;
            $deleteSql = "DELETE FROM {$table} WHERE prompt_id = ? ORDER BY version_number ASC LIMIT ?";
            $stmt = $this->pdo->prepare($deleteSql);
            $stmt->execute([$promptId, $deleteCount]);
        }
    }
    
    /**
     * Get prompt by type and category
     */
    public function getPrompt($promptType, $eventCategory = null, $contentType = null, $version = null) {
        $table = 'llm_prompts';
        
        $sql = "SELECT * FROM {$table} WHERE prompt_type = ?";
        $params = [$promptType];
        
        if ($eventCategory !== null) {
            $sql .= " AND event_category = ?";
            $params[] = $eventCategory;
        } else {
            $sql .= " AND event_category IS NULL";
        }
        
        if ($contentType !== null) {
            $sql .= " AND content_type = ?";
            $params[] = $contentType;
        } else {
            $sql .= " AND content_type IS NULL";
        }
        
        if ($version !== null) {
            // Get specific version from versions table
            $table = 'llm_prompt_versions';
            $sql = "SELECT pv.* FROM {$table} pv 
                    JOIN llm_prompts p ON pv.prompt_id = p.id 
                    WHERE p.prompt_type = ? AND pv.version_number = ?";
            $params = [$promptType, $version];
            
            if ($eventCategory !== null) {
                $sql .= " AND p.event_category = ?";
                $params[] = $eventCategory;
            } else {
                $sql .= " AND p.event_category IS NULL";
            }
            
            if ($contentType !== null) {
                $sql .= " AND p.content_type = ?";
                $params[] = $contentType;
            } else {
                $sql .= " AND p.content_type IS NULL";
            }
        }
        
        $sql .= " AND is_active = " . ($this->isMySQL ? 'TRUE' : '1');
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $prompt = $stmt->fetch();
        
        if ($prompt && $prompt['placeholders']) {
            $prompt['placeholders'] = json_decode($prompt['placeholders'], true);
        }
        
        return $prompt;
    }
    
    /**
     * Get all prompts with optional filtering
     */
    public function getAllPrompts($filters = []) {
        $table = 'llm_prompts';
        $userTable = $this->isMySQL ? 'users' : 'users';
        
        $sql = "SELECT p.*, u.name as created_by_name 
                FROM {$table} p 
                LEFT JOIN {$userTable} u ON p.created_by_user_id = u.id 
                WHERE p.is_active = " . ($this->isMySQL ? 'TRUE' : '1');
        
        $params = [];
        
        if (isset($filters['prompt_type'])) {
            $sql .= " AND p.prompt_type = ?";
            $params[] = $filters['prompt_type'];
        }
        
        if (isset($filters['event_category'])) {
            $sql .= " AND p.event_category = ?";
            $params[] = $filters['event_category'];
        }
        
        if (isset($filters['content_type'])) {
            $sql .= " AND p.content_type = ?";
            $params[] = $filters['content_type'];
        }
        
        $sql .= " ORDER BY p.prompt_type, p.event_category, p.content_type, p.created_date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $prompts = $stmt->fetchAll();
        
        // Parse placeholders JSON
        foreach ($prompts as &$prompt) {
            if ($prompt['placeholders']) {
                $prompt['placeholders'] = json_decode($prompt['placeholders'], true);
            }
        }
        
        return $prompts;
    }
    
    /**
     * Get prompt versions
     */
    public function getPromptVersions($promptId) {
        $table = 'llm_prompt_versions';
        $userTable = $this->isMySQL ? 'users' : 'users';
        
        $sql = "SELECT pv.*, u.name as created_by_name 
                FROM {$table} pv 
                LEFT JOIN {$userTable} u ON pv.created_by_user_id = u.id 
                WHERE pv.prompt_id = ? 
                ORDER BY pv.version_number DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$promptId]);
        $versions = $stmt->fetchAll();
        
        // Parse placeholders JSON
        foreach ($versions as &$version) {
            if ($version['placeholders']) {
                $version['placeholders'] = json_decode($version['placeholders'], true);
            }
        }
        
        return $versions;
    }
    
    /**
     * Rollback to a previous version
     */
    public function rollbackToVersion($promptId, $versionNumber, $userId) {
        try {
            $this->pdo->beginTransaction();
            
            // Get the version to rollback to
            $table = 'llm_prompt_versions';
            $sql = "SELECT * FROM {$table} WHERE prompt_id = ? AND version_number = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$promptId, $versionNumber]);
            $version = $stmt->fetch();
            
            if (!$version) {
                throw new Exception("Version not found");
            }
            
            // Get current prompt to archive it
            $promptTable = 'llm_prompts';
            $sql = "SELECT * FROM {$promptTable} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$promptId]);
            $currentPrompt = $stmt->fetch();
            
            if (!$currentPrompt) {
                throw new Exception("Current prompt not found");
            }
            
            // Archive current version
            $this->archivePromptVersion($currentPrompt);
            
            // Update current prompt with version data
            $newVersionNumber = $currentPrompt['version_number'] + 1;
            $sql = "UPDATE {$promptTable} SET 
                    system_prompt = ?, user_prompt = ?, assistant_prompt = ?,
                    placeholders = ?, version_number = ?, 
                    version_notes = ?, created_by_user_id = ?, created_date = CURRENT_TIMESTAMP
                    WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $version['system_prompt'],
                $version['user_prompt'],
                $version['assistant_prompt'],
                $version['placeholders'],
                $newVersionNumber,
                "Rolled back to version {$versionNumber}",
                $userId,
                $promptId
            ]);
            
            // Clean up old versions
            $this->cleanupOldVersions($promptId);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => "Successfully rolled back to version {$versionNumber}"
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error rolling back prompt: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to rollback: ' . $e->getMessage()];
        }
    }
    
    /**
     * Test a prompt with sample data
     */
    public function testPrompt($promptId, $testData, $userId) {
        try {
            // Get the prompt
            $promptTable = 'llm_prompts';
            $sql = "SELECT * FROM {$promptTable} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$promptId]);
            $prompt = $stmt->fetch();
            
            if (!$prompt) {
                throw new Exception("Prompt not found");
            }
            
            // Replace placeholders in prompts
            $systemPrompt = $this->replacePlaceholders($prompt['system_prompt'], $testData);
            $userPrompt = $this->replacePlaceholders($prompt['user_prompt'], $testData);
            $assistantPrompt = $prompt['assistant_prompt'] ? $this->replacePlaceholders($prompt['assistant_prompt'], $testData) : null;
            
            // Build messages array
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ];
            
            if ($assistantPrompt) {
                $messages[] = ['role' => 'assistant', 'content' => $assistantPrompt];
            }
            
            // Prepare options for LLM call
            $options = [
                'user_id' => $userId,
                'prompt_id' => $promptId,
                'event_category' => $prompt['event_category']
            ];
            
            // Add image data for vision prompts
            if ($prompt['prompt_type'] === 'vision_analysis' && isset($testData['image_url'])) {
                $options['image_url'] = $testData['image_url'];
            }
            
            if ($prompt['prompt_type'] === 'vision_analysis' && isset($testData['image_data'])) {
                $options['image_data'] = $testData['image_data'];
                $options['image_mime_type'] = $testData['image_mime_type'] ?? 'image/jpeg';
            }
            
            // Make the LLM call
            $result = $this->llmManager->makeAPICall(
                $prompt['prompt_type'], 
                $messages, 
                $options
            );
            
            return [
                'success' => true,
                'result' => $result,
                'processed_prompts' => [
                    'system' => $systemPrompt,
                    'user' => $userPrompt,
                    'assistant' => $assistantPrompt
                ],
                'test_data' => $testData
            ];
            
        } catch (Exception $e) {
            error_log("Error testing prompt: " . $e->getMessage());
            return [
                'success' => false, 
                'error' => 'Test failed: ' . $e->getMessage(),
                'processed_prompts' => [
                    'system' => $systemPrompt ?? null,
                    'user' => $userPrompt ?? null,
                    'assistant' => $assistantPrompt ?? null
                ]
            ];
        }
    }
    
    /**
     * Replace placeholders in prompt text
     */
    private function replacePlaceholders($promptText, $data) {
        if (empty($data)) {
            return $promptText;
        }
        
        foreach ($data as $key => $value) {
            // Support various placeholder formats
            $placeholders = [
                '{' . $key . '}',
                '{{' . $key . '}}',
                '[' . $key . ']',
                '$' . $key,
                '%' . $key . '%'
            ];
            
            foreach ($placeholders as $placeholder) {
                $promptText = str_replace($placeholder, $value, $promptText);
            }
        }
        
        return $promptText;
    }
    
    /**
     * Get available placeholders for a prompt type
     */
    public function getAvailablePlaceholders($promptType, $eventCategory = null) {
        $placeholders = [];
        
        switch ($promptType) {
            case 'vision_analysis':
                $placeholders = [
                    'event_categories' => 'List of available event categories',
                    'current_date' => 'Current date',
                    'analysis_instructions' => 'Specific analysis instructions'
                ];
                break;
                
            case 'category_detection':
                $placeholders = [
                    'extracted_data' => 'Data extracted from vision analysis',
                    'event_categories' => 'List of available event categories',
                    'category_descriptions' => 'Descriptions of each category'
                ];
                break;
                
            case 'content_creation':
                $placeholders = [
                    'event_title' => 'Event title',
                    'event_description' => 'Event description',
                    'event_date' => 'Event date',
                    'event_time' => 'Event time',
                    'venue_name' => 'Venue name',
                    'venue_address' => 'Venue address',
                    'contact_info' => 'Contact information',
                    'ticket_price' => 'Ticket price',
                    'ticket_url' => 'Ticket URL',
                    'website_url' => 'Event website',
                    'social_media_links' => 'Social media links',
                    'age_restriction' => 'Age restrictions',
                    'event_category' => 'Event category',
                    'business_name' => 'Business/organizer name',
                    'current_date' => 'Current date'
                ];
                break;
        }
        
        return $placeholders;
    }
    
    /**
     * Delete a prompt
     */
    public function deletePrompt($promptId) {
        try {
            $this->pdo->beginTransaction();
            
            $promptTable = 'llm_prompts';
            $versionTable = 'llm_prompt_versions';
            
            // Delete versions first
            $sql = "DELETE FROM {$versionTable} WHERE prompt_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$promptId]);
            
            // Delete prompt
            $sql = "DELETE FROM {$promptTable} WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$promptId]);
            
            $this->pdo->commit();
            
            return ['success' => true, 'message' => 'Prompt deleted successfully'];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error deleting prompt: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete prompt'];
        }
    }
    
    /**
     * Generate default prompts for a category
     */
    public function generateDefaultPrompts($eventCategory, $userId) {
        $defaultPrompts = [
            [
                'prompt_type' => 'vision_analysis',
                'event_category' => null, // Global vision analysis
                'content_type' => null,
                'system_prompt' => 'You are an expert at analyzing event posters and extracting structured information. Extract all relevant event details from the image with high accuracy.',
                'user_prompt' => 'Analyze this event poster image and extract the following information in JSON format:
- title: Event title/name
- description: Event description or details
- date: Event date (YYYY-MM-DD format if found)
- time: Event time
- venue_name: Venue or location name
- venue_address: Full address if available
- contact_email: Email address if found
- contact_phone: Phone number if found
- contact_name: Contact person name if found
- website_url: Website URL if found
- social_media_links: Array of social media links
- ticket_price: Ticket price or pricing info
- ticket_url: Ticket purchase URL if found
- age_restriction: Age restrictions if mentioned

Return only valid JSON. If information is not found, use null for that field.',
                'assistant_prompt' => null,
                'placeholders' => ['current_date', 'analysis_instructions'],
                'version_notes' => 'Default vision analysis prompt'
            ],
            [
                'prompt_type' => 'category_detection',
                'event_category' => null, // Global category detection
                'content_type' => null,
                'system_prompt' => 'You are an expert at categorizing events based on extracted data. Determine which category best fits the event or if it should be rejected.',
                'user_prompt' => 'Based on this extracted event data: {extracted_data}

Available categories: {event_categories}

Determine the best category for this event. Respond with JSON:
{
  "category": "category_name" or null if no good fit,
  "confidence": 0.0-1.0,
  "reasoning": "explanation of categorization decision"
}

If confidence is below 0.7 or no category fits well, set category to null.',
                'assistant_prompt' => null,
                'placeholders' => ['extracted_data', 'event_categories', 'category_descriptions'],
                'version_notes' => 'Default category detection prompt'
            ]
        ];
        
        // Add content creation prompts for each social media type
        $contentTypes = ['facebook', 'instagram', 'linkedin'];
        foreach ($contentTypes as $contentType) {
            $defaultPrompts[] = [
                'prompt_type' => 'content_creation',
                'event_category' => $eventCategory,
                'content_type' => $contentType,
                'system_prompt' => "You are a social media expert specializing in {$contentType} content. Create engaging, platform-appropriate posts that drive attendance to events.",
                'user_prompt' => $this->getDefaultContentPrompt($contentType),
                'assistant_prompt' => null,
                'placeholders' => [
                    'event_title', 'event_description', 'event_date', 'event_time',
                    'venue_name', 'venue_address', 'contact_info', 'ticket_price',
                    'ticket_url', 'website_url', 'business_name'
                ],
                'version_notes' => "Default {$contentType} content creation prompt for {$eventCategory}"
            ];
        }
        
        $results = [];
        foreach ($defaultPrompts as $promptData) {
            $promptData['created_by_user_id'] = $userId;
            $result = $this->savePrompt($promptData);
            $results[] = $result;
        }
        
        return $results;
    }
    
    /**
     * Get default content prompt for platform
     */
    private function getDefaultContentPrompt($platform) {
        $prompts = [
            'facebook' => 'Create an engaging Facebook post for this event:

Event: {event_title}
Description: {event_description}
Date: {event_date}
Time: {event_time}
Venue: {venue_name}
Address: {venue_address}
Tickets: {ticket_price}
More info: {website_url}

Create a compelling post that:
- Captures attention with an engaging opening
- Includes key event details
- Has a clear call to action
- Uses appropriate hashtags
- Maintains an enthusiastic but professional tone
- Is optimized for Facebook\'s audience

Keep it concise and shareable.',

            'instagram' => 'Create an engaging Instagram caption for this event:

Event: {event_title}
Description: {event_description}
Date: {event_date}
Time: {event_time}
Venue: {venue_name}
Tickets: {ticket_price}
More info: {website_url}

Create a caption that:
- Is visually appealing and Instagram-friendly
- Uses relevant hashtags (8-12 hashtags)
- Has an engaging hook in the first line
- Includes call to action
- Uses emojis appropriately
- Fits Instagram\'s style and audience

Focus on visual appeal and engagement.',

            'linkedin' => 'Create a professional LinkedIn post for this event:

Event: {event_title}
Description: {event_description}
Date: {event_date}
Time: {event_time}
Venue: {venue_name}
Tickets: {ticket_price}
More info: {website_url}

Create a post that:
- Maintains professional tone
- Focuses on networking/business value
- Includes relevant industry hashtags
- Has clear value proposition
- Encourages professional engagement
- Suitable for business audience

Keep it professional and value-focused.'
        ];
        
        return $prompts[$platform] ?? $prompts['facebook'];
    }
}

/**
 * Global helper function
 */
function getLLMPromptManager() {
    static $manager = null;
    if ($manager === null) {
        $manager = new LLMPromptManager();
    }
    return $manager;
}
?>
