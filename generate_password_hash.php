<?php
/**
 * Password Hash Generator
 * Run this file once to generate a new password hash for admin123
 * Then copy the hash and update it in the database
 */

// Generate hash for "admin123"
$password = "admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<!DOCTYPE html>";
echo "<html><head><title>Password Hash Generator</title>";
echo "<style>body{font-family:Arial;padding:20px;max-width:800px;margin:0 auto;}";
echo ".hash{background:#f0f0f0;padding:15px;border-radius:5px;word-break:break-all;margin:10px 0;}";
echo ".code{background:#2d2d2d;color:#fff;padding:15px;border-radius:5px;margin:10px 0;overflow-x:auto;}";
echo "button{background:#4CAF50;color:white;padding:10px 20px;border:none;cursor:pointer;margin:5px;}";
echo "</style></head><body>";

echo "<h1>🔐 Admin Password Hash Generator</h1>";
echo "<p><strong>Password:</strong> admin123</p>";
echo "<p><strong>Generated Hash:</strong></p>";
echo "<div class='hash'>" . htmlspecialchars($hash) . "</div>";

echo "<h2>📋 Step 1: Copy the Hash Above</h2>";
echo "<button onclick=\"navigator.clipboard.writeText('" . htmlspecialchars($hash) . "').then(() => alert('Hash copied to clipboard!'))\">Copy Hash to Clipboard</button>";

echo "<h2>🔧 Step 2: Run This SQL in phpMyAdmin</h2>";
echo "<p>Go to phpMyAdmin → dbenrollment database → SQL tab → Paste and execute:</p>";
echo "<div class='code'>";
echo "UPDATE `tbl_users` <br>";
echo "SET `password` = '" . htmlspecialchars($hash) . "'<br>";
echo "WHERE `username` = 'admin';";
echo "</div>";

echo "<h2>✅ Step 3: Test Login</h2>";
echo "<ul>";
echo "<li>Go to: <a href='login.html' target='_blank'>login.html</a></li>";
echo "<li>Username: <strong>admin</strong></li>";
echo "<li>Password: <strong>admin123</strong></li>";
echo "</ul>";

echo "<h2>🔍 Current Database Check</h2>";
echo "<p>To verify current password hash in database, run this SQL:</p>";
echo "<div class='code'>";
echo "SELECT username, password, role, is_active FROM tbl_users WHERE username = 'admin';";
echo "</div>";

// Test the hash immediately
$verify_test = password_verify("admin123", $hash);
echo "<h2>🧪 Hash Verification Test</h2>";
echo "<p>Testing if hash works with 'admin123': ";
echo $verify_test ? "<strong style='color:green;'>✓ SUCCESS</strong>" : "<strong style='color:red;'>✗ FAILED</strong>";
echo "</p>";

echo "</body></html>";
?>
