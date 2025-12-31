<?php
/**
 * DEBUG CONNECTION SCRIPT
 * Cek semua aspek koneksi database
 */

echo "<h2>🔍 Database Connection Debug</h2>";
echo "<hr>";

// 1. Check if MySQLi extension loaded
echo "<h3>1. MySQLi Extension Check</h3>";
if (extension_loaded('mysqli')) {
    echo "<p style='color: green;'>✅ MySQLi extension LOADED</p>";
} else {
    echo "<p style='color: red;'>❌ MySQLi extension NOT loaded</p>";
    echo "<p>Silakan enable mysqli di php.ini</p>";
}

// 2. Test MySQL Service
echo "<h3>2. MySQL Service Status</h3>";
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

echo "<p>Testing connection to: <strong>$db_host</strong></p>";

// Try different connection methods
echo "<h4>Method A: MySQLi Connection</h4>";
$conn = @mysqli_connect($db_host, $db_user, $db_pass);

if ($conn) {
    echo "<p style='color: green;'>✅ MySQLi Connected Successfully!</p>";
    echo "<p>Server Info: " . mysqli_get_server_info($conn) . "</p>";
    
    // 3. Test specific database
    echo "<h3>3. Database Check</h3>";
    $db_name = 'mobilenest_db';
    
    $db_select = mysqli_select_db($conn, $db_name);
    if ($db_select) {
        echo "<p style='color: green;'>✅ Database '$db_name' EXISTS</p>";
        
        // Check tables
        echo "<h3>4. Tables Check</h3>";
        $result = mysqli_query($conn, "SHOW TABLES");
        
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<p style='color: green;'>✅ Tables found:</p>";
            echo "<ul>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: orange;'>⚠️ No tables found in database</p>";
        }
        
        // Check users table
        echo "<h3>5. Users Table Check</h3>";
        $users_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
        if ($users_check && mysqli_num_rows($users_check) > 0) {
            echo "<p style='color: green;'>✅ Users table EXISTS</p>";
            
            // Count users
            $count = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
            $row = mysqli_fetch_assoc($count);
            echo "<p>Total users: <strong>" . $row['total'] . "</strong></p>";
            
            // Show user columns
            echo "<h4>User Table Structure:</h4>";
            $columns = mysqli_query($conn, "DESCRIBE users");
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($col = mysqli_fetch_assoc($columns)) {
                echo "<tr>";
                echo "<td>" . $col['Field'] . "</td>";
                echo "<td>" . $col['Type'] . "</td>";
                echo "<td>" . $col['Null'] . "</td>";
                echo "<td>" . $col['Key'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ Users table NOT FOUND</p>";
            echo "<p>Run the database setup script first!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Database '$db_name' NOT FOUND</p>";
        
        // Show available databases
        echo "<h3>Available Databases:</h3>";
        $result = mysqli_query($conn, "SHOW DATABASES");
        echo "<ul>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ MySQLi Connection FAILED</p>";
    echo "<p><strong>Error:</strong> " . mysqli_connect_error() . "</p>";
    echo "<hr>";
    echo "<h3>⚠️ TROUBLESHOOTING:</h3>";
    echo "<ol>";
    echo "<li>✓ Check if MySQL service is running in XAMPP Control Panel</li>";
    echo "<li>✓ Click START on MySQL module</li>";
    echo "<li>✓ Wait 5 seconds for MySQL to start</li>";
    echo "<li>✓ Refresh this page (F5)</li>";
    echo "<li>✓ If still fails, check XAMPP error log: C:\\xampp\\mysql\\data\\mysql_error.log</li>";
    echo "</ol>";
    
    echo "<h3>Alternative Hosts to Try:</h3>";
    echo "<ul>";
    echo "<li>127.0.0.1 (instead of localhost)</li>";
    echo "<li>Check if port 3306 is in use</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h3>6. Environment Info</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>OS:</strong> " . php_uname() . "</p>";
echo "<p><strong>Server API:</strong> " . php_sapi_name() . "</p>";

echo "<hr>";
echo "<h3>7. Quick Fix Steps</h3>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Find 'MySQL' module</li>";
echo "<li>Click 'Start' button (should turn green)</li>";
echo "<li>Wait 5-10 seconds</li>";
echo "<li>Refresh this page</li>";
echo "</ol>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h2 { color: #333; }
    h3 { color: #0066cc; margin-top: 20px; }
    h4 { color: #666; }
    p { line-height: 1.6; }
    table { border-collapse: collapse; background: white; }
    td { padding: 10px; border: 1px solid #ddd; }
    th { background: #0066cc; color: white; padding: 10px; }
    ol { background: white; padding: 20px 40px; border-radius: 5px; }
    ul { background: white; padding: 20px 40px; border-radius: 5px; }
</style>
