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
    <title>Location Management</title>
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
    <!-- Header -->
    <div class="row">
        <div class="col-xs-12">
            <div class="admin-header">
                <div class="admin-title">Location Management</div>
                <div class="admin-actions">
                    <a href="dashboard.php" class="btn-back">← Dashboard</a>
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
                    <div class="location-address"><?php echo htmlspecialchars($location['flo_address']); ?></div>
                    <div class="location-type"><?php echo htmlspecialchars($location['flo_type']); ?></div>
                </div>
                
                <div class="location-details">
                    <div class="detail-item">
                        <span class="detail-label">Code:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($location['flo_code']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Users:</span>
                        <span class="detail-value"><?php echo $location['user_count']; ?> assigned</span>
                    </div>
                    <?php if (!empty($location['flo_desc'])): ?>
                    <div class="detail-item">
                        <span class="detail-label">Description:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($location['flo_desc']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="location-actions">
                    <button type="button" class="btn btn-small btn-primary" 
                            onclick="toggleEditForm('<?php echo $location['flo_code']; ?>');">
                        Edit
                    </button>
                    <button type="button" class="btn btn-small btn-danger" 
                            onclick="deleteLocation('<?php echo $location['flo_code']; ?>', '<?php echo addslashes($location['flo_address']); ?>', <?php echo $location['user_count']; ?>);">
                        Delete
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
/* Admin Locations Page Styles - Enhanced Mobile-First */
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    margin: -2px -2px 20px -2px;
    border-radius: 8px;
}

.admin-title {
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-back {
    color: white;
    text-decoration: none;
    padding: 8px 12px;
    background: rgba(255,255,255,0.2);
    border-radius: 5px;
    font-size: 12px;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: rgba(255,255,255,0.3);
}

.message-card {
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-weight: 500;
    text-align: center;
    font-size: 14px;
}

.message-card.success {
    background: #d4edda;
    color: #155724;
    border-left: 3px solid #28a745;
}

.message-card.error {
    background: #f8d7da;
    color: #721c24;
    border-left: 3px solid #dc3545;
}

.section-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 2px solid #eee;
    margin-bottom: 15px;
}

.title-text {
    font-size: 16px;
    font-weight: bold;
    color: #333;
}

.title-count {
    background: #28a745;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
}

.form-section {
    margin: 15px 0;
    animation: slideDown 0.3s ease;
}

.form-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.form-title {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #eee;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #333;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 2px solid #e8e8e8;
    border-radius: 6px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #fafafa;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #28a745;
    background: white;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
}

.form-control:hover {
    border-color: #ddd;
}

select.form-control {
    background: white;
    cursor: pointer;
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
    font-family: inherit;
}

.form-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    text-align: center;
    display: block;
    width: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-secondary {
    background: #f8f9fa;
    color: #666;
    border: 1px solid #e8e8e8;
}

.btn-secondary:hover {
    background: #e9ecef;
    color: #333;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

/* Location Cards - Mobile-First */
.location-card {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e0e0e0;
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

.location-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    border-color: #28a745;
}

.location-header {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
}

.location-address {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    line-height: 1.3;
}

.location-type {
    background: #28a745;
    color: white;
    padding: 3px 10px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    width: fit-content;
}

.location-details {
    margin-bottom: 12px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    padding: 4px 0;
    gap: 2px;
}

.detail-label {
    font-weight: 600;
    color: #666;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    color: #333;
    font-size: 13px;
    line-height: 1.4;
}

.location-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-small {
    padding: 8px 12px;
    font-size: 12px;
    border-radius: 5px;
    width: 100%;
}

.edit-form {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 2px solid #eee;
    animation: slideDown 0.3s ease;
}

.empty-state {
    text-align: center;
    padding: 30px 15px;
    color: #666;
}

.empty-icon {
    font-size: 40px;
    margin-bottom: 12px;
}

.empty-text {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 8px;
}

.empty-desc {
    font-size: 13px;
    opacity: 0.8;
    line-height: 1.4;
}

.action-buttons {
    margin: 20px 0;
    padding: 0;
}

.btn-add {
    width: 100%;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.btn-add .btn-icon {
    font-size: 18px;
}

.btn-add .btn-label {
    line-height: 1;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(40, 167, 69, 0.3);
}

.locations-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
    margin-top: 15px;
}

/* Tablet (768px and up) */
@media (min-width: 768px) {
    .admin-header {
        padding: 20px;
    }
    
    .admin-title {
        font-size: 24px;
    }
    
    .btn-back {
        font-size: 14px;
        padding: 8px 15px;
    }
    
    .form-card {
        padding: 25px;
        border-radius: 12px;
    }
    
    .form-title {
        font-size: 18px;
    }
    
    .form-actions {
        flex-direction: row;
        justify-content: flex-end;
    }
    
    .btn {
        width: auto;
        min-width: 120px;
    }
    
    .btn-add {
        width: auto;
        min-width: 200px;
        margin: 0 auto;
        display: inline-flex;
    }
    
    .action-buttons {
        text-align: center;
    }
    
    .locations-grid {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
    
    .location-header {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
    
    .detail-item {
        flex-direction: row;
        gap: 10px;
    }
    
    .detail-label {
        min-width: 100px;
        font-size: 12px;
    }
    
    .detail-value {
        font-size: 14px;
    }
    
    .location-actions {
        flex-direction: row;
        justify-content: flex-start;
    }
    
    .btn-small {
        width: auto;
    }
}

/* Desktop (1024px and up) */
@media (min-width: 1024px) {
    .location-card {
        padding: 20px;
    }
    
    .section-title {
        padding: 15px 0;
    }
    
    .title-text {
        font-size: 20px;
    }
    
    .title-count {
        font-size: 12px;
        padding: 5px 12px;
    }
    
    .location-address {
        font-size: 18px;
    }
    
    .locations-grid {
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
// Native JavaScript functions
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section.style.display === 'none') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

function toggleEditForm(locationCode) {
    const form = document.getElementById('edit-form-' + locationCode);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function deleteLocation(locationCode, locationAddress, userCount) {
    if (userCount > 0) {
        alert('Cannot delete location "' + locationAddress + '" because it has ' + userCount + ' user(s) assigned. Please remove user assignments first.');
        return;
    }
    
    if (confirm('Are you sure you want to delete location "' + locationAddress + '"? This action cannot be undone.')) {
        document.getElementById('delete-location-code').value = locationCode;
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
                        <div class="empty-state">
                            <div class="empty-icon">📍</div>
                            <div class="empty-text">No locations found</div>
                            <div class="empty-desc">Add your first location using the button above</div>
                        </div>
                        <?php else: ?>
                        <!-- Mobile-friendly location cards instead of table -->
                        <div class="locations-grid">
                            <?php foreach ($locations as $location): ?>
                            <div class="location-card">
                                <div class="location-header">
                                    <div class="location-address"><?php echo htmlspecialchars($location['flo_address']); ?></div>
                                    <div class="location-type"><?php echo htmlspecialchars($location['flo_type']); ?></div>
                                </div>
                                
                                <div class="location-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Code:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($location['flo_code']); ?></span>
                                    </div>
                                    <?php if (!empty($location['flo_desc'])): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Description:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($location['flo_desc']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Assigned Users:</span>
                                        <span class="detail-value"><?php echo $location['user_count']; ?> users</span>
                                    </div>
                                </div>
                                
                                <div class="location-actions">
                                    <button type="button" class="btn btn-small btn-primary" 
                                            onclick="editLocation('<?php echo htmlspecialchars($location['flo_code']); ?>', '<?php echo htmlspecialchars($location['flo_type']); ?>', '<?php echo htmlspecialchars($location['flo_address']); ?>', '<?php echo htmlspecialchars($location['flo_desc']); ?>')">
                                        Edit
                                    </button>
                                    <?php if ($location['user_count'] == 0): ?>
                                    <form method="post" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this location?');">
                                        <input type="hidden" name="action" value="delete_location">
                                        <input type="hidden" name="location_code" value="<?php echo htmlspecialchars($location['flo_code']); ?>">
                                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                    </form>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-small btn-danger" disabled title="Cannot delete location with assigned users">Delete</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
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