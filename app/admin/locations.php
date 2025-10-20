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
                case 'add_location':
                    $stmt = $pdo->prepare("INSERT INTO fict_location (flo_code, flo_type, flo_address, flo_desc, flo_cre_date, flo_cre_by) VALUES (?, ?, ?, ?, CURDATE(), ?)");
                    $stmt->execute([
                        $_POST['location_code'],
                        $_POST['location_type'],
                        $_POST['location_address'],
                        $_POST['location_desc'],
                        $_SESSION['admin_username']
                    ]);
                    $message = "Location added successfully!";
                    break;
                    
                case 'update_location':
                    $stmt = $pdo->prepare("UPDATE fict_location SET flo_type = ?, flo_address = ?, flo_desc = ? WHERE flo_code = ?");
                    $stmt->execute([
                        $_POST['location_type'],
                        $_POST['location_address'],
                        $_POST['location_desc'],
                        $_POST['location_code']
                    ]);
                    $message = "Location updated successfully!";
                    break;
                    
                case 'delete_location':
                    // Check if location has users assigned
                    $stmt = $pdo->prepare("SELECT COUNT(*) as user_count FROM fict_user_locations WHERE ula_location_code = ?");
                    $stmt->execute([$_POST['location_code']]);
                    $userCount = $stmt->fetch()['user_count'];
                    
                    if ($userCount > 0) {
                        $message = "Error: Cannot delete location with assigned users. Remove user assignments first.";
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM fict_location WHERE flo_code = ?");
                        $stmt->execute([$_POST['location_code']]);
                        $message = "Location deleted successfully!";
                    }
                    break;
            }
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Get all locations with user count
$locations = [];
try {
    $stmt = $pdo->query("
        SELECT l.*, COUNT(ula.ula_user_code) as user_count
        FROM fict_location l
        LEFT JOIN fict_user_locations ula ON l.flo_code = ula.ula_location_code AND ula.ula_status = 'active'
        GROUP BY l.flo_code
        ORDER BY l.flo_address
    ");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = "Error fetching locations: " . $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
    <title>Location Management - Admin Panel</title>
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
                    <h1 class="h1">Location Management</h1>
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
        
        <!-- Add New Location Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Add New Location</h3>
                    </div>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="add_location">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="location_code">Location Code</label>
                                        <input type="text" name="location_code" class="form-control" id="location_code" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="location_type">Type</label>
                                        <select name="location_type" class="form-control" id="location_type">
                                            <option value="Main Office">Main Office</option>
                                            <option value="Branch Office">Branch Office</option>
                                            <option value="Warehouse">Warehouse</option>
                                            <option value="Field Office">Field Office</option>
                                            <option value="Remote Site">Remote Site</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location_address">Address</label>
                                        <input type="text" name="location_address" class="form-control" id="location_address" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="location_desc">Description</label>
                                        <textarea name="location_desc" class="form-control" id="location_desc" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Add Location</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Locations List -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header">
                        <h3 class="box-title">Existing Locations</h3>
                    </div>
                    <div class="box-body">
                        <?php if (empty($locations)): ?>
                        <p>No locations found. Add your first location above.</p>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th>Address</th>
                                        <th>Description</th>
                                        <th>Assigned Users</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locations as $location): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($location['flo_code']); ?></td>
                                        <td><?php echo htmlspecialchars($location['flo_type']); ?></td>
                                        <td><?php echo htmlspecialchars($location['flo_address']); ?></td>
                                        <td><?php echo htmlspecialchars($location['flo_desc']); ?></td>
                                        <td><?php echo $location['user_count']; ?> users</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" onclick="editLocation('<?php echo htmlspecialchars($location['flo_code']); ?>', '<?php echo htmlspecialchars($location['flo_type']); ?>', '<?php echo htmlspecialchars($location['flo_address']); ?>', '<?php echo htmlspecialchars($location['flo_desc']); ?>')">Edit</button>
                                            <?php if ($location['user_count'] == 0): ?>
                                            <form method="post" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this location?');">
                                                <input type="hidden" name="action" value="delete_location">
                                                <input type="hidden" name="location_code" value="<?php echo htmlspecialchars($location['flo_code']); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                            <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-danger" disabled title="Cannot delete location with assigned users">Delete</button>
                                            <?php endif; ?>
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

<!-- Edit Location Modal -->
<div id="editLocationModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Location</h4>
                <button type="button" class="close" onclick="closeModal()">&times;</button>
            </div>
            <form method="post" action="">
                <input type="hidden" name="action" value="update_location">
                <input type="hidden" name="location_code" id="edit_location_code">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_location_type">Type</label>
                        <select name="location_type" class="form-control" id="edit_location_type">
                            <option value="Main Office">Main Office</option>
                            <option value="Branch Office">Branch Office</option>
                            <option value="Warehouse">Warehouse</option>
                            <option value="Field Office">Field Office</option>
                            <option value="Remote Site">Remote Site</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_location_address">Address</label>
                        <input type="text" name="location_address" class="form-control" id="edit_location_address" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_location_desc">Description</label>
                        <textarea name="location_desc" class="form-control" id="edit_location_desc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Location</button>
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
function editLocation(code, type, address, description) {
    document.getElementById('edit_location_code').value = code;
    document.getElementById('edit_location_type').value = type;
    document.getElementById('edit_location_address').value = address;
    document.getElementById('edit_location_desc').value = description;
    
    document.getElementById('editLocationModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editLocationModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('editLocationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
</body>
</html>