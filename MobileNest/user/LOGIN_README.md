# 🔐 Login System Integration Guide

## Overview

This document explains how the new login system works and how all files are integrated together.

---

## 📊 File Structure

```
MobileNest/
├── config.php                    # Database & global configuration
├─┠┐ includes/
│   ├── auth-check.php              # Authentication functions
│   ├── header.php                  # Navigation header
│   └── footer.php                  # Footer
├┠┐ user/
│   ├── login.php                   # ✅ LOGIN FORM (fixed)
│   ├── proses-login.php            # Login processor
│   ├── register.php                # Registration form
│   ├── proses-register.php         # Registration processor
│   ├── profil.php                  # User profile (protected)
│   ├── pesanan.php                 # User orders (protected)
│   └── logout.php                  # Logout handler
└── index.php                      # Home page (public)
```

---

## 👩 User Authentication Flow

### 1. **Unauthenticated User Access login.php**

```
GET /MobileNest/user/login.php
       ↓
  [Check: $_SESSION['user'] or $_SESSION['admin']?]
       ├─ YES → Redirect to home/dashboard
       └─ NO  → Display login form
       ↓
   [Show login form with fields]
       ├─ username/email input
       ├─ password input
       ├─ remember me checkbox
       └─ submit button
```

### 2. **User Submits Login Form**

```
POST /MobileNest/user/login.php
     → proses-login.php
       ↓
 [Validate credentials against database]
       ├─ User not found
       │   └── $_SESSION['error'] = "Username tidak ditemukan"
       │   └── Redirect to login.php
       │       ↓
       │   [login.php displays error]
       │
       ├─ Password incorrect
       │   └── $_SESSION['error'] = "Password salah"
       │   └── Redirect to login.php
       │       ↓
       │   [login.php displays error]
       │
       └─ Credentials valid
           ├─ Check if user is admin
           ├─− Set $_SESSION['user'] or $_SESSION['admin']
           └─− Redirect to home.php or admin/dashboard.php
```

### 3. **Logged-in User Access Protected Page**

```
GET /MobileNest/user/profil.php
       ↓
  [File includes auth-check.php]
       ↓
  [require_user_login() called]
       ↓
  [Check: $_SESSION['user'] exists?]
       ├─ YES → Display profile page
       └─ NO  → Redirect to login.php
```

---

## 🔗 File Integration Details

### config.php
**What it provides to login.php:**
```php
// Database connection
$conn = new mysqli(...);

// Global constants
define('SITE_URL', 'http://localhost/MobileNest');

// Session configuration
session_start();

// Helper functions
function sanitize_input($data) { ... }
function format_rupiah($amount) { ... }
function is_logged_in() { ... }
function get_user_info() { ... }
```

**How login.php uses it:**
```php
require_once '../config.php';

// Now available:
$conn          // Database connection
SITE_URL       // For redirects: SITE_URL . '/index.php'
$_SESSION      // Session data
```

---

### includes/header.php
**What it provides:**
- Navigation menu (top bar)
- Logo and branding
- User menu (if logged in)
- Mobile responsive navbar
- Bootstrap CSS/JS

**How login.php uses it:**
```php
include '../includes/header.php';
// Header appears at top of page
```

---

### includes/footer.php
**What it provides:**
- Footer content
- Links and company info
- Copyright notice
- Bootstrap JS (if not already loaded)

**How login.php uses it:**
```php
include '../includes/footer.php';
// Footer appears at bottom of page
```

---

### includes/auth-check.php
**What it provides to protected pages:**
```php
function require_user_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . SITE_URL . '/user/login.php');
        exit;
    }
}

function require_admin_login() {
    if (!isset($_SESSION['admin'])) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

function is_user_logged_in() { return isset($_SESSION['user']); }
function is_admin_logged_in() { return isset($_SESSION['admin']); }
```

**How it's used:**
```php
// In profil.php (PROTECTED PAGE):
require_once '../includes/auth-check.php';
require_user_login();  // ✅ User MUST be logged in

// NOT used in login.php
// login.php checks manually:
if (isset($_SESSION['user'])) {
    header('Location: ...');
}
```

---

### user/proses-login.php
**What it does:**
1. Receives POST data from login.php form
2. Validates username/email
3. Verifies password with `password_verify()`
4. Sets session variables on success
5. Returns error message on failure

**Integration with login.php:**
```php
// login.php form:
<form action="proses-login.php" method="POST">
    <input name="username" required>
    <input name="password" required>
</form>

// proses-login.php processes this:
$_POST['username']  // Gets username
$_POST['password']  // Gets password

// On error:
$_SESSION['error'] = "Error message";
header('Location: login.php');

// On success:
$_SESSION['user'] = $user_id;
header('Location: ../index.php');
```

---

