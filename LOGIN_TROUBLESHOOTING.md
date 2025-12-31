# 🔧 LOGIN TROUBLESHOOTING GUIDE - MobileNestV2

## 📌 Problem: HTTP 500 Error pada Login

**Symptoms:**
- Ketika login, halaman redirect ke `proses-login.php`
- Muncul error "Halaman ini tidak berfungsi"
- HTTP ERROR 500

---

## 🔍 ROOT CAUSE ANALYSIS

### Masalah #1: Session Tidak Dimulai di proses-login.php
**Severity:** 🔴 CRITICAL

**Problem:**
```php
// ❌ SALAH - config.php di-require sebelum session_start()
require_once '../config.php';
$_SESSION['user'] = $user['id_user'];  // Error: Session belum dimulai!
```

**Solution:**
```php
// ✅ BENAR - Session dimulai DULU
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
$_SESSION['user'] = $user['id_user'];  // OK!
```

---

### Masalah #2: Undefined Function di header.php
**Severity:** 🔴 CRITICAL

**Problem:**
```php
// header.php memanggil function yang tidak ada
<?php if (is_logged_in()): ?>
    // ... ini akan error jika function belum defined
<?php endif; ?>
```

**Solution:**
```php
// Tambahkan di header.php setelah require config
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) || 
               (isset($_SESSION['user']) && !empty($_SESSION['user']));
    }
}
```

---

## ✅ FIXES APPLIED

### Commit 1: proses-login.php
```
File: MobileNest/user/proses-login.php
Commit: c208dee48fc27717120b467dcde9cec177895a95
```

**Changes:**
- ✅ Session dimulai SEBELUM require config
- ✅ Added error logging untuk debugging
- ✅ Improved error handling dengan `error_log()`
- ✅ Better error messages

### Commit 2: header.php
```
File: MobileNest/includes/header.php
Commit: cfe41818e7a7e483b7bad70254f58e5b8675e6fc
```

**Changes:**
- ✅ Added fallback `is_logged_in()` function
- ✅ Improved user name display (admin_name fallback)
- ✅ Better error prevention

---

## 🧪 VERIFICATION CHECKLIST

Setelah update, test dengan langkah berikut:

### Step 1: Clear Browser Cache
```
Press: Ctrl + Shift + Delete (Windows/Linux) atau Cmd + Shift + Delete (Mac)
Select: Cookies and cached images/files
Click: Clear
```

### Step 2: Test Login Process
```
1. Go to: http://localhost/MobileNest/user/login.php
2. Enter valid username/email
3. Enter correct password
4. Click "Masuk"
5. Expected: Redirect ke index.php dengan session aktif
```

### Step 3: Check Error Logs
```
Path: C:\xampp\apache\logs\error.log
Look for: "Login Success - User ID: [ID]"
If found: Login process berhasil
```

### Step 4: Verify Session Data
Buat file debug `test-login.php` di root:
```php
<?php
session_start();
echo "<pre>";
echo "Session Data:\n";
print_r($_SESSION);
echo "</pre>";
?>
```

Then visit: `http://localhost/MobileNest/test-login.php`

---

## 🐛 COMMON ISSUES & SOLUTIONS

### Issue 1: Still Getting HTTP 500

**Solution:**
1. Check XAMPP error log:
   ```
   tail -f C:\xampp\apache\logs\error.log
   ```

2. Enable display_errors temporarily (DEVELOPMENT ONLY):
   ```php
   // Di awal proses-login.php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

3. Verify database connection:
   ```
   Run: http://localhost/MobileNest/test-connection.php
   ```

### Issue 2: "Password salah" Even with Correct Password

**Causes:**
- Password di database bukan hashed dengan `password_hash()`
- Password di-hash dengan md5/sha1 (tidak kompatibel dengan password_verify)

**Solution:**
1. Check password hash:
   ```sql
   SELECT username, password FROM users LIMIT 1;
   -- Harus dimulai dengan $2y$ atau $2a$ (bcrypt)
   ```

2. If not bcrypt, re-hash password:
   ```php
   // Create test-rehash.php
   <?php
   require_once 'config.php';
   
   $new_password = password_hash('password123', PASSWORD_BCRYPT);
   echo "New hash: " . $new_password;
   
   // Then manually update in database:
   // UPDATE users SET password = '[new_hash]' WHERE username = 'testuser';
   ?>
   ```

### Issue 3: Session Data Not Persisting

**Check:**
```php
// In proses-login.php, add debug
error_log("Session ID: " . session_id());
error_log("User ID Set: " . $_SESSION['user']);
```

**Solutions:**
- Verify `php.ini` session settings:
  ```
  session.save_path = /xampp/tmp (must exist)
  session.use_cookies = 1
  session.cookie_httponly = 1
  ```

- Clear session files:
  ```
  Delete all files in: C:\xampp\tmp
  ```

---

## 📊 FILE STRUCTURE

```
MobileNest/
├── config.php                    (Database config + global functions)
├── includes/
│   ├── header.php               (Navigation bar - includes function check)
│   ├── footer.php
│   └── auth-check.php           (Auth utilities)
└── user/
    ├── login.php                (Login form)
    ├── proses-login.php         (Login processing - FIXED)
    ├── register.php             (Register form)
    └── proses-register.php      (Register processing)
```

---

## 🔐 SECURITY NOTES

### Password Security
✅ Using `password_hash()` - GOOD
✅ Using `password_verify()` - GOOD
✅ Prepared statements - GOOD

### Session Security
⚠️ Consider adding:
- Session timeout (already in config: 3600 seconds)
- HTTPS support (set session.cookie_secure = 1 on production)
- CSRF token validation
- Rate limiting on login attempts

---

## 📞 NEXT STEPS

If issues persist:

1. **Get detailed error:**
   - Check XAMPP error log
   - Enable display_errors
   - Check browser console (F12)

2. **Database verification:**
   - Run `verify-database-structure.php`
   - Verify users table exists
   - Check column names match code

3. **Restart services:**
   ```
   XAMPP Control Panel → Restart Apache & MySQL
   Clear browser cache
   ```

---

## 📝 CHANGELOG

| Date | File | Change | Status |
|------|------|--------|--------|
| 2025-12-31 | proses-login.php | Fixed session handling | ✅ DONE |
| 2025-12-31 | header.php | Added function check | ✅ DONE |
| 2025-12-31 | login.php | Updated UI styling | ✅ DONE |

---

**Last Updated:** 2025-12-31  
**Tested:** ✅ Session handling  
**Status:** Ready for testing
