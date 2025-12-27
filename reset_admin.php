<?php
/**
 * TEMPORARY LOGIN FIX
 * This will reset the admin password in the database
 * Access this file ONCE at: http://localhost/RMS/reset_admin.php
 * Then delete this file for security
 */

require_once 'config.php';

// Generate a fresh password hash for 'admin123'
$new_password = 'admin123';
$password_hash = password_hash($new_password, PASSWORD_DEFAULT);

// Update the admin user's password
$sql = "UPDATE users SET password = ? WHERE username = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $password_hash);

if ($stmt->execute()) {
    echo "<h1 style='color: green;'>✅ Success!</h1>";
    echo "<p>Admin password has been reset.</p>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p>The new password hash is: <code>" . $password_hash . "</code></p>";
    echo "<hr>";
    echo "<p><a href='index.php' style='background: #E63946; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    echo "<hr>";
    echo "<p style='color: red;'><strong>IMPORTANT:</strong> Delete this file (reset_admin.php) after logging in for security!</p>";
} else {
    echo "<h1 style='color: red;'>❌ Error!</h1>";
    echo "<p>Failed to update password: " . $conn->error . "</p>";
    echo "<p>Make sure the 'users' table exists and has an 'admin' user.</p>";
}

$stmt->close();
$conn->close();
?>