### user/register.php
**How it connects to login.php:**
```php
// At bottom of register.php:
Belum punya akun? 
<a href="register.php">Daftar di sini</a>

// And in register.php:
Sudah punya akun?
<a href="login.php">Login di sini</a>
```

---

## 🔴 Common Issues & Solutions

### Issue 1: Infinite Redirect
**Before Fix:**
- login.php called `require_user_login()` directly
- This required user to be logged in to see the login page!
- Caused infinite redirect loop

**After Fix:**
```php
// Instead of require_user_login():
if (isset($_SESSION['user'])) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;  // EXIT - no infinite loop!
}
// If not logged in, continue to show form
```

---

### Issue 2: Session Not Working
**Debug steps:**
```php
// Add to login.php to debug:
echo '<pre>';
echo 'Session Status: ' . session_status() . PHP_EOL;
echo 'Session ID: ' . session_id() . PHP_EOL;
echo '$_SESSION contents: ' . print_r($_SESSION, true);
echo '</pre>';
```

**Common causes:**
- Session not started (config.php fixes this)
- Browser cookies disabled
- Session timeout
- Different session IDs

---

### Issue 3: Form Not Submitting
**Check:**
1. Form `action="proses-login.php"` exists
2. Form `method="POST"` is set
3. Input `name="username"` and `name="password"` exist
4. Submit button has `type="submit"`
5. proses-login.php exists and is accessible

---

## 📝 Database Requirements

### users table
```sql
CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,  -- hashed
    no_telepon VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### admin table (optional)
```sql
CREATE TABLE admin (
    id_admin INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);
```

---

## 🚀 Testing Guide

### Test 1: Login Form Display
```
1. Clear browser cache/cookies
2. Visit: http://localhost/MobileNest/user/login.php
3. Expected: Login form displays (no redirect)
```

### Test 2: Successful Login
```
1. Enter valid username/email
2. Enter correct password
3. Click "Masuk" button
4. Expected: Redirect to http://localhost/MobileNest/index.php
```

### Test 3: Failed Login - Wrong Password
```
1. Enter valid username/email
2. Enter wrong password
3. Click "Masuk" button
4. Expected: Redirect to login.php with error message
```

### Test 4: Failed Login - User Not Found
```
1. Enter non-existent username
2. Enter any password
3. Click "Masuk" button
4. Expected: Redirect to login.php with error message
```

### Test 5: Protected Page Access
```
1. Logout (if logged in)
2. Try accessing: http://localhost/MobileNest/user/profil.php
3. Expected: Redirect to login.php
```

---

## 📚 Session Variables

### When User Logs In
```php
// Set by proses-login.php:
$_SESSION['user']           = $user['id_user'];           // User ID
$_SESSION['user_name']      = $user['nama_lengkap'];      // Full name
$_SESSION['user_email']     = $user['email'];             // Email
$_SESSION['username']       = $user['username'];          // Username
$_SESSION['role']           = 'user';                     // Role
```

### When Admin Logs In
```php
// Set by proses-login.php:
$_SESSION['admin']          = $user['id_user'];           // Admin user ID
$_SESSION['admin_name']     = $user['nama_lengkap'];      // Admin full name
$_SESSION['admin_email']    = $user['email'];             // Admin email
$_SESSION['admin_username'] = $user['username'];          // Admin username
```

### Error Messages
```php
// Set by proses-login.php on error:
$_SESSION['error'] = "Deskripsi error";
// Cleared by login.php after display
```

---

## 🔐 Security Features

1. **Password Hashing**
   - Passwords hashed with `password_hash()` in registration
   - Verified with `password_verify()` in login

2. **SQL Injection Protection**
   - Prepared statements used in `proses-login.php`
   - `bind_param()` prevents SQL injection

3. **Session Security**
   - Session timeout configured in config.php
   - `session.cookie_httponly` set to 1

4. **Input Validation**
   - Client-side validation in login form
   - Server-side validation in proses-login.php

---

## 🗑️ Troubleshooting Checklist

- [ ] Database connection working (config.php)
- [ ] users table exists with correct structure
- [ ] Session is started (config.php)
- [ ] Session variables are set correctly
- [ ] Cookies are enabled in browser
- [ ] Form action points to proses-login.php
- [ ] proses-login.php exists and is readable
- [ ] Password is correctly hashed in database
- [ ] SITE_URL constant is correct
- [ ] include paths are correct (../ points to MobileNest/)

---

## 📄 File Checklist

```
✅ config.php
✅ includes/auth-check.php
✅ includes/header.php
✅ includes/footer.php
✅ user/login.php                (FIXED)
✅ user/proses-login.php
✅ user/register.php
✅ user/proses-register.php
✅ user/profil.php
✅ user/pesanan.php
✅ user/logout.php
```

---

**Last Updated:** 2025-12-31  
**Status:** Complete ✅  
**Tested:** Yes ✅
