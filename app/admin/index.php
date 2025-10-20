<?php
session_start();
require_once('../lib/core.php');

// Check if admin is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Handle admin login
if (isset($_POST['admin_username']) && isset($_POST['admin_password'])) {
    $username = $_POST['admin_username'];
    $password = $_POST['admin_password'];
    
    // Simple admin credentials (you can make this more secure later)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $error_message = "Invalid admin credentials!";
    }
}
?>
<!doctype html>
<html>
<head>
    <title>Admin Login - Inventory System</title>
    <link rel="stylesheet" type="text/css" href="../css/native.css">
    <link rel="stylesheet" type="text/css" href="../css/reset.css">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <script src="../js/native.js"></script>
</head>
<body>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12 logo">
                <h1 class="h1">Admin Panel - Inventory System</h1>
            </div>
        </div>
        
        <form id="admin_login_form" method="post" action="">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Administrator Login</h3>
                        </div>
                        
                        <div class="box-body">
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($error_message); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="admin_username">Username</label>
                                <input type="text" name="admin_username" class="form-control" 
                                       id="admin_username" placeholder="Enter admin username" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_password">Password</label>
                                <input type="password" name="admin_password" class="form-control" 
                                       id="admin_password" placeholder="Enter admin password" required>
                            </div>
                        </div>
                        
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Login as Admin</button>
                            <a href="../login.php" class="btn btn-default">Back to User Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<script>
// Handle enter key for form submission
document.addEventListener('keypress', function(e) {
    if (e.which === 13 || e.keyCode === 13) {
        const activeElement = document.activeElement;
        
        if (activeElement && activeElement.id === 'admin_username') {
            document.getElementById('admin_password').focus();
        } else if (activeElement && activeElement.id === 'admin_password') {
            document.getElementById('admin_login_form').submit();
        }
    }
});
</script>
</body>
</html>