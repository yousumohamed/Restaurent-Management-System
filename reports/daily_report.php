<?php
/**
 * Restaurant Management System
 * Daily Report
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

// Get selected date or default to today
$selected_date = isset($_GET['date']) ? sanitize_input($_GET['date']) : date('Y-m-d');

// Get data for the day
$total_sales = get_total_sales($conn, $selected_date, $selected_date);
$total_expenses = get_total_expenses($conn, $selected_date, $selected_date);
$profit_loss = calculate_profit_loss($total_sales, $total_expenses);
$order_count = get_order_count($conn, $selected_date, $selected_date);

// Get orders for the day
$orders_sql = "SELECT * FROM orders WHERE order_date = ? ORDER BY created_at DESC";
$orders_stmt = $conn->prepare($orders_sql);
$orders_stmt->bind_param("s", $selected_date);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();
$orders_stmt->close();

// Get expenses for the day
$expenses_sql = "SELECT * FROM expenses WHERE expense_date = ? ORDER BY created_at DESC";
$expenses_stmt = $conn->prepare($expenses_sql);
$expenses_stmt->bind_param("s", $selected_date);
$expenses_stmt->execute();
$expenses = $expenses_stmt->get_result();
$expenses_stmt->close();

// Get top selling items
$top_items_sql = "SELECT food_name, SUM(quantity) as total_quantity, SUM(total_amount) as total_sales
                  FROM orders 
                  WHERE order_date = ?
                  GROUP BY food_name
                  ORDER BY total_sales DESC
                  LIMIT 5";
$top_stmt = $conn->prepare($top_items_sql);
$top_stmt->bind_param("s", $selected_date);
$top_stmt->execute();
$top_items_result = $top_stmt->get_result();

// Fetch all rows into array for multiple uses (Chart + Table)
$top_items_rows = [];
$chart_labels = [];
$chart_data = [];

while($row = $top_items_result->fetch_assoc()) {
    $top_items_rows[] = $row;
    $chart_labels[] = $row['food_name'];
    $chart_data[] = $row['total_quantity'];
}
$top_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/forms-custom.css">
    <style>
        @media print {
            .sidebar, .no-print { display: none; }
            .main-content { margin-left: 0; }
            .page-header { background: white; color: var(--dark-gray); }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>📄 Daily Report</h1>
                <p>Comprehensive daily business report</p>
            </div>
            
            <!-- Date Selector -->
            <div class="card no-print">
                <form method="GET" action="">
                    <div style="display: flex; gap: 15px; align-items: end;">
                        <div class="form-group" style="margin: 0; flex: 1;">
                            <label for="date">Select Date</label>
                            <input type="date" id="date" name="date" class="form-control" 
                                   value="<?php echo $selected_date; ?>">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                            <button type="button" onclick="printReport()" class="btn btn-success">Print Report</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Report Header -->
            <div class="card">
                <div style="text-align: center; padding: 20px;">
                    <h2 style="color: var(--primary-red); margin: 0;">🍽️ Restaurant Management System</h2>
                    <h3 style="margin: 10px 0;">Daily Business Report</h3>
                    <p style="font-size: 1.1rem; color: var(--medium-gray);">
                        Date: <strong><?php echo format_date($selected_date); ?></strong>
                    </p>
                    <p style="color: var(--medium-gray);">
                        Generated on: <?php echo date('d M Y H:i'); ?>
                    </p>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <!-- Top Items Chart -->
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <h3>Top Items Sales (Qty)</h3>
                    </div>
                    <div style="height: 250px; padding: 10px;">
                        <canvas id="itemsChart"></canvas>
                    </div>
                </div>
                
                <!-- Summary Donut Chart -->
                <div class="card" style="margin-bottom: 0;">
                    <div class="card-header">
                        <h3>Income vs Expense</h3>
                    </div>
                    <div style="height: 250px; padding: 10px;">
                        <canvas id="summaryChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Summary Statistics -->
            <div class="card">
                <div class="card-header">
                    <h3>Summary</h3>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card orders">
                        <h4>Total Orders</h4>
                        <div class="stat-value"><?php echo $order_count; ?></div>
                    </div>
                    
                    <div class="stat-card sales">
                        <h4>Total Sales</h4>
                        <div class="stat-value"><?php echo format_currency($total_sales); ?></div>
                    </div>
                    
                    <div class="stat-card expenses">
                        <h4>Total Expenses</h4>
                        <div class="stat-value"><?php echo format_currency($total_expenses); ?></div>
                    </div>
                    
                    <div class="stat-card <?php echo $profit_loss >= 0 ? 'profit' : 'expenses'; ?>">
                        <h4><?php echo $profit_loss >= 0 ? 'Net Profit' : 'Net Loss'; ?></h4>
                        <div class="stat-value <?php echo $profit_loss >= 0 ? 'text-success' : 'text-error'; ?>">
                            <?php echo format_currency(abs($profit_loss)); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Selling Items -->
            <div class="card">
                <div class="card-header">
                    <h3>Top Selling Items</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Food Name</th>
                                <th>Quantity Sold</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($top_items_rows) > 0): ?>
                                <?php $rank = 1; ?>
                                <?php foreach ($top_items_rows as $item): ?>
                                    <tr>
                                        <td><strong>#<?php echo $rank++; ?></strong></td>
                                        <td><?php echo htmlspecialchars($item['food_name']); ?></td>
                                        <td><?php echo $item['total_quantity']; ?></td>
                                        <td><?php echo format_currency($item['total_sales']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No sales data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Orders Details -->
            <div class="card">
                <div class="card-header">
                    <h3>Orders Details</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Food Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders->num_rows > 0): ?>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['food_name']); ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td><?php echo format_currency($order['price']); ?></td>
                                        <td><?php echo format_currency($order['total_amount']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No orders for this date</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Expenses Details -->
            <div class="card">
                <div class="card-header">
                    <h3>Expenses Details</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($expenses->num_rows > 0): ?>
                                <?php while ($expense = $expenses->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $expense['id']; ?></td>
                                        <td><?php echo htmlspecialchars($expense['category']); ?></td>
                                        <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                        <td><?php echo format_currency($expense['amount']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No expenses for this date</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Report Footer -->
            <div class="card">
                <div style="text-align: center; padding: 20px; border-top: 2px solid var(--primary-orange);">
                    <p style="color: var(--medium-gray); margin: 0;">
                        This report was automatically generated by Restaurant Management System
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data from PHP
            const itemLabels = <?php echo json_encode($chart_labels); ?>;
            const itemData = <?php echo json_encode($chart_data); ?>;
            
            const sales = <?php echo max(0, $total_sales); ?>;
            const expenses = <?php echo max(0, $total_expenses); ?>;
            const profit = Math.max(0, sales - expenses);

            // Top Items Chart
            const itemsCtx = document.getElementById('itemsChart');
            if (itemsCtx) {
                new Chart(itemsCtx, {
                    type: 'bar',
                    data: {
                        labels: itemLabels,
                        datasets: [{
                            label: 'Units Sold',
                            data: itemData,
                            backgroundColor: '#5B6CE8',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true } }
                    }
                });
            }

            // Summary Donut Chart
            const summaryCtx = document.getElementById('summaryChart');
            if (summaryCtx) {
                new Chart(summaryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Profit', 'Expenses'],
                        datasets: [{
                            data: [profit, expenses],
                            backgroundColor: ['#48BB78', '#F56565'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>