<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once '../config/kosmarket_db.php';
require_once '../config/helpers.php';
require_once '../classes/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $product_id = $_POST['product_id'] ?? '';
        
        if (empty($product_id)) {
            echo json_encode(['success' => false, 'message' => 'Product ID required']);
            exit;
        }
        
        if ($product->delete($product_id)) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>