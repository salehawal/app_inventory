<?php
session_start();
require_once('../lib/core.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$message = '';
$pdo = db_connect();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add_user':
                    $stmt = $pdo->prepare("INSERT INTO fict_users (fu_code, fu_username, fu_password, fu_role, fu_cre_date, fu_cre_by) VALUES (?, ?, ?, ?, CURDATE(), ?)");
                    $stmt->execute([
                        $_POST['user_code'],
                        $_POST['username'],
                        $_POST['password'], // In production, hash this password
                        $_POST['role'],
                        $_SESSION['admin_username']
                    ]);
                    
                    // Add location assignments
                    if (!empty($_POST['locations'])) {
                        $stmt = $pdo->prepare("INSERT INTO fict_user_locations (ula_user_code, ula_location_code, ula_assigned_date, ula_assigned_by) VALUES (?, ?, CURDATE(), ?)");
                        foreach ($_POST['locations'] as $location_code) {
                            $stmt->execute([$_POST['user_code'], $location_code, $_SESSION['admin_username']]);
                        }
                    }
                    
                    $message = "User added successfully!";
                    break;
                    
                case 'edit_user':
                    $stmt = $pdo->prepare("UPDATE fict_users SET fu_username = ?, fu_role = ? WHERE fu_code = ?");
                    $stmt->execute([
                        $_POST['username'],
                        $_POST['role'],
                        $_POST['user_code']
                    ]);
                    
                    // Update password only if provided
                    if (!empty($_POST['password'])) {
                        $stmt = $pdo->prepare("UPDATE fict_users SET fu_password = ? WHERE fu_code = ?");
                        $stmt->execute([$_POST['password'], $_POST['user_code']]);
                    }
                    
                    $message = "User updated successfully!";
                    break;
                    
                case 'delete_user':
                    $stmt = $pdo->prepare("DELETE FROM fict_users WHERE fu_code = ?");
                    $stmt->execute([$_POST['user_code']]);
                    $message = "User deleted successfully!";
                    break;
                    
                case 'update_locations':
                    // Delete existing assignments
                    $stmt = $pdo->prepare("DELETE FROM fict_user_locations WHERE ula_user_code = ?");
                    $stmt->execute([$_POST['user_code']]);
                    
                    // Add new assignments
                    if (!empty($_POST['locations'])) {
                        $stmt = $pdo->prepare("INSERT INTO fict_user_locations (ula_user_code, ula_location_code, ula_assigned_date, ula_assigned_by) VALUES (?, ?, CURDATE(), ?)");
                        foreach ($_POST['locations'] as $location_code) {
                            $stmt->execute([$_POST['user_code'], $location_code, $_SESSION['admin_username']]);
                        }
                    }
                    
                    $message = "User locations updated successfully!";
                    break;
            }
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Get all users with their locations
$users = [];
try {
    $stmt = $pdo->query("
        SELECT u.*, GROUP_CONCAT(DISTINCT l.flo_address SEPARATOR ', ') as assigned_locations,
               GROUP_CONCAT(DISTINCT ula.ula_location_code SEPARATOR ',') as location_codes
        FROM fict_users u
        LEFT JOIN fict_user_locations ula ON u.fu_code = ula.ula_user_code AND ula.ula_status = 'active'
        LEFT JOIN fict_location l ON ula.ula_location_code = l.flo_code
        GROUP BY u.fu_code
        ORDER BY u.fu_username
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = "Error fetching users: " . $e->getMessage();
}

// Get all locations
$locations = [];
try {
    $stmt = $pdo->query("SELECT * FROM fict_location ORDER BY flo_address");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = "Error fetching locations: " . $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
    <title>Manage Users - Admin</title>
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
                <h1>User Management</h1>
                <div class="admin-actions">
                    <button type="button" class="btn btn-secondary" onclick="go_to_page('dashboard.php');">← Back to Dashboard</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Message Display -->
    <?php if (!empty($message)): ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="message-card <?php echo strpos($message, 'Error') === 0 ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <div class="row">
        <div class="col-xs-12">
            <div class="action-buttons">
                <button type="button" class="btn btn-primary btn-add" onclick="toggleSection('add-user-section');">
                    <span class="btn-icon">👤</span>
                    <span class="btn-label">Add New User</span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Add User Form (Initially Hidden) -->
    <div id="add-user-section" class="form-section hidden-section" style="display: none;">
        <form method="post" class="admin-form">
            <input type="hidden" name="action" value="add_user">
            
            <div class="form-card">
                <div class="form-title">New User Information</div>
                
                <div class="form-group">
                    <label for="user_code">User Code</label>
                    <input type="text" name="user_code" id="user_code" class="form-control" 
                           placeholder="e.g., USR001" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" 
                           placeholder="Enter username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" 
                           placeholder="Enter password" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">Select Role</option>
                        <option value="user">User</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Assign Locations</label>
                    <div class="checkbox-grid">
                        <?php foreach ($locations as $location): ?>
                        <label class="checkbox-item">
                            <input type="checkbox" name="locations[]" value="<?php echo htmlspecialchars($location['flo_code']); ?>">
                            <span class="checkbox-text"><?php echo htmlspecialchars($location['flo_address']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add User</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleSection('add-user-section');">Cancel</button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Users List -->
    <div class="row">
        <div class="col-xs-12">
            <div class="section-title">
                <div class="title-text">Existing Users</div>
                <div class="title-count"><?php echo count($users); ?> users</div>
            </div>
        </div>
    </div>
    
    <?php foreach ($users as $user): ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="user-card">
                <div class="user-header">
                    <div class="user-title">
                        <h3 class="user-name"><?php echo htmlspecialchars($user['fu_username']); ?></h3>
                        <span class="user-badge role-<?php echo strtolower($user['fu_role']); ?>">
                            <?php echo htmlspecialchars($user['fu_role']); ?>
                        </span>
                    </div>
                    <div class="user-meta">
                        <span class="meta-item">
                            <span class="meta-label">Code:</span>
                            <span class="meta-value code"><?php echo htmlspecialchars($user['fu_code']); ?></span>
                        </span>
                        <span class="meta-item">
                            <span class="meta-label">Locations:</span>
                            <span class="meta-value locations"><?php echo $user['assigned_locations'] ?: 'None assigned'; ?></span>
                        </span>
                    </div>
                </div>
                
                <div class="user-actions">
                    <button type="button" class="btn btn-edit" 
                            onclick="toggleEditForm('<?php echo $user['fu_code']; ?>');">
                        <span class="btn-icon">✏️</span> Edit User
                    </button>
                    <button type="button" class="btn btn-locations" 
                            onclick="toggleLocationForm('<?php echo $user['fu_code']; ?>');">
                        <span class="btn-icon">📍</span> Locations
                    </button>
                    <button type="button" class="btn btn-delete" 
                            onclick="deleteUser('<?php echo $user['fu_code']; ?>', '<?php echo addslashes($user['fu_username']); ?>');">
                        <span class="btn-icon">🗑️</span> Delete
                    </button>
                </div>
                
                <!-- Edit User Form (Initially Hidden) -->
                <div id="edit-form-<?php echo $user['fu_code']; ?>" class="edit-form" style="display: none;">
                    <form method="post">
                        <input type="hidden" name="action" value="edit_user">
                        <input type="hidden" name="user_code" value="<?php echo htmlspecialchars($user['fu_code']); ?>">
                        
                        <div class="form-title">Edit User: <?php echo htmlspecialchars($user['fu_username']); ?></div>
                        
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($user['fu_username']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Password (leave blank to keep current)</label>
                            <input type="password" name="password" placeholder="New password">
                        </div>
                        
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" required>
                                <option value="user" <?php echo $user['fu_role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="manager" <?php echo $user['fu_role'] === 'manager' ? 'selected' : ''; ?>>Manager</option>
                                <option value="admin" <?php echo $user['fu_role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update User</button>
                            <button type="button" class="btn btn-secondary" 
                                    onclick="toggleEditForm('<?php echo $user['fu_code']; ?>');">Cancel</button>
                        </div>
                    </form>
                </div>
                
                <!-- Location Edit Form (Initially Hidden) -->
                <div id="location-form-<?php echo $user['fu_code']; ?>" class="location-form" style="display: none;">
                    <form method="post">
                        <input type="hidden" name="action" value="update_locations">
                        <input type="hidden" name="user_code" value="<?php echo htmlspecialchars($user['fu_code']); ?>">
                        
                        <div class="form-title">Update Locations for <?php echo htmlspecialchars($user['fu_username']); ?></div>
                        
                        <div class="checkbox-grid">
                            <?php 
                            $userLocations = explode(',', $user['location_codes'] ?? '');
                            foreach ($locations as $location): 
                            ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="locations[]" 
                                       value="<?php echo htmlspecialchars($location['flo_code']); ?>"
                                       <?php echo in_array($location['flo_code'], $userLocations) ? 'checked' : ''; ?>>
                                <span class="checkbox-text"><?php echo htmlspecialchars($location['flo_address']); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Locations</button>
                            <button type="button" class="btn btn-secondary" 
                                    onclick="toggleLocationForm('<?php echo $user['fu_code']; ?>');">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($users)): ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <div class="empty-text">No users found</div>
                <div class="empty-desc">Click "Add New User" to create your first user</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Hidden Delete Form -->
<form id="delete-form" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete_user">
    <input type="hidden" name="user_code" id="delete-user-code">
</form>

<style>
/* Simple Admin Styles - Matching Main App */
.admin-header {
    padding: 25px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-header h1 {
    margin: 0;
    font-size: 28px;
    color: #333;
    font-weight: bold;
}

.admin-actions button {
    padding: 10px 20px;
    font-size: 14px;
    font-weight: bold;
    background: #337ab7;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.admin-actions button:hover {
    background: #286090;
}

/* Form and card styling */
.form-section {
    background: #f9f9f9;
    padding: 25px;
    margin: 20px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-section h3 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 20px;
    font-weight: bold;
}

.user-card {
    background: white;
    border: 1px solid #ddd;
    padding: 25px;
    margin: 15px 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: box-shadow 0.3s ease;
}

.user-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.user-header {
    margin-bottom: 20px;
}

.user-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}

.user-name {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin: 0;
    flex: 1;
    min-width: 150px;
}

.user-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.role-admin { background: #f8d7da; color: #721c24; }
.role-manager { background: #fff3cd; color: #856404; }
.role-user { background: #d1ecf1; color: #0c5460; }
.role-viewer { background: #e2e3e5; color: #383d41; }

.user-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.meta-label {
    font-size: 12px;
    font-weight: bold;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.meta-value {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.meta-value.code {
    font-family: 'Courier New', monospace;
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 13px;
}

.meta-value.locations {
    color: #666;
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-actions {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 16px;
    font-size: 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-icon {
    font-size: 16px;
}

.btn-edit {
    background: #28a745;
    color: white;
}

.btn-edit:hover {
    background: #218838;
    transform: translateY(-1px);
}

.btn-locations {
    background: #ffc107;
    color: #212529;
}

.btn-locations:hover {
    background: #e0a800;
    transform: translateY(-1px);
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
    transform: translateY(-1px);
}

.message-card {
    padding: 15px 20px;
    margin: 20px 0;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
}

.message-card.success {
    background: #dff0d8;
    border: 1px solid #d6e9c6;
    color: #3c763d;
}

.message-card.error {
    background: #f2dede;
    border: 1px solid #ebccd1;
    color: #a94442;
}

.section-title {
    margin: 30px 0 20px 0;
    padding-bottom: 15px;
    border-bottom: 2px solid #ddd;
}

.title-text {
    font-size: 22px;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.title-count {
    font-size: 14px;
    color: #666;
    margin-top: 8px;
    font-weight: normal;
}

/* Form styling */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #337ab7;
    box-shadow: 0 0 5px rgba(51, 122, 183, 0.3);
}

.edit-form, .location-form {
    margin-top: 15px;
    padding: 20px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
        padding: 20px 15px;
    }
    
    .admin-header h1 {
        font-size: 24px;
    }
    
    .user-card {
        padding: 20px 15px;
    }
    
    .user-title {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .user-name {
        font-size: 18px;
        min-width: auto;
    }
    
    .user-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .user-actions {
        flex-direction: column;
    }
    
    .user-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .form-section {
        padding: 20px 15px;
    }
    
    .meta-value.locations {
        max-width: none;
        white-space: normal;
    }
}

@media (max-width: 480px) {
    .user-badge {
        font-size: 11px;
        padding: 4px 8px;
    }
    
    .meta-label {
        font-size: 11px;
    }
    
    .meta-value {
        font-size: 13px;
    }
}
</style>
<script>
function go_to_page(url) {
    window.location.href = url;
}

// Native JavaScript functions
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        if (section.style.display === 'none' || section.style.display === '') {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    }
}

function toggleEditForm(userCode) {
    const form = document.getElementById('edit-form-' + userCode);
    if (form) {
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
}

function toggleLocationForm(userCode) {
    const form = document.getElementById('location-form-' + userCode);
    if (form) {
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
}

function deleteUser(userCode, username) {
    if (confirm('Are you sure you want to delete user "' + username + '"? This action cannot be undone.')) {
        document.getElementById('delete-user-code').value = userCode;
        document.getElementById('delete-form').submit();
    }
}

// Auto-hide messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const messageCard = document.querySelector('.message-card');
    if (messageCard) {
        setTimeout(function() {
            messageCard.style.opacity = '0';
            setTimeout(function() {
                messageCard.style.display = 'none';
            }, 300);
        }, 5000);
    }
});
</script>
</body>
</html>