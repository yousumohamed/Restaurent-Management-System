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
$recent_orders_sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
$recent_orders = $conn->query($recent_orders_sql);

// Get trending menu items (top 3) - Simplified query
$trending_sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 3";
$trending = $conn->query($trending_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Restaurant Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard-modern.css">
    <link rel="stylesheet" href="assets/css/sidebar-active.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <!-- Top Header with Search and Profile -->
            <div class="top-header">
                <div class="search-box">
                    <form action="orders/view_orders.php" method="GET">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search orders, customers..." autocomplete="off">
                    </form>
                </div>
                <div class="header-actions">
                    <div class="user-profile">
                        <?php if (!empty($_SESSION['profile_image']) && file_exists($_SESSION['profile_image'])): ?>
                            <img src="<?php echo SITE_URL . '/' . $_SESSION['profile_image']; ?>" alt="Profile">
                        <?php else: ?>
                            <div class="avatar-small"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
                        <?php endif; ?>
                        <div class="user-info">
                            <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
                            <span>Manager</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <!-- Left Section -->
                <div class="dashboard-left">
                    <!-- Stats Cards -->
                    <div class="stats-row">
                        <a href="<?php echo SITE_URL; ?>/orders/view_orders.php" style="text-decoration: none;">
                            <div class="stat-card-modern">
                                <div class="stat-header">
                                    <span class="stat-label">Total Menu</span>
                                    <i class="fas fa-utensils stat-icon-small"></i>
                                </div>
                                <div class="stat-number"><?php echo $total_orders; ?></div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i> 12% vs Last Day
                                </div>
                            </div>
                        </a>
                        
                        <a href="<?php echo SITE_URL; ?>/orders/view_orders.php" style="text-decoration: none;">
                            <div class="stat-card-modern">
                                <div class="stat-header">
                                    <span class="stat-label">Total Order</span>
                                    <i class="fas fa-shopping-cart stat-icon-small"></i>
                                </div>
                                <div class="stat-number"><?php echo $month_orders; ?></div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i> 15% vs Last Day
                                </div>
                            </div>
                        </a>
                        
                        <a href="<?php echo SITE_URL; ?>/orders/view_orders.php" style="text-decoration: none;">
                            <div class="stat-card-modern">
                                <div class="stat-header">
                                    <span class="stat-label">Total Customer</span>
                                    <i class="fas fa-users stat-icon-small"></i>
                                </div>
                                <div class="stat-number"><?php echo $month_orders; ?></div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i> 12% vs Last Day
                                </div>
                            </div>
                        </a>
                        
                        <a href="<?php echo SITE_URL; ?>/sales/daily_sales.php" style="text-decoration: none;">
                            <div class="stat-card-modern">
                                <div class="stat-header">
                                    <span class="stat-label">Total Revenue</span>
                                    <i class="fas fa-dollar-sign stat-icon-small"></i>
                                </div>
                                <div class="stat-number"><?php echo format_currency($month_sales); ?></div>
                                <div class="stat-change negative">
                                    <i class="fas fa-arrow-down"></i> 05% vs Last Day
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Live Order Tracking -->
                    <div class="card-modern">
                        <div class="card-header-modern">
                            <h3>Live Order Tracking</h3>
                            <button class="btn-filter">Today <i class="fas fa-chevron-down"></i></button>
                        </div>
                        
                        <div class="table-modern">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Time Placed</th>
                                        <th>Status</th>
                                        <th>Order Type</th>
                                        <th>Priority</th>
                                        <th>Alert</th>
                                        <th>Order Details</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_orders->num_rows > 0): ?>
                                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                            <tr onclick="window.location='<?php echo SITE_URL; ?>/orders/edit_order.php?id=<?php echo $order['id']; ?>'" style="cursor: pointer;">
                                                <td>Order #<?php echo $order['id']; ?></td>
                                                <td><?php echo date('h:i A', strtotime($order['created_at'])); ?></td>
                                                <td><span class="badge-status badge-delivered">Delivered</span></td>
                                                <td><span class="badge-type badge-dine">Dine-in</span></td>
                                                <td><span class="badge-priority badge-normal">Normal</span></td>
                                                <td><i class="fas fa-bell text-warning"></i></td>
                                                <td><a href="<?php echo SITE_URL; ?>/orders/edit_order.php?id=<?php echo $order['id']; ?>" class="link-primary">Details</a></td>
                                                <td><a href="<?php echo SITE_URL; ?>/orders/edit_order.php?id=<?php echo $order['id']; ?>" class="btn-action"><i class="fas fa-ellipsis-h"></i></a></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No orders yet</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- ANIMATED CHARTS SECTION -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
                        <!-- Revenue Bar Chart -->
                        <div class="card-modern">
                            <div class="card-header-modern">
                                <h3>Outlets Operational Cost Vs Revenue</h3>
                            </div>
                            <div style="padding: 24px;">
                                <canvas id="revenueChart" height="200"></canvas>
                            </div>
                        </div>
                        
                        <!-- Team Overview Donut Chart -->
                        <div class="card-modern">
                            <div class="card-header-modern">
                                <h3>Team Overview</h3>
                            </div>
                            <div style="padding: 24px;">
                                <canvas id="teamDonutChart"></canvas>
                                <div style="margin-top: 16px; text-align: center;">
                                    <p style="margin: 0; color: #718096; font-size: 0.85rem;">Total Employees: <strong>150</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sales Breakdown -->
                    <div class="card-modern" style="margin-top: 24px;">
                        <div class="card-header-modern">
                            <h3>Sales Breakdown</h3>
                        </div>
                        <div style="padding: 24px;">
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; text-align: center;">
                                <div>
                                    <div style="font-size: 2rem; font-weight: 700; color: #2D3748;">40%</div>
                                    <div style="color: #718096; font-size: 0.85rem; margin-top: 4px;">Dine-In</div>
                                    <div style="height: 8px; background: #E2E8F0; border-radius: 4px; margin-top: 8px; overflow: hidden;">
                                        <div style="height: 100%; width: 40%; background: linear-gradient(90deg, #48BB78, #38A169); border-radius: 4px; animation: progressBar 2s ease-out;"></div>
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 2rem; font-weight: 700; color: #2D3748;">35%</div>
                                    <div style="color: #718096; font-size: 0.85rem; margin-top: 4px;">Delivery</div>
                                    <div style="height: 8px; background: #E2E8F0; border-radius: 4px; margin-top: 8px; overflow: hidden;">
                                        <div style="height: 100%; width: 35%; background: linear-gradient(90deg, #4299E1, #3182CE); border-radius: 4px; animation: progressBar 2s ease-out 0.2s both;"></div>
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 2rem; font-weight: 700; color: #2D3748;">25%</div>
                                    <div style="color: #718096; font-size: 0.85rem; margin-top: 4px;">Pick-up</div>
                                    <div style="height: 8px; background: #E2E8F0; border-radius: 4px; margin-top: 8px; overflow: hidden;">
                                        <div style="height: 100%; width: 25%; background: linear-gradient(90deg, #F6AD55, #ED8936); border-radius: 4px; animation: progressBar 2s ease-out 0.4s both;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <style>
                        @keyframes progressBar {
                            from { width: 0%; }
                        }
                    </style>
                </div>
                
                <!-- Right Section - Trending Menu -->
                <div class="dashboard-right">
                    <div class="card-modern">
                        <div class="card-header-modern">
                            <h3>Trending Menu</h3>
                            <button class="btn-filter">Today <i class="fas fa-chevron-down"></i></button>
                        </div>
                        
                        <div class="trending-menu">
                            <?php if ($trending->num_rows > 0): ?>
                                <?php while ($item = $trending->fetch_assoc()): ?>
                                    <div class="menu-item-card">
                                        <div class="menu-image">
                                            <?php if ($item['image_path'] && file_exists($item['image_path'])): ?>
                                                <img src="<?php echo $item['image_path']; ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>">
                                            <?php else: ?>
                                                <div class="no-image"><i class="fas fa-utensils"></i></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="menu-info">
                                            <h4><?php echo htmlspecialchars($item['food_name']); ?></h4>
                                            <p class="menu-subtitle">Popular</p>
                                            <div class="menu-stats">
                                                <span><i class="fas fa-heart"></i> <?php echo $item['quantity']; ?></span>
                                                <span><i class="fas fa-shopping-bag"></i> <?php echo $item['quantity']; ?></span>
                                                <span class="menu-price"><?php echo format_currency($item['price']); ?></span>
                                            </div>
                                        </div>
                                        <button class="btn-chart"><i class="fas fa-chart-line"></i></button>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-center">No trending items</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Wait for page to fully load
        document.addEventListener('DOMContentLoaded', function() {
            
            // Revenue Bar Chart
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Oct 01', 'Oct 02', 'Oct 03', 'Oct 04', 'Oct 05', 'Oct 06', 'Oct 07', 'Oct 08'],
                        datasets: [{
                            label: 'Revenue',
                            data: [120, 150, 100, 250, 180, 200, 140, 160],
                            backgroundColor: '#5B6CE8',
                            borderRadius: 8,
                            barThickness: 30
                        }, {
                            label: 'Operational Cost',
                            data: [80, 100, 70, 150, 120, 130, 90, 110],
                            backgroundColor: '#E2E8F0',
                            borderRadius: 8,
                            barThickness: 30
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#2D3748',
                                padding: 12,
                                borderRadius: 8,
                                titleFont: { size: 13 },
                                bodyFont: { size: 12 }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value + 'k';
                                    },
                                    font: { size: 11 }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: { size: 11 }
                                }
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
                console.log('Revenue chart initialized');
            } else {
                console.error('Revenue chart canvas not found');
            }
            
            // Team Overview Donut Chart
            const teamCtx = document.getElementById('teamDonutChart');
            if (teamCtx) {
                new Chart(teamCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['On-Duty: 63', 'On-Duty: 15', 'Absent: 07'],
                        datasets: [{
                            data: [63, 15, 7],
                            backgroundColor: ['#5B6CE8', '#4ECDC4', '#E2E8F0'],
                            borderWidth: 0,
                            cutout: '70%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#2D3748',
                                padding: 12,
                                borderRadius: 8,
                                titleFont: { size: 13 },
                                bodyFont: { size: 12 }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 2000,
                            easing: 'easeInOutQuart'
                        }
                    },
                    plugins: [{
                        id: 'centerText',
                        beforeDraw: function(chart) {
                            const width = chart.width;
                            const height = chart.height;
                            const ctx = chart.ctx;
                            ctx.restore();
                            
                            const fontSize = (height / 160).toFixed(2);
                            ctx.font = 'bold ' + fontSize + 'em sans-serif';
                            ctx.textBaseline = 'middle';
                            
                            const text = '2,375';
                            const textX = Math.round((width - ctx.measureText(text).width) / 2);
                            const textY = height / 2.2;
                            
                            ctx.fillStyle = '#2D3748';
                            ctx.fillText(text, textX, textY);
                            ctx.save();
                        }
                    }]
                });
                console.log('Team chart initialized');
            } else {
                console.error('Team chart canvas not found');
            }
            
            // Animate stat numbers
            function animateCounter(element, target, duration = 2000) {
                let start = 0;
                const increment = target / (duration / 16);
                const timer = setInterval(() => {
                    start += increment;
                    if (start >= target) {
                        element.textContent = Math.round(target);
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.round(start);
                    }
                }, 16);
            }
            
            // Animate all stat numbers
            document.querySelectorAll('.stat-number').forEach(el => {
                const text = el.textContent.replace(/[^0-9]/g, '');
                const target = parseInt(text);
                if (!isNaN(target) && target > 0) {
                    el.textContent = '0';
                    setTimeout(() => {
                        animateCounter(el, target);
                    }, 300);
                }
            });
            
            console.log('All charts and animations initialized');
        });
    </script>
</body>
</html>
