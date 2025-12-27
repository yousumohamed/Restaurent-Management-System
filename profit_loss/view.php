<?php
/**
 * Restaurant Management System
 * Profit & Loss View
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

// Get date range or default to current month
$start_date = isset($_GET['start_date']) ? sanitize_input($_GET['start_date']) : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? sanitize_input($_GET['end_date']) : date('Y-m-t');

// Get totals
$total_sales = get_total_sales($conn, $start_date, $end_date);
$total_expenses = get_total_expenses($conn, $start_date, $end_date);
$profit_loss = calculate_profit_loss($total_sales, $total_expenses);
$is_profit = $profit_loss >= 0;

// Get daily breakdown
$sql = "SELECT 
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
        ORDER BY dates.date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $start_date, $end_date, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date);
$stmt->execute();
$daily_data = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>📈 Profit & Loss</h1>
                <p>View profit and loss analysis</p>
            </div>
            
            <!-- Date Range Selector -->
            <div class="card">
                <form method="GET" action="">
                    <div style="display: flex; gap: 15px; align-items: end;">
                        <div class="form-group" style="margin: 0; flex: 1;">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" 
                                   value="<?php echo $start_date; ?>">
                        </div>
                        
                        <div class="form-group" style="margin: 0; flex: 1;">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" 
                                   value="<?php echo $end_date; ?>">
                        </div>
                        
                        <div>
                            <button type="submit" class="btn btn-primary">View Report</button>
                            <a href="view.php" class="btn btn-secondary">This Month</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Summary -->
            <h3>Summary: <?php echo format_date($start_date); ?> to <?php echo format_date($end_date); ?></h3>
            <div class="stats-grid">
                <div class="stat-card sales">
                    <h4>Total Sales</h4>
                    <div class="stat-value"><?php echo format_currency($total_sales); ?></div>
                    <div class="stat-icon">💰</div>
                </div>
                
                <div class="stat-card expenses">
                    <h4>Total Expenses</h4>
                    <div class="stat-value"><?php echo format_currency($total_expenses); ?></div>
                    <div class="stat-icon">💸</div>
                </div>
                
                <div class="stat-card <?php echo $is_profit ? 'profit' : 'expenses'; ?>">
                    <h4><?php echo $is_profit ? 'Net Profit' : 'Net Loss'; ?></h4>
                    <div class="stat-value <?php echo $is_profit ? 'text-success' : 'text-error'; ?>">
                        <?php echo format_currency(abs($profit_loss)); ?>
                    </div>
                    <div class="stat-icon"><?php echo $is_profit ? '📈' : '📉'; ?></div>
                </div>
                
                <div class="stat-card">
                    <h4>Profit Margin</h4>
                    <div class="stat-value <?php echo $is_profit ? 'text-success' : 'text-error'; ?>">
                        <?php 
                        $margin = $total_sales > 0 ? ($profit_loss / $total_sales) * 100 : 0;
                        echo number_format($margin, 1) . '%';
                        ?>
                    </div>
                    <div class="stat-icon">📊</div>
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
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($daily_data->num_rows > 0): ?>
                                <?php while ($day = $daily_data->fetch_assoc()): ?>
                                    <?php $day_is_profit = $day['profit_loss'] >= 0; ?>
                                    <tr>
                                        <td><strong><?php echo format_date($day['date']); ?></strong></td>
                                        <td><?php echo format_currency($day['sales']); ?></td>
                                        <td><?php echo format_currency($day['expenses']); ?></td>
                                        <td class="<?php echo $day_is_profit ? 'text-success' : 'text-error'; ?>">
                                            <strong><?php echo format_currency($day['profit_loss']); ?></strong>
                                        </td>
                                        <td>
                                            <span style="padding: 5px 15px; border-radius: 20px; font-weight: 600; 
                                                         background: <?php echo $day_is_profit ? 'var(--success)' : 'var(--error)'; ?>; 
                                                         color: white;">
                                                <?php echo $day_is_profit ? '✓ Profit' : '✗ Loss'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No data available for this date range</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if ($daily_data->num_rows > 0): ?>
                            <tfoot style="background: var(--cream); font-weight: 700; font-size: 1.1rem;">
                                <tr>
                                    <td>TOTAL:</td>
                                    <td><?php echo format_currency($total_sales); ?></td>
                                    <td><?php echo format_currency($total_expenses); ?></td>
                                    <td class="<?php echo $is_profit ? 'text-success' : 'text-error'; ?>">
                                        <?php echo format_currency($profit_loss); ?>
                                    </td>
                                    <td>
                                        <span style="padding: 5px 15px; border-radius: 20px; font-weight: 600; 
                                                     background: <?php echo $is_profit ? 'var(--success)' : 'var(--error)'; ?>; 
                                                     color: white;">
                                            <?php echo $is_profit ? '✓ Profit' : '✗ Loss'; ?>
                                        </span>
                                    </td>
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
