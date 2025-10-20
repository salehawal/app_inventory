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
    <title>User Management - Admin Panel</title>
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
                    <h1 class="h1">User Management</h1>
                    <div class="toolbar-actions">
                        <a href="dashboard.php" class="btn btn-default">Back to Dashboard</a>
                        <a href="?action=logout" class="btn btn-danger btn-sm">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($message): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert <?php echo strpos($message, 'Error') === 0 ? 'alert-danger' : 'alert-success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Add New User Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Add New User</h3>
                    </div>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="add_user">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="user_code">User ID</label>
                                        <input type="text" name="user_code" class="form-control" id="user_code" placeholder="Employee ID" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" name="username" class="form-control" id="username" placeholder="Full Name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" class="form-control" id="password" placeholder="User Password" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select name="role" class="form-control" id="role">
                                            <option value="employee">Employee</option>
                                            <option value="supervisor">Supervisor</option>
                                            <option value="manager">Manager</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Assign Locations</label>
                                        <div class="location-checkboxes">
                                            <?php foreach ($locations as $location): ?>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="locations[]" value="<?php echo htmlspecialchars($location['flo_code']); ?>">
                                                <?php echo htmlspecialchars($location['flo_address']); ?>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Users List -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header">
                        <h3 class="box-title">Existing Users</h3>
                    </div>
                    <div class="box-body">
                        <?php if (empty($users)): ?>
                        <p>No users found. Add your first user above.</p>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Assigned Locations</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['fu_code']); ?></td>
                                        <td><?php echo htmlspecialchars($user['fu_username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['fu_role']); ?></td>
                                        <td><?php echo htmlspecialchars($user['assigned_locations'] ?: 'No locations assigned'); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" onclick="editUserLocations('<?php echo htmlspecialchars($user['fu_code']); ?>', '<?php echo htmlspecialchars($user['location_codes']); ?>')">Edit Locations</button>
                                            <form method="post" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="user_code" value="<?php echo htmlspecialchars($user['fu_code']); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Edit Locations Modal -->
<div id="editLocationsModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit User Locations</h4>
                <button type="button" class="close" onclick="closeModal()">&times;</button>
            </div>
            <form method="post" action="">
                <input type="hidden" name="action" value="update_locations">
                <input type="hidden" name="user_code" id="edit_user_code">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Assign Locations</label>
                        <div id="edit_location_checkboxes">
                            <?php foreach ($locations as $location): ?>
                            <label class="checkbox-block">
                                <input type="checkbox" name="locations[]" value="<?php echo htmlspecialchars($location['flo_code']); ?>">
                                <?php echo htmlspecialchars($location['flo_address']); ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Locations</button>
                </div>
            </form>
        </div>
    </div>
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

.checkbox-inline {
    display: inline-block;
    margin-right: 15px;
    margin-bottom: 10px;
}

.checkbox-block {
    display: block;
    margin-bottom: 10px;
}

.location-checkboxes {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 4px;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-dialog {
    max-width: 500px;
    margin: 50px auto;
}

.modal-content {
    background: white;
    border-radius: 5px;
    overflow: hidden;
}

.modal-header {
    padding: 15px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 15px;
}

.modal-footer {
    padding: 15px;
    border-top: 1px solid #ddd;
    text-align: right;
}

.close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
}
</style>

<script>
function editUserLocations(userCode, locationCodes) {
    document.getElementById('edit_user_code').value = userCode;
    
    // Clear all checkboxes
    const checkboxes = document.querySelectorAll('#edit_location_checkboxes input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);
    
    // Check the assigned locations
    if (locationCodes) {
        const assignedCodes = locationCodes.split(',');
        assignedCodes.forEach(code => {
            const checkbox = document.querySelector(`#edit_location_checkboxes input[value="${code}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }
    
    document.getElementById('editLocationsModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editLocationsModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('editLocationsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
</body>
</html>