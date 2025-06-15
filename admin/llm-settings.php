<?php
/**
 * LLM Settings Admin Interface
 * Manage LLM providers, configurations, and prompts
 */

require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/llm-manager.php';
require_once '../includes/llm-prompt-manager.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$llmManager = getLLMManager();
$promptManager = getLLMPromptManager();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'save_provider_config':
            // Save provider configuration
            $result = saveProviderConfiguration($_POST);
            echo json_encode($result);
            exit;
            
        case 'save_prompt':
            // Save prompt
            $promptData = [
                'prompt_type' => $_POST['prompt_type'],
                'event_category' => $_POST['event_category'] ?: null,
                'content_type' => $_POST['content_type'] ?: null,
                'system_prompt' => $_POST['system_prompt'],
                'user_prompt' => $_POST['user_prompt'],
                'assistant_prompt' => $_POST['assistant_prompt'] ?: null,
                'version_notes' => $_POST['version_notes'] ?: null,
                'created_by_user_id' => getCurrentUser()['id']
            ];
            
            $result = $promptManager->savePrompt($promptData);
            echo json_encode($result);
            exit;
            
        case 'test_prompt':
            // Test prompt with sample data
            $testData = json_decode($_POST['test_data'], true);
            $result = $promptManager->testPrompt($_POST['prompt_id'], $testData, getCurrentUser()['id']);
            echo json_encode($result);
            exit;
            
        case 'get_cost_analytics':
            // Get cost analytics
            $result = $llmManager->getCostAnalytics(null, $_POST['days'] ?? 7);
            echo json_encode(['success' => true, 'data' => $result]);
            exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

// Get current configurations and prompts
$providers = getProviders();
$configurations = getConfigurations();
$prompts = $promptManager->getAllPrompts();
$categories = getEventCategories();

function saveProviderConfiguration($data) {
    // Implementation for saving provider config
    // This would update the llm_configurations table
    return ['success' => true, 'message' => 'Configuration saved'];
}

