<?php
/**
 * Restaurant Management System
 * Monthly Report
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

// Get selected month/year or default to current month
$selected_month = isset($_GET['month']) ? sanitize_input($_GET['month']) : date('Y-m');
$start_date = $selected_month . '-01';
$end_date = date('Y-m-t', strtotime($start_date));

// Get monthly totals
$total_sales = get_total_sales($conn, $start_date, $end_date);
$total_expenses = get_total_expenses($conn, $start_date, $end_date);
$profit_loss = calculate_profit_loss($total_sales, $total_expenses);
$order_count = get_order_count($conn, $start_date, $end_date);

// Get daily breakdown
$daily_sql = "SELECT 
                dates.date,
                COALESCE(sales.total_sales, 0) as sales,
                COALESCE(expenses.total_expenses, 0) as expenses,
                (COALESCE(sales.total_sales, 0) - COALESCE(expenses.total_expenses, 0)) as profit_loss
              FROM (
                SELECT DISTINCT order_date as date FROM orders WHERE order_date BETWEEN ? AND ?
                UNION
                SELECT DISTINCT expense_date as date FROM expenses WHERE expense_date BETWEEN ? AND ?
              ) as dates
              LEFT JOIN (
                SELECT order_date as date, SUM(total_amount) as total_sales 
                FROM orders 
                WHERE order_date BETWEEN ? AND ?
                GROUP BY order_date
              ) as sales ON dates.date = sales.date
              LEFT JOIN (
                SELECT expense_date as date, SUM(amount) as total_expenses 
                FROM expenses 
                WHERE expense_date BETWEEN ? AND ?
                GROUP BY expense_date
              ) as expenses ON dates.date = expenses.date
              ORDER BY dates.date ASC";

$daily_stmt = $conn->prepare($daily_sql);
$daily_stmt->bind_param("ssssssss", $start_date, $end_date, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date);
$daily_stmt->execute();
$daily_data = $daily_stmt->get_result();
$daily_stmt->close();

// Get top selling items for the month
$top_items_sql = "SELECT food_name, SUM(quantity) as total_quantity, SUM(total_amount) as total_sales, COUNT(*) as order_count
                  FROM orders 
                  WHERE order_date BETWEEN ? AND ?
                  GROUP BY food_name
                  ORDER BY total_sales DESC
                  LIMIT 10";
$top_stmt = $conn->prepare($top_items_sql);
$top_stmt->bind_param("ss", $start_date, $end_date);
$top_stmt->execute();
$top_items = $top_stmt->get_result();
$top_stmt->close();

// Get expenses by category
$category_sql = "SELECT category, SUM(amount) as total_amount, COUNT(*) as count
                 FROM expenses 
                 WHERE expense_date BETWEEN ? AND ?
                 GROUP BY category
                 ORDER BY total_amount DESC";
$category_stmt = $conn->prepare($category_sql);
$category_stmt->bind_param("ss", $start_date, $end_date);
$category_stmt->execute();
$expense_categories = $category_stmt->get_result();
$category_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                <h1>📅 Monthly Report</h1>
                <p>Comprehensive monthly business report</p>
            </div>
            
            <!-- Month Selector -->
            <div class="card no-print">
                <form method="GET" action="">
                    <div style="display: flex; gap: 15px; align-items: end;">
                        <div class="form-group" style="margin: 0; flex: 1;">
                            <label for="month">Select Month</label>
                            <input type="month" id="month" name="month" class="form-control" 
                                   value="<?php echo $selected_month; ?>">
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
                    <h3 style="margin: 10px 0;">Monthly Business Report</h3>
                    <p style="font-size: 1.1rem; color: var(--medium-gray);">
                        Period: <strong><?php echo date('F Y', strtotime($start_date)); ?></strong>
                    </p>
                    <p style="color: var(--medium-gray);">
                        From <?php echo format_date($start_date); ?> to <?php echo format_date($end_date); ?>
                    </p>
                    <p style="color: var(--medium-gray);">
                        Generated on: <?php echo date('d M Y H:i'); ?>
                    </p>
                </div>
            </div>
            
            <!-- Summary Statistics -->
            <div class="card">
                <div class="card-header">
                    <h3>Monthly Summary</h3>
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
                
                <div style="margin-top: 20px; padding: 20px; background: var(--cream); border-radius: var(--radius-md);">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center;">
                        <div>
                            <p style="color: var(--medium-gray); margin: 0;">Average Daily Sales</p>
                            <p style="font-size: 1.5rem; font-weight: 600; color: var(--primary-red); margin: 5px 0;">
                                <?php 
                                $days_in_month = date('t', strtotime($start_date));
                                echo format_currency($total_sales / $days_in_month);
                                ?>
                            </p>
                        </div>
                        <div>
                            <p style="color: var(--medium-gray); margin: 0;">Average Order Value</p>
                            <p style="font-size: 1.5rem; font-weight: 600; color: var(--primary-orange); margin: 5px 0;">
                                <?php echo format_currency($order_count > 0 ? $total_sales / $order_count : 0); ?>
                            </p>
                        </div>
                        <div>
                            <p style="color: var(--medium-gray); margin: 0;">Profit Margin</p>
                            <p style="font-size: 1.5rem; font-weight: 600; color: <?php echo $profit_loss >= 0 ? 'var(--success)' : 'var(--error)'; ?>; margin: 5px 0;">
                                <?php 
                                $margin = $total_sales > 0 ? ($profit_loss / $total_sales) * 100 : 0;
                                echo number_format($margin, 1) . '%';
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Selling Items -->
            <div class="card">
                <div class="card-header">
                    <h3>Top 10 Selling Items</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Food Name</th>
                                <th>Orders</th>
                                <th>Quantity Sold</th>
                                <th>Total Sales</th>
                                <th>% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($top_items->num_rows > 0): ?>
                                <?php $rank = 1; ?>
                                <?php while ($item = $top_items->fetch_assoc()): ?>
                                    <?php $percentage = $total_sales > 0 ? ($item['total_sales'] / $total_sales) * 100 : 0; ?>
                                    <tr>
                                        <td><strong>#<?php echo $rank++; ?></strong></td>
                                        <td><?php echo htmlspecialchars($item['food_name']); ?></td>
                                        <td><?php echo $item['order_count']; ?></td>
                                        <td><?php echo $item['total_quantity']; ?></td>
                                        <td><?php echo format_currency($item['total_sales']); ?></td>
                                        <td><?php echo number_format($percentage, 1); ?>%</td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No sales data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Expenses by Category -->
            <div class="card">
                <div class="card-header">
                    <h3>Expenses by Category</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Number of Expenses</th>
                                <th>Total Amount</th>
                                <th>% of Total Expenses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($expense_categories->num_rows > 0): ?>
                                <?php while ($category = $expense_categories->fetch_assoc()): ?>
                                    <?php $percentage = $total_expenses > 0 ? ($category['total_amount'] / $total_expenses) * 100 : 0; ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($category['category']); ?></strong></td>
                                        <td><?php echo $category['count']; ?></td>
                                        <td><?php echo format_currency($category['total_amount']); ?></td>
                                        <td><?php echo number_format($percentage, 1); ?>%</td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No expenses data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Daily Breakdown -->
            <div class="card">
                <div class="card-header">
                    <h3>Daily Breakdown</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sales</th>
                                <th>Expenses</th>
                                <th>Profit/Loss</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($daily_data->num_rows > 0): ?>
                                <?php while ($day = $daily_data->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo format_date($day['date']); ?></td>
                                        <td><?php echo format_currency($day['sales']); ?></td>
                                        <td><?php echo format_currency($day['expenses']); ?></td>
                                        <td class="<?php echo $day['profit_loss'] >= 0 ? 'text-success' : 'text-error'; ?>">
                                            <?php echo format_currency($day['profit_loss']); ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No data available</td>
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
</body>
</html>
