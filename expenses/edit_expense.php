<?php
/**
 * Restaurant Management System
 * Edit Expense
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

$success = '';
$error = '';

// Expense categories
$categories = ['Rent', 'Salaries', 'Ingredients', 'Utilities', 'Maintenance', 'Marketing', 'Supplies', 'Other'];

// Get expense ID
$expense_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($expense_id <= 0) {
    header('Location: view_expenses.php');
    exit();
}

// Fetch expense details
$sql = "SELECT * FROM expenses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expense_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: view_expenses.php');
    exit();
}

$expense = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = sanitize_input($_POST['category']);
    $description = sanitize_input($_POST['description']);
    $amount = floatval($_POST['amount']);
    $expense_date = sanitize_input($_POST['expense_date']);
    
    // Validate inputs
    if (empty($category)) {
        $error = 'Category is required';
    } elseif ($amount <= 0) {
        $error = 'Amount must be greater than 0';
    } elseif (empty($expense_date)) {
        $error = 'Expense date is required';
    } else {
        // Update expense
        $sql = "UPDATE expenses SET category = ?, description = ?, amount = ?, expense_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi", $category, $description, $amount, $expense_date, $expense_id);
        
        if ($stmt->execute()) {
            $success = 'Expense updated successfully!';
            // Refresh expense data
            $expense['category'] = $category;
            $expense['description'] = $description;
            $expense['amount'] = $amount;
            $expense['expense_date'] = $expense_date;
        } else {
            $error = 'Failed to update expense: ' . $conn->error;
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/forms-custom.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>✏️ Edit Expense #<?php echo $expense_id; ?></h1>
                <p>Update expense details</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3>Expense Details</h3>
                </div>
                
                <form method="POST" action="" onsubmit="return validateExpenseForm()">
                    <div class="form-group">
                        <label for="category" class="required">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo $expense['category'] == $cat ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" 
                                  placeholder="Enter expense description (optional)" 
                                  rows="4"><?php echo htmlspecialchars($expense['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount" class="required">Amount ($)</label>
                        <input type="number" id="amount" name="amount" class="form-control" 
                               min="0" step="0.01" required
                               value="<?php echo $expense['amount']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="expense_date" class="required">Expense Date</label>
                        <input type="date" id="expense_date" name="expense_date" class="form-control" required
                               value="<?php echo $expense['expense_date']; ?>">
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">Update Expense</button>
                        <a href="view_expenses.php" class="btn btn-secondary">Back to Expenses</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
