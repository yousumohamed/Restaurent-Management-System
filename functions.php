<?php
/**
 * Restaurant Management System
 * Reusable Functions Library
 */

/**
 * Sanitize user input to prevent XSS attacks
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect to login if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit();
    }
}

/**
 * Format currency
 */
function format_currency($amount) {
    return 'KSh ' . number_format($amount, 2);
}

/**
 * Format date
 */
function format_date($date) {
    return date('d M Y', strtotime($date));
}

/**
 * Format datetime
 */
function format_datetime($datetime) {
    return date('d M Y H:i', strtotime($datetime));
}

/**
 * Handle image upload
 */
function upload_image($file) {
    $upload_dir = UPLOAD_DIR;
    
    // Create upload directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Check if file was uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error occurred'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds maximum limit of 5MB'];
    }
    
    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check file extension
    if (!in_array($file_extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed'];
    }
    
    // Generate unique filename
    $new_filename = 'order_' . uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . $new_filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $new_filename, 'path' => 'uploads/' . $new_filename];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}

/**
 * Delete image file
 */
function delete_image($image_path) {
    if (!empty($image_path)) {
        $full_path = __DIR__ . '/' . $image_path;
        if (file_exists($full_path)) {
            return unlink($full_path);
        }
    }
    return false;
}

/**
 * Get total sales for a date range
 */
function get_total_sales($conn, $start_date = null, $end_date = null) {
    $sql = "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE 1=1";
    
    if ($start_date) {
        $sql .= " AND order_date >= '" . $conn->real_escape_string($start_date) . "'";
    }
    if ($end_date) {
        $sql .= " AND order_date <= '" . $conn->real_escape_string($end_date) . "'";
    }
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

/**
 * Get total expenses for a date range
 */
function get_total_expenses($conn, $start_date = null, $end_date = null) {
    $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE 1=1";
    
    if ($start_date) {
        $sql .= " AND expense_date >= '" . $conn->real_escape_string($start_date) . "'";
    }
    if ($end_date) {
        $sql .= " AND expense_date <= '" . $conn->real_escape_string($end_date) . "'";
    }
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

/**
 * Calculate profit/loss
 */
function calculate_profit_loss($sales, $expenses) {
    return $sales - $expenses;
}

/**
 * Get order count
 */
function get_order_count($conn, $start_date = null, $end_date = null) {
    $sql = "SELECT COUNT(*) as count FROM orders WHERE 1=1";
    
    if ($start_date) {
        $sql .= " AND order_date >= '" . $conn->real_escape_string($start_date) . "'";
    }
    if ($end_date) {
        $sql .= " AND order_date <= '" . $conn->real_escape_string($end_date) . "'";
    }
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

/**
 * Display success message
 */
function show_success($message) {
    return '<div class="alert alert-success">' . sanitize_input($message) . '</div>';
}

/**
 * Display error message
 */
function show_error($message) {
    return '<div class="alert alert-error">' . sanitize_input($message) . '</div>';
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Pagination helper
 */
function paginate($total_records, $records_per_page, $current_page) {
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;
    
    return [
        'total_pages' => $total_pages,
        'offset' => $offset,
        'current_page' => $current_page
    ];
}
?>
