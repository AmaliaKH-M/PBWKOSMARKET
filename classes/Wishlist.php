<?php
class Wishlist {
    private $conn;
    private $table_name = "wishlist";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function addToWishlist($user_id, $product_id) {
        // Check if item already exists
        if ($this->isInWishlist($user_id, $product_id)) {
            return false;
        }
        
        $query = "INSERT INTO " . $this->table_name . " (id_user, id_produk, created_at) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $product_id]);
    }
    
    public function removeFromWishlist($user_id, $product_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_user = ? AND id_produk = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $product_id]);
    }
    
    public function isInWishlist($user_id, $product_id) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id_user = ? AND id_produk = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $product_id]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function getWishlistItems($user_id) {
        $query = "SELECT w.*, p.judul, p.harga, p.foto1, p.tipe_barang, p.kondisi, u.nama as nama_penjual, u.lokasi_kos as lokasi_penjual
                  FROM " . $this->table_name . " w
                  JOIN produk p ON w.id_produk = p.id_produk
                  JOIN users u ON p.id_user = u.id_user
                  WHERE w.id_user = ?
                  ORDER BY w.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getWishlistCount($user_id) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id_user = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}
?>