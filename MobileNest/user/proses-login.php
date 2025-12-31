<?php
// ENABLE ERROR REPORTING FOR DEBUGGING
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

// START SESSION FIRST before any other operations
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log("=== LOGIN PROCESS STARTED ===");
error_log("Session started: " . session_id());

// Then require config
try {
    require_once '../config.php';
    error_log("Config loaded successfully");
} catch (Exception $e) {
    error_log("ERROR: Config loading failed - " . $e->getMessage());
    $_SESSION['error'] = "System error: Could not load configuration";
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    
    $username_or_email = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    error_log("Attempting login with: " . $username_or_email);

    // Validasi input
    if (empty($username_or_email) || empty($password)) {
        error_log("ERROR: Empty username or password");
        $_SESSION['error'] = "Username/email dan password wajib diisi.";
        header('Location: login.php');
        exit;
    }

    // Query user berdasarkan email atau username (PREPARED STATEMENT)
    $sql = "SELECT id_user, nama_lengkap, email, username, password 
            FROM users 
            WHERE username = ? OR email = ? 
            LIMIT 1";
    
    error_log("Preparing SQL statement");
    
    try {
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        error_log("SQL statement prepared successfully");
        
        error_log("Binding parameters: username=" . $username_or_email);
        $stmt->bind_param('ss', $username_or_email, $username_or_email);
        
        error_log("Executing statement");
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        error_log("Getting results");
        $result = $stmt->get_result();
        error_log("Rows found: " . $result->num_rows);

        if ($result->num_rows === 1) {
            error_log("User found in database");
            $user = $result->fetch_assoc();
            
            error_log("User ID: " . $user['id_user']);
            error_log("Username: " . $user['username']);
            error_log("Email: " . $user['email']);
            error_log("Password hash starts with: " . substr($user['password'], 0, 10));

            // Verifikasi password (hashed dengan password_hash)
            error_log("Verifying password with password_verify()");
            
            if (password_verify($password, $user['password'])) {
                error_log("Password verification PASSED");
                
                // Check if user is admin (optional)
                $is_admin = false;
                error_log("Checking if user is admin");
                
                $table_check = $conn->query("SHOW TABLES LIKE 'admin'");
                
                if ($table_check && $table_check->num_rows > 0) {
                    error_log("Admin table exists");
                    $admin_sql = "SELECT id_admin FROM admin WHERE id_user = ?";
                    $admin_stmt = $conn->prepare($admin_sql);
                    
                    if ($admin_stmt) {
                        $admin_stmt->bind_param('i', $user['id_user']);
                        $admin_stmt->execute();
                        $admin_result = $admin_stmt->get_result();
                        
                        if ($admin_result->num_rows > 0) {
                            error_log("User is ADMIN");
                            $is_admin = true;
                        } else {
                            error_log("User is REGULAR USER");
                        }
                        $admin_stmt->close();
                    }
                } else {
                    error_log("Admin table does not exist");
                }
                
                if ($is_admin) {
                    error_log("Setting session as ADMIN");
                    // LOGIN AS ADMIN
                    $_SESSION['admin'] = $user['id_user'];
                    $_SESSION['admin_name'] = $user['nama_lengkap'];
                    $_SESSION['admin_email'] = $user['email'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['success'] = "Login berhasil. Selamat datang, Admin " . $user['nama_lengkap'] . "!";
                    
                    error_log("Admin login successful - User ID: " . $user['id_user']);
                    error_log("Redirecting to: ../admin/dashboard.php");
                    header('Location: ../admin/dashboard.php');
                    exit;
                } else {
                    error_log("Setting session as REGULAR USER");
                    // LOGIN AS USER
                    $_SESSION['user'] = $user['id_user'];
                    $_SESSION['user_name'] = $user['nama_lengkap'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = 'user';
                    $_SESSION['success'] = "Login berhasil. Selamat datang, " . $user['nama_lengkap'] . "!";
                    
                    error_log("User login successful - User ID: " . $user['id_user']);
                    
                    // Redirect ke halaman sebelumnya atau home
                    $redirect_to = $_SESSION['redirect_after_login'] ?? '../index.php';
                    unset($_SESSION['redirect_after_login']);
                    
                    error_log("Redirecting to: " . $redirect_to);
                    header('Location: ' . $redirect_to);
                    exit;
                }
            } else {
                error_log("Password verification FAILED");
                $_SESSION['error'] = "Password salah.";
                header('Location: login.php');
                exit;
            }
        } else {
            error_log("User NOT found in database");
            $_SESSION['error'] = "Username atau email tidak ditemukan.";
            header('Location: login.php');
            exit;
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("EXCEPTION: " . $e->getMessage());
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header('Location: login.php');
        exit;
    }
    
} else {
    // Jika bukan POST request, redirect ke login
    error_log("ERROR: Not a POST request");
    header('Location: login.php');
    exit;
}

error_log("=== LOGIN PROCESS ENDED ===");
?>