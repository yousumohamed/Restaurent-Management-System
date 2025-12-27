<?php
/**
 * Restaurant Management System
 * Edit Order
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

$success = '';
$error = '';

// Get order ID
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id <= 0) {
    header('Location: view_orders.php');
    exit();
}

// Fetch order details
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: view_orders.php');
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = sanitize_input($_POST['customer_name']);
    $food_name = sanitize_input($_POST['food_name']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $order_date = sanitize_input($_POST['order_date']);
    
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
        $image_path = $order['image_path'];
        
        // Handle new image upload
        if (isset($_FILES['order_image']) && $_FILES['order_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_image($_FILES['order_image']);
            
            if ($upload_result['success']) {
                // Delete old image
                if (!empty($order['image_path'])) {
                    delete_image($order['image_path']);
                }
                $image_path = $upload_result['path'];
            } else {
                $error = $upload_result['message'];
            }
        }
        
        // Update order if no errors
        if (empty($error)) {
            $sql = "UPDATE orders SET customer_name = ?, food_name = ?, quantity = ?, price = ?, 
                    order_date = ?, image_path = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiissi", $customer_name, $food_name, $quantity, $price, $order_date, $image_path, $order_id);
            
            if ($stmt->execute()) {
                $success = 'Order updated successfully!';
                // Refresh order data
                $order['customer_name'] = $customer_name;
                $order['food_name'] = $food_name;
                $order['quantity'] = $quantity;
                $order['price'] = $price;
                $order['order_date'] = $order_date;
                $order['image_path'] = $image_path;
            } else {
                $error = 'Failed to update order: ' . $conn->error;
            }
            
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>✏️ Edit Order #<?php echo $order_id; ?></h1>
                <p>Update order details</p>
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
                               value="<?php echo htmlspecialchars($order['customer_name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="food_name" class="required">Food Name</label>
                        <input type="text" id="food_name" name="food_name" class="form-control" 
                               placeholder="Enter food name" required
                               value="<?php echo htmlspecialchars($order['food_name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity" class="required">Quantity</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" 
                               min="1" step="1" required
                               value="<?php echo $order['quantity']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="price" class="required">Price (KSh)</label>
                        <input type="number" id="price" name="price" class="form-control" 
                               min="0" step="0.01" required
                               value="<?php echo $order['price']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <div id="totalDisplay" style="font-size: 1.2rem; font-weight: 600; color: var(--primary-red); margin-top: 10px;">
                            Total: KSh <?php echo number_format($order['quantity'] * $order['price'], 2); ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="order_date" class="required">Order Date</label>
                        <input type="date" id="order_date" name="order_date" class="form-control" required
                               value="<?php echo $order['order_date']; ?>">
                    </div>
                    
                    <?php if ($order['image_path']): ?>
                        <div class="form-group">
                            <label>Current Image</label>
                            <div class="image-preview">
                                <img src="../<?php echo htmlspecialchars($order['image_path']); ?>" 
                                     alt="Current Order Image" style="max-width: 300px;">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="order_image">Change Food Image</label>
                        <input type="file" id="order_image" name="order_image" class="form-control" 
                               accept="image/jpeg,image/png,image/gif">
                        <small style="color: #8D99AE;">Leave empty to keep current image</small>
                    </div>
                    
                    <div id="imagePreviewContainer" class="image-preview" style="display: none;">
                        <img id="imagePreview" src="" alt="New Image Preview">
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">Update Order</button>
                        <a href="view_orders.php" class="btn btn-secondary">Back to Orders</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
