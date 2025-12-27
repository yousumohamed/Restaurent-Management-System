<?php
/**
 * Restaurant Management System
 * Delete Order
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

// Get order ID
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id <= 0) {
    header('Location: view_orders.php');
    exit();
}

// Fetch order to get image path
$sql = "SELECT image_path FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: view_orders.php');
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();

// Delete image file if exists
if (!empty($order['image_path'])) {
    delete_image($order['image_path']);
}

// Delete order from database
$sql = "DELETE FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = 'Order deleted successfully!';
} else {
    $_SESSION['error_message'] = 'Failed to delete order';
}

$stmt->close();

// Redirect back to orders page
header('Location: view_orders.php');
exit();
?>
