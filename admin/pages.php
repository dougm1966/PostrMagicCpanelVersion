<?php
// Include auth helper and require admin access
require_once __DIR__ . '/../includes/auth-helper.php';
requireAdmin();

// Set page title
$page_title = "Static Pages";

// Initialize variables
$message = '';
$error = '';
$editing_page = null;

// Database connection
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
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $content = $_POST['content'] ?? '';
        $meta_description = trim($_POST['meta_description'] ?? '');
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        
        // Validate required fields
        if (empty($title)) {
            throw new Exception('Title is required');
        }
        
        // Generate slug if empty
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            $slug = preg_replace('/-+/', '-', $slug); // Replace multiple dashes with single
            $slug = trim($slug, '-'); // Trim dashes from beginning/end
            
            // Ensure slug is not empty
            if (empty($slug)) {
                $slug = 'page-' . time();
            }
            
            // Make slug unique
            $original_slug = $slug;
            $counter = 1;
            
            do {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM static_pages WHERE slug = ?" . 
                    (($action === 'update' && !empty($_POST['id'])) ? " AND id != ?" : ""));
                
                $params = [$slug];
                if ($action === 'update' && !empty($_POST['id'])) {
                    $params[] = intval($_POST['id']);
                }
                
                $stmt->execute($params);
                $exists = $stmt->fetchColumn() > 0;
                
                if ($exists) {
                    $slug = $original_slug . '-' . $counter++;
                }
            } while ($exists && $counter < 100);
        }
        
        // Validate slug format
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new Exception('Slug can only contain lowercase letters, numbers, and hyphens');
        }
        
        if ($action === 'create') {
            // Create new page
            $stmt = $pdo->prepare("INSERT INTO static_pages (title, slug, content, meta_description, is_published) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $content, $meta_description, $is_published]);
            $message = 'Page created successfully.';
            
            // Redirect to edit page
            header("Location: pages.php?edit=" . $pdo->lastInsertId() . "&created=1");
            exit();
            
        } elseif ($action === 'update' && !empty($_POST['id'])) {
            // Update existing page
            $id = intval($_POST['id']);
            $stmt = $pdo->prepare("UPDATE static_pages SET title = ?, slug = ?, content = ?, meta_description = ?, is_published = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$title, $slug, $content, $meta_description, $is_published, $id]);
            $message = 'Page updated successfully.';
            
            // Redirect to prevent form resubmission
            header("Location: pages.php?edit=$id&updated=1");
            exit();
            
        } elseif ($action === 'delete' && !empty($_POST['id'])) {
            // Delete page
            $id = intval($_POST['id']);
            $stmt = $pdo->prepare("DELETE FROM static_pages WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Page deleted successfully.';
            
            // Redirect to prevent form resubmission
            header("Location: pages.php?deleted=1");
            exit();
        }
    }
    
    // Get pages list
    $stmt = $pdo->query("SELECT * FROM static_pages ORDER BY created_at DESC");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get page being edited
    if (isset($_GET['edit'])) {
        $edit_id = intval($_GET['edit']);
        $stmt = $pdo->prepare("SELECT * FROM static_pages WHERE id = ?");
        $stmt->execute([$edit_id]);
        $editing_page = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
} catch (Exception $e) {
    $error = 'Error: ' . $e->getMessage();
}

// Include dashboard header
require_once __DIR__ . '/../includes/dashboard-header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $editing_page ? 'Edit Page' : 'Create New Page' ?></h1>
        <a href="pages.php" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
            Back to Pages
        </a>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-8">
        <form method="post" action="pages.php" class="space-y-6">
            <input type="hidden" name="action" value="<?= $editing_page ? 'update' : 'create' ?>">
            <?php if ($editing_page): ?>
                <input type="hidden" name="id" value="<?= $editing_page['id'] ?>">
            <?php endif; ?>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Page Title *</label>
                <input type="text" id="title" name="title" required
                       value="<?= htmlspecialchars($editing_page['title'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL Slug *</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        <?= rtrim($base_url, '/') ?>/page/
                    </span>
                    <input type="text" id="slug" name="slug" required
                           value="<?= htmlspecialchars($editing_page['slug'] ?? '') ?>"
                           class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Leave blank to auto-generate from title
                </p>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                <textarea id="content" name="content" rows="10"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($editing_page['content'] ?? '') ?></textarea>
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($editing_page['meta_description'] ?? '') ?></textarea>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Optional. A brief description of the page for search engines.
                </p>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_published" name="is_published" value="1"
                       <?= (!isset($editing_page['is_published']) || $editing_page['is_published']) ? 'checked' : '' ?>
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                <label for="is_published" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                    Publish this page
                </label>
            </div>

            <div class="flex justify-end space-x-4 pt-6">
                <?php if ($editing_page): ?>
                    <a href="pages.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                <?php endif; ?>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <?= $editing_page ? 'Update Page' : 'Create Page' ?>
                </button>
            </div>
        </form>
    </div>

    <?php if (empty($editing_page)): ?>
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">All Pages</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">URL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($pages as $page): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($page['title']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        /page/<?= htmlspecialchars($page['slug']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $page['is_published'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' ?>">
                                        <?= $page['is_published'] ? 'Published' : 'Draft' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="?edit=<?= $page['id'] ?>" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-4">Edit</a>
                                    <button onclick="if(confirm('Are you sure you want to delete this page?')) { document.getElementById('delete-form-<?= $page['id'] ?>').submit(); }" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Delete
                                    </button>
                                    <form id="delete-form-<?= $page['id'] ?>" action="pages.php" method="POST" class="hidden">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $page['id'] ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pages)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No pages found. Create your first page above.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Auto-generate slug from title when title changes and slug is empty
const titleInput = document.getElementById('title');
const slugInput = document.getElementById('slug');

if (titleInput && slugInput) {
    titleInput.addEventListener('input', function() {
        // Only auto-generate if slug is empty or matches the auto-generated pattern
        if (!slugInput.value || slugInput.value === slugify(titleInput.value)) {
            slugInput.value = slugify(titleInput.value);
        }
    });

    // Convert string to URL-friendly slug
    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')          // Replace multiple - with single -
            .replace(/^-+/, '')              // Trim - from start of text
            .replace(/-+$/, '');             // Trim - from end of text
    }
}
</script>

<?php
// Include dashboard footer
require_once __DIR__ . '/../includes/dashboard-footer.php';
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
            <form method="POST" class="p-6 space-y-8">
                <input type="hidden" name="action" value="<?= $editing_page ? 'update' : 'create' ?>">
                <?php if ($editing_page): ?>
                <input type="hidden" name="id" value="<?= $editing_page['id'] ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Page Title</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($editing_page['title'] ?? '') ?>" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3" onkeyup="generateSlug()">
                    </div>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">URL Slug</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">/page/</span>
                            <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($editing_page['slug'] ?? '') ?>" required class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 dark:border-gray-600 focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                        </div>
                    </div>
                </div>
                
                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Description</label>
                    <input type="text" id="meta_description" name="meta_description" value="<?= htmlspecialchars($editing_page['meta_description'] ?? '') ?>" maxlength="160" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3">
                    <p class="mt-2 text-sm text-gray-500">SEO meta description (max 160 characters)</p>
                </div>
                
                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Page Content</label>
                    <textarea id="content" name="content" rows="15" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3"><?= htmlspecialchars($editing_page['content'] ?? '') ?></textarea>
                    <p class="mt-2 text-sm text-gray-500">You can use HTML and basic styling. For complex layouts, consider using a page builder.</p>
                </div>
                
                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_published" name="is_published" <?= ($editing_page['is_published'] ?? 1) ? 'checked' : '' ?> class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="is_published" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Published (visible to public)</label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="cancelEdit()" class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-6 py-3 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
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