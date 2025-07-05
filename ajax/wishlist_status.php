<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

require_once '../config/kosmarket_db.php';
require_once '../classes/Wishlist.php';

$database = new Database();
$db = $database->getConnection();
$wishlist = new Wishlist($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_ids = $_POST['product_ids'] ?? '';
    
    if (empty($product_ids)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $product_ids_array = explode(',', $product_ids);
    $wishlist_items = [];
    
    foreach ($product_ids_array as $product_id) {
        if ($wishlist->isInWishlist($user_id, trim($product_id))) {
            $wishlist_items[] = trim($product_id);
        }
    }
    
    echo json_encode(['success' => true, 'wishlist' => $wishlist_items]);
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}
?>