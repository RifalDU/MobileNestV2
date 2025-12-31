<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

$page_title = "Login";
$errors = [];
$success = '';

// Jika sudah login, redirect ke home
if (isset($_SESSION['user'])) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}
if (isset($_SESSION['admin'])) {
    header('Location: ' . SITE_URL . '/admin/dashboard.php');
    exit;
}

// Tangkap pesan error/success dari session
if (isset($_SESSION['error'])) {
    $errors[] = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

$css_path = "../assets/css/style.css";
$js_path = "../assets/js/script.js";
$logo_path = "../assets/images/logo.jpg";
$home_url = "../index.php";
$produk_url = "../produk/list-produk.php";
$login_url = "login.php";
$register_url = "register.php";
$keranjang_url = "../transaksi/keranjang.php";

include '../includes/header.php';
?>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="card shadow border-0 rounded-lg">
                    <div class="card-body p-4 p-sm-5">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <img src="<?php echo $logo_path; ?>" alt="MobileNest Logo" height="50" class="mb-3">
                            <h3 class="fw-bold text-primary">MobileNest</h3>
                            <p class="text-muted">Masuk ke akun Anda</p>
                        </div>

                        <!-- Success Message -->
                        <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Error Messages -->
                        <?php if ($errors): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <?php foreach ($errors as $error): ?>
                            <div>• <?php echo htmlspecialchars($error); ?></div>
                            <?php endforeach; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>

                        <!-- Form -->
                        <form action="proses-login.php" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold">Username atau Email</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       placeholder="Masukkan username atau email Anda" 
                                       required
                                       autocomplete="username">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Masukkan password Anda" 
                                           required
                                           autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Ingat saya
                                    </label>
                                </div>
                                <a href="#" class="text-decoration-none fw-bold text-primary" style="font-size: 14px;">
                                    Lupa password?
                                </a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="my-4 text-center">
                            <small class="text-muted">atau</small>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="mb-0">Belum punya akun? 
                                <a href="register.php" class="text-decoration-none fw-bold">Daftar di sini</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!username || !password) {
            e.preventDefault();
            alert('Username/Email dan Password harus diisi!');
            return false;
        }
    });
</script>
