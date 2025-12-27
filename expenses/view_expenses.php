<?php
/**
 * Restaurant Management System
 * View All Expenses
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

// Pagination settings
$records_per_page = 20;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Filter functionality
$category = isset($_GET['category']) ? sanitize_input($_GET['category']) : '';
$start_date = isset($_GET['start_date']) ? sanitize_input($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? sanitize_input($_GET['end_date']) : '';

// Build query
$where_conditions = [];
$params = [];
$types = '';

if (!empty($category)) {
    $where_conditions[] = "category = ?";
    $params[] = $category;
    $types .= 's';
}

if (!empty($start_date)) {
    $where_conditions[] = "expense_date >= ?";
    $params[] = $start_date;
    $types .= 's';
}

if (!empty($end_date)) {
    $where_conditions[] = "expense_date <= ?";
    $params[] = $end_date;
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total records
$count_sql = "SELECT COUNT(*) as total FROM expenses $where_clause";
$count_stmt = $conn->prepare($count_sql);

if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

// Calculate pagination
$pagination = paginate($total_records, $records_per_page, $current_page);

// Get expenses
$sql = "SELECT * FROM expenses $where_clause ORDER BY expense_date DESC, created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

$params[] = $records_per_page;
$params[] = $pagination['offset'];
$types .= 'ii';

$stmt->bind_param($types, ...$params);
$stmt->execute();
$expenses = $stmt->get_result();
$stmt->close();

// Get total expenses for filtered results
$total_sql = "SELECT COALESCE(SUM(amount), 0) as total FROM expenses $where_clause";
$total_stmt = $conn->prepare($total_sql);

if (!empty($where_conditions)) {
    // Remove the last two parameters (limit and offset)
    $filter_params = array_slice($params, 0, -2);
    $filter_types = substr($types, 0, -2);
    $total_stmt->bind_param($filter_types, ...$filter_params);
}

$total_stmt->execute();
$total_expenses_amount = $total_stmt->get_result()->fetch_assoc()['total'];
$total_stmt->close();

// Expense categories
$categories = ['Rent', 'Salaries', 'Ingredients', 'Utilities', 'Maintenance', 'Marketing', 'Supplies', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Expenses - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>💸 All Expenses</h1>
                <p>View and manage expenses</p>
            </div>
            
            <!-- Filter Form -->
            <div class="card">
                <form method="GET" action="">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                        <div class="form-group" style="margin: 0;">
                            <label for="category">Category</label>
                            <select id="category" name="category" class="form-control">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php echo $category == $cat ? 'selected' : ''; ?>>
                                        <?php echo $cat; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin: 0;">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control"
                                   value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        
                        <div class="form-group" style="margin: 0;">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control"
                                   value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        
                        <div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="view_expenses.php" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Total Expenses -->
            <div class="stats-grid">
                <div class="stat-card expenses">
                    <h4>Total Expenses (Filtered)</h4>
                    <div class="stat-value"><?php echo format_currency($total_expenses_amount); ?></div>
                    <div class="stat-icon">💸</div>
                </div>
                
                <div class="stat-card">
                    <h4>Number of Expenses</h4>
                    <div class="stat-value"><?php echo $total_records; ?></div>
                    <div class="stat-icon">📋</div>
                </div>
            </div>
            
            <!-- Expenses Table -->
            <div class="card">
                <div class="card-header">
                    <h3>Expenses List</h3>
                </div>
                
                <div class="table-responsive">
                    <table id="dataTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Expense Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($expenses->num_rows > 0): ?>
                                <?php while ($expense = $expenses->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $expense['id']; ?></td>
                                        <td><span style="background: var(--light-orange); padding: 5px 10px; border-radius: 5px;">
                                            <?php echo htmlspecialchars($expense['category']); ?>
                                        </span></td>
                                        <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                        <td><strong><?php echo format_currency($expense['amount']); ?></strong></td>
                                        <td><?php echo format_date($expense['expense_date']); ?></td>
                                        <td>
                                            <a href="edit_expense.php?id=<?php echo $expense['id']; ?>" 
                                               class="btn btn-sm btn-warning">Edit</a>
                                            <a href="delete_expense.php?id=<?php echo $expense['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirmDelete('expense', <?php echo $expense['id']; ?>)">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No expenses found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($current_page > 1): ?>
                            <a href="?page=<?php echo $current_page - 1; ?>&category=<?php echo urlencode($category); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>">
                                ← Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if ($i == $current_page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>&category=<?php echo urlencode($category); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $pagination['total_pages']): ?>
                            <a href="?page=<?php echo $current_page + 1; ?>&category=<?php echo urlencode($category); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>">
                                Next →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-20">
                    <a href="add_expense.php" class="btn btn-primary">Add New Expense</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
