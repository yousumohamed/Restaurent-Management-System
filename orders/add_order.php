<?php
/**
 * Restaurant Management System
 * Add New Order with Image Upload
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = sanitize_input($_POST['customer_name']);
    $food_name = sanitize_input($_POST['food_name']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $order_date = sanitize_input($_POST['order_date']);
    
    // Set default customer name if empty
    if (empty($customer_name)) {
        $customer_name = 'Walk-in Customer';
    }
    
    // Validate inputs
    if (empty($food_name)) {
        $error = 'Food name is required';
    } elseif ($quantity <= 0) {
        $error = 'Quantity must be greater than 0';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than 0';
    } elseif (empty($order_date)) {
        $error = 'Order date is required';
    } else {
        // Handle image upload
        $image_path = null;
        
        if (isset($_FILES['order_image']) && $_FILES['order_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_image($_FILES['order_image']);
            
            if ($upload_result['success']) {
                $image_path = $upload_result['path'];
            } else {
                $error = $upload_result['message'];
            }
        }
        
        // Insert order if no errors
        if (empty($error)) {
            $sql = "INSERT INTO orders (customer_name, food_name, quantity, price, order_date, image_path) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiiss", $customer_name, $food_name, $quantity, $price, $order_date, $image_path);
            
            if ($stmt->execute()) {
                $success = 'Order added successfully!';
                // Clear form
                $customer_name = '';
                $food_name = '';
                $quantity = 1;
                $price = 0;
                $order_date = date('Y-m-d');
            } else {
                $error = 'Failed to add order: ' . $conn->error;
            }
            
            $stmt->close();
        }
    }
}

// Set default values
if (!isset($customer_name)) $customer_name = '';
if (!isset($food_name)) $food_name = '';
if (!isset($quantity)) $quantity = 1;
if (!isset($price)) $price = 0;
if (!isset($order_date)) $order_date = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Order - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/forms-custom.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>➕ Add New Order</h1>
                <p>Register a new order with food image</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3>Order Details</h3>
                </div>
                
                <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateOrderForm()">
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" 
                               placeholder="Enter customer name (optional)" 
                               value="<?php echo htmlspecialchars($customer_name); ?>">
                        <small style="color: #8D99AE;">Leave empty for walk-in customers</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="food_name" class="required">Food Name</label>
                        <input type="text" id="food_name" name="food_name" class="form-control" 
                               placeholder="Enter food name" required
                               value="<?php echo htmlspecialchars($food_name); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity" class="required">Quantity</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" 
                               min="1" step="1" required
                               value="<?php echo $quantity; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="price" class="required">Price ($)</label>
                        <input type="number" id="price" name="price" class="form-control" 
                               min="0" step="0.01" required
                               value="<?php echo $price; ?>">
                    </div>
                    
                    <div class="form-group">
                        <div id="totalDisplay" style="font-size: 1.2rem; font-weight: 600; color: var(--primary-red); margin-top: 10px;">
                            Total: $0.00
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="order_date" class="required">Order Date</label>
                        <input type="date" id="order_date" name="order_date" class="form-control" required
                               value="<?php echo $order_date; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="order_image">Food Image</label>
                        <input type="file" id="order_image" name="order_image" class="form-control" 
                               accept="image/jpeg,image/png,image/gif">
                        <small style="color: #8D99AE;">Allowed formats: JPG, PNG, GIF (Max 5MB)</small>
                    </div>
                    
                    <div id="imagePreviewContainer" class="image-preview" style="display: none;">
                        <img id="imagePreview" src="" alt="Image Preview">
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">Add Order</button>
                        <a href="view_orders.php" class="btn btn-secondary">View All Orders</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
