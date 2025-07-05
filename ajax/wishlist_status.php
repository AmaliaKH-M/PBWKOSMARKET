<?php
session_start();
require_once '../config/kosmarket_db.php';
require_once '../classes/Wishlist.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$product_ids = $_POST['product_ids'] ?? '';
$user_id = $_SESSION['user_id'];

if (empty($product_ids)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $wishlist = new Wishlist($db);

    $product_ids_array = explode(',', $product_ids);
    $wishlist_items = $wishlist->getUserWishlist($user_id);
    
    // Extract product IDs from wishlist
    $wishlist_product_ids = array_column($wishlist_items, 'id_produk');
    
    echo json_encode([
        'success' => true, 
        'wishlist' => $wishlist_product_ids
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>