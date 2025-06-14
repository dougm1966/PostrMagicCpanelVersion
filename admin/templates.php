<?php
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
requireAdmin();

// Set page title
$page_title = "Email Templates";

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = getDBConnection();
        
        // Create email_templates table if it doesn't exist
        $createTable = "
            CREATE TABLE IF NOT EXISTS email_templates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                type VARCHAR(100) NOT NULL,
                is_active BOOLEAN DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )";
        
        if (DB_TYPE === 'mysql') {
            $createTable = str_replace('INTEGER PRIMARY KEY AUTOINCREMENT', 'INT AUTO_INCREMENT PRIMARY KEY', $createTable);
            $createTable = str_replace('DATETIME DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT NOW()', $createTable);
        }
        
        $pdo->exec($createTable);
        
        if ($action === 'create' || $action === 'update') {
            $name = $_POST['name'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $content = $_POST['content'] ?? '';
            $type = $_POST['type'] ?? '';
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO email_templates (name, subject, content, type, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $subject, $content, $type, $is_active]);
                $message = 'Email template created successfully.';
            } else {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE email_templates SET name = ?, subject = ?, content = ?, type = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$name, $subject, $content, $type, $is_active, $id]);
                $message = 'Email template updated successfully.';
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['id']);
            $stmt = $pdo->prepare("DELETE FROM email_templates WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Email template deleted successfully.';
        }
        
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get templates
$templates = [];
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM email_templates ORDER BY created_at DESC");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Table might not exist yet
}

// Get editing template if specified
$editing_template = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    foreach ($templates as $template) {
        if ($template['id'] == $edit_id) {
            $editing_template = $template;
            break;
        }
    }
}

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
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Email Templates</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage system email templates and notifications</p>
                </div>
            </div>
            <div>
                <button onclick="toggleTemplateForm()" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <i data-lucide="plus" class="inline-block mr-2 h-4 w-4"></i>
                    New Template
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

        <!-- Template Form -->
        <div id="template-form" class="<?= $editing_template ? '' : 'hidden' ?> bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <?= $editing_template ? 'Edit Template' : 'Create New Template' ?>
                </h3>
            </div>
            <form method="POST" class="p-6 space-y-8">
                <input type="hidden" name="action" value="<?= $editing_template ? 'update' : 'create' ?>">
                <?php if ($editing_template): ?>
                <input type="hidden" name="id" value="<?= $editing_template['id'] ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Template Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($editing_template['name'] ?? '') ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Template Type</label>
                        <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                            <option value="">Select Type</option>
                            <option value="welcome" <?= ($editing_template['type'] ?? '') === 'welcome' ? 'selected' : '' ?>>Welcome Email</option>
                            <option value="password_reset" <?= ($editing_template['type'] ?? '') === 'password_reset' ? 'selected' : '' ?>>Password Reset</option>
                            <option value="event_reminder" <?= ($editing_template['type'] ?? '') === 'event_reminder' ? 'selected' : '' ?>>Event Reminder</option>
                            <option value="event_confirmation" <?= ($editing_template['type'] ?? '') === 'event_confirmation' ? 'selected' : '' ?>>Event Confirmation</option>
                            <option value="notification" <?= ($editing_template['type'] ?? '') === 'notification' ? 'selected' : '' ?>>System Notification</option>
                        </select>
                    </div>
                </div>
                
                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Subject</label>
                    <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($editing_template['subject'] ?? '') ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                </div>
                
                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Content</label>
                    <textarea id="content" name="content" rows="10" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3"><?= htmlspecialchars($editing_template['content'] ?? '') ?></textarea>
                    <p class="mt-2 text-sm text-gray-500">Use {{variable_name}} for dynamic content. Available variables: {{user_name}}, {{site_name}}, {{event_name}}, {{event_date}}, {{event_location}}, {{event_url}}</p>
                </div>
                
                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" <?= ($editing_template['is_active'] ?? 1) ? 'checked' : '' ?> class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Active Template</label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="cancelEdit()" class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-6 py-3 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <?= $editing_template ? 'Update Template' : 'Create Template' ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Templates List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Email Templates</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php if (empty($templates)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i data-lucide="mail" class="mx-auto h-8 w-8 mb-2"></i>
                                <p>No email templates found.</p>
                                <p class="text-sm">Create your first template to get started.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($templates as $template): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($template['name']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $template['type']))) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($template['subject']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $template['is_active'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' ?>">
                                    <?= $template['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="?edit=<?= $template['id'] ?>" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                                        <i data-lucide="edit" class="h-4 w-4"></i>
                                    </a>
                                    <button onclick="deleteTemplate(<?= $template['id'] ?>)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Delete confirmation form -->
<form id="delete-form" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="delete-id">
</form>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    function toggleTemplateForm() {
        const form = document.getElementById('template-form');
        form.classList.toggle('hidden');
        
        if (!form.classList.contains('hidden')) {
            document.getElementById('name').focus();
        }
    }
    
    function cancelEdit() {
        window.location.href = window.location.pathname;
    }
    
    function deleteTemplate(id) {
        if (confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
            document.getElementById('delete-id').value = id;
            document.getElementById('delete-form').submit();
        }
    }
</script>

<?php require_once __DIR__ . '/../includes/dashboard-footer.php'; ?>