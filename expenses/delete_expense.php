<?php
/**
 * Restaurant Management System
 * Delete Expense
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

// Get expense ID
$expense_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($expense_id <= 0) {
    header('Location: view_expenses.php');
    exit();
}

// Delete expense from database
$sql = "DELETE FROM expenses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expense_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = 'Expense deleted successfully!';
} else {
    $_SESSION['error_message'] = 'Failed to delete expense';
}

$stmt->close();

// Redirect back to expenses page
header('Location: view_expenses.php');
exit();
?>
