<?php
class Product {
    private $conn;
    private $table_name = "produk";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll($limit = 10, $search = null, $category = null, $kondisi = null, $tipe = null, $seller_id = null) {
        $query = "SELECT p.*, u.nama as nama_penjual, u.lokasi_kos as lokasi_penjual, k.nama_kategori 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN users u ON p.id_penjual = u.id_user 
                  LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                  WHERE 1=1";
        
        $params = [];
        
        if ($search) {
            $query .= " AND (p.judul LIKE :search OR p.deskripsi LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        if ($category) {
            $query .= " AND p.id_kategori = :category";
            $params[':category'] = $category;
        }
        
        if ($kondisi) {
            $query .= " AND p.kondisi = :kondisi";
            $params[':kondisi'] = $kondisi;
        }
        
        if ($tipe) {
            $query .= " AND p.tipe_barang = :tipe";
            $params[':tipe'] = $tipe;
        }
        
        if ($seller_id) {
            $query .= " AND p.id_penjual = :seller_id";
            $params[':seller_id'] = $seller_id;
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
            $params[':limit'] = $limit;
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            if ($key === ':limit') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategories() {
        $query = "SELECT * FROM kategori ORDER BY nama_kategori";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT p.*, u.nama as nama_penjual, u.lokasi_kos as lokasi_penjual, u.nomor_wa, k.nama_kategori 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN users u ON p.id_penjual = u.id_user 
                  LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                  WHERE p.id_produk = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_penjual, id_kategori, judul, deskripsi, harga, harga_asli, kondisi, tipe_barang, foto1, foto2, foto3, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['id_penjual'], 
            $data['id_kategori'], 
            $data['judul'], 
            $data['deskripsi'],
            $data['harga'], 
            $data['harga_asli'], 
            $data['kondisi'], 
            $data['tipe_barang'], 
            $data['foto1'], 
            $data['foto2'], 
            $data['foto3']
        ]);
    }
    
    public function getTotalProducts() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function getTotalSold() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE status = 'terjual'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function getTotalDonated() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE status = 'terdonasi'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_produk = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>