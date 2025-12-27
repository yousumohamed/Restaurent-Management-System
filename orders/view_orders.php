<?php
/**
 * Restaurant Management System
 * View All Orders with Search and Pagination
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

// Pagination settings
$records_per_page = 20;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Search functionality
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? sanitize_input($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? sanitize_input($_GET['end_date']) : '';

// Build query
$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(food_name LIKE ? OR customer_name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

if (!empty($start_date)) {
    $where_conditions[] = "order_date >= ?";
    $params[] = $start_date;
    $types .= 's';
}

if (!empty($end_date)) {
    $where_conditions[] = "order_date <= ?";
    $params[] = $end_date;
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total records
$count_sql = "SELECT COUNT(*) as total FROM orders $where_clause";
$count_stmt = $conn->prepare($count_sql);

if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

// Calculate pagination
$pagination = paginate($total_records, $records_per_page, $current_page);

// Get orders
$sql = "SELECT * FROM orders $where_clause ORDER BY order_date DESC, created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

$params[] = $records_per_page;
$params[] = $pagination['offset'];
$types .= 'ii';

$stmt->bind_param($types, ...$params);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>📦 All Orders</h1>
                <p>View and manage all orders</p>
            </div>
            
            <!-- Search and Filter -->
            <div class="card">
                <form method="GET" action="">
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 15px; align-items: end;">
                        <div class="form-group" style="margin: 0;">
                            <label for="search">Search by Food or Customer Name</label>
                            <input type="text" id="search" name="search" class="form-control" 
                                   placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
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
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="view_orders.php" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h3>Orders List (<?php echo $total_records; ?> total)</h3>
                </div>
                
                <div class="table-responsive">
                    <table id="dataTable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Food Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders->num_rows > 0): ?>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php if ($order['image_path']): ?>
                                                <img src="../<?php echo htmlspecialchars($order['image_path']); ?>" 
                                                     alt="<?php echo htmlspecialchars($order['food_name']); ?>" 
                                                     class="table-image"
                                                     onclick="window.open('../<?php echo htmlspecialchars($order['image_path']); ?>', '_blank')"
                                                     style="cursor: pointer;">
                                            <?php else: ?>
                                                <span style="color: #8D99AE;">No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['food_name']); ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td><?php echo format_currency($order['price']); ?></td>
                                        <td><strong><?php echo format_currency($order['total_amount']); ?></strong></td>
                                        <td><?php echo format_date($order['order_date']); ?></td>
                                        <td>
                                            <a href="edit_order.php?id=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-warning">Edit</a>
                                            <a href="delete_order.php?id=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirmDelete('order', <?php echo $order['id']; ?>)">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No orders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($current_page > 1): ?>
                            <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>">
                                ← Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if ($i == $current_page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($current_page < $pagination['total_pages']): ?>
                            <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>">
                                Next →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-20">
                    <a href="add_order.php" class="btn btn-primary">Add New Order</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
