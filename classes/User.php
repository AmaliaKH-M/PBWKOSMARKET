<?php
class User {
    private $conn;
    private $table_name = "users";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_user = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getUserStats($user_id) {
        $query = "SELECT 
                    COUNT(*) as total_produk,
                    SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END) as tersedia,
                    SUM(CASE WHEN status = 'terjual' THEN 1 ELSE 0 END) as terjual,
                    SUM(CASE WHEN status = 'terdonasi' THEN 1 ELSE 0 END) as terdonasi
                  FROM produk 
                  WHERE id_penjual = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function register($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nama, email, password, nomor_wa, lokasi_kos, role, created_at) 
                  VALUES (?, ?, ?, ?, ?, 'user', NOW())";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['nama'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['nomor_wa'],
            $data['lokasi_kos']
        ]);
    }
    
    public function emailExists($email) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function getRecentUsers($limit = 10) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>