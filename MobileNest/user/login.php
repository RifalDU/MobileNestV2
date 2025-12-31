<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config.php';
require_once '../includes/auth-check.php';
require_user_login();

$user_id = $_SESSION['user'];
$errors = [];
$message = '';

$sql = "SELECT * FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_profil'])) {
        $nama = trim($_POST['nama_lengkap']);
        $email = trim($_POST['email']);
        $telepon = trim($_POST['no_telepon']);
        $alamat = trim($_POST['alamat']);
        
        if (empty($nama)) $errors[] = 'Nama tidak boleh kosong';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid';
        
        if (empty($errors)) {
            $update = $conn->prepare("UPDATE users SET nama_lengkap=?, email=?, no_telepon=?, alamat=? WHERE id_user=?");
            $update->bind_param('ssssi', $nama, $email, $telepon, $alamat, $user_id);
            if ($update->execute()) {
                $message = 'Profil berhasil diperbarui!';
                $user_data = ['nama_lengkap'=>$nama, 'email'=>$email, 'no_telepon'=>$telepon, 'alamat'=>$alamat];
            }
            $update->close();
        }
    }
    
    if (isset($_POST['ubah_password'])) {
        $old = $_POST['password_lama'];
        $new = $_POST['password_baru'];
        $confirm = $_POST['password_konfirm'];
        
        if (empty($old)) $errors[] = 'Password lama kosong';
        if (strlen($new) < 6) $errors[] = 'Password baru minimal 6 karakter';
        if ($new !== $confirm) $errors[] = 'Password tidak sama';
        
        if (empty($errors)) {
            $check = $conn->prepare("SELECT password FROM users WHERE id_user=?");
            $check->bind_param('i', $user_id);
            $check->execute();
            $pwd = $check->get_result()->fetch_assoc();
            $check->close();
            
            if (password_verify($old, $pwd['password'])) {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password=? WHERE id_user=?");
                $update->bind_param('si', $hash, $user_id);
                if ($update->execute()) $message = 'Password berhasil diubah!';
                $update->close();
            } else {
                $errors[] = 'Password lama salah';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil - MobileNest</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    min-height: 100vh;
}
.profile-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    margin-bottom: 25px;
}
.profile-header {
    text-align: center;
    padding: 40px 20px;
}
.avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 50px;
    color: white;
    margin-bottom: 15px;
}
.nav-tabs {
    border: none;
    background: white;
    border-radius: 15px;
    padding: 10px;
    margin-bottom: 20px;
}
.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    padding: 12px 25px;
    border-radius: 10px;
}
.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}
.form-label {
    font-weight: 600;
    color: #2c3e50;
}
.form-control {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 12px;
}
.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    border-radius: 10px;
    padding: 12px;
}
.btn-danger {
    background: linear-gradient(135deg, #f093fb, #f5576c);
    border: none;
    border-radius: 10px;
    padding: 12px;
}
.btn-back {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
    border-radius: 10px;
    padding: 10px 20px;
    text-decoration: none;
    display: inline-block;
}
</style>
</head>
<body>
<div class="container py-4">
<div class="row justify-content-center">
<div class="col-md-8">

<a href="<?php echo SITE_URL; ?>/index.php" class="btn-back mb-3">← Kembali</a>

<div class="profile-card profile-header">
<div class="avatar"><i class="bi bi-person-circle"></i></div>
<h4><?php echo htmlspecialchars($user_data['nama_lengkap']); ?></h4>
<p><?php echo htmlspecialchars($user_data['email']); ?></p>
</div>

<?php if($message): ?>
<div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<?php if($errors): ?>
<div class="alert alert-danger">
<?php foreach($errors as $e) echo "<div>$e</div>"; ?>
</div>
<?php endif; ?>

<ul class="nav nav-tabs">
<li class="nav-item">
<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#data">Data Pribadi</button>
</li>
<li class="nav-item">
<button class="nav-link" data-bs-toggle="tab" data-bs-target="#password">Keamanan</button>
</li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="data">
<div class="profile-card" style="padding:30px">
<h5>Edit Profil</h5>
<form method="POST">
<div class="mb-3">
<label class="form-label">Nama Lengkap</label>
<input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>" required>
</div>
<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
</div>
<div class="mb-3">
<label class="form-label">No. Telepon</label>
<input type="text" class="form-control" name="no_telepon" value="<?php echo htmlspecialchars($user_data['no_telepon']); ?>">
</div>
<div class="mb-3">
<label class="form-label">Alamat</label>
<textarea class="form-control" name="alamat" rows="3"><?php echo htmlspecialchars($user_data['alamat']); ?></textarea>
</div>
<button type="submit" name="edit_profil" class="btn btn-primary w-100">Simpan</button>
</form>
</div>
</div>

<div class="tab-pane fade" id="password">
<div class="profile-card" style="padding:30px">
<h5>Ubah Password</h5>
<form method="POST">
<div class="mb-3">
<label class="form-label">Password Lama</label>
<input type="password" class="form-control" name="password_lama" required>
</div>
<div class="mb-3">
<label class="form-label">Password Baru (min 6 karakter)</label>
<input type="password" class="form-control" name="password_baru" required>
</div>
<div class="mb-3">
<label class="form-label">Konfirmasi Password</label>
<input type="password" class="form-control" name="password_konfirm" required>
</div>
<button type="submit" name="ubah_password" class="btn btn-danger w-100">Ubah Password</button>
</form>
</div>
</div>
</div>

</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
