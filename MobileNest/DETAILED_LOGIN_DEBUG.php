<?php
/**
 * DETAILED LOGIN DEBUG SCRIPT
 * Menjalankan setiap step login secara terpisah
 */

echo "<h1>🔍 DETAILED LOGIN DEBUG</h1>";
echo "<hr>";

echo "<h2>Step 1: Session Check</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "<p style='color: green;'>✅ Session started successfully</p>";
} else {
    echo "<p style='color: green;'>✅ Session already active (status = " . session_status() . ")</p>";
}
echo "<p>Session ID: <code>" . session_id() . "</code></p>";
echo "<p>Session name: <code>" . session_name() . "</code></p>";

echo "<h2>Step 2: Require Config</h2>";
try {
    require_once __DIR__ . '/config.php';
    echo "<p style='color: green;'>✅ Config loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Config loading FAILED: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 3: Check Database Connection</h2>";
if ($conn) {
    echo "<p style='color: green;'>✅ Database connection OK</p>";
    echo "<p>Connection type: <code>" . get_class($conn) . "</code></p>";
    echo "<p>Server: <code>" . $conn->server_info . "</code></p>";
    echo "<p>Charset: <code>" . $conn->character_set_name() . "</code></p>";
} else {
    echo "<p style='color: red;'>❌ Database connection NOT available</p>";
    exit;
}

echo "<h2>Step 4: Check SITE_URL Constant</h2>";
if (defined('SITE_URL')) {
    echo "<p style='color: green;'>✅ SITE_URL defined: <code>" . SITE_URL . "</code></p>";
} else {
    echo "<p style='color: red;'>❌ SITE_URL not defined</p>";
}

echo "<h2>Step 5: Check Functions from Config</h2>";
$functions_to_check = [
    'is_logged_in',
    'is_admin',
    'sanitize_input',
    'format_rupiah',
    'fetch_single',
    'fetch_all'
];

echo "<ul>";
foreach ($functions_to_check as $func) {
    if (function_exists($func)) {
        echo "<li style='color: green;'>✅ <code>" . $func . "()</code> exists</li>";
    } else {
        echo "<li style='color: red;'>❌ <code>" . $func . "()</code> NOT found</li>";
    }
}
echo "</ul>";

echo "<h2>Step 6: Simulate Login Process</h2>";

// Simulate credentials
$test_username = 'testuser';
echo "<p>Testing with username: <code>" . $test_username . "</code></p>";

echo "<h3>6a. Prepare SQL Statement</h3>";
$sql = "SELECT id_user, nama_lengkap, email, username, password FROM users WHERE username = ? OR email = ? LIMIT 1";

try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    echo "<p style='color: green;'>✅ SQL prepared successfully</p>";
    echo "<p>Query: <code>" . htmlspecialchars($sql) . "</code></p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Prepare failed: " . $e->getMessage() . "</p>";
}

echo "<h3>6b. Bind Parameters</h3>";
try {
    $stmt->bind_param('ss', $test_username, $test_username);
    echo "<p style='color: green;'>✅ Parameters bound successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Bind failed: " . $e->getMessage() . "</p>";
}

echo "<h3>6c. Execute Query</h3>";
try {
    $stmt->execute();
    echo "<p style='color: green;'>✅ Query executed successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Execute failed: " . $e->getMessage() . "</p>";
}

echo "<h3>6d. Get Results</h3>";
try {
    $result = $stmt->get_result();
    echo "<p style='color: green;'>✅ Results retrieved</p>";
    echo "<p>Rows found: <code>" . $result->num_rows . "</code></p>";
    
    if ($result->num_rows > 0) {
        echo "<h4>User Data Found:</h4>";
        while ($user = $result->fetch_assoc()) {
            echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
            echo htmlspecialchars(print_r($user, true));
            echo "</pre>";
            
            // Check password hash
            echo "<h4>Password Hash Check:</h4>";
            echo "<p>Hash starts with: <code>" . substr($user['password'], 0, 10) . "...</code></p>";
            
            if (substr($user['password'], 0, 4) === '$2y$' || substr($user['password'], 0, 4) === '$2a$') {
                echo "<p style='color: green;'>✅ Password is bcrypt hashed (correct)</p>";
                
                // Test password_verify with sample password
                echo "<h4>Test password_verify():</h4>";
                $test_password = 'password123'; // Change this to test
                if (password_verify($test_password, $user['password'])) {
                    echo "<p style='color: green;'>✅ password_verify() works with 'password123'</p>";
                } else {
                    echo "<p style='color: orange;'>⚠️ password_verify() failed with 'password123'</p>";
                    echo "<p>This is expected if the actual password is different.</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Password is NOT bcrypt hashed!</p>";
                echo "<p>Hash type detected: <code>" . substr($user['password'], 0, 20) . "</code></p>";
                echo "<p>This may be MD5 or plain text. Need to re-hash!</p>";
            }
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No user found with username: <code>" . $test_username . "</code></p>";
        echo "<h4>Available users in database:</h4>";
        $all_users = $conn->query("SELECT id_user, username, email FROM users LIMIT 10");
        if ($all_users && $all_users->num_rows > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th></tr>";
            while ($u = $all_users->fetch_assoc()) {
                echo "<tr><td>" . $u['id_user'] . "</td><td>" . $u['username'] . "</td><td>" . $u['email'] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ NO USERS IN DATABASE!</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Get result failed: " . $e->getMessage() . "</p>";
}

$stmt->close();

echo "<h2>Step 7: Check $_SESSION After Simulated Login</h2>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo htmlspecialchars(print_r($_SESSION, true));
echo "</pre>";

echo "<h2>Step 8: Check Error Logs</h2>";
echo "<p>Check these files for errors:</p>";
echo "<ul>";
echo "<li>Apache: <code>C:\\xampp\\apache\\logs\\error.log</code></li>";
echo "<li>PHP: <code>C:\\xampp\\php\\logs\\php_error_log</code> (if exists)</li>";
echo "<li>MySQL: <code>C:\\xampp\\mysql\\data\\mysql_error.log</code></li>";
echo "</ul>";

echo "<h2>Step 9: Actual Login Test</h2>";
echo "<p>Now try actual login with these credentials:</p>";
echo "<form method='POST' action='user/proses-login.php'>";
echo "<p>";
echo "<label>Username/Email: </label>";
echo "<input type='text' name='username' placeholder='Enter username or email' required>";
echo "</p>";
echo "<p>";
echo "<label>Password: </label>";
echo "<input type='password' name='password' placeholder='Enter password' required>";
echo "</p>";
echo "<p>";
echo "<button type='submit'>Test Login</button>";
echo "</p>";
echo "</form>";

echo "<hr>";
echo "<p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; line-height: 1.6; }
    h1 { color: #c4302b; border-bottom: 3px solid #c4302b; padding-bottom: 10px; }
    h2 { color: #0066cc; margin-top: 30px; }
    h3 { color: #666; margin-top: 15px; }
    h4 { color: #999; }
    p { margin: 8px 0; }
    code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    pre { background: #f5f5f5; border: 1px solid #ddd; padding: 15px; border-radius: 5px; overflow-x: auto; }
    table { border-collapse: collapse; margin: 10px 0; background: white; }
    td, th { padding: 10px; border: 1px solid #ddd; }
    th { background: #0066cc; color: white; }
    form { background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #0066cc; }
    input { padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; }
    button { background: #0066cc; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
    button:hover { background: #0052a3; }
    ul { background: white; padding: 20px 40px; border-radius: 5px; }
</style>
