<?php
// Mawal Inventory Management System - PHP Backend
// Following Mawal Coding Specification: . → I → O → A → H

// Centralized storage path for persisted database
$databaseFile = __DIR__ . '/db/file.db';

/**
 * Ensure incoming database payloads always contain the expected table layout.
 */
function normalizeDatabaseStructure(array $data, array $defaultStructure): array
{
    foreach ($defaultStructure as $table => $rows) {
        if (!array_key_exists($table, $data) || !is_array($data[$table])) {
            $data[$table] = array();
        }
    }

    foreach ($data as $table => $rows) {
        if (!is_array($rows)) {
            $data[$table] = array();
        }
    }

    return $data;
}

/**
 * Load database from disk if available, otherwise fall back to the seeded data.
 */
function loadDatabaseFromFile(string $path, array $seed): array
{
    if (!file_exists($path)) {
        return $seed;
    }

    $contents = file_get_contents($path);
    if ($contents === false || trim($contents) === '') {
        return $seed;
    }

    $decoded = json_decode($contents, true);
    if (!is_array($decoded)) {
        return $seed;
    }

    return normalizeDatabaseStructure($decoded, $seed);
}

/**
 * Persist the current database state to disk using an atomic write.
 */
function persistDatabaseToFile(string $path, array $database): void
{
    $directory = dirname($path);
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Database directory is not accessible: ' . $directory);
    }

    if (!is_writable($directory)) {
        throw new RuntimeException('Database directory is not writable: ' . $directory);
    }

    if (file_exists($path) && !is_writable($path)) {
        throw new RuntimeException('Database file is not writable: ' . $path);
    }

    $payload = json_encode($database, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if ($payload === false) {
        $jsonError = function_exists('json_last_error_msg') ? json_last_error_msg() : 'JSON encode error';
        throw new RuntimeException('Failed to encode database payload: ' . $jsonError);
    }

    if (file_put_contents($path, $payload . PHP_EOL, LOCK_EX) === false) {
        $error = error_get_last();
        $message = $error['message'] ?? 'Unknown write failure';
        throw new RuntimeException('Failed to write database file: ' . $message);
    }
}

/**
 * Primary key mapping for known tables.
 */
function getPrimaryKeyField(string $table): string
{
    $mapping = array(
        'inv_aircondition' => 'iac_id',
        'inv_batteries' => 'iba_id',
        'inv_hardware' => 'ihd_id',
        'sys_images' => 'sig_id',
        'sys_location' => 'slc_id',
        'sys_users' => 'sus_id',
        'sys_user_locations' => 'sul_id'
    );

    return $mapping[$table] ?? 'id';
}

// . (dot): Raw Data - Embedded Database
$DATABASE = array(
    'sys_users' => array(
        array('sus_id' => 'USR0001', 'sus_username' => 'admin', 'sus_role' => 'admin', 'sus_password' => 'admin123', 'sus_rep_manager' => '', 'sus_location' => 'LOC0001', 'sus_cre_by' => 'system', 'sus_cre_date' => '2025-11-14', 'sus_remarks' => 'System Administrator'),
        array('sus_id' => 'USR0002', 'sus_username' => 'user', 'sus_role' => 'user', 'sus_password' => 'user123', 'sus_rep_manager' => 'admin', 'sus_location' => 'LOC0001', 'sus_cre_by' => 'admin', 'sus_cre_date' => '2025-11-14', 'sus_remarks' => 'Standard user')
    ),
    'sys_location' => array(
        array('slc_id' => 'LOC0001', 'slc_type' => 'Office', 'slc_address' => 'Main Building Floor 1', 'slc_desc' => 'Primary office location', 'slc_cre_by' => 'admin', 'slc_cre_date' => '2025-11-14'),
        array('slc_id' => 'LOC0002', 'slc_type' => 'Warehouse', 'slc_address' => 'Storage Building Floor 2', 'slc_desc' => 'Equipment storage location', 'slc_cre_by' => 'admin', 'slc_cre_date' => '2025-11-14'),
        array('slc_id' => 'LOC0003', 'slc_type' => 'Data Center', 'slc_address' => 'Server Room Building B', 'slc_desc' => 'IT equipment and server location', 'slc_cre_by' => 'admin', 'slc_cre_date' => '2025-11-14')
    ),
    'inv_aircondition' => array(
        array('iac_id' => 'IAC0001', 'iac_desc' => 'Central AC Unit - Main Hall', 'iac_location' => 'LOC0001', 'iac_operational' => 'Y', 'iac_cre_by' => 'admin', 'iac_cre_date' => '2025-11-14'),
        array('iac_id' => 'IAC0002', 'iac_desc' => 'AC Unit - Conference Room', 'iac_location' => 'LOC0001', 'iac_operational' => 'Y', 'iac_cre_by' => 'admin', 'iac_cre_date' => '2025-11-14')
    ),
    'inv_batteries' => array(
        array('iba_id' => 'IBA0001', 'iba_battery' => 'Li-Ion 12V', 'iba_operational' => 'Y', 'iba_camera_unit' => 'CAM-UNIT-001', 'iba_location' => 'LOC0001'),
        array('iba_id' => 'IBA0002', 'iba_battery' => 'NiMH 9V', 'iba_operational' => 'N', 'iba_camera_unit' => 'CAM-UNIT-002', 'iba_location' => 'LOC0002')
    ),
    'inv_hardware' => array(
        array('ihd_id' => 'IHD0001', 'ihd_type' => 'Computer', 'ihd_make' => 'Dell', 'ihd_model' => 'OptiPlex 3080', 'ihd_location' => 'LOC0001', 'ihd_cre_by' => 'admin', 'ihd_cre_date' => '2025-11-14'),
        array('ihd_id' => 'IHD0002', 'ihd_type' => 'Server', 'ihd_make' => 'HP', 'ihd_model' => 'ProLiant DL380', 'ihd_location' => 'LOC0003', 'ihd_cre_by' => 'admin', 'ihd_cre_date' => '2025-11-14')
    ),
    'sys_images' => array(),
    'sys_user_locations' => array(
        array('sul_id' => 1, 'sul_user_id' => 'USR0001', 'sul_location_id' => 'LOC0001', 'sul_assigned_date' => '2025-11-14', 'sul_assigned_by' => 'system', 'sul_status' => 'active'),
        array('sul_id' => 2, 'sul_user_id' => 'USR0002', 'sul_location_id' => 'LOC0001', 'sul_assigned_date' => '2025-11-14', 'sul_assigned_by' => 'admin', 'sul_status' => 'active')
    )
);

$DEFAULT_DATABASE = $DATABASE;
$DATABASE = loadDatabaseFromFile($databaseFile, $DEFAULT_DATABASE);

