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
    <title>Manage Locations - Admin</title>
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
                <h1>Location Management</h1>
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
                <button type="button" class="btn btn-primary btn-add" onclick="toggleSection('add-location-section');">
                    <span class="btn-icon">📍</span>
                    <span class="btn-label">Add New Location</span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Add Location Form (Initially Hidden) -->
    <div id="add-location-section" class="form-section" style="display: none;">
        <form method="post" class="admin-form">
            <input type="hidden" name="action" value="add_location">
            
            <div class="form-card">
                <div class="form-title">New Location Information</div>
                
                <div class="form-group">
                    <label for="location_code">Location Code</label>
                    <input type="text" name="location_code" id="location_code" class="form-control" 
                           placeholder="e.g., LOC001" required>
                </div>
                
                <div class="form-group">
                    <label for="location_type">Location Type</label>
                    <select name="location_type" id="location_type" class="form-control" required>
                        <option value="">Select Type</option>
                        <option value="Branch">Branch</option>
                        <option value="Warehouse">Warehouse</option>
                        <option value="Office">Office</option>
                        <option value="Field">Field Location</option>
                        <option value="Remote">Remote Site</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="location_address">Address</label>
                    <input type="text" name="location_address" id="location_address" class="form-control" 
                           placeholder="Enter full address" required>
                </div>
                
                <div class="form-group">
                    <label for="location_desc">Description</label>
                    <textarea name="location_desc" id="location_desc" class="form-control" 
                              placeholder="Additional details about this location" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Location</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleSection('add-location-section');">Cancel</button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Locations List -->
    <div class="row">
        <div class="col-xs-12">
            <div class="section-title">
                <div class="title-text">Existing Locations</div>
                <div class="title-count"><?php echo count($locations); ?> locations</div>
            </div>
        </div>
    </div>
    
    <?php foreach ($locations as $location): ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="location-card">
                <div class="location-header">
                    <div class="location-title">
                        <h3 class="location-name"><?php echo htmlspecialchars($location['flo_address']); ?></h3>
                        <span class="location-badge location-type-<?php echo strtolower($location['flo_type']); ?>">
                            <?php echo htmlspecialchars($location['flo_type']); ?>
                        </span>
                    </div>
                    <div class="location-meta">
                        <span class="meta-item">
                            <span class="meta-label">Code:</span>
                            <span class="meta-value code"><?php echo htmlspecialchars($location['flo_code']); ?></span>
                        </span>
                        <span class="meta-item">
                            <span class="meta-label">Users:</span>
                            <span class="meta-value users-count"><?php echo $location['user_count']; ?> assigned</span>
                        </span>
                    </div>
                </div>
                
                <?php if (!empty($location['flo_desc'])): ?>
                <div class="location-description">
                    <div class="description-label">Description:</div>
                    <div class="description-text"><?php echo htmlspecialchars($location['flo_desc']); ?></div>
                </div>
                <?php endif; ?>
                
                <div class="location-actions">
                    <button type="button" class="btn btn-edit" 
                            onclick="toggleEditForm('<?php echo $location['flo_code']; ?>');">
                        <span class="btn-icon">✏️</span> Edit
                    </button>
                    <button type="button" class="btn btn-delete" 
                            onclick="deleteLocation('<?php echo $location['flo_code']; ?>', '<?php echo addslashes($location['flo_address']); ?>', <?php echo $location['user_count']; ?>);">
                        <span class="btn-icon">🗑️</span> Delete
                    </button>
                </div>
                
                <!-- Edit Form (Initially Hidden) -->
                <div id="edit-form-<?php echo $location['flo_code']; ?>" class="edit-form" style="display: none;">
                    <form method="post">
                        <input type="hidden" name="action" value="update_location">
                        <input type="hidden" name="location_code" value="<?php echo htmlspecialchars($location['flo_code']); ?>">
                        
                        <div class="form-title">Edit <?php echo htmlspecialchars($location['flo_address']); ?></div>
                        
                        <div class="form-group">
                            <label for="edit_location_type_<?php echo $location['flo_code']; ?>">Location Type</label>
                            <select name="location_type" id="edit_location_type_<?php echo $location['flo_code']; ?>" class="form-control" required>
                                <option value="Branch" <?php echo $location['flo_type'] === 'Branch' ? 'selected' : ''; ?>>Branch</option>
                                <option value="Warehouse" <?php echo $location['flo_type'] === 'Warehouse' ? 'selected' : ''; ?>>Warehouse</option>
                                <option value="Office" <?php echo $location['flo_type'] === 'Office' ? 'selected' : ''; ?>>Office</option>
                                <option value="Field" <?php echo $location['flo_type'] === 'Field' ? 'selected' : ''; ?>>Field Location</option>
                                <option value="Remote" <?php echo $location['flo_type'] === 'Remote' ? 'selected' : ''; ?>>Remote Site</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_location_address_<?php echo $location['flo_code']; ?>">Address</label>
                            <input type="text" name="location_address" id="edit_location_address_<?php echo $location['flo_code']; ?>" 
                                   class="form-control" value="<?php echo htmlspecialchars($location['flo_address']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_location_desc_<?php echo $location['flo_code']; ?>">Description</label>
                            <textarea name="location_desc" id="edit_location_desc_<?php echo $location['flo_code']; ?>" 
                                      class="form-control" rows="3"><?php echo htmlspecialchars($location['flo_desc']); ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Location</button>
                            <button type="button" class="btn btn-secondary" 
                                    onclick="toggleEditForm('<?php echo $location['flo_code']; ?>');">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($locations)): ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="empty-state">
                <div class="empty-icon">📍</div>
                <div class="empty-text">No locations found</div>
                <div class="empty-desc">Click "Add New Location" to create your first location</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Hidden Delete Form -->
<form id="delete-form" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete_location">
    <input type="hidden" name="location_code" id="delete-location-code">
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

.location-card {
    background: white;
    border: 1px solid #ddd;
    padding: 25px;
    margin: 15px 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: box-shadow 0.3s ease;
}

.location-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.location-header {
    margin-bottom: 20px;
}

.location-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}

.location-name {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin: 0;
    flex: 1;
    min-width: 200px;
}

.location-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.location-type-branch { background: #e8f5e8; color: #2e7d2e; }
.location-type-warehouse { background: #fff3cd; color: #856404; }
.location-type-office { background: #d1ecf1; color: #0c5460; }
.location-type-field { background: #f8d7da; color: #721c24; }
.location-type-remote { background: #e2e3e5; color: #383d41; }

.location-meta {
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

.meta-value.users-count {
    color: #666;
}

.location-description {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    border-left: 4px solid #007bff;
}

.description-label {
    font-size: 12px;
    font-weight: bold;
    color: #666;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.description-text {
    color: #555;
    line-height: 1.4;
    font-size: 14px;
}

.location-actions {
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

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
    transform: translateY(-1px);
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

.edit-form {
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
    
    .location-card {
        padding: 20px 15px;
    }
    
    .location-title {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .location-name {
        font-size: 18px;
        min-width: auto;
    }
    
    .location-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .location-actions {
        flex-direction: column;
    }
    
    .location-actions .btn {
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
    .location-badge {
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

function toggleLocationForm(locationCode) {
    const form = document.getElementById('edit-form-' + locationCode);
    if (form) {
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
</script>
</body>
</html>
