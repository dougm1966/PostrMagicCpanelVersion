<?php 
include __DIR__ . '/includes/dashboard-header.php';

// Mock data (would come from database in production)
$subscription = [
    'plan' => 'Pro',
    'status' => 'active',
    'next_billing_date' => 'June 15, 2025',
    'price' => '$29.99',
    'billing_cycle' => 'Monthly',
    'payment_method' => [
        'type' => 'Visa',
        'last4' => '4242',
        'expiry' => '12/25'
    ]
];

$invoices = [
    ['id' => 'INV-001', 'date' => 'May 15, 2023', 'amount' => '$29.99', 'status' => 'Paid', 'download' => '#'],
    ['id' => 'INV-002', 'date' => 'April 15, 2023', 'amount' => '$29.99', 'status' => 'Paid', 'download' => '#'],
    ['id' => 'INV-003', 'date' => 'March 15, 2023', 'amount' => '$29.99', 'status' => 'Paid', 'download' => '#']
];
?>

<main class="flex-1 p-4 lg:p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Billing & Subscriptions</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your subscription and payment methods</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <button class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <i class="fas fa-download mr-2"></i> Export Invoices
                </button>
                <button class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark">
                    <i class="fas fa-plus mr-2"></i> Add Payment Method
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Subscription Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Current Plan -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Current Plan</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">You're currently on the <?= $subscription['plan'] ?> plan</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                            <?= ucfirst($subscription['status']) ?>
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Plan Price</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white"><?= $subscription['price'] ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">per month</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Billing Cycle</p>
                            <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white"><?= $subscription['billing_cycle'] ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Next billing: <?= $subscription['next_billing_date'] ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Usage</p>
                            <div class="mt-1 flex items-baseline">
                                <p class="text-xl font-semibold text-gray-900 dark:text-white">85%</p>
                                <p class="ml-2 text-sm text-gray-500 dark:text-gray-400">of events</p>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-primary h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                        <button class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark">
                            Upgrade Plan
                        </button>
                        <button class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Cancel Subscription
                        </button>
                    </div>
                </div>

                <!-- Billing History -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Billing History</h2>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach($invoices as $invoice): ?>
                        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Invoice #<?= $invoice['id'] ?></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?= $invoice['date'] ?></p>
                                </div>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-4 flex items-center">
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?= $invoice['amount'] ?></p>
                                <span class="ml-4 px-2.5 py-0.5 rounded-full text-xs font-medium <?= $invoice['status'] === 'Paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' ?>">
                                    <?= $invoice['status'] ?>
                                </span>
                                <a href="<?= $invoice['download'] ?>" class="ml-4 text-sm font-medium text-primary hover:text-primary-dark">
                                    Download
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 text-right">
                        <a href="#" class="text-sm font-medium text-primary hover:text-primary-dark">
                            View all billing history
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Payment Method -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Method</h2>
                        <button class="text-sm text-primary hover:text-primary-dark font-medium">Edit</button>
                    </div>
                    
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-12 bg-white dark:bg-gray-600 rounded flex items-center justify-center shadow-sm">
                                    <i class="fab fa-cc-<?= strtolower($subscription['payment_method']['type']) ?> text-xl text-gray-700 dark:text-gray-200"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?= $subscription['payment_method']['type'] ?> •••• <?= $subscription['payment_method']['last4'] ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Expires <?= $subscription['payment_method']['expiry'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Update Payment Method
                        </button>
                    </div>
                </div>
                
                <!-- Billing Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Billing Information</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Business Name</p>
                            <p class="text-sm text-gray-900 dark:text-white">My Awesome Business</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-sm text-gray-900 dark:text-white">billing@mybusiness.com</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tax ID</p>
                            <p class="text-sm text-gray-900 dark:text-white">12-3456789</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Billing Address</p>
                            <p class="text-sm text-gray-900 dark:text-white">123 Business St.<br>Denver, CO 80202<br>United States</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Update Billing Info
                        </button>
                    </div>
                </div>
                
                <!-- Need Help? -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-question-circle text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Need help with billing?</h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <p>Our support team is here to help with any billing questions you may have.</p>
                            </div>
                            <div class="mt-4">
                                <a href="#" class="text-sm font-medium text-blue-700 dark:text-blue-200 hover:text-blue-600 dark:hover:text-blue-100">
                                    Contact support <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/dashboard-footer.php'; ?>
