<?php
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
requireAdmin();

// Set page title
$page_title = "System Logs";

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = getDBConnection();
        
        if ($action === 'clear_logs') {
            $log_type = $_POST['log_type'] ?? '';
            if ($log_type && $log_type !== 'all') {
                $stmt = $pdo->prepare("DELETE FROM system_logs WHERE log_type = ?");
                $stmt->execute([$log_type]);
                $message = ucfirst($log_type) . ' logs cleared successfully.';
            } elseif ($log_type === 'all') {
                $pdo->exec("DELETE FROM system_logs");
                $message = 'All logs cleared successfully.';
            }
        }
        
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Create system_logs table if it doesn't exist
try {
    $pdo = getDBConnection();
    $createTable = "
        CREATE TABLE IF NOT EXISTS system_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            log_type VARCHAR(50) NOT NULL,
            level VARCHAR(20) NOT NULL,
            message TEXT NOT NULL,
            context TEXT,
            user_id INTEGER,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
    
    if (DB_TYPE === 'mysql') {
        $createTable = str_replace('INTEGER PRIMARY KEY AUTOINCREMENT', 'INT AUTO_INCREMENT PRIMARY KEY', $createTable);
        $createTable = str_replace('DATETIME DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT NOW()', $createTable);
    }
    
    $pdo->exec($createTable);
    
    // Insert some sample logs if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM system_logs");
    if ($stmt->fetchColumn() == 0) {
        $sampleLogs = [
            ['security', 'info', 'User login successful', json_encode(['username' => 'admin']), $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? ''],
            ['application', 'info', 'System started', null, null, '127.0.0.1', 'System'],
            ['security', 'warning', 'Failed login attempt', json_encode(['username' => 'unknown']), null, '192.168.1.100', 'Mozilla/5.0'],
            ['database', 'error', 'Connection timeout', json_encode(['timeout' => 30]), null, '127.0.0.1', 'System'],
            ['application', 'info', 'Email template created', json_encode(['template_id' => 1]), $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? ''],
            ['security', 'info', 'Password changed', json_encode(['user_id' => $_SESSION['user_id']]), $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $_SERVER['HTTP_USER_AGENT'] ?? ''],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO system_logs (log_type, level, message, context, user_id, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($sampleLogs as $log) {
            $stmt->execute($log);
        }
    }
} catch (Exception $e) {
    // Handle table creation error
}

// Get filter parameters
$filter_type = $_GET['type'] ?? 'all';
$filter_level = $_GET['level'] ?? 'all';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get logs with filters
$logs = [];
$total_logs = 0;
try {
    $pdo = getDBConnection();
    
    // Build query conditions
    $conditions = [];
    $params = [];
    
    if ($filter_type !== 'all') {
        $conditions[] = "log_type = ?";
        $params[] = $filter_type;
    }
    
    if ($filter_level !== 'all') {
        $conditions[] = "level = ?";
        $params[] = $filter_level;
    }
    
    $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    
    // Get total count
    $countQuery = "SELECT COUNT(*) FROM system_logs $whereClause";
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($params);
    $total_logs = $stmt->fetchColumn();
    
    // Get logs with pagination
    $query = "SELECT sl.*, u.username 
              FROM system_logs sl 
              LEFT JOIN users u ON sl.user_id = u.id 
              $whereClause 
              ORDER BY sl.created_at DESC 
              LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = 'Error fetching logs: ' . $e->getMessage();
}

$total_pages = ceil($total_logs / $per_page);

// Include dashboard header
require_once __DIR__ . '/../includes/dashboard-header.php';
?>

<!-- Main Content -->
<main class="main-content" id="main-content">
    <!-- Top Bar -->
    <div class="top-bar bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 md:px-6 beautiful-shadow relative z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button id="sidebar-toggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i data-lucide="menu" class="h-5 w-5 text-gray-600 dark:text-gray-300"></i>
                </button>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">System Logs</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitor system activity and security events</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button onclick="showClearModal()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <i data-lucide="trash-2" class="inline-block mr-2 h-4 w-4"></i>
                    Clear Logs
                </button>
                <button onclick="refreshLogs()" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <i data-lucide="refresh-cw" class="inline-block mr-2 h-4 w-4"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4 md:p-6 space-y-6">
        <!-- Messages -->
        <?php if ($message): ?>
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-md">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-md">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Log Type</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                        <option value="all" <?= $filter_type === 'all' ? 'selected' : '' ?>>All Types</option>
                        <option value="security" <?= $filter_type === 'security' ? 'selected' : '' ?>>Security</option>
                        <option value="application" <?= $filter_type === 'application' ? 'selected' : '' ?>>Application</option>
                        <option value="database" <?= $filter_type === 'database' ? 'selected' : '' ?>>Database</option>
                        <option value="email" <?= $filter_type === 'email' ? 'selected' : '' ?>>Email</option>
                    </select>
                </div>
                
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Log Level</label>
                    <select id="level" name="level" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                        <option value="all" <?= $filter_level === 'all' ? 'selected' : '' ?>>All Levels</option>
                        <option value="info" <?= $filter_level === 'info' ? 'selected' : '' ?>>Info</option>
                        <option value="warning" <?= $filter_level === 'warning' ? 'selected' : '' ?>>Warning</option>
                        <option value="error" <?= $filter_level === 'error' ? 'selected' : '' ?>>Error</option>
                        <option value="critical" <?= $filter_level === 'critical' ? 'selected' : '' ?>>Critical</option>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Log Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <?php
            $stats = [];
            try {
                $stmt = $pdo->query("SELECT level, COUNT(*) as count FROM system_logs GROUP BY level");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $stats[$row['level']] = $row['count'];
                }
            } catch (Exception $e) {}
            ?>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i data-lucide="info" class="h-4 w-4 text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Info</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?= $stats['info'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="h-4 w-4 text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Warning</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?= $stats['warning'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <i data-lucide="x-circle" class="h-4 w-4 text-red-600 dark:text-red-400"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Error</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?= $stats['error'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <i data-lucide="zap" class="h-4 w-4 text-purple-600 dark:text-purple-400"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Critical</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?= $stats['critical'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    System Logs (<?= number_format($total_logs) ?> total)
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i data-lucide="file-text" class="mx-auto h-8 w-8 mb-2"></i>
                                <p>No logs found.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="showLogDetails(<?= htmlspecialchars(json_encode($log)) ?>)">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $levelColors = [
                                    'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'error' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    'critical' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
                                ];
                                $colorClass = $levelColors[$log['level']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $colorClass ?>">
                                    <?= ucfirst($log['level']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900 dark:text-white"><?= ucfirst($log['log_type']) ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($log['message']) ?></div>
                                <?php if ($log['ip_address']): ?>
                                <div class="text-xs text-gray-500 dark:text-gray-400">IP: <?= htmlspecialchars($log['ip_address']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= $log['username'] ? htmlspecialchars($log['username']) : 'System' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= date('M j, Y g:i A', strtotime($log['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <?= (($page - 1) * $per_page) + 1 ?> to <?= min($page * $per_page, $total_logs) ?> of <?= number_format($total_logs) ?> results
                    </div>
                    <div class="flex space-x-1">
                        <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="px-3 py-2 text-sm <?= $i === $page ? 'bg-purple-600 text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' ?> rounded">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Log Details Modal -->
<div id="log-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Log Details</h3>
                <button onclick="hideLogDetails()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>
            <div id="log-details-content" class="space-y-4">
                <!-- Details will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div id="clear-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Clear Logs</h3>
            <form method="POST">
                <input type="hidden" name="action" value="clear_logs">
                <div class="mb-4">
                    <label for="log_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Log Type to Clear</label>
                    <select id="log_type" name="log_type" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select...</option>
                        <option value="all">All Logs</option>
                        <option value="security">Security Logs</option>
                        <option value="application">Application Logs</option>
                        <option value="database">Database Logs</option>
                        <option value="email">Email Logs</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideClearModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Clear Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    function showLogDetails(log) {
        const content = document.getElementById('log-details-content');
        const context = log.context ? JSON.parse(log.context) : null;
        
        content.innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Level:</label>
                    <p class="text-sm text-gray-900 dark:text-white">${log.level}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Type:</label>
                    <p class="text-sm text-gray-900 dark:text-white">${log.log_type}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">User:</label>
                    <p class="text-sm text-gray-900 dark:text-white">${log.username || 'System'}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">IP Address:</label>
                    <p class="text-sm text-gray-900 dark:text-white">${log.ip_address || 'N/A'}</p>
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Message:</label>
                <p class="text-sm text-gray-900 dark:text-white">${log.message}</p>
            </div>
            ${context ? `
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Context:</label>
                <pre class="text-sm text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 p-2 rounded overflow-auto">${JSON.stringify(context, null, 2)}</pre>
            </div>
            ` : ''}
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">User Agent:</label>
                <p class="text-sm text-gray-900 dark:text-white break-all">${log.user_agent || 'N/A'}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Timestamp:</label>
                <p class="text-sm text-gray-900 dark:text-white">${new Date(log.created_at).toLocaleString()}</p>
            </div>
        `;
        
        document.getElementById('log-modal').classList.remove('hidden');
    }
    
    function hideLogDetails() {
        document.getElementById('log-modal').classList.add('hidden');
    }
    
    function showClearModal() {
        document.getElementById('clear-modal').classList.remove('hidden');
    }
    
    function hideClearModal() {
        document.getElementById('clear-modal').classList.add('hidden');
    }
    
    function refreshLogs() {
        window.location.reload();
    }
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        const logModal = document.getElementById('log-modal');
        const clearModal = document.getElementById('clear-modal');
        
        if (event.target === logModal) {
            hideLogDetails();
        }
        if (event.target === clearModal) {
            hideClearModal();
        }
    }
</script>

<?php require_once __DIR__ . '/../includes/dashboard-footer.php'; ?>
