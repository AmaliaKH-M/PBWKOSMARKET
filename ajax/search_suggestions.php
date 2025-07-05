<?php
require_once '../config/kosmarket_db.php';
require_once '../classes/Product.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$query = $_POST['query'] ?? '';

if (empty($query) || strlen($query) < 2) {
    echo json_encode(['success' => false, 'suggestions' => []]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);

    $suggestions = $product->getSearchSuggestions($query);
    
    echo json_encode([
        'success' => true, 
        'suggestions' => $suggestions
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>