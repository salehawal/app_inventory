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
    <title>Admin Dashboard - Inventory System</title>
    <link rel="stylesheet" type="text/css" href="../css/native.css">
    <link rel="stylesheet" type="text/css" href="../css/reset.css">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <script src="../js/native.js"></script>
</head>
<body>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="header-toolbar">
                    <h1 class="h1">Admin Dashboard</h1>
                    <div class="toolbar-actions">
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                        <a href="?action=logout" class="btn btn-danger btn-sm">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="admin-menu">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">User Management</h3>
                                </div>
                                <div class="box-body">
                                    <p>Manage users and their location assignments</p>
                                    <a href="users.php" class="btn btn-primary">Manage Users</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="box box-info">
                                <div class="box-header">
                                    <h3 class="box-title">Location Management</h3>
                                </div>
                                <div class="box-body">
                                    <p>Add, edit, and manage inventory locations</p>
                                    <a href="locations.php" class="btn btn-info">Manage Locations</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header">
                        <h3 class="box-title">Quick Stats</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        // Get quick statistics
                        try {
                            $pdo = db_connect();
                            
                            $stmt = $pdo->query("SELECT COUNT(*) as user_count FROM fict_users");
                            $user_count = $stmt->fetch()['user_count'];
                            
                            $stmt = $pdo->query("SELECT COUNT(*) as location_count FROM fict_location");
                            $location_count = $stmt->fetch()['location_count'];
                            
                        } catch (Exception $e) {
                            $user_count = 0;
                            $location_count = 0;
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-box">
                                    <h4>Total Users</h4>
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
}
</style>
</body>
</html>