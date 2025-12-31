<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

$page_title = "Login - MobileNest";
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

include '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-5">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            <div class="card shadow border-0 rounded-lg" style="border-radius: 20px;">
                <div class="card-body p-4 p-sm-5">
                    <!-- Logo & Title -->
                    <div class="text-center mb-5">
                        <img src="<?php echo SITE_URL; ?>/assets/images/logo.jpg" alt="MobileNest Logo" height="60" class="mb-3">
                        <h2 class="fw-bold text-primary mb-2">MobileNest</h2>
                        <p class="text-muted">Masuk ke akun Anda</p>
                    </div>

                    <!-- Success Message -->
                    <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-check-circle me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Error Messages -->
                    <?php if ($errors): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?php foreach ($errors as $error): ?>
                        <div>• <?php echo htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form action="proses-login.php" method="POST" novalidate>
                        <div class="mb-4">
                            <label for="username" class="form-label fw-bold mb-2">Username atau Email</label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="username" 
                                   name="username" 
                                   placeholder="Masukkan username atau email Anda" 
                                   required
                                   autocomplete="username"
                                   style="border-radius: 10px; border: 2px solid #e9ecef;">
                            <small class="text-muted d-block mt-2">Gunakan username atau email yang terdaftar</small>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold mb-2">Password</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Masukkan password Anda" 
                                       required
                                       autocomplete="current-password"
                                       style="border-radius: 10px 0 0 10px; border: 2px solid #e9ecef;">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword"
                                        style="border: 2px solid #e9ecef; border-radius: 0 10px 10px 0;">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                            <a href="#" class="text-decoration-none fw-bold" style="color: #667eea; font-size: 14px;">
                                Lupa password?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 fw-bold" style="border-radius: 10px; background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 12px;">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="my-4 d-flex align-items-center">
                        <hr class="flex-grow-1">
                        <small class="text-muted px-2">atau</small>
                        <hr class="flex-grow-1">
                    </div>

                    <!-- Register Link -->
                    <div class="text-center">
                        <p class="mb-0 text-muted">
                            Belum punya akun? 
                            <a href="register.php" class="text-decoration-none fw-bold" style="color: #667eea;">
                                Daftar di sini
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    Dengan login, Anda menyetujui 
                    <a href="#" class="text-decoration-none" style="color: #667eea;">Syarat & Ketentuan</a>
                    kami
                </small>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .card {
        background: white;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .form-control,
    .form-control:focus {
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-outline-secondary {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        color: #667eea;
        border-color: #667eea;
    }

    .form-check-input {
        cursor: pointer;
        border: 2px solid #e9ecef;
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .alert {
        border: none;
        background-color: #f8f9fa;
        padding: 12px 15px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 1.5rem !important;
        }

        h2 {
            font-size: 1.5rem;
        }
    }
</style>

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

<?php include '../includes/footer.php'; ?>
