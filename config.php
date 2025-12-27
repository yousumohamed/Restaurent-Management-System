<?php
/**
 * Restaurant Management System
 * Database Configuration File
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'restaurant_management');

// Application Configuration
define('SITE_URL', 'http://localhost/RMS');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage() . "<br>Please check your database configuration.");
}

// Timezone
date_default_timezone_set('Africa/Nairobi');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Security: Prevent direct access to config file
 */
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Direct access not permitted');
}
?>
