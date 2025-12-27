<?php
/**
 * Restaurant Management System
 * Add Expense
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

$success = '';
$error = '';

// Expense categories
$categories = ['Rent', 'Salaries', 'Ingredients', 'Utilities', 'Maintenance', 'Marketing', 'Supplies', 'Other'];

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
        // Insert expense
        $sql = "INSERT INTO expenses (category, description, amount, expense_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssds", $category, $description, $amount, $expense_date);
        
        if ($stmt->execute()) {
            $success = 'Expense added successfully!';
            // Clear form
            $category = '';
            $description = '';
            $amount = 0;
            $expense_date = date('Y-m-d');
        } else {
            $error = 'Failed to add expense: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

// Set default values
if (!isset($category)) $category = '';
if (!isset($description)) $description = '';
if (!isset($amount)) $amount = 0;
if (!isset($expense_date)) $expense_date = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>➕ Add Expense</h1>
                <p>Record a new expense</p>
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
                                <option value="<?php echo $cat; ?>" <?php echo $category == $cat ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" 
                                  placeholder="Enter expense description (optional)" 
                                  rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount" class="required">Amount (KSh)</label>
                        <input type="number" id="amount" name="amount" class="form-control" 
                               min="0" step="0.01" required
                               placeholder="Enter amount"
                               value="<?php echo $amount; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="expense_date" class="required">Expense Date</label>
                        <input type="date" id="expense_date" name="expense_date" class="form-control" required
                               value="<?php echo $expense_date; ?>">
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">Add Expense</button>
                        <a href="view_expenses.php" class="btn btn-secondary">View All Expenses</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
