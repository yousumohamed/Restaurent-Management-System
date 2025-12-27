<?php
/**
 * Restaurant Management System
 * Daily Sales Summary
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

// Get selected date or default to today
$selected_date = isset($_GET['date']) ? sanitize_input($_GET['date']) : date('Y-m-d');

// Get sales data for selected date
$total_sales = get_total_sales($conn, $selected_date, $selected_date);
$order_count = get_order_count($conn, $selected_date, $selected_date);
$average_order = $order_count > 0 ? $total_sales / $order_count : 0;

// Get orders breakdown for the day
$sql = "SELECT food_name, SUM(quantity) as total_quantity, SUM(total_amount) as total_sales, COUNT(*) as order_count
        FROM orders 
        WHERE order_date = ?
        GROUP BY food_name
        ORDER BY total_sales DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$breakdown = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/forms-custom.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>💰 Daily Sales</h1>
                <p>View sales summary and breakdown</p>
            </div>
            
            <!-- Date Selector -->
            <div class="card">
                <form method="GET" action="">
                    <div style="display: flex; gap: 15px; align-items: end;">
                        <div class="form-group" style="margin: 0; flex: 1;">
                            <label for="date">Select Date</label>
                            <input type="date" id="date" name="date" class="form-control" 
                                   value="<?php echo $selected_date; ?>">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">View Sales</button>
                            <a href="daily_sales.php" class="btn btn-secondary">Today</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Sales Summary -->
            <h3>Sales Summary for <?php echo format_date($selected_date); ?></h3>
            <div class="stats-grid">
                <div class="stat-card sales">
                    <h4>Total Sales</h4>
                    <div class="stat-value"><?php echo format_currency($total_sales); ?></div>
                    <div class="stat-icon">💰</div>
                </div>
                
                <div class="stat-card orders">
                    <h4>Total Orders</h4>
                    <div class="stat-value"><?php echo $order_count; ?></div>
                    <div class="stat-icon">📦</div>
                </div>
                
                <div class="stat-card profit">
                    <h4>Average Order Value</h4>
                    <div class="stat-value"><?php echo format_currency($average_order); ?></div>
                    <div class="stat-icon">📊</div>
                </div>
            </div>
            
            <!-- Sales Breakdown -->
            <div class="card">
                <div class="card-header">
                    <h3>Sales Breakdown by Food Item</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Food Name</th>
                                <th>Orders Count</th>
                                <th>Total Quantity Sold</th>
                                <th>Total Sales</th>
                                <th>% of Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($breakdown->num_rows > 0): ?>
                                <?php while ($item = $breakdown->fetch_assoc()): ?>
                                    <?php 
                                    $percentage = $total_sales > 0 ? ($item['total_sales'] / $total_sales) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($item['food_name']); ?></strong></td>
                                        <td><?php echo $item['order_count']; ?></td>
                                        <td><?php echo $item['total_quantity']; ?></td>
                                        <td><?php echo format_currency($item['total_sales']); ?></td>
                                        <td><?php echo number_format($percentage, 1); ?>%</td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No sales data for this date</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if ($total_sales > 0): ?>
                            <tfoot style="background: var(--cream); font-weight: 600;">
                                <tr>
                                    <td colspan="3" class="text-right">TOTAL:</td>
                                    <td><?php echo format_currency($total_sales); ?></td>
                                    <td>100%</td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
