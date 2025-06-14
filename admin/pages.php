<?php
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
requireAdmin();

// Set page title
$page_title = "Static Pages";

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = getDBConnection();
        
        // Create static_pages table if it doesn't exist
        $createTable = "
            CREATE TABLE IF NOT EXISTS static_pages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                content TEXT NOT NULL,
                meta_description TEXT,
                is_published BOOLEAN DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )";
        
        if (DB_TYPE === 'mysql') {
            $createTable = str_replace('INTEGER PRIMARY KEY AUTOINCREMENT', 'INT AUTO_INCREMENT PRIMARY KEY', $createTable);
            $createTable = str_replace('DATETIME DEFAULT CURRENT_TIMESTAMP', 'DATETIME DEFAULT NOW()', $createTable);
        }
        
        $pdo->exec($createTable);
        
        if ($action === 'create' || $action === 'update') {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $content = $_POST['content'] ?? '';
            $meta_description = $_POST['meta_description'] ?? '';
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            
            // Generate slug if empty
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            }
            
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO static_pages (title, slug, content, meta_description, is_published) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $content, $meta_description, $is_published]);
                $message = 'Page created successfully.';
            } else {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE static_pages SET title = ?, slug = ?, content = ?, meta_description = ?, is_published = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$title, $slug, $content, $meta_description, $is_published, $id]);
                $message = 'Page updated successfully.';
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['id']);
            $stmt = $pdo->prepare("DELETE FROM static_pages WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Page deleted successfully.';
        }
        
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Get pages
$pages = [];
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM static_pages ORDER BY created_at DESC");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Table might not exist yet
}

// Get editing page if specified
$editing_page = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    foreach ($pages as $page) {
        if ($page['id'] == $edit_id) {
            $editing_page = $page;
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
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Static Pages</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage website content and static pages</p>
                </div>
            </div>
            <div>
                <button onclick="togglePageForm()" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <i data-lucide="plus" class="inline-block mr-2 h-4 w-4"></i>
                    New Page
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

        <!-- Page Form -->
        <div id="page-form" class="<?= $editing_page ? '' : 'hidden' ?> bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <?= $editing_page ? 'Edit Page' : 'Create New Page' ?>
                </h3>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <input type="hidden" name="action" value="<?= $editing_page ? 'update' : 'create' ?>">
                <?php if ($editing_page): ?>
                <input type="hidden" name="id" value="<?= $editing_page['id'] ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Page Title</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($editing_page['title'] ?? '') ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white" onkeyup="generateSlug()">
                    </div>
                    
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL Slug</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">/page/</span>
                            <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($editing_page['slug'] ?? '') ?>" required class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 dark:border-gray-600 focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Description</label>
                    <input type="text" id="meta_description" name="meta_description" value="<?= htmlspecialchars($editing_page['meta_description'] ?? '') ?>" maxlength="160" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    <p class="mt-1 text-sm text-gray-500">SEO meta description (max 160 characters)</p>
                </div>
                
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Page Content</label>
                    <textarea id="content" name="content" rows="15" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($editing_page['content'] ?? '') ?></textarea>
                    <p class="mt-1 text-sm text-gray-500">You can use HTML and basic styling. For complex layouts, consider using a page builder.</p>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is_published" name="is_published" <?= ($editing_page['is_published'] ?? 1) ? 'checked' : '' ?> class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="is_published" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Published (visible to public)</label>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="cancelEdit()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <?= $editing_page ? 'Update Page' : 'Create Page' ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Pages List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pages</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">URL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Updated</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php if (empty($pages)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i data-lucide="file-text" class="mx-auto h-8 w-8 mb-2"></i>
                                <p>No pages found.</p>
                                <p class="text-sm">Create your first page to get started.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($pages as $page): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($page['title']) ?></div>
                                <?php if ($page['meta_description']): ?>
                                <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs"><?= htmlspecialchars($page['meta_description']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="text-sm text-gray-600 dark:text-gray-400">/page/<?= htmlspecialchars($page['slug']) ?></code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $page['is_published'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' ?>">
                                    <?= $page['is_published'] ? 'Published' : 'Draft' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= date('M j, Y', strtotime($page['updated_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <?php if ($page['is_published']): ?>
                                    <a href="/page/<?= htmlspecialchars($page['slug']) ?>" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View Page">
                                        <i data-lucide="external-link" class="h-4 w-4"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="?edit=<?= $page['id'] ?>" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300" title="Edit">
                                        <i data-lucide="edit" class="h-4 w-4"></i>
                                    </a>
                                    <button onclick="deletePage(<?= $page['id'] ?>)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
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
    
    function togglePageForm() {
        const form = document.getElementById('page-form');
        form.classList.toggle('hidden');
        
        if (!form.classList.contains('hidden')) {
            document.getElementById('title').focus();
        }
    }
    
    function cancelEdit() {
        window.location.href = window.location.pathname;
    }
    
    function deletePage(id) {
        if (confirm('Are you sure you want to delete this page? This action cannot be undone.')) {
            document.getElementById('delete-id').value = id;
            document.getElementById('delete-form').submit();
        }
    }
    
    function generateSlug() {
        const title = document.getElementById('title').value;
        const slug = title.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .replace(/-+/g, '-') // Replace multiple hyphens with single
            .trim('-'); // Remove leading/trailing hyphens
            
        document.getElementById('slug').value = slug;
    }
</script>

<?php require_once __DIR__ . '/../includes/dashboard-footer.php'; ?>