<?php
// Helper functions for KosMarket application

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function getCategoryEmoji($category) {
    $emojis = [
        'Elektronik' => '📱',
        'Pakaian' => '👕',
        'Furniture' => '🛏️',
        'Buku' => '📚',
        'Aksesoris' => '🎒',
        'Makanan' => '🍕',
        'Peralatan' => '🔧',
        'Olahraga' => '⚽',
        'Lainnya' => '📦'
    ];
    
    return $emojis[$category] ?? '📦';
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function showError($message) {
    echo '<div class="alert alert-danger">' . $message . '</div>';
}

function showSuccess($message) {
    echo '<div class="alert alert-success">' . $message . '</div>';
}

function uploadImage($file) {
    $uploadDir = 'uploads/produk/';
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $uploadPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $filename;
    }
    
    return false;
}
?>