// I: Features - API Handler
if (isset($_REQUEST['action'])) {
    header('Content-Type: application/json');
    $action = $_REQUEST['action'];
    $table = $_REQUEST['table'] ?? '';
    
    switch ($action) {
        case 'get_database':
            echo json_encode(array('success' => true, 'data' => $DATABASE));
            exit;
            
        case 'get_table':
            echo json_encode(array('success' => true, 'data' => $DATABASE[$table] ?? array()));
            exit;
            
        case 'add_record':
            $record = json_decode($_POST['record'] ?? '{}', true);
            if ($table && isset($DATABASE[$table])) {
                $DATABASE[$table][] = $record;
                try {
                    persistDatabaseToFile($databaseFile, $DATABASE);
                    echo json_encode(array('success' => true, 'id' => $record['id'] ?? uniqid()));
                } catch (RuntimeException $exception) {
                    http_response_code(500);
                    echo json_encode(array('success' => false, 'error' => $exception->getMessage()));
                }
            } else {
                echo json_encode(array('success' => false, 'error' => 'Invalid table'));
            }
            exit;
            
        case 'update_record':
            $record = json_decode($_POST['record'] ?? '{}', true);
            $recordId = $_POST['id'] ?? '';
            if ($table && isset($DATABASE[$table])) {
                $primaryKey = getPrimaryKeyField($table);
                $targetId = $record[$primaryKey] ?? $recordId;
                $updated = false;

                if ($targetId) {
                    foreach ($DATABASE[$table] as $index => $existing) {
                        if (is_array($existing) && isset($existing[$primaryKey]) && $existing[$primaryKey] === $targetId) {
                            $DATABASE[$table][$index] = $record;
                            $updated = true;
                            break;
                        }
                    }
                }

                if (!$updated) {
                    $DATABASE[$table][] = $record;
                }

                try {
                    persistDatabaseToFile($databaseFile, $DATABASE);
                    echo json_encode(array('success' => true, 'updated' => $updated));
                } catch (RuntimeException $exception) {
                    http_response_code(500);
                    echo json_encode(array('success' => false, 'error' => $exception->getMessage()));
                }
            } else {
                echo json_encode(array('success' => false, 'error' => 'Invalid table'));
            }
            exit;
            
        case 'delete_record':
            if ($table && isset($DATABASE[$table])) {
                $recordId = $_POST['id'] ?? '';
                $primaryKey = getPrimaryKeyField($table);

                $DATABASE[$table] = array_values(array_filter(
                    $DATABASE[$table],
                    function ($row) use ($recordId, $primaryKey) {
                        if (!is_array($row)) {
                            return true;
                        }
                        if ($recordId === '') {
                            return true;
                        }
                        if (!isset($row[$primaryKey])) {
                            return true;
                        }

                        return $row[$primaryKey] !== $recordId;
                    }
                ));

                try {
                    persistDatabaseToFile($databaseFile, $DATABASE);
                    echo json_encode(array('success' => true));
                } catch (RuntimeException $exception) {
                    http_response_code(500);
                    echo json_encode(array('success' => false, 'error' => $exception->getMessage()));
                }
            } else {
                echo json_encode(array('success' => false, 'error' => 'Invalid table'));
            }
            exit;
            
        case 'save_record':
            $record = json_decode($_POST['record'], true);
            if ($table && isset($DATABASE[$table])) {
                $DATABASE[$table][] = $record;
                try {
                    persistDatabaseToFile($databaseFile, $DATABASE);
                    echo json_encode(array('success' => true));
                } catch (RuntimeException $exception) {
                    http_response_code(500);
                    echo json_encode(array('success' => false, 'error' => $exception->getMessage()));
                }
            } else {
                echo json_encode(array('success' => false, 'error' => 'Invalid table'));
            }
            exit;

        case 'save_database':
            $rawInput = file_get_contents('php://input');
            $payload = json_decode($rawInput, true);

            if (!is_array($payload) || !isset($payload['database']) || !is_array($payload['database'])) {
                http_response_code(400);
                echo json_encode(array('success' => false, 'error' => 'Invalid database payload'));
                exit;
            }

            $DATABASE = normalizeDatabaseStructure($payload['database'], $DEFAULT_DATABASE);

            try {
                persistDatabaseToFile($databaseFile, $DATABASE);
                echo json_encode(array('success' => true, 'data' => $DATABASE));
            } catch (RuntimeException $exception) {
                http_response_code(500);
                echo json_encode(array('success' => false, 'error' => $exception->getMessage()));
            }
            exit;
            
        default:
            echo json_encode(array('success' => false, 'error' => 'Unknown action: ' . $action));
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory App</title>
    <style>
        /* Minimal CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        .header {
            background: #fff;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        
        .header h1 {
            color: #333;
            font-size: 24px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* Navigation */
        .nav-tabs {
            background: #fff;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            gap: 5px;
            overflow-x: auto;
            width: 100%;
        }
        
        .tab-btn {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
            white-space: nowrap;
        }
        
        .tab-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            width: 100%;
            overflow-y: auto;
        }
        
        /* Login Form */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width: 100%;
        }
        
        .login-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
            min-width: 300px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        
        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 20px;
        }
        
        .data-table th,
        .data-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        /* Forms */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 4px;
            border: 1px solid #ddd;
            text-align: center;
            cursor: pointer;
        }
        
        .stat-card:hover {
            background: #f8f9fa;
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
        }
        
        /* Inventory Grid */
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }
        
        .inventory-card {
            background: white;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .inventory-card h3 {
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        
        .item-detail {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }
        
        /* Search */
        .search-section {
            background: white;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        }
        
        .toast {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
            padding: 16px 20px;
            border-left: 4px solid #007bff;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease-out;
            position: relative;
            min-height: 60px;
            display: flex;
            align-items: center;
        }
        
        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        .toast.hide {
            opacity: 0;
            transform: translateX(100%);
        }
        
        .toast-success {
            border-left-color: #28a745;
            background: linear-gradient(to right, #f8fff8, white);
        }
        
        .toast-error {
            border-left-color: #dc3545;
            background: linear-gradient(to right, #fff5f5, white);
        }
        
        .toast-info {
            border-left-color: #17a2b8;
            background: linear-gradient(to right, #f0f8ff, white);
        }
        
        .toast-warning {
            border-left-color: #ffc107;
            background: linear-gradient(to right, #fffbf0, white);
        }
        
        .toast-content {
            flex: 1;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .toast-icon {
            margin-right: 12px;
            font-size: 18px;
            font-weight: bold;
        }
        
        .toast-success .toast-icon {
            color: #28a745;
        }
        
        .toast-error .toast-icon {
            color: #dc3545;
        }
        
        .toast-info .toast-icon {
            color: #17a2b8;
        }
        
        .toast-warning .toast-icon {
            color: #ffc107;
        }
        
        .toast-close {
            position: absolute;
            top: 8px;
            right: 12px;
            background: none;
            border: none;
            font-size: 18px;
            color: #999;
            cursor: pointer;
            line-height: 1;
            padding: 0;
            width: 20px;
            height: 20px;
        }
        
        .toast-close:hover {
            color: #333;
        }
        
        /* Legacy alert styles for backward compatibility */
        .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: none;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        /* Utility Classes */
        .hidden {
            display: none !important;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mb-20 {
            margin-bottom: 20px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 10px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .nav-tabs {
                padding: 5px;
            }
            
            .tab-btn {
                padding: 8px 12px;
                font-size: 12px;
            }
            
            .main-content {
                padding: 10px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            
            .search-form {
                grid-template-columns: 1fr;
            }
            
            .data-table {
                font-size: 12px;
            }
            
            .data-table th,
            .data-table td {
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Toast Notification Container -->
    <div id="toast-container" class="toast-container"></div>
    
    <!-- Login Screen -->
    <div id="login-screen" class="login-container">
        <div class="login-form">
            <h2 style="text-align: center; margin-bottom: 20px;">📦 Inventory Login</h2>
            <form onsubmit="return handleLoginSubmit(event)">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" class="form-control" required>
                </div>
                <div class="form-group" id="location-group" style="display: none;">
                    <label for="login-location">Select Location:</label>
                    <select id="login-location" class="form-control">
                        <option value="">-- Select Location --</option>
                    </select>
                    <small style="color: #666; font-size: 11px;">You can only access locations you're authorized for</small>
                </div>
                <div id="login-error" style="color: #dc3545; font-size: 12px; margin-top: 5px; min-height: 18px;"></div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            <div style="margin-top: 15px; text-align: center; font-size: 12px; color: #666;">
                <p><strong>Admin:</strong> admin / admin123 | <strong>User:</strong> user / user123</p>
                <button onclick="testDatabase()" style="margin-top: 10px; padding: 5px 10px; font-size: 11px; background: #17a2b8; color: white; border: none; border-radius: 3px; cursor: pointer;">
                    🔧 Test Database Connection
                </button>
                <button onclick="quickLogin()" style="margin-top: 10px; padding: 5px 10px; font-size: 11px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; margin-left: 5px;">
                    ⚡ Quick Admin Login
                </button>
                <div id="db-test-result" style="margin-top: 5px; font-size: 10px;"></div>
            </div>
        </div>
        
        <!-- Logout message with login option -->
        <div id="logout-message" style="display: none; text-align: center; padding: 40px;">
            <h2 style="color: #666; margin-bottom: 20px;">✅ Logged out successfully</h2>
            <p style="color: #888; margin-bottom: 20px;">You have been logged out of the inventory system.</p>
            <button onclick="showLoginForm()" class="btn btn-primary">🔐 Login Again</button>
        </div>
    </div>

    <!-- Main Application -->
    <div id="main-app" class="hidden">
        <!-- Header -->
        <header class="header">
            <h1>📦 Inventory App</h1>
            <div class="user-info">
                <span id="user-display"></span>
                <button class="btn btn-danger btn-sm" onclick="logout()">Logout</button>
            </div>
        </header>

        <!-- Alert Container -->
        <div id="alert-container"></div>

        <!-- Navigation -->
        <nav class="nav-tabs" id="navigation">
            <!-- Navigation will be populated based on user role -->
        </nav>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Admin Dashboard Tab -->
            <div id="admin-dashboard-tab" class="tab-content hidden">
                <h2>📊 Admin Dashboard</h2>
                
                <!-- H - Product Unit: Database Management Controls -->
                <div class="save-controls" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                    <h3 style="margin-top: 0; color: #007bff;">💾 Database Management</h3>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 10px;">
                        <button onclick="getFileHandle()" style="background: #6f42c1; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            🔗 Connect to file.db
                        </button>
                        <div style="padding: 10px; background: #e8f5e8; border: 1px solid #28a745; border-radius: 4px;">
                            <span style="color: #28a745; font-weight: bold;">🔌 PHP Backend Connected</span>
                            <span id="save-status" style="margin-left: 10px; font-size: 0.9em; color: #6c757d;">Ready</span>
                        </div>
                    </div>
                    <div style="font-size: 0.8em; color: #6c757d; margin-top: 8px;">
                        <strong>� Connect to file.db:</strong> First, connect to your actual file.db to enable direct writing<br>
                        <strong>💾 Save to file.db:</strong> Writes directly to connected file.db or asks for location<br>
                        <strong>⚡ Auto-save:</strong> Automatically saves to memory and connected file when available
                    </div>
                </div>
                
                <div class="stats-grid" id="admin-stats">
                    <!-- Admin stats will be populated here -->
                </div>
            </div>

            <!-- Inventory Dashboard Tab -->
            <div id="inventory-dashboard-tab" class="tab-content hidden">
                <h2>📦 Inventory Dashboard</h2>
                
                <!-- Simple Auto-save Status -->
                <div class="save-controls" style="margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #28a745;">
                    <div style="display: flex; gap: 10px; align-items: center; font-size: 0.9em;">
                        <span style="color: #28a745;">⚡ Auto-save active</span>
                        <span id="userSaveStatus" style="color: #6c757d; font-size: 0.85em;">Ready</span>
                    </div>
                    <div style="font-size: 0.75em; color: #6c757d; margin-top: 5px;">
                        Changes are automatically saved to persistent storage.
                    </div>
                </div>
                
                <!-- Search Section -->
                <div class="search-section">
                    <h3>🔍 Search Inventory</h3>
                    <div class="search-form">
                        <div class="form-group">
                            <label for="search-type">Category:</label>
                            <select id="search-type" class="form-control">
                                <option value="">All Categories</option>
                                <option value="inv_aircondition">Air Condition</option>
                                <option value="inv_batteries">Batteries</option>
                                <option value="inv_hardware">Hardware</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="search-term">Search Term:</label>
                            <input type="text" id="search-term" class="form-control" placeholder="Enter search term...">
                        </div>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary" onclick="performSearch()">Search</button>
                        </div>
                    </div>
                </div>

                <!-- Inventory Stats -->
                <div class="stats-grid" id="inventory-stats">
                    <!-- Inventory stats will be populated here -->
                </div>

                <!-- Inventory Display -->
                <div id="inventory-display" class="inventory-grid">
                    <!-- Inventory items will be displayed here -->
                </div>
            </div>

            <!-- Air Condition Management Tab -->
            <div id="aircondition-tab" class="tab-content hidden">
                <h2>❄️ Air Condition Management</h2>
                <div class="mb-20">
                    <button class="btn btn-success" onclick="showAddForm('inv_aircondition')">➕ Add Air Condition Unit</button>
                </div>
                
                <form id="aircondition-form" class="form-grid hidden">
                    <div class="form-group">
                        <label for="iac_id">ID:</label>
                        <input type="text" id="iac_id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="iac_desc">Description:</label>
                        <input type="text" id="iac_desc" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="iac_location">Location:</label>
                        <select id="iac_location" class="form-control">
                            <option value="">Select Location</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="iac_operational">Operational:</label>
                        <select id="iac_operational" class="form-control">
                            <option value="">Select Status</option>
                            <option value="Y">Yes</option>
                            <option value="N">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success" onclick="saveRecord('inv_aircondition')">💾 Save</button>
                        <button type="button" class="btn btn-warning" onclick="cancelForm()">❌ Cancel</button>
                    </div>
                </form>

                <table class="data-table" id="aircondition-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Operational</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="aircondition-tbody">
                    </tbody>
                </table>
            </div>

            <!-- Similar tabs for Batteries and Hardware -->
            <div id="batteries-tab" class="tab-content hidden">
                <h2>🔋 Battery Management</h2>
                <div class="mb-20">
                    <button class="btn btn-success" onclick="showAddForm('inv_batteries')">➕ Add Battery Unit</button>
                </div>
                
                <form id="batteries-form" class="form-grid hidden">
                    <div class="form-group">
                        <label for="iba_id">ID:</label>
                        <input type="text" id="iba_id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="iba_battery">Battery Type:</label>
                        <input type="text" id="iba_battery" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="iba_operational">Operational:</label>
                        <select id="iba_operational" class="form-control">
                            <option value="">Select Status</option>
                            <option value="Y">Yes</option>
                            <option value="N">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="iba_camera_unit">Camera Unit:</label>
                        <input type="text" id="iba_camera_unit" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="iba_location">Location:</label>
                        <select id="iba_location" class="form-control">
                            <option value="">Select Location</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success" onclick="saveRecord('inv_batteries')">💾 Save</button>
                        <button type="button" class="btn btn-warning" onclick="cancelForm()">❌ Cancel</button>
                    </div>
                </form>

                <table class="data-table" id="batteries-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Battery Type</th>
                            <th>Operational</th>
                            <th>Camera Unit</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="batteries-tbody">
                    </tbody>
                </table>
            </div>

            <div id="hardware-tab" class="tab-content hidden">
                <h2>💻 Hardware Management</h2>
                <div class="mb-20">
                    <button class="btn btn-success" onclick="showAddForm('inv_hardware')">➕ Add Hardware Device</button>
                </div>
                
                <form id="hardware-form" class="form-grid hidden">
                    <div class="form-group">
                        <label for="ihd_id">ID:</label>
                        <input type="text" id="ihd_id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="ihd_type">Type:</label>
                        <input type="text" id="ihd_type" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ihd_make">Make:</label>
                        <input type="text" id="ihd_make" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ihd_model">Model:</label>
                        <input type="text" id="ihd_model" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ihd_location">Location:</label>
                        <select id="ihd_location" class="form-control">
                            <option value="">Select Location</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success" onclick="saveRecord('inv_hardware')">💾 Save</button>
                        <button type="button" class="btn btn-warning" onclick="cancelForm()">❌ Cancel</button>
                    </div>
                </form>

                <table class="data-table" id="hardware-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="hardware-tbody">
                    </tbody>
                </table>
            </div>

            <!-- Admin Only Tabs -->
            <div id="locations-tab" class="tab-content hidden">
                <h2>📍 Location Management</h2>
                <div class="mb-20">
                    <button class="btn btn-success" onclick="showAddForm('sys_location')">➕ Add Location</button>
                </div>
                
                <form id="locations-form" class="form-grid hidden">
                    <div class="form-group">
                        <label for="slc_id">ID:</label>
                        <input type="text" id="slc_id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="slc_type">Type:</label>
                        <input type="text" id="slc_type" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="slc_address">Address:</label>
                        <textarea id="slc_address" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="slc_desc">Description:</label>
                        <textarea id="slc_desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success" onclick="saveRecord('sys_location')">💾 Save</button>
                        <button type="button" class="btn btn-warning" onclick="cancelForm()">❌ Cancel</button>
                    </div>
                </form>

                <table class="data-table" id="locations-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Address</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="locations-tbody">
                    </tbody>
                </table>
            </div>

            <div id="users-tab" class="tab-content hidden">
                <h2>👤 User Management</h2>
                <div class="mb-20">
                    <button class="btn btn-success" onclick="showAddForm('sys_users')">➕ Add User</button>
                </div>
                
                <form id="users-form" class="form-grid hidden">
                    <div class="form-group">
                        <label for="sus_id">ID:</label>
                        <input type="text" id="sus_id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="sus_username">Username:</label>
                        <input type="text" id="sus_username" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="sus_role">Role:</label>
                        <select id="sus_role" class="form-control">
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sus_password">Password:</label>
                        <input type="password" id="sus_password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="sus_location">Location:</label>
                        <select id="sus_location" class="form-control">
                            <option value="">Select Location</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success" onclick="saveRecord('sys_users')">💾 Save</button>
                        <button type="button" class="btn btn-warning" onclick="cancelForm()">❌ Cancel</button>
                    </div>
                </form>

                <table class="data-table" id="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-tbody">
                    </tbody>
                </table>
            </div>

            <div id="user-locations-tab" class="tab-content hidden">
                <h2>🔗 User Location Assignments</h2>
                <div class="mb-20">
                    <button class="btn btn-success" onclick="showAddForm('sys_user_locations')">➕ Assign User to Location</button>
                </div>
                
                <form id="user-locations-form" class="form-grid hidden">
                    <div class="form-group">
                        <label for="sul_id">ID:</label>
                        <input type="text" id="sul_id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="sul_user_id">User:</label>
                        <select id="sul_user_id" class="form-control" required>
                            <option value="">Select User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sul_location_id">Location:</label>
                        <select id="sul_location_id" class="form-control" required>
                            <option value="">Select Location</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sul_status">Status:</label>
                        <select id="sul_status" class="form-control">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success" onclick="saveRecord('sys_user_locations')">💾 Save</button>
                        <button type="button" class="btn btn-warning" onclick="cancelForm()">❌ Cancel</button>
                    </div>
                </form>

                <table class="data-table" id="user-locations-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="user-locations-tbody">
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // IMMEDIATE TEST: Check if JavaScript is working
        console.log('🚀 JavaScript is loading...');
        
        // Inventory System - Role-Based JavaScript
        // Following Aseel Language Pattern: . → I → O → A → H
        
        // . (DOT) - Raw Data Sources and Storage
        let DB = {};
        let currentUser = null;
        let currentEditRecord = null;
        
        // PHP Backend Database Operations via AJAX
        async function initDatabase() {
            try {
                console.log('🔌 Loading database from PHP backend...');
                
                const response = await fetch('?action=get_database');
                const result = await response.json();
                
                if (result.success) {
                    DB = result.data;
                    console.log('✅ Database loaded from PHP backend');
                    console.log('📊 Tables loaded:', Object.keys(DB));
                    console.log('👥 Users available:', DB.sys_users?.map(u => u.sus_username) || []);
                    return DB;
                } else {
                    throw new Error('API returned success: false');
                }
            } catch (error) {
                console.error('❌ Database load error:', error);
                console.log('📝 Error details:', error.message);
                DB = {
                    sys_users: [],
                    sys_location: [],
                    inv_aircondition: [],
                    inv_batteries: [],
                    inv_hardware: [],
                    sys_images: [],
                    sys_user_locations: []
                };
                console.log('⚠️ Using empty fallback database');
                return DB;
            }
        }
        
        // Quick login function - CLEAN VERSION
        function quickLogin() {
            console.log('Quick login started');
            
            var resultDiv = document.getElementById('db-test-result');
            if (!resultDiv) return;
            
            resultDiv.textContent = 'Connecting...';
            
            // Simple direct approach without complex nesting
            var loginSuccess = function() {
                resultDiv.textContent = 'Quick login successful!';
                resultDiv.style.color = 'green';
            };
            
            var loginError = function(msg) {
                resultDiv.textContent = 'Error: ' + msg;
                resultDiv.style.color = 'red';
            };
            
            // Try to load database and login
            initDatabase().then(function() {
                if (DB && DB.sys_users) {
                    var admin = null;
                    for (var i = 0; i < DB.sys_users.length; i++) {
                        if (DB.sys_users[i].sus_username === 'admin') {
                            admin = DB.sys_users[i];
                            break;
                        }
                    }
                    
                    if (admin) {
                        currentUser = admin;
                        sessionStorage.setItem('mawal_current_user', JSON.stringify(admin));
                        sessionStorage.setItem('mawal_login_time', Date.now().toString());
                        
                        document.getElementById('login-screen').classList.add('hidden');
                        document.getElementById('main-app').classList.remove('hidden');
                        
                        updateUserDisplay();
                        initializeNavigation();
                        showDefaultTab();
                        
                        loginSuccess();
                    } else {
                        loginError('Admin user not found');
                    }
                } else {
                    loginError('Database not loaded');
                }
            }).catch(function(error) {
                loginError(error.message || 'Unknown error');
            });
        }
        
        // Test function to debug database loading
        async function testDatabase() {
            const resultDiv = document.getElementById('db-test-result');
            resultDiv.textContent = '🔄 Testing...';
            
            try {
                console.log('=== DATABASE TEST START ===');
                await initDatabase();
                
                const userCount = DB.sys_users ? DB.sys_users.length : 0;
                const locationCount = DB.sys_location ? DB.sys_location.length : 0;
                
                resultDiv.innerHTML = `
                    ✅ Database loaded!<br>
                    Users: ${userCount}, Locations: ${locationCount}<br>
                    Available users: ${DB.sys_users ? DB.sys_users.map(u => u.sus_username).join(', ') : 'None'}
                `;
                resultDiv.style.color = 'green';
                
                console.log('Test completed successfully');
                console.log('Database state:', DB);
                
            } catch (error) {
                resultDiv.textContent = `❌ Error: ${error.message}`;
                resultDiv.style.color = 'red';
                console.error('Database test failed:', error);
            }
        }
        
        // Function registration check
        console.log('🔧 testDatabase function defined:', typeof testDatabase);
        
        // Persistence control (O stage — Program Module Core)
        const AUTO_SAVE_DELAY_MS = 1500;
        let autoSaveEnabled = true;
        let autoSaveTimeout = null;

        function saveToLocalStorage() {
            try {
                localStorage.setItem('mawal_inventory_db', JSON.stringify(DB));
                console.log('💾 Database cached in localStorage');
                return true;
            } catch (error) {
                console.warn('⚠️ localStorage backup failed:', error);
                return false;
            }
        }

        function updateSaveStatus(message) {
            const adminStatus = document.getElementById('saveStatus');
            const userStatus = document.getElementById('userSaveStatus');

            let color = '#6c757d';
            if (message.includes('failed') || message.includes('❌')) {
                color = '#dc3545';
            } else if (message.includes('Saved') || message.includes('saved') || message.includes('✓')) {
                color = '#28a745';
            }

            if (adminStatus) {
                adminStatus.textContent = message;
                adminStatus.style.color = color;
            }
            if (userStatus) {
                userStatus.textContent = message;
                userStatus.style.color = color;
            }

            if (message.includes('✓')) {
                setTimeout(() => {
                    if (adminStatus && adminStatus.textContent === message) {
                        adminStatus.textContent = 'Ready';
                        adminStatus.style.color = '#6c757d';
                    }
                    if (userStatus && userStatus.textContent === message) {
                        userStatus.textContent = 'Ready';
                        userStatus.style.color = '#6c757d';
                    }
                }, 2500);
            }
        }

        async function persistDatabase(reason = 'auto') {
            if (!DB) {
                throw new Error('Database not loaded');
            }

            const response = await fetch('?action=save_database', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    database: DB,
                    reason: reason,
                    savedAt: new Date().toISOString()
                })
            });

            let result = null;
            try {
                result = await response.json();
            } catch (error) {
                throw new Error('Server returned invalid JSON');
            }

            if (!response.ok || !result.success) {
                const message = result && result.error ? result.error : `HTTP ${response.status}`;
                throw new Error(message);
            }

            if (result.data) {
                DB = result.data;
            }

            return true;
        }

        async function autoSaveDatabase(reason = 'auto') {
            if (!autoSaveEnabled) {
                return false;
            }

            try {
                saveToLocalStorage();
                updateSaveStatus('Saving...');
                await persistDatabase(reason);
                updateSaveStatus('Saved ✓');
                return true;
            } catch (error) {
                console.error('❌ Auto-save failed:', error);
                updateSaveStatus('Save failed ❌');
                showAlert('error', 'Save failed: ' + error.message);
                return false;
            }
        }

        function scheduleAutoSave(reason = 'auto') {
            if (!autoSaveEnabled) {
                return;
            }

            if (autoSaveTimeout) {
                clearTimeout(autoSaveTimeout);
            }

            autoSaveTimeout = setTimeout(() => {
                autoSaveDatabase(reason);
            }, AUTO_SAVE_DELAY_MS);
        }

        async function saveDatabase() {
            updateSaveStatus('Saving...');
            try {
                saveToLocalStorage();
                await persistDatabase('manual');
                updateSaveStatus('Saved ✓');
                showAlert('success', 'Database saved to server');
            } catch (error) {
                updateSaveStatus('Save failed ❌');
                showAlert('error', 'Failed to save: ' + error.message);
            }
        }

        function enableAutoSave() {
            autoSaveEnabled = true;
            console.log('⚡ Auto-save enabled');
        }

        function disableAutoSave() {
            autoSaveEnabled = false;
            if (autoSaveTimeout) {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = null;
            }
            console.log('⏸️ Auto-save disabled');
        }

        function toggleAutoSave() {
            const toggle = document.getElementById('autoSaveToggle');
            if (toggle && toggle.checked) {
                enableAutoSave();
                updateSaveStatus('Auto-save enabled');
            } else {
                disableAutoSave();
                updateSaveStatus('Auto-save disabled');
            }
        }
        
        // I - Feature Definition: Authentication and Role Management
        
        // Function to check username and populate authorized locations
        async function checkUsernameAndShowLocations() {
            const username = document.getElementById('username').value.trim();
            const locationGroup = document.getElementById('location-group');
            const locationSelect = document.getElementById('login-location');
            
            if (!username) {
                locationGroup.style.display = 'none';
                locationSelect.removeAttribute('required'); // Remove required when hidden
                return;
            }
            
            // Ensure database is loaded
            if (!DB || !DB.sys_users) {
                await initDatabase();
            }
            
            // Find user
            const user = DB.sys_users.find(u => u.sus_username === username);
            
            if (!user) {
                locationGroup.style.display = 'none';
                locationSelect.removeAttribute('required'); // Remove required for invalid users
                return;
            }
            
            // Admin users don't need location selection
            if (user.sus_role === 'admin') {
                locationGroup.style.display = 'none';
                locationSelect.removeAttribute('required'); // Remove required for admin users
                return;
            }
            
            // For regular users, show location dropdown
            locationGroup.style.display = 'block';
            locationSelect.setAttribute('required', 'required'); // Add required for regular users
            
            // Get user's authorized locations
            const userLocations = DB.sys_user_locations.filter(ul => 
                ul.sul_user_id === user.sus_id && ul.sul_status === 'active'
            );
            
            // Clear and populate location dropdown
            locationSelect.innerHTML = '<option value="">-- Select Location --</option>';
            
            userLocations.forEach(userLoc => {
                const location = DB.sys_location.find(loc => loc.slc_id === userLoc.sul_location_id);
                if (location) {
                    const option = document.createElement('option');
                    option.value = location.slc_id;
                    option.textContent = `${location.slc_desc} (${location.slc_type})`;
                    locationSelect.appendChild(option);
                }
            });
            
            // If no authorized locations, show message
            if (userLocations.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No authorized locations';
                option.disabled = true;
                locationSelect.appendChild(option);
            }
        }
        
        function handleLoginSubmit(event) {
            console.log('=== FORM SUBMIT TRIGGERED ===');
            event.preventDefault();
            
            // Add visual feedback
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Logging in...';
            submitBtn.disabled = true;
            
            // Call the async login function
            handleLogin().catch(error => {
                console.error('Login error:', error);
                document.getElementById('login-error').textContent = 'Login failed: ' + error.message;
            }).finally(() => {
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
            
            return false; // Prevent form submission
        }
        
        async function handleLogin() {
            try {
                console.log('=== LOGIN DEBUG START ===');
                console.log('Login function called');
                
                // Clear any previous error messages
                const errorDiv = document.getElementById('login-error');
                errorDiv.textContent = '';
                
                console.log('🔍 Checking credentials...');
                
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value;
                
                console.log('Input values:', { username: username, password: password ? 'PROVIDED' : 'EMPTY' });
                
                // Validate input fields
                if (!username) {
                    errorDiv.textContent = 'Please enter your username';
                    document.getElementById('username').focus();
                    return;
                }
                
                if (!password) {
                    errorDiv.textContent = 'Please enter your password';
                    document.getElementById('password').focus();
                    return;
                }
                
                console.log('Login attempt:', username, '(password hidden)');
                
                // Ensure database is loaded - wait if necessary
                if (!DB || !DB.sys_users) {
                    console.log('Database not ready, initializing...');
                    console.log('📊 Loading database...');
                    await initDatabase();
                }
                
                console.log('Current DB state:', DB);
                console.log('Database users:', DB.sys_users);
                
                // Ensure database is loaded
                if (!DB.sys_users || DB.sys_users.length === 0) {
                    errorDiv.textContent = 'Database not loaded. Please refresh the page.';
                    console.log('❌ Database users empty or not loaded');
                    return;
                }
                
                // First check if username exists
                const userExists = DB.sys_users.find(u => u.sus_username === username);
                console.log('User exists check:', userExists ? 'FOUND' : 'NOT FOUND');
                
                if (!userExists) {
                    errorDiv.textContent = `Username "${username}" not found. Please check your username.`;
                    console.log('Username not found:', username);
                    console.log('Available usernames:', DB.sys_users.map(u => u.sus_username));
                    document.getElementById('username').focus();
                    return;
                }
                
                // Check if password is correct for this username
                const user = DB.sys_users.find(u => 
                    u.sus_username === username && u.sus_password === password
                );
                
                console.log('Password check result:', user ? 'CORRECT' : 'INCORRECT');
                console.log('Expected password for', username + ':', userExists.sus_password);
                console.log('Provided password:', password);
                
                if (!user) {
                    errorDiv.textContent = 'Invalid password. Please try again.';
                    document.getElementById('password').focus();
                    document.getElementById('password').value = '';
                    return;
                }
                
                console.log('Found user:', user ? 'Yes' : 'No');
                
                if (user) {
                    // For admin users, skip location check
                    if (user.sus_role === 'admin') {
                        console.log('Admin user - skipping location check');
                        // Set current user
                        currentUser = user;
                        
                        // Save to session storage
                        sessionStorage.setItem('mawal_current_user', JSON.stringify(user));
                        sessionStorage.setItem('mawal_login_time', Date.now().toString());
                        
                        // Hide login screen, show main app
                        document.getElementById('login-screen').classList.add('hidden');
                        document.getElementById('main-app').classList.remove('hidden');
                        
                        updateUserDisplay();
                        initializeNavigation();
                        showDefaultTab();
                        
                        console.log(`✅ Admin login successful: Welcome ${user.sus_username}!`);
                        return;
                    }
                    
                    // For regular users, check location authorization
                    if (user.sus_role === 'user') {
                        const selectedLocation = document.getElementById('login-location').value;
                        console.log('Selected location:', selectedLocation);
                        
                        if (!selectedLocation) {
                            errorDiv.textContent = 'Please select a location to continue.';
                            document.getElementById('login-location').focus();
                            return;
                        }
                        
                        // Check if user is authorized for this location
                        const userLocationAccess = DB.sys_user_locations.find(ul => 
                            ul.sul_user_id === user.sus_id && 
                            ul.sul_location_id === selectedLocation && 
                            ul.sul_status === 'active'
                        );
                        
                        console.log('User location access check:', userLocationAccess ? 'AUTHORIZED' : 'NOT AUTHORIZED');
                        
                        if (!userLocationAccess) {
                            const location = DB.sys_location.find(loc => loc.slc_id === selectedLocation);
                            const locationName = location ? location.slc_desc : selectedLocation;
                            errorDiv.textContent = `❌ Access denied: You are not authorized to access "${locationName}". Please contact your administrator.`;
                            console.log(`Access denied for user ${username} to location ${selectedLocation}`);
                            return;
                        }
                        
                        // Add selected location to user object for session
                        user.selected_location = selectedLocation;
                        const location = DB.sys_location.find(loc => loc.slc_id === selectedLocation);
                        user.selected_location_name = location ? location.slc_desc : selectedLocation;
                        
                        console.log(`✅ Location authorized: ${user.selected_location_name}`);
                    }
                    
                    // Clear any error messages on success
                    errorDiv.textContent = '';
                    currentUser = user;
                    
                    // Save login session
                    sessionStorage.setItem('mawal_current_user', JSON.stringify(user));
                    sessionStorage.setItem('mawal_login_time', Date.now().toString());
                    
                    const locationInfo = user.selected_location_name ? ` at ${user.selected_location_name}` : '';
                    console.log(`✅ Login successful: Welcome ${user.sus_username}! Role: ${user.sus_role}${locationInfo}`);
                    
                    document.getElementById('login-screen').classList.add('hidden');
                    document.getElementById('main-app').classList.remove('hidden');
                    updateUserDisplay();
                    initializeNavigation();
                    showDefaultTab();
                } else {
                    // Username exists but password is wrong
                    errorDiv.textContent = 'Incorrect password. Please try again.';
                    console.log('Password incorrect for user:', username);
                    
                    // Clear the password field but keep username
                    document.getElementById('password').value = '';
                    document.getElementById('password').focus();
                }
            } catch (error) {
                console.error('Login error:', error);
                const errorDiv = document.getElementById('login-error');
                errorDiv.textContent = 'Login failed: ' + error.message;
            }
        }
        
        function logout() {
            currentUser = null;
            
            // Clear session storage
            sessionStorage.removeItem('mawal_current_user');
            sessionStorage.removeItem('mawal_login_time');
            
            // Get elements
            const loginScreen = document.getElementById('login-screen');
            const mainApp = document.getElementById('main-app');
            
            // Show login screen and hide main app
            if (loginScreen) {
                loginScreen.classList.remove('hidden');
                loginScreen.style.display = '';
                
                // Get form and message elements
                const loginForm = loginScreen.querySelector('.login-form');
                const logoutMessage = document.getElementById('logout-message');
                
                if (loginForm && logoutMessage) {
                    // Hide login form and show logout message
                    loginForm.style.display = 'none';
                    logoutMessage.style.display = 'block';
                    loginScreen.setAttribute('data-logout-state', 'true');
                } else {
                    // Fallback: ensure login form is visible if logout message fails
                    if (loginForm) {
                        loginForm.style.display = '';
                    }
                }
            }
            
            if (mainApp) {
                mainApp.classList.add('hidden');
            }
            
            // Clear login form values
            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('password');
            const locationSelect = document.getElementById('login-location');
            const locationGroup = document.getElementById('location-group');
            
            if (usernameField) usernameField.value = '';
            if (passwordField) passwordField.value = '';
            if (locationSelect) locationSelect.selectedIndex = 0;
            if (locationGroup) locationGroup.style.display = 'none';
            
            // Clear login error message
            const errorDiv = document.getElementById('login-error');
            if (errorDiv) {
                errorDiv.textContent = '';
            }
            
            // Reset location selection and requirements
            const logoutLocationGroup = document.getElementById('location-group');
            const logoutLocationSelect = document.getElementById('login-location');
            if (logoutLocationGroup && logoutLocationSelect) {
                logoutLocationGroup.style.display = 'none';
                logoutLocationSelect.removeAttribute('required');
                logoutLocationSelect.value = '';
            }
            
            // Hide all tabs and forms in the main app only
            hideAllTabs();
            
            // Hide only the data entry forms, not the login form
            const appForms = document.querySelectorAll('#main-app form');
            appForms.forEach(form => {
                form.classList.add('hidden');
            });
            
            // Clear any edit state
            currentEditRecord = null;
            
            console.log('✅ Logged out successfully');
        }
        
        function showLoginForm() {
            const loginScreen = document.getElementById('login-screen');
            const loginForm = document.querySelector('.login-form');
            const logoutMessage = document.getElementById('logout-message');
            
            // Clear the logout state flag
            if (loginScreen) {
                loginScreen.removeAttribute('data-logout-state');
            }
            
            if (loginForm) {
                loginForm.style.display = '';
                loginForm.style.visibility = 'visible';
                loginForm.style.opacity = '1';
            }
            
            if (logoutMessage) {
                logoutMessage.style.display = 'none';
            }
            
            // Reset location selection and requirements for fresh login
            const showLocationGroup = document.getElementById('location-group');
            const showLocationSelect = document.getElementById('login-location');
            if (showLocationGroup && showLocationSelect) {
                showLocationGroup.style.display = 'none';
                showLocationSelect.removeAttribute('required');
                showLocationSelect.value = '';
            }
            
            // Clear form fields
            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('password');
            if (usernameField) usernameField.value = '';
            if (passwordField) passwordField.value = '';
            
            // Clear error messages
            const errorDiv = document.getElementById('login-error');
            if (errorDiv) errorDiv.textContent = '';
            
            // Focus on username field
            setTimeout(() => {
                if (usernameField) {
                    usernameField.focus();
                }
            }, 100);
        }
        
        // Emergency function to force login form to appear
        function forceShowLoginForm() {
            const loginScreen = document.getElementById('login-screen');
            const loginForm = document.querySelector('.login-form');
            const logoutMessage = document.getElementById('logout-message');
            
            if (!loginScreen) {
                return;
            }
            
            if (!loginForm) {
                recreateLoginForm();
                return;
            }
            
            // Force show everything
            loginScreen.classList.remove('hidden');
            loginScreen.style.display = 'flex';
            loginScreen.style.visibility = 'visible';
            loginScreen.removeAttribute('data-logout-state');
            
            loginForm.style.display = 'block';
            loginForm.style.visibility = 'visible';
            loginForm.style.opacity = '1';
            
            if (logoutMessage) {
                logoutMessage.style.display = 'none';
            }
            
            // Hide main app
            const mainApp = document.getElementById('main-app');
            if (mainApp) {
                mainApp.classList.add('hidden');
            }
            
            // Focus username field
            setTimeout(() => {
                const username = document.getElementById('username');
                if (username) {
                    username.focus();
                }
            }, 200);
        }
        
        // Function to recreate login form if it's missing from DOM
        function recreateLoginForm() {
            const loginScreen = document.getElementById('login-screen');
            if (!loginScreen) {
                return;
            }
            
            loginScreen.innerHTML = `
                <div class="login-form">
                    <h2 style="text-align: center; margin-bottom: 20px;">📦 Inventory Login</h2>
                    <form onsubmit="return handleLoginSubmit(event)">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group" id="location-group" style="display: none;">
                            <label for="login-location">Select Location:</label>
                            <select id="login-location" class="form-control">
                                <option value="">-- Select Location --</option>
                            </select>
                            <small style="color: #666; font-size: 11px;">You can only access locations you're authorized for</small>
                        </div>
                        <div id="login-error" style="color: #dc3545; font-size: 12px; margin-top: 5px; min-height: 18px;"></div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                    </form>
                    <div style="margin-top: 15px; text-align: center; font-size: 12px; color: #666;">
                        <p><strong>Admin:</strong> admin / admin123 | <strong>User:</strong> user / user123</p>
                    </div>
                </div>
                
                <!-- Logout message with login option -->
                <div id="logout-message" style="display: none; text-align: center; padding: 40px;">
                    <h2 style="color: #666; margin-bottom: 20px;">✅ Logged out successfully</h2>
                    <p style="color: #888; margin-bottom: 20px;">You have been logged out of the inventory system.</p>
                    <button onclick="showLoginForm()" class="btn btn-primary">🔐 Login Again</button>
                </div>
                    <small style="color: #999;">Login form not showing? Press Ctrl+L or use buttons above</small>
                </div>
            `;
        }
        
        function updateUserDisplay() {
            const display = document.getElementById('user-display');
            const locationInfo = currentUser.selected_location_name ? ` @ ${currentUser.selected_location_name}` : '';
            display.textContent = `👤 ${currentUser.sus_username} (${currentUser.sus_role})${locationInfo}`;
        }
        
        // O - Program Module Core: Navigation and Role-Based Access
        function initializeNavigation() {
            const nav = document.getElementById('navigation');
            nav.innerHTML = '';
            
            if (currentUser.sus_role === 'admin') {
                // Admin can see: Dashboard + Users + Locations + User Locations
                nav.innerHTML = `
                    <button class="tab-btn active" onclick="showTab('admin-dashboard-tab')">📊 Dashboard</button>
                    <button class="tab-btn" onclick="showTab('users-tab')">👤 Users</button>
                    <button class="tab-btn" onclick="showTab('locations-tab')">📍 Locations</button>
                    <button class="tab-btn" onclick="showTab('user-locations-tab')">🔗 User Locations</button>
                `;
            } else {
                // Users can see: Inventory Dashboard + Inventory Management
                nav.innerHTML = `
                    <button class="tab-btn active" onclick="showTab('inventory-dashboard-tab')">📦 Dashboard</button>
                    <button class="tab-btn" onclick="showTab('aircondition-tab')">❄️ Air Condition</button>
                    <button class="tab-btn" onclick="showTab('batteries-tab')">🔋 Batteries</button>
                    <button class="tab-btn" onclick="showTab('hardware-tab')">💻 Hardware</button>
                `;
            }
        }
        
        function showDefaultTab() {
            try {
                if (currentUser.sus_role === 'admin') {
                    showTab('admin-dashboard-tab');
                    loadAdminDashboard();
                } else {
                    showTab('inventory-dashboard-tab');
                    loadInventoryDashboard();
                }
            } catch (error) {
                console.error('Error in showDefaultTab:', error);
                console.error('❌ Error loading interface:', error.message);
            }
        }
        
        // A - Application Model: Tab Management and Data Operations
        function showTab(tabId, clickedButton = null) {
            // Hide all tabs
            hideAllTabs();
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabId).classList.remove('hidden');
            
            // Add active class to clicked button or find the right button
            if (clickedButton) {
                clickedButton.classList.add('active');
            } else if (event && event.target) {
                event.target.classList.add('active');
            } else {
                // Find the button that corresponds to this tab
                const buttons = document.querySelectorAll('.tab-btn');
                buttons.forEach(btn => {
                    if (btn.onclick && btn.onclick.toString().includes(tabId)) {
                        btn.classList.add('active');
                    }
                });
                // Fallback: make first button active
                if (buttons.length > 0) {
                    buttons[0].classList.add('active');
                }
            }
            
            // Load tab data
            loadTabData(tabId);
        }
        
        function hideAllTabs() {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
        }
        
        function loadTabData(tabId) {
            switch(tabId) {
                case 'admin-dashboard-tab':
                    loadAdminDashboard();
                    break;
                case 'inventory-dashboard-tab':
                    loadInventoryDashboard();
                    break;
                case 'aircondition-tab':
                    loadTable('inv_aircondition');
                    populateLocationDropdowns();
                    break;
                case 'batteries-tab':
                    loadTable('inv_batteries');
                    populateLocationDropdowns();
                    break;
                case 'hardware-tab':
                    loadTable('inv_hardware');
                    populateLocationDropdowns();
                    break;
                case 'locations-tab':
                    loadTable('sys_location');
                    break;
                case 'users-tab':
                    loadTable('sys_users');
                    populateLocationDropdowns();
                    break;
                case 'user-locations-tab':
                    loadTable('sys_user_locations');
                    populateUserLocationDropdowns();
                    break;
            }
        }
        
        // H - Product Unit: Dashboard and Statistics
        function loadAdminDashboard() {
            const statsContainer = document.getElementById('admin-stats');
            const userCount = DB.sys_users.length;
            const locationCount = DB.sys_location.length;
            const assignmentCount = DB.sys_user_locations.length;
            
            statsContainer.innerHTML = `
                <div class="stat-card" onclick="showTab('users-tab')">
                    <div class="stat-number">${userCount}</div>
                    <div>Total Users</div>
                </div>
                <div class="stat-card" onclick="showTab('locations-tab')">
                    <div class="stat-number">${locationCount}</div>
                    <div>Locations</div>
                </div>
                <div class="stat-card" onclick="showTab('user-locations-tab')">
                    <div class="stat-number">${assignmentCount}</div>
                    <div>User Assignments</div>
                </div>
            `;
        }
        
        function loadInventoryDashboard() {
            const statsContainer = document.getElementById('inventory-stats');
            const acCount = DB.inv_aircondition.length;
            const batteryCount = DB.inv_batteries.length;
            const hardwareCount = DB.inv_hardware.length;
            const totalItems = acCount + batteryCount + hardwareCount;
            
            statsContainer.innerHTML = `
                <div class="stat-card" onclick="showTab('aircondition-tab')">
                    <div class="stat-number">${acCount}</div>
                    <div>Air Condition Units</div>
                </div>
                <div class="stat-card" onclick="showTab('batteries-tab')">
                    <div class="stat-number">${batteryCount}</div>
                    <div>Battery Units</div>
                </div>
                <div class="stat-card" onclick="showTab('hardware-tab')">
                    <div class="stat-number">${hardwareCount}</div>
                    <div>Hardware Devices</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${totalItems}</div>
                    <div>Total Items</div>
                </div>
            `;
            
            // Load recent inventory items
            loadInventoryItems();
        }
        
        function loadInventoryItems() {
            const container = document.getElementById('inventory-display');
            container.innerHTML = '';
            
            // Air Condition Items
            DB.inv_aircondition.forEach(item => {
                container.innerHTML += createInventoryCard('❄️ Air Condition', item, [
                    { label: 'ID', value: item.iac_id },
                    { label: 'Description', value: item.iac_desc || 'N/A' },
                    { label: 'Location', value: item.iac_location || 'N/A' },
                    { label: 'Operational', value: item.iac_operational || 'N/A' }
                ]);
            });
            
            // Battery Items
            DB.inv_batteries.forEach(item => {
                container.innerHTML += createInventoryCard('🔋 Battery', item, [
                    { label: 'ID', value: item.iba_id },
                    { label: 'Battery Type', value: item.iba_battery || 'N/A' },
                    { label: 'Camera Unit', value: item.iba_camera_unit || 'N/A' },
                    { label: 'Location', value: item.iba_location || 'N/A' },
                    { label: 'Operational', value: item.iba_operational || 'N/A' }
                ]);
            });
            
            // Hardware Items
            DB.inv_hardware.forEach(item => {
                container.innerHTML += createInventoryCard('💻 Hardware', item, [
                    { label: 'ID', value: item.ihd_id },
                    { label: 'Type', value: item.ihd_type || 'N/A' },
                    { label: 'Make', value: item.ihd_make || 'N/A' },
                    { label: 'Model', value: item.ihd_model || 'N/A' },
                    { label: 'Location', value: item.ihd_location || 'N/A' }
                ]);
            });
        }
        
        function createInventoryCard(category, item, details) {
            const detailsHtml = details.map(detail => 
                `<div class="item-detail">
                    <strong>${detail.label}:</strong>
                    <span>${detail.value}</span>
                </div>`
            ).join('');
            
            return `
                <div class="inventory-card">
                    <h3>${category}</h3>
                    ${detailsHtml}
                </div>
            `;
        }
        
        // Data Management Functions
        function loadTable(tableName) {
            const data = DB[tableName] || [];
            const tableBody = document.getElementById(getTableBodyId(tableName));
            
            if (!tableBody) return;
            
            tableBody.innerHTML = '';
            
            data.forEach(record => {
                const row = createTableRow(tableName, record);
                tableBody.appendChild(row);
            });
        }
        
        function getTableBodyId(tableName) {
            const mapping = {
                'inv_aircondition': 'aircondition-tbody',
                'inv_batteries': 'batteries-tbody',
                'inv_hardware': 'hardware-tbody',
                'sys_location': 'locations-tbody',
                'sys_users': 'users-tbody',
                'sys_user_locations': 'user-locations-tbody'
            };
            return mapping[tableName];
        }
        
        function createTableRow(tableName, record) {
            const tr = document.createElement('tr');
            
            switch(tableName) {
                case 'inv_aircondition':
                    tr.innerHTML = `
                        <td>${record.iac_id}</td>
                        <td>${record.iac_desc || ''}</td>
                        <td>${record.iac_location || ''}</td>
                        <td>${record.iac_operational || ''}</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editRecord('${tableName}', '${record.iac_id}')">✏️ Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteRecord('${tableName}', '${record.iac_id}')">🗑️ Delete</button>
                        </td>
                    `;
                    break;
                case 'inv_batteries':
                    tr.innerHTML = `
                        <td>${record.iba_id}</td>
                        <td>${record.iba_battery || ''}</td>
                        <td>${record.iba_operational || ''}</td>
                        <td>${record.iba_camera_unit || ''}</td>
                        <td>${record.iba_location || ''}</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editRecord('${tableName}', '${record.iba_id}')">✏️ Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteRecord('${tableName}', '${record.iba_id}')">🗑️ Delete</button>
                        </td>
                    `;
                    break;
                case 'inv_hardware':
                    tr.innerHTML = `
                        <td>${record.ihd_id}</td>
                        <td>${record.ihd_type || ''}</td>
                        <td>${record.ihd_make || ''}</td>
                        <td>${record.ihd_model || ''}</td>
                        <td>${record.ihd_location || ''}</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editRecord('${tableName}', '${record.ihd_id}')">✏️ Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteRecord('${tableName}', '${record.ihd_id}')">🗑️ Delete</button>
                        </td>
                    `;
                    break;
                case 'sys_location':
                    tr.innerHTML = `
                        <td>${record.slc_id}</td>
                        <td>${record.slc_type || ''}</td>
                        <td>${record.slc_address || ''}</td>
                        <td>${record.slc_desc || ''}</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editRecord('${tableName}', '${record.slc_id}')">✏️ Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteRecord('${tableName}', '${record.slc_id}')">🗑️ Delete</button>
                        </td>
                    `;
                    break;
                case 'sys_users':
                    tr.innerHTML = `
                        <td>${record.sus_id}</td>
                        <td>${record.sus_username || ''}</td>
                        <td>${record.sus_role || ''}</td>
                        <td>${record.sus_location || ''}</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editRecord('${tableName}', '${record.sus_id}')">✏️ Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteRecord('${tableName}', '${record.sus_id}')">🗑️ Delete</button>
                        </td>
                    `;
                    break;
                case 'sys_user_locations':
                    const user = DB.sys_users.find(u => u.sus_id === record.sul_user_id);
                    const location = DB.sys_location.find(l => l.slc_id === record.sul_location_id);
                    tr.innerHTML = `
                        <td>${record.sul_id}</td>
                        <td>${user ? user.sus_username : record.sul_user_id}</td>
                        <td>${location ? location.slc_type + ' - ' + location.slc_address : record.sul_location_id}</td>
                        <td>${record.sul_status || ''}</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="editRecord('${tableName}', '${record.sul_id}')">✏️ Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteRecord('${tableName}', '${record.sul_id}')">🗑️ Delete</button>
                        </td>
                    `;
                    break;
            }
            
            return tr;
        }
        
        // Form Management
        function showAddForm(tableName) {
            console.log('📋 ShowAddForm called for:', tableName);
            console.log('🎯 Current edit mode:', !!currentEditRecord);
            
            const formId = getFormId(tableName);
            const form = document.getElementById(formId);
            if (form) {
                form.classList.remove('hidden');
                
                // Only clear form and generate new ID if this is NOT an edit operation
                if (!currentEditRecord) {
                    console.log('🆕 This is a new record, clearing form and generating ID');
                    clearForm(formId);
                    
                    // Populate dropdowns for new records
                    populateLocationDropdowns();
                    if (tableName === 'sys_user_locations') {
                        populateUserLocationDropdowns();
                    }
                    
                    // Show auto-generated ID for new records
                    const idField = getIdFieldName(tableName);
                    if (idField) {
                        const newId = generateId(tableName);
                        document.getElementById(idField).value = newId;
                    }
                } else {
                    console.log('✏️ This is an edit operation, preserving form data');
                    // For edit mode, just ensure dropdowns are populated
                    // but don't clear the form or generate new ID
                    populateLocationDropdowns();
                    if (tableName === 'sys_user_locations') {
                        populateUserLocationDropdowns();
                    }
                }
            }
        }
        
        function getIdFieldName(tableName) {
            const mapping = {
                'inv_aircondition': 'iac_id',
                'inv_batteries': 'iba_id',
                'inv_hardware': 'ihd_id',
                'sys_location': 'slc_id',
                'sys_users': 'sus_id',
                'sys_user_locations': 'sul_id'
            };
            return mapping[tableName];
        }
        
        function getFormId(tableName) {
            const mapping = {
                'inv_aircondition': 'aircondition-form',
                'inv_batteries': 'batteries-form',
                'inv_hardware': 'hardware-form',
                'sys_location': 'locations-form',
                'sys_users': 'users-form',
                'sys_user_locations': 'user-locations-form'
            };
            return mapping[tableName];
        }
        
        function clearForm(formId) {
            const form = document.getElementById(formId);
            if (form) {
                form.querySelectorAll('input, textarea').forEach(input => {
                    if (!input.readOnly) {
                        input.value = '';
                    }
                });
                form.querySelectorAll('select').forEach(select => {
                    select.selectedIndex = 0; // Reset to first option (usually "Select...")
                });
            }
        }
        
        function cancelForm() {
            console.log('❌ Cancel form called, clearing edit mode');
            document.querySelectorAll('form').forEach(form => {
                form.classList.add('hidden');
            });
            currentEditRecord = null;
            console.log('🔄 currentEditRecord cleared');
        }
        
        function saveRecord(tableName) {
            console.log('🔄 Saving record for table:', tableName);
            console.log('📝 Current edit record:', currentEditRecord);
            console.log('🆕 Is this an edit operation?', !!currentEditRecord);
            
            const formData = getFormData(tableName);
            
            if (!formData) {
                showAlert('error', 'Please fill in all required fields');
                return;
            }
            
            console.log('📋 Form data collected:', formData);
            
            if (currentEditRecord) {
                // Update existing record
                console.log('✏️ Updating existing record');
                updateRecord(tableName, formData);
                showAlert('success', 'Record updated successfully!');
            } else {
                // Add new record
                console.log('➕ Adding new record');
                addRecord(tableName, formData);
                showAlert('success', 'Record added successfully!');
            }
            
            // Refresh table and hide form
            loadTable(tableName);
            cancelForm();
            autoSaveDatabase('record change');
        }
        
        function getFormData(tableName) {
            switch(tableName) {
                case 'inv_aircondition':
                    return {
                        iac_id: document.getElementById('iac_id').value,
                        iac_desc: document.getElementById('iac_desc').value,
                        iac_location: document.getElementById('iac_location').value,
                        iac_operational: document.getElementById('iac_operational').value,
                        iac_cre_by: currentUser.sus_username,
                        iac_cre_date: new Date().toISOString().split('T')[0]
                    };
                case 'inv_batteries':
                    return {
                        iba_id: document.getElementById('iba_id').value,
                        iba_battery: document.getElementById('iba_battery').value,
                        iba_operational: document.getElementById('iba_operational').value,
                        iba_camera_unit: document.getElementById('iba_camera_unit').value,
                        iba_location: document.getElementById('iba_location').value
                    };
                case 'inv_hardware':
                    return {
                        ihd_id: document.getElementById('ihd_id').value,
                        ihd_type: document.getElementById('ihd_type').value,
                        ihd_make: document.getElementById('ihd_make').value,
                        ihd_model: document.getElementById('ihd_model').value,
                        ihd_location: document.getElementById('ihd_location').value,
                        ihd_cre_by: currentUser.sus_username,
                        ihd_cre_date: new Date().toISOString().split('T')[0]
                    };
                case 'sys_location':
                    return {
                        slc_id: document.getElementById('slc_id').value,
                        slc_type: document.getElementById('slc_type').value,
                        slc_address: document.getElementById('slc_address').value,
                        slc_desc: document.getElementById('slc_desc').value,
                        slc_cre_by: currentUser.sus_username,
                        slc_cre_date: new Date().toISOString().split('T')[0]
                    };
                case 'sys_users':
                    return {
                        sus_id: document.getElementById('sus_id').value,
                        sus_username: document.getElementById('sus_username').value,
                        sus_role: document.getElementById('sus_role').value,
                        sus_password: document.getElementById('sus_password').value,
                        sus_rep_manager: document.getElementById('sus_role').value === 'admin' ? '' : 'admin',
                        sus_location: document.getElementById('sus_location').value,
                        sus_cre_by: currentUser.sus_username,
                        sus_cre_date: new Date().toISOString().split('T')[0],
                        sus_remarks: document.getElementById('sus_role').value === 'admin' ? 'System Administrator' : 'Standard user account'
                    };
                case 'sys_user_locations':
                    return {
                        sul_id: document.getElementById('sul_id').value,
                        sul_user_id: document.getElementById('sul_user_id').value,
                        sul_location_id: document.getElementById('sul_location_id').value,
                        sul_assigned_date: new Date().toISOString().split('T')[0],
                        sul_assigned_by: currentUser.sus_username,
                        sul_status: document.getElementById('sul_status').value
                    };
                default:
                    return null;
            }
        }
        
        function addRecord(tableName, data) {
            if (!DB[tableName]) {
                DB[tableName] = [];
            }
            DB[tableName].push(data);
        }
        
        function updateRecord(tableName, data) {
            console.log('🔧 Updating record in table:', tableName);
            console.log('📊 Data to update with:', data);
            console.log('🎯 Looking for record to update...');
            
            const records = DB[tableName];
            const index = records.findIndex(record => {
                let match = false;
                switch(tableName) {
                    case 'inv_aircondition': 
                        match = record.iac_id === data.iac_id;
                        break;
                    case 'inv_batteries': 
                        match = record.iba_id === data.iba_id;
                        break;
                    case 'inv_hardware': 
                        match = record.ihd_id === data.ihd_id;
                        break;
                    case 'sys_location': 
                        match = record.slc_id === data.slc_id;
                        break;
                    case 'sys_users': 
                        match = record.sus_id === data.sus_id;
                        break;
                    case 'sys_user_locations': 
                        match = record.sul_id === data.sul_id;
                        break;
                    default: 
                        match = false;
                }
                console.log(`🔍 Checking record:`, record, 'Match:', match);
                return match;
            });
            
            if (index !== -1) {
                console.log(`✅ Found record at index ${index}, updating...`);
                records[index] = data;
                console.log('🎉 Record updated successfully');
            } else {
                console.error('❌ Record not found for update!');
                console.log('Available records:', records);
            }
        }
        
        function editRecord(tableName, id) {
            console.log('✏️ Edit record called for table:', tableName, 'ID:', id);
            
            const record = findRecord(tableName, id);
            if (!record) {
                console.error('❌ Record not found for editing');
                showAlert('error', 'Record not found');
                return;
            }
            
            console.log('📋 Found record for editing:', record);
            currentEditRecord = record;
            console.log('🎯 Set currentEditRecord to:', currentEditRecord);
            
            populateForm(tableName, record);
            showAddForm(tableName);
        }
        
        function findRecord(tableName, id) {
            const records = DB[tableName];
            return records.find(record => {
                switch(tableName) {
                    case 'inv_aircondition': return record.iac_id === id;
                    case 'inv_batteries': return record.iba_id === id;
                    case 'inv_hardware': return record.ihd_id === id;
                    case 'sys_location': return record.slc_id === id;
                    case 'sys_users': return record.sus_id === id;
                    case 'sys_user_locations': return record.sul_id === id || record.sul_user_id === id;
                    default: return null;
                }
            });
        }
        
        function populateForm(tableName, record) {
            console.log('Populating form for:', tableName, 'with record:', record);
            
            // First ensure all dropdowns are populated
            populateLocationDropdowns();
            if (tableName === 'sys_user_locations') {
                populateUserLocationDropdowns();
            }
            
            // Use setTimeout to ensure dropdowns are populated before setting values
            setTimeout(() => {
                switch(tableName) {
                    case 'inv_aircondition':
                        document.getElementById('iac_id').value = record.iac_id || '';
                        document.getElementById('iac_desc').value = record.iac_desc || '';
                        setSelectValue('iac_location', record.iac_location);
                        setSelectValue('iac_operational', record.iac_operational);
                        break;
                    case 'inv_batteries':
                        document.getElementById('iba_id').value = record.iba_id || '';
                        document.getElementById('iba_battery').value = record.iba_battery || '';
                        setSelectValue('iba_operational', record.iba_operational);
                        document.getElementById('iba_camera_unit').value = record.iba_camera_unit || '';
                        setSelectValue('iba_location', record.iba_location);
                        break;
                    case 'inv_hardware':
                        document.getElementById('ihd_id').value = record.ihd_id || '';
                        document.getElementById('ihd_type').value = record.ihd_type || '';
                        document.getElementById('ihd_make').value = record.ihd_make || '';
                        document.getElementById('ihd_model').value = record.ihd_model || '';
                        setSelectValue('ihd_location', record.ihd_location);
                        break;
                    case 'sys_location':
                        document.getElementById('slc_id').value = record.slc_id || '';
                        document.getElementById('slc_type').value = record.slc_type || '';
                        document.getElementById('slc_address').value = record.slc_address || '';
                        document.getElementById('slc_desc').value = record.slc_desc || '';
                        break;
                    case 'sys_users':
                        document.getElementById('sus_id').value = record.sus_id || '';
                        document.getElementById('sus_username').value = record.sus_username || '';
                        setSelectValue('sus_role', record.sus_role);
                        document.getElementById('sus_password').value = record.sus_password || '';
                        setSelectValue('sus_location', record.sus_location);
                        break;
                    case 'sys_user_locations':
                        document.getElementById('sul_id').value = record.sul_id || '';
                        setSelectValue('sul_user_id', record.sul_user_id);
                        setSelectValue('sul_location_id', record.sul_location_id);
                        setSelectValue('sul_status', record.sul_status);
                        break;
                }
            }, 100); // Small delay to ensure dropdowns are populated
        }
        
        // Helper function to set select field values properly
        function setSelectValue(selectId, value) {
            const select = document.getElementById(selectId);
            if (select && value) {
                console.log(`Setting select ${selectId} to value:`, value);
                
                // First try direct value assignment
                select.value = value;
                
                // If direct assignment didn't work, search through options
                if (select.value !== value) {
                    console.log(`Direct assignment failed for ${selectId}, searching options...`);
                    for (let i = 0; i < select.options.length; i++) {
                        const option = select.options[i];
                        console.log(`Checking option ${i}:`, option.value, option.textContent);
                        if (option.value === value || option.value === value.toString()) {
                            select.selectedIndex = i;
                            console.log(`Selected option ${i} for ${selectId}`);
                            break;
                        }
                    }
                }
                
                // Final fallback - try partial matching for complex values
                if (select.value !== value && select.selectedIndex === 0) {
                    console.log(`Still not matched for ${selectId}, trying partial matching...`);
                    for (let i = 0; i < select.options.length; i++) {
                        const option = select.options[i];
                        if (option.textContent.includes(value) || value.includes(option.value)) {
                            select.selectedIndex = i;
                            console.log(`Partial match found at option ${i} for ${selectId}`);
                            break;
                        }
                    }
                }
                
                console.log(`Final value for ${selectId}:`, select.value);
            }
        }
        
        function deleteRecord(tableName, id) {
            if (!confirm('Are you sure you want to delete this record?')) return;
            
            const records = DB[tableName];
            const index = records.findIndex(record => {
                switch(tableName) {
                    case 'inv_aircondition': return record.iac_id === id;
                    case 'inv_batteries': return record.iba_id === id;
                    case 'inv_hardware': return record.ihd_id === id;
                    case 'sys_location': return record.slc_id === id;
                    case 'sys_users': return record.sus_id === id;
                    case 'sys_user_locations': return record.sul_id === id || record.sul_user_id === id;
                    default: return false;
                }
            });
            
            if (index !== -1) {
                records.splice(index, 1);
                loadTable(tableName);
                autoSaveDatabase('delete');
                showAlert('success', 'Record deleted successfully!');
            }
        }
        
        // Dropdown Population
        function populateLocationDropdowns() {
            const selectors = [
                'iac_location', 'iba_location', 'ihd_location', 'sus_location'
            ];
            
            selectors.forEach(selector => {
                const dropdown = document.getElementById(selector);
                if (dropdown) {
                    dropdown.innerHTML = '<option value="">Select Location</option>';
                    DB.sys_location.forEach(location => {
                        dropdown.innerHTML += `<option value="${location.slc_id}">${location.slc_type} - ${location.slc_address}</option>`;
                    });
                }
            });
        }
        
        function populateUserLocationDropdowns() {
            // Populate user dropdown
            const userDropdown = document.getElementById('sul_user_id');
            if (userDropdown) {
                userDropdown.innerHTML = '<option value="">Select User</option>';
                DB.sys_users.forEach(user => {
                    userDropdown.innerHTML += `<option value="${user.sus_id}">${user.sus_username} (${user.sus_role})</option>`;
                });
            }
            
            // Populate location dropdown
            const locationDropdown = document.getElementById('sul_location_id');
            if (locationDropdown) {
                locationDropdown.innerHTML = '<option value="">Select Location</option>';
                DB.sys_location.forEach(location => {
                    locationDropdown.innerHTML += `<option value="${location.slc_id}">${location.slc_type} - ${location.slc_address}</option>`;
                });
            }
        }
        
        // Search Functionality
        function performSearch() {
            const searchType = document.getElementById('search-type').value;
            const searchTerm = document.getElementById('search-term').value.toLowerCase();
            
            let results = [];
            
            if (!searchType || searchType === '') {
                // Search all categories
                results = [
                    ...searchInTable('inv_aircondition', searchTerm),
                    ...searchInTable('inv_batteries', searchTerm),
                    ...searchInTable('inv_hardware', searchTerm)
                ];
            } else {
                // Search specific category
                results = searchInTable(searchType, searchTerm);
            }
            
            displaySearchResults(results);
        }
        
        function searchInTable(tableName, term) {
            if (!term) return DB[tableName] || [];
            
            return (DB[tableName] || []).filter(record => {
                return Object.values(record).some(value => 
                    String(value).toLowerCase().includes(term)
                );
            });
        }
        
        function displaySearchResults(results) {
            const container = document.getElementById('inventory-display');
            container.innerHTML = '';
            
            if (results.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">No items found matching your search criteria.</p>';
                return;
            }
            
            results.forEach(item => {
                if (item.iac_id) {
                    // Air Condition
                    container.innerHTML += createInventoryCard('❄️ Air Condition', item, [
                        { label: 'ID', value: item.iac_id },
                        { label: 'Description', value: item.iac_desc || 'N/A' },
                        { label: 'Location', value: item.iac_location || 'N/A' },
                        { label: 'Operational', value: item.iac_operational || 'N/A' }
                    ]);
                } else if (item.iba_id) {
                    // Battery
                    container.innerHTML += createInventoryCard('🔋 Battery', item, [
                        { label: 'ID', value: item.iba_id },
                        { label: 'Battery Type', value: item.iba_battery || 'N/A' },
                        { label: 'Camera Unit', value: item.iba_camera_unit || 'N/A' },
                        { label: 'Location', value: item.iba_location || 'N/A' },
                        { label: 'Operational', value: item.iba_operational || 'N/A' }
                    ]);
                } else if (item.ihd_id) {
                    // Hardware
                    container.innerHTML += createInventoryCard('💻 Hardware', item, [
                        { label: 'ID', value: item.ihd_id },
                        { label: 'Type', value: item.ihd_type || 'N/A' },
                        { label: 'Make', value: item.ihd_make || 'N/A' },
                        { label: 'Model', value: item.ihd_model || 'N/A' },
                        { label: 'Location', value: item.ihd_location || 'N/A' }
                    ]);
                }
            });
        }
        
        // Utility Functions - Toast Notification System
        function showAlert(type, message) {
            console.log('🍞 Toast called:', type, message);
            
            const container = document.getElementById('toast-container');
            console.log('🍞 Toast container found:', !!container);
            console.log('🍞 Container element:', container);
            
            if (!container) {
                console.error('❌ Toast container not found');
                // Fallback to console alert for debugging
                console.warn(`TOAST FALLBACK: ${type.toUpperCase()}: ${message}`);
                return;
            }
            
            console.log('🍞 Creating toast element...');
            
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            // Get icon based on type
            const icons = {
                'success': '✅',
                'error': '❌',
                'info': 'ℹ️',
                'warning': '⚠️'
            };
            
            const icon = icons[type] || 'ℹ️';
            
            // Create toast HTML
            toast.innerHTML = `
                <div class="toast-icon">${icon}</div>
                <div class="toast-content">${message}</div>
                <button class="toast-close" onclick="removeToast(this.parentElement)">×</button>
            `;
            
            console.log('🍞 Adding toast to container...');
            
            // Add to container
            container.appendChild(toast);
            
            console.log('🍞 Toast added, triggering animation...');
            
            // Trigger animation
            setTimeout(() => {
                toast.classList.add('show');
                console.log('🍞 Toast animation triggered');
            }, 100);
            
            // Auto-remove after delay
            const duration = type === 'error' ? 5000 : 3000; // Errors stay longer
            setTimeout(() => {
                console.log('🍞 Auto-removing toast after', duration + 'ms');
                removeToast(toast);
            }, duration);
            
            // Limit number of toasts (keep only last 5)
            const toasts = container.querySelectorAll('.toast');
            console.log('🍞 Total toasts in container:', toasts.length);
            if (toasts.length > 5) {
                for (let i = 0; i < toasts.length - 5; i++) {
                    removeToast(toasts[i]);
                }
            }
        }
        
        function removeToast(toast) {
            if (!toast || !toast.parentElement) return;
            
            toast.classList.add('hide');
            toast.classList.remove('show');
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.parentElement.removeChild(toast);
                }
            }, 300);
        }
        
        // Quick toast functions for convenience
        function showSuccess(message) {
            showAlert('success', message);
        }
        
        function showError(message) {
            showAlert('error', message);
        }
        
        function showInfo(message) {
            showAlert('info', message);
        }
        
        function showWarning(message) {
            showAlert('warning', message);
        }
        
        function generateId(tableName) {
            console.log(`🆔 Generating ID for table: ${tableName}`);
            
            // Initialize counters based on existing data if not present
            if (!DB.counters) {
                console.log('🔧 Initializing counters from existing data...');
                DB.counters = {
                    iac_id: getMaxIdFromTable('inv_aircondition', 'iac_id', 'IAC'),
                    iba_id: getMaxIdFromTable('inv_batteries', 'iba_id', 'IBA'),
                    ihd_id: getMaxIdFromTable('inv_hardware', 'ihd_id', 'IHD'),
                    sig_id: getMaxIdFromTable('sys_images', 'sig_id', 'SIG'),
                    slc_id: getMaxIdFromTable('sys_location', 'slc_id', 'LOC'),
                    sus_id: getMaxIdFromTable('sys_users', 'sus_id', 'USR'),
                    sul_id: getMaxIdFromTable('sys_user_locations', 'sul_id', 'UL')
                };
                console.log('✅ Counters initialized:', DB.counters);
            }
            
            let prefix = '';
            let counterKey = '';
            
            switch(tableName) {
                case 'inv_aircondition':
                    prefix = 'IAC';
                    counterKey = 'iac_id';
                    break;
                case 'inv_batteries':
                    prefix = 'IBA';
                    counterKey = 'iba_id';
                    break;
                case 'inv_hardware':
                    prefix = 'IHD';
                    counterKey = 'ihd_id';
                    break;
                case 'sys_location':
                    prefix = 'LOC';
                    counterKey = 'slc_id';
                    break;
                case 'sys_users':
                    prefix = 'USR';
                    counterKey = 'sus_id';
                    break;
                case 'sys_user_locations':
                    prefix = 'UL';
                    counterKey = 'sul_id';
                    break;
                case 'sys_images':
                    prefix = 'SIG';
                    counterKey = 'sig_id';
                    break;
                default:
                    return 'ID' + Date.now();
            }
            
            // Increment counter
            DB.counters[counterKey]++;
            
            // Format with leading zeros
            const counter = DB.counters[counterKey].toString().padStart(4, '0');
            const newId = prefix + counter;
            
            console.log(`✅ Generated ID: ${newId} (counter: ${DB.counters[counterKey]})`);
            return newId;
        }
        
        // Helper function to find the maximum ID number from existing records
        function getMaxIdFromTable(tableName, idField, prefix) {
            const table = DB[tableName];
            if (!table || table.length === 0) {
                console.log(`📋 Table ${tableName} is empty, starting counter at 0`);
                return 0;
            }
            
            let maxId = 0;
            table.forEach(record => {
                const idValue = record[idField];
                if (idValue && typeof idValue === 'string' && idValue.startsWith(prefix)) {
                    // Extract number part (e.g., "IAC0005" -> 5)
                    const numberPart = parseInt(idValue.substring(prefix.length));
                    if (!isNaN(numberPart) && numberPart > maxId) {
                        maxId = numberPart;
                    }
                }
            });
            
            console.log(`📊 Table ${tableName}: found max ID ${maxId}`);
            return maxId;
        }
        
        // Initialize Application on Page Load
        document.addEventListener('DOMContentLoaded', async function() {
            console.log('🚀 Initializing Inventory System...');
            console.log('DOM Content Loaded');
            
            console.log('📊 Loading system...');
            
            try {
                await initDatabase();
                console.log('✅ System Ready!');
                console.log('Available users:', DB.sys_users);
                
                // Ensure the database is properly loaded
                if (!DB.sys_users || DB.sys_users.length === 0) {
                    console.error('❌ No users found in database');
                    console.error('❌ Database initialization failed. Please check the console.');
                } else {
                    console.log('✅ Found', DB.sys_users.length, 'users in database');
                    
                    // H - Product Unit: Initialize simple auto-save system
                    enableAutoSave();
                    console.log('⚡ Simple auto-save system initialized');
                    updateSaveStatus('System ready ✓');
                    
                    // Check for existing login session
                    const savedUser = sessionStorage.getItem('mawal_current_user');
                    const loginTime = sessionStorage.getItem('mawal_login_time');
                    
                    if (savedUser && loginTime) {
                        const sessionAge = Date.now() - parseInt(loginTime);
                        const maxSessionAge = 24 * 60 * 60 * 1000; // 24 hours
                        
                        if (sessionAge < maxSessionAge) {
                            // Restore session
                            currentUser = JSON.parse(savedUser);
                            console.log('🔄 Restoring session for user:', currentUser.sus_username);
                            
                            document.getElementById('login-screen').classList.add('hidden');
                            document.getElementById('main-app').classList.remove('hidden');
                            updateUserDisplay();
                            initializeNavigation();
                            showDefaultTab();
                            console.log(`✅ Session restored: Welcome back ${currentUser.sus_username}!`);
                        } else {
                            // Session expired
                            sessionStorage.removeItem('mawal_current_user');
                            sessionStorage.removeItem('mawal_login_time');
                            console.log('⚠️ Session expired. Please log in again.');
                        }
                    } else {
                        console.log('✅ System ready! You can now log in.');
                        
                        // Ensure proper login screen state on fresh page load
                        const loginScreen = document.getElementById('login-screen');
                        const loginForm = document.querySelector('.login-form');
                        const logoutMessage = document.getElementById('logout-message');
                        
                        // Reset any logout state
                        if (loginScreen) {
                            loginScreen.removeAttribute('data-logout-state');
                        }
                        
                        // Show login form, hide logout message
                        if (loginForm) {
                            loginForm.style.display = '';
                            console.log('✅ Login form set to visible');
                        }
                        if (logoutMessage) {
                            logoutMessage.style.display = 'none';
                            console.log('✅ Logout message hidden');
                        }
                    }
                }
            } catch (error) {
                console.error('❌ Initialization error:', error);
                console.error('❌ System initialization failed:', error.message);
            }
        });
        
        // Add username field listener for location checking
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener to username field
            function addUsernameListener() {
                const usernameField = document.getElementById('username');
                if (usernameField) {
                    usernameField.addEventListener('blur', checkUsernameAndShowLocations);
                    usernameField.addEventListener('input', checkUsernameAndShowLocations); // Real-time checking
                    usernameField.addEventListener('keyup', function(e) {
                        if (e.key === 'Tab' || e.key === 'Enter') {
                            checkUsernameAndShowLocations();
                        }
                    });
                    console.log('✅ Username field listener added');
                } else {
                    // Retry if username field not found yet
                    setTimeout(addUsernameListener, 500);
                }
            }
            addUsernameListener();
        });
        
        window.DB = {}; // Make sure DB is available globally
    </script>
</body>
</html>