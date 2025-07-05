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

$action = $_POST['action'] ?? '';
$product_id = $_POST['product_id'] ?? '';
$user_id = $_SESSION['user_id'];

if (empty($action) || empty($product_id)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $wishlist = new Wishlist($db);

    if ($action === 'add') {
        if ($wishlist->add($user_id, $product_id)) {
            echo json_encode(['success' => true, 'message' => 'Berhasil ditambahkan ke wishlist']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan ke wishlist']);
        }
    } elseif ($action === 'remove') {
        if ($wishlist->remove($user_id, $product_id)) {
            echo json_encode(['success' => true, 'message' => 'Berhasil dihapus dari wishlist']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus dari wishlist']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>