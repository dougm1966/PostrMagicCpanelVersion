<?php 
include __DIR__ . '/includes/dashboard-header.php';
?>

<main class="flex-1 p-4 lg:p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">How can we help you?</h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">Find answers in our help center or contact our support team for personalized assistance.</p>
            
            <!-- Search -->
            <div class="mt-8 max-w-2xl mx-auto relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" class="block w-full pl-10 pr-3 py-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Search help articles...">
                <button class="absolute right-2.5 top-2.5 px-4 py-1.5 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Search
                </button>
            </div>
        </div>

        <!-- Help Categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <!-- Getting Started -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4">
                    <i class="fas fa-rocket text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Getting Started</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">New to PostrMagic? Learn the basics and set up your account.</p>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-primary hover:underline">Create your first event</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Import existing events</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Invite team members</a></li>
                </ul>
            </div>

            <!-- Event Management -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Event Management</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Everything you need to create, manage, and promote your events.</p>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-primary hover:underline">Create and edit events</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Sell tickets online</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Promote your events</a></li>
                </ul>
            </div>

            <!-- Media Library -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center mb-4">
                    <i class="fas fa-image text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Media Library</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Upload, organize, and manage your media files in one place.</p>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-primary hover:underline">Upload and organize media</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Best practices for images</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Using the media picker</a></li>
                </ul>
            </div>

            <!-- Billing & Plans -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-12 h-12 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 flex items-center justify-center mb-4">
                    <i class="fas fa-credit-card text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Billing & Plans</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Manage your subscription, payment methods, and invoices.</p>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-primary hover:underline">Update payment method</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Change your plan</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Billing FAQ</a></li>
                </ul>
            </div>

            <!-- Account Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center mb-4">
                    <i class="fas fa-user-cog text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Account Settings</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Customize your account preferences and security settings.</p>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-primary hover:underline">Update profile information</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Change password</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Two-factor authentication</a></li>
                </ul>
            </div>

            <!-- API & Integrations -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-4">
                    <i class="fas fa-plug text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">API & Integrations</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Connect PostrMagic with your favorite tools and services.</p>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-primary hover:underline">API documentation</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Zapier integration</a></li>
                    <li><a href="#" class="text-sm text-primary hover:underline">Webhooks guide</a></li>
                </ul>
            </div>
        </div>

        <!-- Popular Articles -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Popular Help Articles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-2">How to create and publish an event</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">Step-by-step guide to creating and publishing your first event on PostrMagic.</p>
                    <a href="#" class="text-sm font-medium text-primary hover:underline">Read article <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-2">Managing ticket types and pricing</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">Learn how to set up different ticket types, pricing tiers, and discounts.</p>
                    <a href="#" class="text-sm font-medium text-primary hover:underline">Read article <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-2">Troubleshooting common issues</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">Solutions to common problems users encounter when using PostrMagic.</p>
                    <a href="#" class="text-sm font-medium text-primary hover:underline">Read article <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-2">Exporting attendee data</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">How to export and analyze your event attendee information.</p>
                    <a href="#" class="text-sm font-medium text-primary hover:underline">Read article <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 text-center">
            <div class="max-w-2xl mx-auto">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Still need help?</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Our support team is available 24/7 to help you with any questions or issues you may have.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#" class="px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary hover:bg-primary-dark">
                        <i class="fas fa-comments mr-2"></i> Live Chat
                    </a>
                    <a href="#" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <i class="fas fa-envelope mr-2"></i> Email Us
                    </a>
                    <a href="#" class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <i class="fas fa-phone-alt mr-2"></i> Call Support
                    </a>
                </div>
                <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                    <p>Phone support available Monday-Friday, 9am-5pm EST</p>
                    <p class="mt-1">Email response time: Typically within 2 hours</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/dashboard-footer.php'; ?>
