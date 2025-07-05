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
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? '';
    
    if (empty($action) || empty($product_id)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    if ($action === 'add') {
        if ($wishlist->addToWishlist($user_id, $product_id)) {
            echo json_encode(['success' => true, 'message' => 'Berhasil ditambahkan ke wishlist']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Barang sudah ada di wishlist atau terjadi kesalahan']);
        }
    } elseif ($action === 'remove') {
        if ($wishlist->removeFromWishlist($user_id, $product_id)) {
            echo json_encode(['success' => true, 'message' => 'Berhasil dihapus dari wishlist']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
}
?>