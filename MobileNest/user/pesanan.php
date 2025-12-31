<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config.php';
require_once '../includes/auth-check.php';
require_user_login();

// GET USER ID - Support both old and new session variables
$user_id = $_SESSION['user_id'] ?? $_SESSION['user'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Query pesanan
$sql = "SELECT p.*, GROUP_CONCAT(dp.nama_produk SEPARATOR ', ') as produk_list 
        FROM pesanan p 
        JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan 
        WHERE p.id_user = ? 
        GROUP BY p.id_pesanan 
        ORDER BY p.tanggal_pesanan DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>Pesanan Saya - MobileNest</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-bag-check"></i> Pesanan Saya</h2>

    <?php if($result->num_rows > 0): ?>
        <div class="row">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-12 mb-3">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1 fw-bold text-primary">Order #<?php echo $row['id_pesanan']; ?></h5>
                                <small class="text-muted"><i class="bi bi-calendar"></i> <?php echo date('d M Y H:i', strtotime($row['tanggal_pesanan'])); ?></small>
                            </div>
                            <span class="badge bg-<?php echo $row['status_pembayaran'] == 'Lunas' ? 'success' : 'warning'; ?> p-2 rounded-pill">
                                <?php echo $row['status_pembayaran']; ?>
                            </span>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-8">
                                <p class="mb-1 fw-bold">Produk:</p>
                                <p class="text-muted"><?php echo $row['produk_list']; ?></p>
                            </div>
                            <div class="col-md-4 text-end">
                                <p class="mb-1 fw-bold">Total Belanja:</p>
                                <h5 class="text-danger fw-bold">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></h5>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <a href="detail-pesanan.php?id=<?php echo $row['id_pesanan']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <h4>Belum ada pesanan</h4>
            <p class="text-muted">Yuk mulai belanja smartphone impianmu!</p>
            <a href="../produk/list-produk.php" class="btn btn-primary rounded-pill px-4 mt-2">Mulai Belanja</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>