<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi input
    if (empty($username_or_email) || empty($password)) {
        $_SESSION['error'] = "Username/email dan password wajib diisi.";
        header('Location: login.php');
        exit;
    }

    // Query user berdasarkan email atau username (PREPARED STATEMENT)
    $sql = "SELECT id_user, nama_lengkap, email, username, password 
            FROM users 
            WHERE username = ? OR email = ? 
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $_SESSION['error'] = "Database error: " . $conn->error;
        header('Location: login.php');
        exit;
    }
    
    $stmt->bind_param('ss', $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password (hashed dengan password_hash)
        if (password_verify($password, $user['password'])) {
            
            // Check if user is admin (optional)
            $is_admin = false;
            $table_check = $conn->query("SHOW TABLES LIKE 'admin'");
            
            if ($table_check && $table_check->num_rows > 0) {
                $admin_sql = "SELECT id_admin FROM admin WHERE id_user = ?";
                $admin_stmt = $conn->prepare($admin_sql);
                
                if ($admin_stmt) {
                    $admin_stmt->bind_param('i', $user['id_user']);
                    $admin_stmt->execute();
                    $admin_result = $admin_stmt->get_result();
                    
                    if ($admin_result->num_rows > 0) {
                        $is_admin = true;
                    }
                    $admin_stmt->close();
                }
            }
            
            if ($is_admin) {
                // LOGIN AS ADMIN
                $_SESSION['admin'] = $user['id_user'];
                $_SESSION['admin_name'] = $user['nama_lengkap'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['success'] = "Login berhasil. Selamat datang, Admin " . $user['nama_lengkap'] . "!";
                
                header('Location: ../admin/dashboard.php');
                exit;
            } else {
                // LOGIN AS USER
                $_SESSION['user'] = $user['id_user'];
                $_SESSION['user_name'] = $user['nama_lengkap'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'user';
                $_SESSION['success'] = "Login berhasil. Selamat datang, " . $user['nama_lengkap'] . "!";
                
                // Redirect ke halaman sebelumnya atau home
                $redirect_to = $_SESSION['redirect_after_login'] ?? '../index.php';
                unset($_SESSION['redirect_after_login']);
                
                header('Location: ' . $redirect_to);
                exit;
            }
        } else {
            $_SESSION['error'] = "Password salah.";
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "Username atau email tidak ditemukan.";
        header('Location: login.php');
        exit;
    }
    
    $stmt->close();
} else {
    // Jika bukan POST request, redirect ke login
    header('Location: login.php');
    exit;
}
?>