-- ============================================
-- Restaurant Management System Database
-- Compatible with XAMPP phpMyAdmin
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS restaurant_management;
USE restaurant_management;

-- ============================================
-- Table: users (Admin Authentication)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: orders (Order Management with Images)
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) DEFAULT 'Walk-in Customer',
    food_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) GENERATED ALWAYS AS (quantity * price) STORED,
    order_date DATE NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_date (order_date),
    INDEX idx_food_name (food_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: expenses (Expense Tracking)
-- ============================================
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    amount DECIMAL(10, 2) NOT NULL,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_expense_date (expense_date),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Insert Sample Data
-- ============================================

-- Insert Admin User (password: admin123)
INSERT INTO users (username, password, full_name, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@restaurant.com');

-- Insert Sample Orders
INSERT INTO orders (customer_name, food_name, quantity, price, order_date, image_path) VALUES
('John Doe', 'Grilled Chicken', 2, 15.99, '2025-12-27', 'uploads/grilled_chicken.jpg'),
('Jane Smith', 'Margherita Pizza', 1, 12.50, '2025-12-27', 'uploads/pizza.jpg'),
('Walk-in Customer', 'Caesar Salad', 3, 8.99, '2025-12-27', 'uploads/salad.jpg'),
('Mike Johnson', 'Beef Burger', 2, 11.99, '2025-12-27', 'uploads/burger.jpg'),
('Sarah Williams', 'Pasta Carbonara', 1, 14.50, '2025-12-26', 'uploads/pasta.jpg'),
('Walk-in Customer', 'Fish and Chips', 2, 13.99, '2025-12-26', 'uploads/fish.jpg'),
('David Brown', 'Chicken Wings', 4, 9.99, '2025-12-26', 'uploads/wings.jpg'),
('Emily Davis', 'Vegetable Stir Fry', 1, 10.99, '2025-12-25', 'uploads/stirfry.jpg'),
('Walk-in Customer', 'Chocolate Cake', 2, 6.99, '2025-12-25', 'uploads/cake.jpg'),
('Robert Wilson', 'Steak Dinner', 1, 24.99, '2025-12-25', 'uploads/steak.jpg');

-- Insert Sample Expenses
INSERT INTO expenses (category, description, amount, expense_date) VALUES
('Rent', 'Monthly restaurant rent', 2000.00, '2025-12-01'),
('Salaries', 'Staff salaries for December', 5000.00, '2025-12-01'),
('Ingredients', 'Fresh vegetables and meat', 800.00, '2025-12-25'),
('Utilities', 'Electricity bill', 350.00, '2025-12-20'),
('Utilities', 'Water bill', 120.00, '2025-12-20'),
('Ingredients', 'Dairy products and cheese', 450.00, '2025-12-26'),
('Maintenance', 'Kitchen equipment repair', 200.00, '2025-12-22'),
('Marketing', 'Social media advertising', 150.00, '2025-12-15'),
('Ingredients', 'Spices and condiments', 180.00, '2025-12-27'),
('Supplies', 'Cleaning supplies', 95.00, '2025-12-24');

-- ============================================
-- Create Views for Reports
-- ============================================

-- View: Daily Sales Summary
CREATE OR REPLACE VIEW daily_sales_summary AS
SELECT 
    order_date,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_sales,
    AVG(total_amount) as average_order_value
FROM orders
GROUP BY order_date
ORDER BY order_date DESC;

-- View: Daily Expenses Summary
CREATE OR REPLACE VIEW daily_expenses_summary AS
SELECT 
    expense_date,
    COUNT(*) as total_expenses_count,
    SUM(amount) as total_expenses
FROM expenses
GROUP BY expense_date
ORDER BY expense_date DESC;

-- View: Daily Profit/Loss
CREATE OR REPLACE VIEW daily_profit_loss AS
SELECT 
    COALESCE(s.order_date, e.expense_date) as date,
    COALESCE(s.total_sales, 0) as sales,
    COALESCE(e.total_expenses, 0) as expenses,
    (COALESCE(s.total_sales, 0) - COALESCE(e.total_expenses, 0)) as profit_loss
FROM daily_sales_summary s
LEFT JOIN daily_expenses_summary e ON s.order_date = e.expense_date
UNION
SELECT 
    COALESCE(s.order_date, e.expense_date) as date,
    COALESCE(s.total_sales, 0) as sales,
    COALESCE(e.total_expenses, 0) as expenses,
    (COALESCE(s.total_sales, 0) - COALESCE(e.total_expenses, 0)) as profit_loss
FROM daily_sales_summary s
RIGHT JOIN daily_expenses_summary e ON s.order_date = e.expense_date
ORDER BY date DESC;

-- ============================================
-- End of Database Setup
-- ============================================
