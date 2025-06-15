<?php
/**
 * LLM Manager Class
 * Handles LLM provider management, API calls, fallbacks, and cost tracking
 */

require_once __DIR__ . '/../config/config.php';

class LLMManager {
    
    private $pdo;
    private $isMySQL;
    private $providers = [];
    private $configurations = [];
    
    // Cost per token for each provider (in USD)
    private $costRates = [
        'openai' => [
            'gpt-4' => ['input' => 0.00003, 'output' => 0.00006],
            'gpt-4-turbo' => ['input' => 0.00001, 'output' => 0.00003],
            'gpt-3.5-turbo' => ['input' => 0.0000015, 'output' => 0.000002],
            'gpt-4-vision-preview' => ['input' => 0.00001, 'output' => 0.00003]
        ],
        'anthropic' => [
            'claude-3-opus' => ['input' => 0.000015, 'output' => 0.000075],
            'claude-3-sonnet' => ['input' => 0.000003, 'output' => 0.000015],
            'claude-3-haiku' => ['input' => 0.00000025, 'output' => 0.00000125]
        ],
        'gemini' => [
            'gemini-pro' => ['input' => 0.0000005, 'output' => 0.0000015],
            'gemini-pro-vision' => ['input' => 0.0000005, 'output' => 0.0000015]
        ]
    ];
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->isMySQL = (DB_TYPE === 'mysql');
        $this->loadProviders();
        $this->loadConfigurations();
    }
    
    /**
     * Load active providers from database
     */
    private function loadProviders() {
        $sql = "SELECT * FROM llm_providers WHERE is_enabled = 1";
        
        $stmt = $this->pdo->query($sql);
        $this->providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Load provider configurations
     */
    private function loadConfigurations() {
        $table = 'llm_configurations';
        $sql = "SELECT * FROM {$table} WHERE is_enabled = " . ($this->isMySQL ? 'TRUE' : '1') . " ORDER BY priority_order ASC";
        
        $stmt = $this->pdo->query($sql);
        $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize configurations by content type and category
        foreach ($configs as $config) {
            $key = $config['content_type'] . '_' . ($config['event_category'] ?? 'global');
            if (!isset($this->configurations[$key])) {
                $this->configurations[$key] = [];
            }
            $this->configurations[$key][] = $config;
        }
    }
    
    /**
     * Get the best provider configuration for a given content type and category
     */
    public function getProviderConfig($contentType, $eventCategory = null) {
        // First try category-specific config
        if ($eventCategory) {
            $key = $contentType . '_' . $eventCategory;
            if (isset($this->configurations[$key]) && !empty($this->configurations[$key])) {
                return $this->configurations[$key]; // Returns array of providers in priority order
            }
        }
        
        // Fall back to global config
        $key = $contentType . '_global';
        return $this->configurations[$key] ?? [];
    }
    
    /**
     * Make an LLM API call with automatic fallback
     */
    public function makeAPICall($contentType, $messages, $options = []) {
        $startTime = microtime(true);
        $eventCategory = $options['event_category'] ?? null;
        $userId = $options['user_id'] ?? null;
        $eventId = $options['event_id'] ?? null;
        $promptId = $options['prompt_id'] ?? null;
        
        $providers = $this->getProviderConfig($contentType, $eventCategory);
        
        if (empty($providers)) {
            throw new Exception("No providers configured for content type: {$contentType}");
        }
        
        $lastError = null;
        
        foreach ($providers as $providerConfig) {
            try {
                $provider = $this->getProviderById($providerConfig['provider_id']);
                if (!$provider) continue;
                
                // Determine which API key to use
                $apiKey = $this->getAPIKey($provider['name'], $userId);
                $apiKeySource = $this->getAPIKeySource($provider['name'], $userId);
                
                // Make the API call
                $result = $this->callProvider(
                    $provider['name'], 
                    $providerConfig, 
                    $messages, 
                    $apiKey,
                    $options
                );
                
                if ($result['success']) {
                    // Log successful usage
                    $this->logUsage([
                        'provider_id' => $provider['id'],
                        'user_id' => $userId,
                        'event_id' => $eventId,
                        'prompt_id' => $promptId,
                        'prompt_type' => $contentType,
                        'model_name' => $providerConfig['model_name'],
                        'input_tokens' => $result['usage']['input_tokens'] ?? 0,
                        'output_tokens' => $result['usage']['output_tokens'] ?? 0,
                        'total_tokens' => $result['usage']['total_tokens'] ?? 0,
                        'estimated_cost' => $this->calculateCost($provider['name'], $providerConfig['model_name'], $result['usage'] ?? []),
                        'response_time_ms' => (microtime(true) - $startTime) * 1000,
                        'was_successful' => true,
                        'api_key_source' => $apiKeySource,
                        'request_data' => json_encode(['messages' => $messages, 'config' => $providerConfig]),
                        'response_data' => json_encode($result['data'] ?? [])
                    ]);
                    
                    return $result;
                }
                
            } catch (Exception $e) {
                $lastError = $e;
                
                // Log failed usage
                $this->logUsage([
                    'provider_id' => $provider['id'],
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'prompt_id' => $promptId,
                    'prompt_type' => $contentType,
                    'model_name' => $providerConfig['model_name'],
                    'response_time_ms' => (microtime(true) - $startTime) * 1000,
                    'was_successful' => false,
                    'error_message' => $e->getMessage(),
                    'api_key_source' => $apiKeySource ?? 'admin',
                    'request_data' => json_encode(['messages' => $messages, 'config' => $providerConfig])
                ]);
                
                continue; // Try next provider
            }
        }
        
        // All providers failed
        throw new Exception("All LLM providers failed. Last error: " . ($lastError ? $lastError->getMessage() : 'Unknown error'));
    }
    
    /**
     * Call specific provider
     */
    private function callProvider($providerName, $config, $messages, $apiKey, $options = []) {
        switch ($providerName) {
            case 'openai':
                return $this->callOpenAI($config, $messages, $apiKey, $options);
            case 'anthropic':
                return $this->callAnthropic($config, $messages, $apiKey, $options);
            case 'gemini':
                return $this->callGemini($config, $messages, $apiKey, $options);
            default:
                throw new Exception("Unsupported provider: {$providerName}");
        }
    }
    
    /**
     * Call OpenAI API
     */
    private function callOpenAI($config, $messages, $apiKey, $options = []) {
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $data = [
            'model' => $config['model_name'],
            'messages' => $messages,
            'max_tokens' => $config['max_tokens'],
            'temperature' => (float) $config['temperature'],
            'top_p' => (float) $config['top_p'],
            'frequency_penalty' => (float) $config['frequency_penalty'],
            'presence_penalty' => (float) $config['presence_penalty']
        ];
        
        // Add vision support if needed
        if (strpos($config['model_name'], 'vision') !== false && isset($options['image_url'])) {
            $data['messages'] = $this->formatMessagesForVision($messages, $options['image_url'], 'openai');
        }
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ];
        
        $response = $this->makeHTTPRequest($url, $data, $headers, $config['timeout_seconds']);
        
        if (isset($response['error'])) {
            throw new Exception("OpenAI API Error: " . $response['error']['message']);
        }
        
        return [
            'success' => true,
            'content' => $response['choices'][0]['message']['content'],
            'usage' => [
                'input_tokens' => $response['usage']['prompt_tokens'] ?? 0,
                'output_tokens' => $response['usage']['completion_tokens'] ?? 0,
                'total_tokens' => $response['usage']['total_tokens'] ?? 0
            ],
            'data' => $response
        ];
    }
    
    /**
     * Call Anthropic API
     */
    private function callAnthropic($config, $messages, $apiKey, $options = []) {
        $url = 'https://api.anthropic.com/v1/messages';
        
        // Convert messages format for Anthropic
        $systemMessage = '';
        $userMessages = [];
        
        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $systemMessage = $message['content'];
            } else {
                $userMessages[] = $message;
            }
        }
        
        $data = [
            'model' => $config['model_name'],
            'max_tokens' => $config['max_tokens'],
            'temperature' => (float) $config['temperature'],
            'top_p' => (float) $config['top_p'],
            'messages' => $userMessages
        ];
        
        if ($systemMessage) {
            $data['system'] = $systemMessage;
        }
        
        // Add vision support if needed
        if (isset($options['image_url'])) {
            $data['messages'] = $this->formatMessagesForVision($userMessages, $options['image_url'], 'anthropic');
        }
        
        $headers = [
            'Content-Type: application/json',
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01'
        ];
        
        $response = $this->makeHTTPRequest($url, $data, $headers, $config['timeout_seconds']);
        
        if (isset($response['error'])) {
            throw new Exception("Anthropic API Error: " . $response['error']['message']);
        }
        
        return [
            'success' => true,
            'content' => $response['content'][0]['text'],
            'usage' => [
                'input_tokens' => $response['usage']['input_tokens'] ?? 0,
                'output_tokens' => $response['usage']['output_tokens'] ?? 0,
                'total_tokens' => ($response['usage']['input_tokens'] ?? 0) + ($response['usage']['output_tokens'] ?? 0)
            ],
            'data' => $response
        ];
    }
    
    /**
     * Call Google Gemini API
     */
    private function callGemini($config, $messages, $apiKey, $options = []) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$config['model_name']}:generateContent?key={$apiKey}";
        
        // Convert messages format for Gemini
        $parts = [];
        foreach ($messages as $message) {
            if ($message['role'] !== 'system') { // Gemini doesn't have system role
                $parts[] = ['text' => $message['content']];
            }
        }
        
        // Add image for vision if provided
        if (isset($options['image_data'])) {
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $options['image_mime_type'] ?? 'image/jpeg',
                    'data' => $options['image_data']
                ]
            ];
        }
        
        $data = [
            'contents' => [
                ['parts' => $parts]
            ],
            'generationConfig' => [
                'maxOutputTokens' => $config['max_tokens'],
                'temperature' => (float) $config['temperature'],
                'topP' => (float) $config['top_p']
            ]
        ];
        
        $headers = [
            'Content-Type: application/json'
        ];
        
        $response = $this->makeHTTPRequest($url, $data, $headers, $config['timeout_seconds']);
        
        if (isset($response['error'])) {
            throw new Exception("Gemini API Error: " . $response['error']['message']);
        }
        
        $content = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        return [
            'success' => true,
            'content' => $content,
            'usage' => [
                'input_tokens' => $response['usageMetadata']['promptTokenCount'] ?? 0,
                'output_tokens' => $response['usageMetadata']['candidatesTokenCount'] ?? 0,
                'total_tokens' => $response['usageMetadata']['totalTokenCount'] ?? 0
            ],
            'data' => $response
        ];
    }
    
    /**
     * Format messages for vision APIs
     */
    private function formatMessagesForVision($messages, $imageUrl, $provider) {
        if ($provider === 'openai') {
            // OpenAI vision format
            foreach ($messages as &$message) {
                if ($message['role'] === 'user') {
                    $message['content'] = [
                        ['type' => 'text', 'text' => $message['content']],
                        ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]]
                    ];
                    break;
                }
            }
        } elseif ($provider === 'anthropic') {
            // Anthropic vision format
            foreach ($messages as &$message) {
                if ($message['role'] === 'user') {
                    $message['content'] = [
                        ['type' => 'text', 'text' => $message['content']],
                        ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => 'image/jpeg', 'data' => $imageUrl]]
                    ];
                    break;
                }
            }
        }
        
        return $messages;
    }
    
    /**
     * Make HTTP request to LLM API
     */
    private function makeHTTPRequest($url, $data, $headers, $timeout = 30) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("HTTP Error: " . $error);
        }
        
        if ($httpCode !== 200) {
            throw new Exception("HTTP Error {$httpCode}: " . $response);
        }
        
        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response: " . json_last_error_msg());
        }
        
        return $decoded;
    }
    
    /**
     * Get API key for provider (admin or user)
     */
    private function getAPIKey($providerName, $userId = null) {
        // Check if user has credits and should use admin key
        if ($userId && $this->userHasCredits($userId)) {
            return $this->getAdminAPIKey($providerName);
        }
        
        // Check if user has their own API key
        if ($userId) {
            $userKey = $this->getUserAPIKey($userId, $providerName);
            if ($userKey) {
                return $userKey;
            }
        }
        
        // Fall back to admin key
        return $this->getAdminAPIKey($providerName);
    }
    
    /**
     * Get API key source (admin or user)
     */
    private function getAPIKeySource($providerName, $userId = null) {
        if ($userId && $this->userHasCredits($userId)) {
            return 'admin';
        }
        
        if ($userId) {
            $userKey = $this->getUserAPIKey($userId, $providerName);
            if ($userKey) {
                return 'user';
            }
        }
        
        return 'admin';
    }
    
    /**
     * Get admin API key for provider
     */
    private function getAdminAPIKey($providerName) {
        switch ($providerName) {
            case 'openai':
                return defined('OPENAI_API_KEY') ? OPENAI_API_KEY : null;
            case 'anthropic':
                return defined('ANTHROPIC_API_KEY') ? ANTHROPIC_API_KEY : null;
            case 'gemini':
                return defined('GEMINI_API_KEY') ? GEMINI_API_KEY : null;
            default:
                return null;
        }
    }
    
    /**
     * Get user's API key for provider
     */
    private function getUserAPIKey($userId, $providerName) {
        try {
            $table = 'user_api_keys';
            $providerTable = 'llm_providers';
            
            $sql = "SELECT uk.api_key_encrypted 
                    FROM {$table} uk 
                    JOIN {$providerTable} p ON uk.provider_id = p.id 
                    WHERE uk.user_id = ? AND p.name = ? AND uk.is_enabled = " . ($this->isMySQL ? 'TRUE' : '1');
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $providerName]);
            $result = $stmt->fetch();
            
            if ($result) {
                // Decrypt the API key (implement proper encryption/decryption)
                return $this->decryptAPIKey($result['api_key_encrypted']);
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error getting user API key: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if user has credits
     */
    private function userHasCredits($userId) {
        // TODO: Implement credit checking logic
        // For now, assume all users have credits
        return true;
    }
    
    /**
     * Calculate estimated cost
     */
    private function calculateCost($providerName, $modelName, $usage) {
        if (!isset($this->costRates[$providerName][$modelName])) {
            return 0.0;
        }
        
        $rates = $this->costRates[$providerName][$modelName];
        $inputCost = ($usage['input_tokens'] ?? 0) * $rates['input'];
        $outputCost = ($usage['output_tokens'] ?? 0) * $rates['output'];
        
        return $inputCost + $outputCost;
    }
    
    /**
     * Log usage for analytics and cost tracking
     */
    private function logUsage($data) {
        try {
            $table = 'llm_usage_logs';
            
            $sql = "INSERT INTO {$table} (
                provider_id, user_id, event_id, prompt_id, prompt_type, model_name,
                input_tokens, output_tokens, total_tokens, estimated_cost,
                response_time_ms, was_successful, error_message, api_key_source,
                request_data, response_data
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['provider_id'],
                $data['user_id'],
                $data['event_id'],
                $data['prompt_id'],
                $data['prompt_type'],
                $data['model_name'],
                $data['input_tokens'],
                $data['output_tokens'],
                $data['total_tokens'],
                $data['estimated_cost'],
                $data['response_time_ms'],
                $data['was_successful'] ? ($this->isMySQL ? 1 : 1) : ($this->isMySQL ? 0 : 0),
                $data['error_message'] ?? null,
                $data['api_key_source'],
                $data['request_data'] ?? null,
                $data['response_data'] ?? null
            ]);
            
            // Update cost tracking summary
            $this->updateCostTracking($data);
            
        } catch (Exception $e) {
            error_log("Error logging LLM usage: " . $e->getMessage());
        }
    }
    
    /**
     * Update daily cost tracking summary
     */
    private function updateCostTracking($data) {
        try {
            $table = 'llm_cost_tracking';
            $today = date('Y-m-d');
            
            $sql = "INSERT INTO {$table} (
                provider_id, user_id, event_category, content_type, date_period,
                total_requests, successful_requests, total_input_tokens,
                total_output_tokens, total_cost, avg_response_time_ms
            ) VALUES (?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                total_requests = total_requests + 1,
                successful_requests = successful_requests + ?,
                total_input_tokens = total_input_tokens + ?,
                total_output_tokens = total_output_tokens + ?,
                total_cost = total_cost + ?,
                avg_response_time_ms = (avg_response_time_ms + ?) / 2";
            
            if (!$this->isMySQL) {
                $sql = "INSERT OR REPLACE INTO {$table} (
                    provider_id, user_id, event_category, content_type, date_period,
                    total_requests, successful_requests, total_input_tokens,
                    total_output_tokens, total_cost, avg_response_time_ms
                ) VALUES (?, ?, ?, ?, ?, 
                    COALESCE((SELECT total_requests FROM {$table} WHERE provider_id = ? AND user_id = ? AND date_period = ?), 0) + 1,
                    COALESCE((SELECT successful_requests FROM {$table} WHERE provider_id = ? AND user_id = ? AND date_period = ?), 0) + ?,
                    COALESCE((SELECT total_input_tokens FROM {$table} WHERE provider_id = ? AND user_id = ? AND date_period = ?), 0) + ?,
                    COALESCE((SELECT total_output_tokens FROM {$table} WHERE provider_id = ? AND user_id = ? AND date_period = ?), 0) + ?,
                    COALESCE((SELECT total_cost FROM {$table} WHERE provider_id = ? AND user_id = ? AND date_period = ?), 0) + ?,
                    (COALESCE((SELECT avg_response_time_ms FROM {$table} WHERE provider_id = ? AND user_id = ? AND date_period = ?), 0) + ?) / 2
                )";
            }
            
            $successfulRequest = $data['was_successful'] ? 1 : 0;
            
            $stmt = $this->pdo->prepare($sql);
            if ($this->isMySQL) {
                $stmt->execute([
                    $data['provider_id'],
                    $data['user_id'],
                    null, // event_category - will be set later
                    $data['prompt_type'],
                    $today,
                    $successfulRequest,
                    $data['input_tokens'],
                    $data['output_tokens'],
                    $data['estimated_cost'],
                    $data['response_time_ms'],
                    $successfulRequest,
                    $data['input_tokens'],
                    $data['output_tokens'],
                    $data['estimated_cost'],
                    $data['response_time_ms']
                ]);
            } else {
                // SQLite version with more parameters
                $params = [
                    $data['provider_id'], $data['user_id'], null, $data['prompt_type'], $today,
                    $data['provider_id'], $data['user_id'], $today,
                    $data['provider_id'], $data['user_id'], $today, $successfulRequest,
                    $data['provider_id'], $data['user_id'], $today, $data['input_tokens'],
                    $data['provider_id'], $data['user_id'], $today, $data['output_tokens'],
                    $data['provider_id'], $data['user_id'], $today, $data['estimated_cost'],
                    $data['provider_id'], $data['user_id'], $today, $data['response_time_ms']
                ];
                $stmt->execute($params);
            }
            
        } catch (Exception $e) {
            error_log("Error updating cost tracking: " . $e->getMessage());
        }
    }
    
    /**
     * Get provider by ID
     */
    private function getProviderById($providerId) {
        foreach ($this->providers as $provider) {
            if ($provider['id'] == $providerId) {
                return $provider;
            }
        }
        return null;
    }
    
    /**
     * Encrypt API key for storage
     */
    private function encryptAPIKey($apiKey) {
        // Simple encryption - in production, use proper encryption
        return base64_encode($apiKey);
    }
    
    /**
     * Decrypt API key from storage
     */
    private function decryptAPIKey($encryptedKey) {
        // Simple decryption - in production, use proper decryption
        return base64_decode($encryptedKey);
    }
    
    /**
     * Get cost analytics
     */
    public function getCostAnalytics($userId = null, $dateRange = 7) {
        try {
            $table = 'llm_cost_tracking';
            $providerTable = 'llm_providers';
            
            $sql = "SELECT 
                        p.name as provider_name,
                        p.display_name,
                        ct.content_type,
                        SUM(ct.total_requests) as requests,
                        SUM(ct.successful_requests) as successful_requests,
                        SUM(ct.total_cost) as total_cost,
                        AVG(ct.avg_response_time_ms) as avg_response_time
                    FROM {$table} ct
                    JOIN {$providerTable} p ON ct.provider_id = p.id
                    WHERE ct.date_period >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            
            $params = [$dateRange];
            
            if ($userId) {
                $sql .= " AND ct.user_id = ?";
                $params[] = $userId;
            }
            
            $sql .= " GROUP BY p.id, ct.content_type ORDER BY total_cost DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error getting cost analytics: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Global helper function
 */
function getLLMManager() {
    static $manager = null;
    if ($manager === null) {
        $manager = new LLMManager();
    }
    return $manager;
}
?>