<?php
// AJAX endpoint for image removal
header('Content-Type: application/json');

// Include required files
require_once('core.php');
require_once('app.php');

// Initialize system
sys_init();

// Check if user is logged in
user_login_check();

// Handle AJAX image removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_image') {
    
    if (!isset($_POST['image_id']) || empty($_POST['image_id'])) {
        echo json_encode(['success' => false, 'error' => 'Missing image ID']);
        exit;
    }
    
    $image_id = $_POST['image_id'];
    
    try {
        // Remove the image
        $result = remove_image($image_id);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Image removed successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to remove image from database']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>