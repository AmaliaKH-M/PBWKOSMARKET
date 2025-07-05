<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isAdmin()) {
    header('Location: login.php');
    exit;
}

require_once 'config/kosmarket_db.php';
require_once 'config/helpers.php';
require_once 'classes/User.php';
require_once 'classes/Product.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$product = new Product($db);

// Get admin statistics
$admin_stats = [
    'total_users' => $user->getTotalUsers(),
    'total_products' => $product->getTotalProducts(),
    'total_sold' => $product->getTotalSold(),
    'total_donated' => $product->getTotalDonated()
];

// Get recent products
$recent_products = $product->getAll(20);

// Get recent users
$recent_users = $user->getRecentUsers(10);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KosMarket</title>
    <meta name="description" content="Admin dashboard KosMarket">
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
                <li><a href="admin.php" class="active">🛡️ Admin</a></li>
                <li><a href="dashboard.php">👤 Dashboard</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>

            <button class="mobile-menu-btn">
                <span class="hamburger">☰</span>
            </button>
        </div>
    </nav>

    <main class="container" style="margin-top: 2rem;">
        <div class="breadcrumb">
            <a href="index.php">Beranda</a>
            <span class="separator">></span>
            <span>Admin Dashboard</span>
        </div>

        <div class="dashboard-header">
            <h1>🛡️ Admin Dashboard</h1>
            <p class="text-muted">Panel administrasi KosMarket</p>
        </div>

        <div class="dashboard-grid">
            <!-- Statistics Cards -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>📊 Statistik Keseluruhan</h3>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?= $admin_stats['total_users'] ?></div>
                            <div class="stat-label">Total Pengguna</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $admin_stats['total_products'] ?></div>
                            <div class="stat-label">Total Produk</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $admin_stats['total_sold'] ?></div>
                            <div class="stat-label">Terjual</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $admin_stats['total_donated'] ?></div>
                            <div class="stat-label">Terdonasi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>⚡ Aksi Cepat</h3>
                </div>
                <div class="card-body">
                    <div class="admin-actions">
                        <a href="admin_users.php" class="btn btn-primary">👥 Kelola Pengguna</a>
                        <a href="admin_products.php" class="btn btn-primary">📦 Kelola Produk</a>
                        <a href="admin_categories.php" class="btn btn-primary">📂 Kelola Kategori</a>
                        <a href="admin_reports.php" class="btn btn-primary">📊 Laporan</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>📦 Produk Terbaru</h2>
                <a href="admin_products.php" class="btn btn-outline">Lihat Semua</a>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Judul</th>
                            <th>Penjual</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($recent_products, 0, 10) as $product): ?>
                            <tr>
                                <td>
                                    <img src="<?= $product['foto1'] ? 'uploads/produk/' . $product['foto1'] : 'assets/images/no-image.svg' ?>" 
                                         alt="<?= htmlspecialchars($product['judul']) ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product['judul']) ?></strong><br>
                                    <small class="text-muted"><?= $product['kondisi'] ?></small>
                                </td>
                                <td><?= htmlspecialchars($product['nama_penjual']) ?></td>
                                <td>
                                    <?php if ($product['tipe_barang'] === 'jual'): ?>
                                        <?= formatRupiah($product['harga']) ?>
                                    <?php else: ?>
                                        <span class="badge badge-success">GRATIS</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $product['status'] === 'tersedia' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($product['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($product['created_at'])) ?></td>
                                <td>
                                    <a href="product.php?id=<?= $product['id_produk'] ?>" class="btn btn-sm btn-outline">👁 Lihat</a>
                                    <button onclick="deleteProduct(<?= $product['id_produk'] ?>)" class="btn btn-sm btn-danger">🗑️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>👥 Pengguna Terbaru</h2>
                <a href="admin_users.php" class="btn btn-outline">Lihat Semua</a>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Lokasi</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_users as $user_item): ?>
                            <tr>
                                <td><?= htmlspecialchars($user_item['nama']) ?></td>
                                <td><?= htmlspecialchars($user_item['email']) ?></td>
                                <td><?= htmlspecialchars($user_item['lokasi_kos']) ?></td>
                                <td><?= date('d/m/Y', strtotime($user_item['created_at'])) ?></td>
                                <td>
                                    <button onclick="toggleUserStatus(<?= $user_item['id_user'] ?>)" class="btn btn-sm btn-outline">
                                        <?= $user_item['is_active'] ? '🔒 Nonaktifkan' : '🔓 Aktifkan' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
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
    <script>
        function deleteProduct(productId) {
            if (confirm('Yakin ingin menghapus produk ini?')) {
                fetch('ajax/admin_product.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=delete&product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus produk');
                    }
                });
            }
        }

        function toggleUserStatus(userId) {
            fetch('ajax/admin_user.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=toggle_status&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal mengubah status pengguna');
                }
            });
        }
    </script>
</body>
</html>