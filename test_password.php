<?php
/**
 * Password Test Script
 * This will test if the password hash is working correctly
 */

require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Password Test</title>";
echo "<style>body{font-family:Arial;padding:20px;max-width:800px;margin:0 auto;}";
echo ".pass{color:green;font-weight:bold;} .fail{color:red;font-weight:bold;}";
echo ".info{background:#f0f0f0;padding:10px;margin:10px 0;border-radius:5px;}";
echo "table{border-collapse:collapse;width:100%;margin:20px 0;}";
echo "th,td{border:1px solid #ddd;padding:12px;text-align:left;}";
echo "th{background:#667eea;color:white;}</style></head><body>";

echo "<h1>🔐 Password Authentication Test</h1>";

// Test 1: Check database connection
echo "<h2>Test 1: Database Connection</h2>";
if ($conn->connect_error) {
    echo "<p class='fail'>❌ FAILED: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p class='pass'>✅ PASSED: Connected to database</p>";
}

// Test 2: Check if tbl_users exists
echo "<h2>Test 2: Users Table Exists</h2>";
$result = $conn->query("SHOW TABLES LIKE 'tbl_users'");
if ($result->num_rows == 0) {
    echo "<p class='fail'>❌ FAILED: tbl_users table does not exist</p>";
    echo "<p>Run the migration SQL file first!</p>";
    exit;
} else {
    echo "<p class='pass'>✅ PASSED: tbl_users table exists</p>";
}

// Test 3: Get admin user
echo "<h2>Test 3: Admin User Exists</h2>";
$sql = "SELECT user_id, username, password, role, email, full_name, is_active, is_deleted 
        FROM tbl_users 
        WHERE username = 'admin'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<p class='fail'>❌ FAILED: Admin user not found</p>";
    exit;
} else {
    echo "<p class='pass'>✅ PASSED: Admin user found</p>";
    $user = $result->fetch_assoc();
    
    echo "<div class='info'>";
    echo "<strong>User Details:</strong><br>";
    echo "User ID: " . $user['user_id'] . "<br>";
    echo "Username: " . $user['username'] . "<br>";
    echo "Role: " . $user['role'] . "<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Full Name: " . $user['full_name'] . "<br>";
    echo "Is Active: " . ($user['is_active'] ? 'Yes' : 'No') . "<br>";
    echo "Is Deleted: " . ($user['is_deleted'] ? 'Yes' : 'No') . "<br>";
    echo "Password Hash: <code>" . substr($user['password'], 0, 30) . "...</code><br>";
    echo "</div>";
}

// Test 4: Check if account is active
echo "<h2>Test 4: Account Status</h2>";
if ($user['is_active'] != 1) {
    echo "<p class='fail'>❌ FAILED: Account is inactive</p>";
} else {
    echo "<p class='pass'>✅ PASSED: Account is active</p>";
}

if ($user['is_deleted'] != 0) {
    echo "<p class='fail'>❌ FAILED: Account is marked as deleted</p>";
} else {
    echo "<p class='pass'>✅ PASSED: Account is not deleted</p>";
}

// Test 5: Test password verification
echo "<h2>Test 5: Password Verification</h2>";
$test_password = "admin123";
$stored_hash = $user['password'];

echo "<div class='info'>";
echo "<strong>Testing password:</strong> " . htmlspecialchars($test_password) . "<br>";
echo "<strong>Stored hash:</strong> <code>" . htmlspecialchars($stored_hash) . "</code><br>";
echo "</div>";

if (password_verify($test_password, $stored_hash)) {
    echo "<p class='pass'>✅ PASSED: Password 'admin123' matches the hash!</p>";
    echo "<p><strong>The password should work for login!</strong></p>";
} else {
    echo "<p class='fail'>❌ FAILED: Password 'admin123' does NOT match the hash</p>";
    echo "<p><strong>This is the problem! Let's fix it...</strong></p>";
    
    // Generate new hash
    $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
    echo "<div class='info'>";
    echo "<h3>🔧 Solution: Update Password Hash</h3>";
    echo "<p><strong>New generated hash:</strong></p>";
    echo "<code>" . htmlspecialchars($new_hash) . "</code><br><br>";
    echo "<p><strong>Run this SQL in phpMyAdmin:</strong></p>";
    echo "<textarea style='width:100%;height:80px;font-family:monospace;'>";
    echo "UPDATE `tbl_users` SET `password` = '" . $new_hash . "' WHERE `username` = 'admin';";
    echo "</textarea>";
    echo "</div>";
}

// Test 6: Check PHP version
echo "<h2>Test 6: PHP Environment</h2>";
echo "<div class='info'>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "<strong>password_hash available:</strong> " . (function_exists('password_hash') ? 'Yes' : 'No') . "<br>";
echo "<strong>password_verify available:</strong> " . (function_exists('password_verify') ? 'Yes' : 'No') . "<br>";
echo "</div>";

// Test 7: Recent login attempts
echo "<h2>Test 7: Recent Login Attempts</h2>";
$sql = "SELECT log_id, user_id, action, description, ip_address, created_at 
        FROM tbl_activity_logs 
        WHERE action LIKE 'login%' 
        ORDER BY created_at DESC 
        LIMIT 10";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Log ID</th><th>Action</th><th>Description</th><th>IP</th><th>Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $action_class = ($row['action'] == 'login') ? 'pass' : 'fail';
        echo "<tr>";
        echo "<td>" . $row['log_id'] . "</td>";
        echo "<td class='" . $action_class . "'>" . $row['action'] . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>" . $row['ip_address'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No login attempts found</p>";
}

echo "<h2>📋 Summary</h2>";
echo "<p>If all tests passed, you should be able to login with:</p>";
echo "<div class='info'>";
echo "<strong>URL:</strong> <a href='login.html'>login.html</a><br>";
echo "<strong>Username:</strong> admin<br>";
echo "<strong>Password:</strong> admin123<br>";
echo "</div>";

echo "<p><a href='login.html'>← Go to Login Page</a></p>";

echo "</body></html>";

$conn->close();
?>
