<?php
session_start();
require_once('../lib/core.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
<!doctype html>
<html>
<head>
    <title>Admin Panel</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Native CSS and JavaScript - No External Dependencies -->
    <link rel="stylesheet" type="text/css" href="../css/native.css">
    <link rel="stylesheet" type="text/css" href="../css/reset.css">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <script src="../js/funcs.js"></script>
    <script src="../js/native.js"></script>
</head>
<body>
<div class="content">
    <!-- Header similar to main app -->
    <div class="row">
        <div class="col-xs-12">
            <div class="admin-header">
                <div class="admin-title">Admin Panel</div>
                <div class="admin-user">
                    <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Admin Menu - Same style as main app buttons -->
        <!-- Navigation Menu -->
    <div class="dashboard-nav">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-4">
                <a href="users.php" class="nav-card">
                    <div class="nav-icon">👥</div>
                    <div class="nav-title">User Management</div>
                    <div class="nav-desc">Manage users and permissions</div>
                </a>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-4">
                <a href="locations.php" class="nav-card">
                    <div class="nav-icon">📍</div>
                    <div class="nav-title">Location Management</div>
                    <div class="nav-desc">Add and manage locations</div>
                </a>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-4">
                <button type="button" class="nav-card nav-button" onclick="toggleStats();">
                    <div class="nav-icon">�</div>
                    <div class="nav-title">System Statistics</div>
                    <div class="nav-desc">View system overview</div>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats section -->
    <div id="stats-section" class="stats-container">
        <div class="row">
            <div class="col-xs-12">
                <div class="section-title">
                    <h3>System Overview</h3>
                </div>
                <div class="stats-grid">
                    <?php
                    // Get quick statistics
                    try {
                        $pdo = db_connect();
                        
                        $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM fict_users");
                        $user_count = $stmt->fetch()['user_count'];
                        
                        $stmt = $pdo->query("SELECT COUNT(*) as location_count FROM fict_location");
                        $location_count = $stmt->fetch()['location_count'];
                        
                        // Get inventory counts
                        $stmt = $pdo->query("SELECT COUNT(*) as hardware_count FROM fict_hardware");
                        $hardware_count = $stmt->fetch()['hardware_count'] ?? 0;
                        
                        $stmt = $pdo->query("SELECT COUNT(*) as software_count FROM fict_software");
                        $software_count = $stmt->fetch()['software_count'] ?? 0;
                        
                    } catch (Exception $e) {
                        $user_count = 0;
                        $location_count = 0;
                        $hardware_count = 0;
                        $software_count = 0;
                    }
                    ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $user_count; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $location_count; ?></div>
                        <div class="stat-label">Total Locations</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $hardware_count; ?></div>
                        <div class="stat-label">Hardware Items</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $software_count; ?></div>
                        <div class="stat-label">Software Items</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats section -->
    <div id="stats-section" class="stats-container">
        <div class="row">
            <div class="col-xs-12">
                <div class="section-title">
                    <h3>System Overview</h3>
                </div>
                <div class="stats-grid">
                    <?php
                    // Get quick statistics
                    try {
                        $pdo = db_connect();
                        
                        $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM fict_users");
                        $user_count = $stmt->fetch()['user_count'];
                        
                        $stmt = $pdo->query("SELECT COUNT(*) as location_count FROM fict_location");
                        $location_count = $stmt->fetch()['location_count'];
                        
                        // Get inventory counts
                        $stmt = $pdo->query("SELECT COUNT(*) as hardware_count FROM fict_hardware");
                        $hardware_count = $stmt->fetch()['hardware_count'] ?? 0;
                        
                        $stmt = $pdo->query("SELECT COUNT(*) as software_count FROM fict_software");
                        $software_count = $stmt->fetch()['software_count'] ?? 0;
                        
                    } catch (Exception $e) {
                        $user_count = 0;
                        $location_count = 0;
                        $hardware_count = 0;
                        $software_count = 0;
                    }
                    ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $user_count; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $location_count; ?></div>
                        <div class="stat-label">Locations</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $hardware_count; ?></div>
                        <div class="stat-label">Hardware Items</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $software_count; ?></div>
                        <div class="stat-label">Software Items</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Logout button -->
    <div class="logout-section">
        <div class="row">
            <div class="col-xs-12">
                <button type="button" class="btn-logout-main" onclick="confirmLogout();">Logout</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Admin-specific styles matching main app */
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin: -2px -2px 20px -2px;
    border-radius: 8px;
}

.admin-title {
    font-size: 24px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.admin-user {
    font-size: 14px;
    opacity: 0.9;
}

/* Enhanced button styles for admin */
.btn-nav-menu .btn {
    position: relative;
    padding: 25px 20px;
    text-align: left;
    height: auto;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    background: white;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.btn-nav-menu .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #667eea;
}

.btn-icon {
    font-size: 32px;
    margin-bottom: 10px;
    line-height: 1;
}

.btn-text {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.btn-desc {
    font-size: 14px;
    color: #666;
    line-height: 1.3;
}

/* Stats container */
.stats-container {
    margin: 20px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Logout section */
.logout-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #eee;
}

.btn-logout-main {
    width: 100%;
    padding: 20px;
    font-size: 18px;
    font-weight: bold;
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    border-radius: 8px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-logout-main:hover {
    background-color: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .admin-title {
        font-size: 20px;
    }
    
    .btn-nav-menu .btn {
        padding: 20px 15px;
        min-height: 80px;
    }
    
    .btn-icon {
        font-size: 28px;
    }
    
    .btn-text {
        font-size: 16px;
    }
    
    .btn-desc {
        font-size: 13px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .stat-card {
        padding: 15px;
    }
    
    .stat-number {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .btn-nav-menu .btn {
        min-height: 70px;
    }
}
</style>

<script>
// Native JavaScript - No jQuery dependencies
function go_to_page(url) {
    window.location.href = url;
}

function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '?action=logout';
    }
}

function showStats() {
    const statsSection = document.getElementById('stats-section');
    if (statsSection.style.display === 'none') {
        statsSection.style.display = 'block';
    } else {
        statsSection.style.display = 'none';
    }
}

// Add smooth scrolling for better UX
document.addEventListener('DOMContentLoaded', function() {
    document.documentElement.style.scrollBehavior = 'smooth';
});
</script>
</body>
</html>
                                    <p class="stat-number"><?php echo $user_count; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-box">
                                    <h4>Total Locations</h4>
                                    <p class="stat-number"><?php echo $location_count; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.header-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.toolbar-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.admin-menu {
    margin-bottom: 30px;
}

.stat-box {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 5px;
}

.stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #337ab7;
    margin: 10px 0;
</style>

<script>
function toggleStats() {
    // Stats are always visible now
    return;
}

function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '?action=logout';
    }
}
</script>
</body>
</html>