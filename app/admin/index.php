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
    <title>Admin Login - Inventory Collection</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Optimized CSS for Full Screen Responsive Design -->
    <link rel="stylesheet" type="text/css" href="../css/optimized.css">
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <script src="../js/funcs.js"></script>
</head>
<body class="admin-login">
    <script src="../js/native.js"></script>
</head>
<body>
<div class="content">
    <!-- Header -->
    <div class="row">
        <div class="col-xs-12">
            <div class="login-header">
                <div class="login-title">Admin Panel</div>
                <div class="login-subtitle">Inventory Management System</div>
            </div>
        </div>
    </div>
    
    <!-- Login Form -->
    <form id="admin_login_form" method="post" action="">
        <div class="row">
            <div class="col-xs-12">
                <div class="login-card">
                    <?php if (isset($error_message)): ?>
                        <div class="error-message">
                            ⚠️ <?php echo htmlspecialchars($error_message); ?>
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
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-login">Login</button>
                        <a href="../login.php" class="btn btn-secondary">Back to User Login</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* Login-specific styles matching main app design */
.content {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-header {
    text-align: center;
    margin-bottom: 40px;
}

.login-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
}

.login-subtitle {
    font-size: 14px;
    color: #666;
}

.login-card {
    background: #f9f9f9;
    padding: 30px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.error-message {
    background: #f2dede;
    color: #a94442;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 20px;
    border: 1px solid #ebccd1;
    text-align: center;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #5bc0de;
    box-shadow: 0 0 5px rgba(91, 192, 222, 0.3);
}

.form-actions {
    margin-top: 25px;
}

.btn {
    display: block;
    width: 100%;
    padding: 12px;
    margin-bottom: 10px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-primary {
    background-color: #337ab7;
    color: white;
}

.btn-primary:hover {
    background-color: #286090;
}

.btn-secondary {
    background-color: #f5f5f5;
    color: #333;
    border: 1px solid #ddd;
}

.btn-secondary:hover {
    background-color: #e8e8e8;
}

/* Mobile responsiveness */
@media (max-width: 480px) {
    .content {
        padding: 15px;
    }
    
    .login-card {
        padding: 20px;
    }
    
    .login-title {
        font-size: 20px;
    }
}

/* Focus styles */
.btn:focus {
    outline: 2px solid #337ab7;
    outline-offset: 2px;
}
</style>

<script>
// Simple login form handling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('admin_login_form');
    const usernameInput = document.getElementById('admin_username');
    const passwordInput = document.getElementById('admin_password');
    
    // Auto-focus on username field
    usernameInput.focus();
    
    // Handle enter key navigation
    usernameInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            passwordInput.focus();
        }
    });
    
    passwordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            form.submit();
        }
    });
});
</script>
</body>
</html>