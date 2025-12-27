<?php
/**
 * Food Management - Add New Food Item
 */

require_once '../config.php';
require_once '../functions.php';
require_login();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $food_name = sanitize_input($_POST['food_name']);
    $description = sanitize_input($_POST['description']);
    $category = sanitize_input($_POST['category']);
    $price = floatval($_POST['price']);
    
    // Validate inputs
    if (empty($food_name)) {
        $error = 'Food name is required';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than 0';
    } else {
        // Handle image upload
        $image_path = null;
        
        if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_image($_FILES['food_image'], 'foods');
            
            if ($upload_result['success']) {
                $image_path = $upload_result['path'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        // Insert food if no errors
        if (empty($error)) {
            $sql = "INSERT INTO foods (food_name, description, category, price, image_path, is_available) 
                    VALUES (?, ?, ?, ?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssds", $food_name, $description, $category, $price, $image_path);
            
            if ($stmt->execute()) {
                $success = 'Food item added successfully!';
                // Clear form
                $food_name = '';
                $description = '';
                $category = '';
                $price = 0;
            } else {
                $error = 'Failed to add food: ' . $conn->error;
            }
            
            $stmt->close();
        }
    }
}

// Set default values
if (!isset($food_name)) $food_name = '';
if (!isset($description)) $description = '';
if (!isset($category)) $category = '';
if (!isset($price)) $price = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Food - Restaurant Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/forms-custom.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <div>
                    <h1>Add New Food Item</h1>
                    <p>Register a new food item to your menu</p>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3>Food Details</h3>
                </div>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="food_name" class="required">Food Name</label>
                        <input type="text" id="food_name" name="food_name" class="form-control" 
                               placeholder="Enter food name" required
                               value="<?php echo htmlspecialchars($food_name); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" 
                                  placeholder="Enter food description"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">Select Category</option>
                            <option value="Appetizers" <?php echo $category == 'Appetizers' ? 'selected' : ''; ?>>Appetizers</option>
                            <option value="Main Course" <?php echo $category == 'Main Course' ? 'selected' : ''; ?>>Main Course</option>
                            <option value="Salad" <?php echo $category == 'Salad' ? 'selected' : ''; ?>>Salad</option>
                            <option value="Pizza" <?php echo $category == 'Pizza' ? 'selected' : ''; ?>>Pizza</option>
                            <option value="Burgers" <?php echo $category == 'Burgers' ? 'selected' : ''; ?>>Burgers</option>
                            <option value="Pasta" <?php echo $category == 'Pasta' ? 'selected' : ''; ?>>Pasta</option>
                            <option value="Desserts" <?php echo $category == 'Desserts' ? 'selected' : ''; ?>>Desserts</option>
                            <option value="Beverages" <?php echo $category == 'Beverages' ? 'selected' : ''; ?>>Beverages</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="price" class="required">Price ($)</label>
                        <input type="number" id="price" name="price" class="form-control" 
                               min="0" step="0.01" required
                               value="<?php echo $price; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="food_image">Food Image</label>
                        <input type="file" id="food_image" name="food_image" class="form-control" 
                               accept="image/*" onchange="previewImage(event)">
                        <small style="color: #8D99AE;">Allowed formats: JPG, PNG, GIF (Max 5MB)</small>
                        <div class="image-preview" id="imagePreview"></div>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">Add Food Item</button>
                        <a href="view_foods.php" class="btn btn-secondary">View All Foods</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
