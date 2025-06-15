<?php
/**
 * Admin Header Layout
 * Navigation header for admin pages
 */
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../admin/">
            <i class="fas fa-cogs me-2"></i><?php echo APP_NAME; ?> Admin
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../admin/media.php">
                        <i class="fas fa-images me-1"></i>Media Library
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../admin/llm-settings.php">
                        <i class="fas fa-robot me-1"></i>LLM Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../admin/users.php">
                        <i class="fas fa-users me-1"></i>Users
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="analyticsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-chart-line me-1"></i>Analytics
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../admin/analytics.php">Overview</a></li>
                        <li><a class="dropdown-item" href="../admin/llm-analytics.php">LLM Usage</a></li>
                        <li><a class="dropdown-item" href="../admin/content-analytics.php">Content Generation</a></li>
                    </ul>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars(getCurrentUser()['name'] ?? 'Admin'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="../profile.php">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>