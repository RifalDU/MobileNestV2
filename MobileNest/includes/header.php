<?php
// FILE: includes/header.php
// 1. Cek Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 2. Panggil Config (Wajib ada biar SITE_URL jalan)
require_once dirname(__DIR__) . '/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'MobileNest'; ?> - E-Commerce Smartphone</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    
    <link rel="shortcut icon" href="<?php echo SITE_URL; ?>/assets/images/logo.jpg" type="image/x-icon">
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo SITE_URL; ?>/index.php">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.jpg" alt="Logo" height="40" class="me-2 rounded">
                <span class="text-primary">MobileNest</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/index.php"><i class="bi bi-house"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/produk/list-produk.php"><i class="bi bi-phone"></i> Produk</a></li>
                    
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Akun'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/profil.php">Profil</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/pesanan.php">Pesanan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/user/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo SITE_URL; ?>/user/login.php">Masuk</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-primary text-white ms-2 px-3" href="<?php echo SITE_URL; ?>/user/register.php">Daftar</a></li>
                    <?php endif; ?>

                    <li class="nav-item ms-lg-2">
                        <a class="nav-link position-relative btn btn-light border px-3" href="<?php echo SITE_URL; ?>/transaksi/keranjang.php">
                            <i class="bi bi-cart-fill text-primary"></i>
                            <span id="cart-count-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>