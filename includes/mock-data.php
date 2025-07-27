<?php
// Mock data for development - will be replaced with database queries

// Mock users
function getMockUsers() {
    return [
        [
            'id' => 1,
            'name' => 'Alex Johnson',
            'email' => 'alex@example.com',
            'role' => 'admin',
            'status' => 'active',
            'created_date' => '2024-01-15',
            'last_login' => '2025-01-14 10:30:00',
            'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg'
        ],
        [
            'id' => 2,
            'name' => 'Sarah Williams',
            'email' => 'sarah@example.com',
            'role' => 'user',
            'status' => 'active',
            'created_date' => '2024-02-20',
            'last_login' => '2025-01-13 14:22:00',
            'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg'
        ],
        [
            'id' => 3,
            'name' => 'Michael Chen',
            'email' => 'michael@example.com',
            'role' => 'user',
            'status' => 'active',
            'created_date' => '2024-03-10',
            'last_login' => '2025-01-12 09:15:00',
            'avatar' => 'https://randomuser.me/api/portraits/men/18.jpg'
        ],
        [
            'id' => 4,
            'name' => 'Emma Davis',
            'email' => 'emma@example.com',
            'role' => 'admin',
            'status' => 'active',
            'created_date' => '2024-01-20',
            'last_login' => '2025-01-14 11:45:00',
            'avatar' => 'https://randomuser.me/api/portraits/women/68.jpg'
        ],
        [
            'id' => 5,
            'name' => 'James Wilson',
            'email' => 'james@example.com',
            'role' => 'user',
            'status' => 'suspended',
            'created_date' => '2024-04-05',
            'last_login' => '2025-01-01 16:30:00',
            'avatar' => 'https://randomuser.me/api/portraits/men/52.jpg'
        ],
        [
            'id' => 6,
            'name' => 'Lisa Martinez',
            'email' => 'lisa@example.com',
            'role' => 'user',
            'status' => 'active',
            'created_date' => '2024-05-12',
            'last_login' => '2025-01-14 08:20:00',
            'avatar' => 'https://randomuser.me/api/portraits/women/21.jpg'
        ],
        [
            'id' => 7,
            'name' => 'Robert Taylor',
            'email' => 'robert@example.com',
            'role' => 'user',
            'status' => 'active',
            'created_date' => '2024-06-18',
            'last_login' => '2025-01-13 17:10:00',
            'avatar' => 'https://randomuser.me/api/portraits/men/36.jpg'
        ],
        [
            'id' => 8,
            'name' => 'Jennifer Brown',
            'email' => 'jennifer@example.com',
            'role' => 'user',
            'status' => 'pending',
            'created_date' => '2024-12-28',
            'last_login' => null,
            'avatar' => 'https://randomuser.me/api/portraits/women/89.jpg'
        ]
    ];
}

// Mock events for admin view
function getMockAdminEvents() {
    return [
        [
            'id' => 1,
            'title' => 'Summer Music Festival',
            'creator' => 'Sarah Williams',
            'date' => '2025-07-15',
            'status' => 'active',
            'views' => 1250,
            'reports' => 0,
            'revenue' => 12500
        ],
        [
            'id' => 2,
            'title' => 'Tech Conference 2025',
            'creator' => 'Michael Chen',
            'date' => '2025-08-22',
            'status' => 'pending_review',
            'views' => 0,
            'reports' => 0,
            'revenue' => 0
        ],
        [
            'id' => 3,
            'title' => 'Art Exhibition Opening',
            'creator' => 'Emma Davis',
            'date' => '2025-06-30',
            'status' => 'active',
            'views' => 879,
            'reports' => 1,
            'revenue' => 3200
        ],
        [
            'id' => 4,
            'title' => 'Charity Run Marathon',
            'creator' => 'James Wilson',
            'date' => '2025-09-10',
            'status' => 'flagged',
            'views' => 2100,
            'reports' => 5,
            'revenue' => 0
        ],
        [
            'id' => 5,
            'title' => 'Food & Wine Festival',
            'creator' => 'Lisa Martinez',
            'date' => '2025-05-20',
            'status' => 'active',
            'views' => 3421,
            'reports' => 0,
            'revenue' => 28900
        ]
    ];
}

// Mock system stats
function getMockSystemStats() {
    return [
        'total_users' => 1847,
        'active_users' => 1523,
        'new_users_today' => 12,
        'total_events' => 342,
        'active_events' => 198,
        'pending_events' => 23,
        'flagged_events' => 7,
        'total_revenue' => 156789,
        'revenue_this_month' => 23456,
        'server_status' => 'healthy',
        'cpu_usage' => 45,
        'memory_usage' => 62,
        'disk_usage' => 38,
        'error_count' => 3,
        'api_calls_today' => 15234
    ];
}

// Mock activity logs
function getMockActivityLogs() {
    return [
        [
            'id' => 1,
            'user' => 'Sarah Williams',
            'action' => 'Created new event',
            'details' => 'Summer Music Festival',
            'timestamp' => '2025-01-14 10:15:00',
            'ip' => '192.168.1.100'
        ],
        [
            'id' => 2,
            'user' => 'Michael Chen',
            'action' => 'Updated profile',
            'details' => 'Changed avatar and bio',
            'timestamp' => '2025-01-14 09:30:00',
            'ip' => '192.168.1.101'
        ],
        [
            'id' => 3,
            'user' => 'System',
            'action' => 'Automated backup',
            'details' => 'Daily backup completed',
            'timestamp' => '2025-01-14 03:00:00',
            'ip' => '127.0.0.1'
        ],
        [
            'id' => 4,
            'user' => 'Emma Davis',
            'action' => 'User suspended',
            'details' => 'Suspended user: James Wilson',
            'timestamp' => '2025-01-13 16:45:00',
            'ip' => '192.168.1.102'
        ],
        [
            'id' => 5,
            'user' => 'Lisa Martinez',
            'action' => 'Event published',
            'details' => 'Food & Wine Festival',
            'timestamp' => '2025-01-13 14:20:00',
            'ip' => '192.168.1.103'
        ]
    ];
}

// Mock error logs
function getMockErrorLogs() {
    return [
        [
            'id' => 1,
            'level' => 'warning',
            'message' => 'Failed login attempt for user: unknown@example.com',
            'file' => '/login.php',
            'line' => 45,
            'timestamp' => '2025-01-14 11:30:00'
        ],
        [
            'id' => 2,
            'level' => 'error',
            'message' => 'Database connection timeout',
            'file' => '/includes/db.php',
            'line' => 23,
            'timestamp' => '2025-01-14 08:15:00'
        ],
        [
            'id' => 3,
            'level' => 'warning',
            'message' => 'Deprecated function used: mysql_connect()',
            'file' => '/legacy/old-code.php',
            'line' => 156,
            'timestamp' => '2025-01-13 22:45:00'
        ]
    ];
}
