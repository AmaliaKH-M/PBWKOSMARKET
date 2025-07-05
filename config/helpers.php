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
?>