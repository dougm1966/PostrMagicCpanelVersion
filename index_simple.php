<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PostrMagic - AI Event Poster Magic</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">PostrMagic</h1>
                <p class="text-gray-600 mb-8">AI Event Poster to Social Media Magic</p>
                
                <div class="space-y-4">
                    <a href="<?= BASE_URL ?>login.php" class="block w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors">
                        Login
                    </a>
                    
                    <div class="text-sm text-gray-500">
                        <p><strong>Admin:</strong> admin@admin.com / admin</p>
                        <p><strong>User:</strong> bob@bob.com / bob</p>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <a href="<?= BASE_URL ?>admin/profile.php" class="text-blue-600 hover:underline">Admin Profile</a>
                        <a href="<?= BASE_URL ?>user-profile.php" class="text-blue-600 hover:underline">User Profile</a>
                        <a href="<?= BASE_URL ?>setup_database.php" class="text-blue-600 hover:underline">Database Setup</a>
                        <a href="<?= BASE_URL ?>add_profile_fields_safe.php" class="text-blue-600 hover:underline">Profile Migration</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