function getProviders() {
    $pdo = getDBConnection();
    $isMySQL = (DB_TYPE === 'mysql');
    $table = $isMySQL ? 'llm_providers' : 'llm_providers_sqlite';
    
    $sql = "SELECT * FROM {$table} ORDER BY display_name";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getConfigurations() {
    $pdo = getDBConnection();
    $isMySQL = (DB_TYPE === 'mysql');
    $configTable = $isMySQL ? 'llm_configurations' : 'llm_configurations_sqlite';
    $providerTable = $isMySQL ? 'llm_providers' : 'llm_providers_sqlite';
    
    $sql = "SELECT c.*, p.display_name as provider_name 
            FROM {$configTable} c 
            JOIN {$providerTable} p ON c.provider_id = p.id 
            ORDER BY c.content_type, c.priority_order";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getEventCategories() {
    $pdo = getDBConnection();
    $isMySQL = (DB_TYPE === 'mysql');
    $table = $isMySQL ? 'event_categories' : 'event_categories_sqlite';
    
    $sql = "SELECT * FROM {$table} WHERE is_enabled = " . ($isMySQL ? 'TRUE' : '1') . " ORDER BY category_name";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LLM Settings - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .nav-pills .nav-link.active { background-color: #0d6efd; }
        .prompt-editor { min-height: 200px; }
        .analytics-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .provider-status { padding: 2px 8px; border-radius: 12px; font-size: 0.8em; }
        .provider-enabled { background-color: #d4edda; color: #155724; }
        .provider-disabled { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php include '../layouts/admin-header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-robot me-2"></i>LLM Management</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills flex-column" id="llm-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="providers-tab" data-bs-toggle="pill" href="#providers" role="tab">
                                    <i class="fas fa-server me-2"></i>Providers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="prompts-tab" data-bs-toggle="pill" href="#prompts" role="tab">
                                    <i class="fas fa-edit me-2"></i>Prompts
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="analytics-tab" data-bs-toggle="pill" href="#analytics" role="tab">
                                    <i class="fas fa-chart-line me-2"></i>Analytics
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="testing-tab" data-bs-toggle="pill" href="#testing" role="tab">
                                    <i class="fas fa-flask me-2"></i>Testing
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="tab-content" id="llm-tabContent">
                    
                    <!-- Providers Tab -->
                    <div class="tab-pane fade show active" id="providers" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-server me-2"></i>LLM Provider Configuration</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addConfigModal">
                                    <i class="fas fa-plus me-1"></i>Add Configuration
                                </button>
                            </div>
                            <div class="card-body">
                                
                                <!-- Provider Status Overview -->
                                <div class="row mb-4">
                                    <?php foreach ($providers as $provider): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-brain fa-2x mb-2 text-primary"></i>
                                                <h6><?php echo htmlspecialchars($provider['display_name']); ?></h6>
                                                <span class="provider-status <?php echo $provider['is_enabled'] ? 'provider-enabled' : 'provider-disabled'; ?>">
                                                    <?php echo $provider['is_enabled'] ? 'Enabled' : 'Disabled'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Configurations Table -->
                                <h6>Current Configurations</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Provider</th>
                                                <th>Content Type</th>
                                                <th>Category</th>
                                                <th>Model</th>
                                                <th>Priority</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($configurations as $config): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($config['provider_name']); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($config['content_type']); ?></span></td>
                                                <td><?php echo htmlspecialchars($config['event_category'] ?: 'Global'); ?></td>
                                                <td><code><?php echo htmlspecialchars($config['model_name']); ?></code></td>
                                                <td><?php echo $config['priority_order']; ?></td>
                                                <td>
                                                    <span class="badge <?php echo $config['is_enabled'] ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo $config['is_enabled'] ? 'Enabled' : 'Disabled'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editConfig(<?php echo $config['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Prompts Tab -->
                    <div class="tab-pane fade" id="prompts" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-edit me-2"></i>Prompt Management</h5>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPromptModal">
                                    <i class="fas fa-plus me-1"></i>Add Prompt
                                </button>
                            </div>
                            <div class="card-body">
                                
                                <!-- Prompt Types Overview -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <i class="fas fa-eye fa-2x mb-2"></i>
                                                <h6>Vision Analysis</h6>
                                                <small>Extract data from posters</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <i class="fas fa-tags fa-2x mb-2"></i>
                                                <h6>Category Detection</h6>
                                                <small>Categorize events</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <i class="fas fa-share-alt fa-2x mb-2"></i>
                                                <h6>Content Creation</h6>
                                                <small>Generate social media posts</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Prompts Table -->
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Category</th>
                                                <th>Content Type</th>
                                                <th>Version</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($prompts as $prompt): ?>
                                            <tr>
                                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($prompt['prompt_type']); ?></span></td>
                                                <td><?php echo htmlspecialchars($prompt['event_category'] ?: 'Global'); ?></td>
                                                <td><?php echo htmlspecialchars($prompt['content_type'] ?: 'N/A'); ?></td>
                                                <td>v<?php echo $prompt['version_number']; ?></td>
                                                <td><?php echo date('M j, Y', strtotime($prompt['created_date'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editPrompt(<?php echo $prompt['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" onclick="testPrompt(<?php echo $prompt['id']; ?>)">
                                                        <i class="fas fa-flask"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Analytics Tab -->
                    <div class="tab-pane fade" id="analytics" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="card analytics-card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <h3 id="total-requests">-</h3>
                                                <small>Total Requests</small>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h3 id="total-cost">-</h3>
                                                <small>Total Cost (USD)</small>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h3 id="avg-response-time">-</h3>
                                                <small>Avg Response Time</small>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h3 id="success-rate">-</h3>
                                                <small>Success Rate</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-line me-2"></i>Usage Analytics</h5>
                            </div>
                            <div class="card-body">
                                <div id="analytics-table"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Testing Tab -->
                    <div class="tab-pane fade" id="testing" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-flask me-2"></i>Prompt Testing</h5>
                            </div>
                            <div class="card-body">
                                <form id="testForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Prompt Type</label>
                                                <select class="form-select" name="prompt_type" required>
                                                    <option value="">Select Type</option>
                                                    <option value="vision_analysis">Vision Analysis</option>
                                                    <option value="category_detection">Category Detection</option>
                                                    <option value="content_creation">Content Creation</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Event Category</label>
                                                <select class="form-select" name="event_category">
                                                    <option value="">Global</option>
                                                    <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['category_name']; ?>">
                                                        <?php echo htmlspecialchars($category['display_name']); ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Test Data (JSON)</label>
                                        <textarea class="form-control" name="test_data" rows="6" placeholder='{"event_title": "Sample Event", "venue_name": "Sample Venue"}'></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-play me-1"></i>Run Test
                                    </button>
                                </form>
                                
                                <div id="test-results" class="mt-4" style="display: none;">
                                    <h6>Test Results</h6>
                                    <pre id="test-output" class="bg-light p-3 rounded"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Add Configuration Modal -->
    <div class="modal fade" id="addConfigModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Provider Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="configForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Provider</label>
                                    <select class="form-select" name="provider_id" required>
                                        <option value="">Select Provider</option>
                                        <?php foreach ($providers as $provider): ?>
                                        <option value="<?php echo $provider['id']; ?>"><?php echo htmlspecialchars($provider['display_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Content Type</label>
                                    <select class="form-select" name="content_type" required>
                                        <option value="">Select Type</option>
                                        <option value="vision_analysis">Vision Analysis</option>
                                        <option value="category_detection">Category Detection</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="linkedin">LinkedIn</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Model Name</label>
                            <input type="text" class="form-control" name="model_name" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Tokens</label>
                                    <input type="number" class="form-control" name="max_tokens" value="1000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Temperature</label>
                                    <input type="number" class="form-control" name="temperature" value="0.7" min="0" max="2" step="0.1">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveConfig()">Save Configuration</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Prompt Modal -->
    <div class="modal fade" id="addPromptModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Prompt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="promptForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Prompt Type</label>
                                    <select class="form-select" name="prompt_type" required>
                                        <option value="">Select Type</option>
                                        <option value="vision_analysis">Vision Analysis</option>
                                        <option value="category_detection">Category Detection</option>
                                        <option value="content_creation">Content Creation</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Event Category</label>
                                    <select class="form-select" name="event_category">
                                        <option value="">Global</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['category_name']; ?>">
                                            <?php echo htmlspecialchars($category['display_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Content Type</label>
                                    <select class="form-select" name="content_type">
                                        <option value="">N/A</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="linkedin">LinkedIn</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">System Prompt</label>
                            <textarea class="form-control prompt-editor" name="system_prompt" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">User Prompt</label>
                            <textarea class="form-control prompt-editor" name="user_prompt" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Assistant Prompt (Optional)</label>
                            <textarea class="form-control" name="assistant_prompt" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Version Notes</label>
                            <input type="text" class="form-control" name="version_notes">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="savePrompt()">Save Prompt</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize analytics when analytics tab is shown
        document.getElementById('analytics-tab').addEventListener('click', function() {
            loadAnalytics();
        });
        
        function loadAnalytics() {
            fetch('llm-settings.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get_cost_analytics&days=7'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateAnalyticsDisplay(data.data);
                }
            });
        }
        
        function updateAnalyticsDisplay(analytics) {
            // Update summary cards
            let totalRequests = analytics.reduce((sum, item) => sum + parseInt(item.requests), 0);
            let totalCost = analytics.reduce((sum, item) => sum + parseFloat(item.total_cost), 0);
            let avgResponseTime = analytics.reduce((sum, item) => sum + parseInt(item.avg_response_time), 0) / analytics.length;
            let successRate = analytics.reduce((sum, item) => sum + parseInt(item.successful_requests), 0) / totalRequests * 100;
            
            document.getElementById('total-requests').textContent = totalRequests.toLocaleString();
            document.getElementById('total-cost').textContent = '$' + totalCost.toFixed(4);
            document.getElementById('avg-response-time').textContent = Math.round(avgResponseTime) + 'ms';
            document.getElementById('success-rate').textContent = Math.round(successRate) + '%';
            
            // Create analytics table
            let tableHtml = '<table class="table table-striped"><thead><tr><th>Provider</th><th>Content Type</th><th>Requests</th><th>Cost</th><th>Avg Time</th></tr></thead><tbody>';
            analytics.forEach(item => {
                tableHtml += `<tr>
                    <td>${item.provider_name}</td>
                    <td><span class="badge bg-secondary">${item.content_type}</span></td>
                    <td>${item.requests}</td>
                    <td>$${parseFloat(item.total_cost).toFixed(4)}</td>
                    <td>${Math.round(item.avg_response_time)}ms</td>
                </tr>`;
            });
            tableHtml += '</tbody></table>';
            document.getElementById('analytics-table').innerHTML = tableHtml;
        }
        
        function saveConfig() {
            const form = document.getElementById('configForm');
            const formData = new FormData(form);
            formData.append('action', 'save_provider_config');
            
            fetch('llm-settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Configuration saved successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }
        
        function savePrompt() {
            const form = document.getElementById('promptForm');
            const formData = new FormData(form);
            formData.append('action', 'save_prompt');
            
            fetch('llm-settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Prompt saved successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }
        
        function editConfig(configId) {
            // Implementation for editing configuration
            alert('Edit config ' + configId);
        }
        
        function editPrompt(promptId) {
            // Implementation for editing prompt
            alert('Edit prompt ' + promptId);
        }
        
        function testPrompt(promptId) {
            // Implementation for testing prompt
            alert('Test prompt ' + promptId);
        }
        
        // Test form submission
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'test_prompt');
            
            fetch('llm-settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('test-results').style.display = 'block';
                document.getElementById('test-output').textContent = JSON.stringify(data, null, 2);
            });
        });
    </script>
</body>
</html>