<?php
/**
 * Restaurant Management System
 * Dashboard - Main Overview Page
 */

require_once 'config.php';
require_once 'functions.php';
require_login();

// Get today's date
$today = date('Y-m-d');
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');

// Get statistics
$total_orders = get_order_count($conn);
$today_orders = get_order_count($conn, $today, $today);
$month_orders = get_order_count($conn, $current_month_start, $current_month_end);

$total_sales = get_total_sales($conn);
$today_sales = get_total_sales($conn, $today, $today);
$month_sales = get_total_sales($conn, $current_month_start, $current_month_end);

$total_expenses = get_total_expenses($conn);
$today_expenses = get_total_expenses($conn, $today, $today);
$month_expenses = get_total_expenses($conn, $current_month_start, $current_month_end);

$total_profit = calculate_profit_loss($total_sales, $total_expenses);
$today_profit = calculate_profit_loss($today_sales, $today_expenses);
$month_profit = calculate_profit_loss($month_sales, $month_expenses);

// Get recent orders
$recent_orders_sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 10";
$recent_orders = $conn->query($recent_orders_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Restaurant Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>📊 Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
            </div>
            
            <!-- Today's Statistics -->
            <h3>Today's Overview</h3>
            <div class="stats-grid">
                <div class="stat-card orders">
                    <h4>Today's Orders</h4>
                    <div class="stat-value"><?php echo $today_orders; ?></div>
                    <div class="stat-icon">📦</div>
                </div>
                
                <div class="stat-card sales">
                    <h4>Today's Sales</h4>
                    <div class="stat-value"><?php echo format_currency($today_sales); ?></div>
                    <div class="stat-icon">💰</div>
                </div>
                
                <div class="stat-card expenses">
                    <h4>Today's Expenses</h4>
                    <div class="stat-value"><?php echo format_currency($today_expenses); ?></div>
                    <div class="stat-icon">💸</div>
                </div>
                
                <div class="stat-card profit">
                    <h4>Today's Profit/Loss</h4>
                    <div class="stat-value <?php echo $today_profit >= 0 ? 'text-success' : 'text-error'; ?>">
                        <?php echo format_currency($today_profit); ?>
                    </div>
                    <div class="stat-icon"><?php echo $today_profit >= 0 ? '📈' : '📉'; ?></div>
                </div>
            </div>
            
            <!-- Monthly Statistics -->
            <h3>This Month's Overview</h3>
            <div class="stats-grid">
                <div class="stat-card orders">
                    <h4>Monthly Orders</h4>
                    <div class="stat-value"><?php echo $month_orders; ?></div>
                    <div class="stat-icon">📦</div>
                </div>
                
                <div class="stat-card sales">
                    <h4>Monthly Sales</h4>
                    <div class="stat-value"><?php echo format_currency($month_sales); ?></div>
                    <div class="stat-icon">💰</div>
                </div>
                
                <div class="stat-card expenses">
                    <h4>Monthly Expenses</h4>
                    <div class="stat-value"><?php echo format_currency($month_expenses); ?></div>
                    <div class="stat-icon">💸</div>
                </div>
                
                <div class="stat-card profit">
                    <h4>Monthly Profit/Loss</h4>
                    <div class="stat-value <?php echo $month_profit >= 0 ? 'text-success' : 'text-error'; ?>">
                        <?php echo format_currency($month_profit); ?>
                    </div>
                    <div class="stat-icon"><?php echo $month_profit >= 0 ? '📈' : '📉'; ?></div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <h3>Recent Orders</h3>
                </div>
                
                <div class="table-responsive">
                    <table id="dataTable">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Food Name</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_orders->num_rows > 0): ?>
                                <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php if ($order['image_path']): ?>
                                                <img src="<?php echo htmlspecialchars($order['image_path']); ?>" 
                                                     alt="Order Image" class="table-image">
                                            <?php else: ?>
                                                <span>No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['food_name']); ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td><?php echo format_currency($order['total_amount']); ?></td>
                                        <td><?php echo format_date($order['order_date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No orders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-20">
                    <a href="orders/view_orders.php" class="btn btn-primary">View All Orders</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
