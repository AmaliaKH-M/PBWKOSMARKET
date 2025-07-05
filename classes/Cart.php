<?php
class Cart {
    private $conn;
    private $table_name = "keranjang";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getItemCount($user_id) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE id_user = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
    
    public function getItems($user_id) {
        $query = "SELECT k.*, p.judul, p.harga, p.foto1, p.tipe_barang, u.nama as nama_penjual
                  FROM " . $this->table_name . " k
                  JOIN produk p ON k.id_produk = p.id_produk
                  JOIN users u ON p.id_penjual = u.id_user
                  WHERE k.id_user = ?
                  ORDER BY k.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addItem($user_id, $product_id) {
        $query = "INSERT INTO " . $this->table_name . " (id_user, id_produk, created_at) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $product_id]);
    }
    
    public function removeItem($user_id, $product_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_user = ? AND id_produk = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $product_id]);
    }
}
?>