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
    <title>Admin Dashboard - Inventory Collection</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Optimized CSS for Full Screen Responsive Design -->
    <link rel="stylesheet" type="text/css" href="../css/optimized.css">
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <script src="../js/funcs.js"></script>
</head>
<body>
<div class="content">
    <!-- Header -->
    <div class="row">
        <div class="col-xs-12">
            <div class="admin-header">
                <h1>Admin Panel</h1>
                <div class="admin-user"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="row btn-nav-menu">
        <div class="col-xs-12">
            <button type="button" class="btn btn-default" onclick="go_to_page('users.php');">User Management</button>
        </div>
    </div>
    
    <div class="row btn-nav-menu">
        <div class="col-xs-12">
            <button type="button" class="btn btn-default" onclick="go_to_page('locations.php');">Location Management</button>
        </div>
    </div>
    
    <!-- Logout button -->
    <div class="row btn-nav-menu logout-section">
        <div class="col-xs-12">
            <button type="button" class="btn btn-danger btn-logout-main" onclick="confirmLogout();">Logout</button>
        </div>
    </div>
</div>

<style>
/* Simple Admin Styles - Matching Main App */
.admin-header {
    padding: 25px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
    margin-bottom: 30px;
    text-align: center;
}

.admin-header h1 {
    margin: 0;
    font-size: 28px;
    color: #333;
    font-weight: bold;
}

.admin-user {
    font-size: 14px;
    color: #666;
    margin-top: 8px;
}

.content {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.btn-nav-menu {
    margin-bottom: 20px;
}

.btn-nav-menu button {
    width: 100%;
    padding: 30px 25px;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    color: #333;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-nav-menu button:hover {
    background: #f5f5f5;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    color: #337ab7;
}

.logout-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    text-align: center;
}

.btn-logout-main {
    padding: 15px 30px;
    font-size: 16px;
    font-weight: bold;
    background: #d9534f;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-logout-main:hover {
    background: #c9302c;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .admin-header {
        padding: 20px 15px;
    }
    
    .admin-header h1 {
        font-size: 24px;
    }
    
    .content {
        padding: 15px;
    }
    
    .btn-nav-menu button {
        padding: 25px 20px;
        font-size: 16px;
    }
}
</style>

<script>
function go_to_page(url) {
    window.location.href = url;
}

function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '?action=logout';
    }
}
</script>
</body>
</html>
