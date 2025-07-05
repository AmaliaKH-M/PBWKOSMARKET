<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/kosmarket_db.php';
require_once 'config/helpers.php';
require_once 'classes/Wishlist.php';

$database = new Database();
$db = $database->getConnection();
$wishlist = new Wishlist($db);

// Get wishlist items
$wishlist_items = $wishlist->getWishlistItems($_SESSION['user_id']);
$wishlist_count = $wishlist->getWishlistCount($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - KosMarket</title>
    <meta name="description" content="Wishlist barang favorit Anda di KosMarket">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo logo-font">
                K<span class="heart">❤️</span>sMarket
            </a>

            <div class="search-box">
                <form action="products.php" method="GET" class="search-form">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" placeholder="Cari barang preloved..." autocomplete="off">
                </form>
            </div>

            <ul class="nav-menu">
                <li><a href="products.php">Semua Produk</a></li>
                <li><a href="sell.php" class="btn btn-primary"><span class="icon">+</span> Jual/Donasi</a></li>
                <li><a href="wishlist.php" class="active"><span class="icon">♡</span> Wishlist (<?= $wishlist_count ?>)</a></li>
                <li><a href="dashboard.php"><span class="icon">👤</span> Dashboard</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>

            <button class="mobile-menu-btn">
                <span class="hamburger">☰</span>
            </button>
        </div>

        <div class="mobile-menu">
            <ul class="nav-menu">
                <li><a href="products.php">Semua Produk</a></li>
                <li><a href="sell.php"><span class="icon">+</span> Jual/Donasi</a></li>
                <li><a href="wishlist.php"><span class="icon">♡</span> Wishlist (<?= $wishlist_count ?>)</a></li>
                <li><a href="dashboard.php"><span class="icon">👤</span> Dashboard</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </div>
    </nav>

    <main class="container" style="margin-top: 2rem;">
        <div class="breadcrumb">
            <a href="index.php">Beranda</a>
            <span class="separator">></span>
            <span>Wishlist</span>
        </div>

        <div class="dashboard-header">
            <h1>♡ Wishlist Saya</h1>
            <p class="text-muted">Barang-barang yang Anda sukai dan ingin beli nanti</p>
        </div>

        <?php if (empty($wishlist_items)): ?>
            <div class="empty-state">
                <div class="empty-icon">♡</div>
                <h3>Wishlist Masih Kosong</h3>
                <p>Belum ada barang yang Anda tambahkan ke wishlist. Mulai jelajahi produk dan klik tombol love untuk menambahkan ke wishlist!</p>
                <a href="products.php" class="btn btn-primary">🔍 Jelajahi Produk</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="card">
                        <div style="position: relative;">
                            <img src="<?= $item['foto1'] ? 'uploads/produk/' . $item['foto1'] : 'assets/images/no-image.svg' ?>" 
                                 alt="<?= htmlspecialchars($item['judul']) ?>" class="card-img">
                            
                            <div class="ribbon <?= $item['tipe_barang'] === 'donasi' ? 'free' : '' ?>">
                                <span><?= $item['tipe_barang'] === 'donasi' ? 'GRATIS' : 'DIJUAL' ?></span>
                            </div>

                            <button class="wishlist-btn active" data-product-id="<?= $item['id_produk'] ?>">
                                <span class="heart">❤️</span>
                            </button>
                        </div>

                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($item['judul']) ?></h3>
                            <div class="badge badge-secondary mb-2"><?= $item['kondisi'] ?></div>
                            
                            <?php if ($item['tipe_barang'] === 'jual'): ?>
                                <div class="card-price"><?= formatRupiah($item['harga']) ?></div>
                            <?php else: ?>
                                <div class="card-price free">GRATIS</div>
                            <?php endif; ?>

                            <div class="card-location">
                                <span class="location-icon">📍</span>
                                <span><?= htmlspecialchars($item['lokasi_penjual']) ?></span>
                            </div>

                            <div class="card-footer">
                                <span class="card-seller">oleh <?= htmlspecialchars($item['nama_penjual']) ?></span>
                                <a href="product.php?id=<?= $item['id_produk'] ?>" class="btn btn-primary view-btn">
                                    <span class="view-icon">👁</span> Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="logo-font">K<span class="heart">❤️</span>sMarket</h3>
                    <p>Platform jual-beli dan donasi barang preloved khusus untuk komunitas mahasiswa dan penghuni kos.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Menu Utama</h3>
                    <ul>
                        <li><a href="products.php">Semua Produk</a></li>
                        <li><a href="sell.php">Jual/Donasi</a></li>
                        <li><a href="about.php">Tentang Kami</a></li>
                        <li><a href="contact.php">Kontak</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Kontak</h3>
                    <ul>
                        <li><span class="contact-icon">📧</span> info@kosmarket.com</li>
                        <li><span class="contact-icon">📞</span> +62 812-3456-7890</li>
                        <li><span class="contact-icon">📍</span> Malang, Jawa Timur</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 KosMarket. Dibuat dengan <span style="color: #e74c3c;">❤️</span> untuk komunitas mahasiswa.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>