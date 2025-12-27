-- ============================================
-- Food Menu Management System
-- ============================================

-- Create foods table for menu management
CREATE TABLE IF NOT EXISTS foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_food_name (food_name),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample foods
INSERT INTO foods (food_name, description, category, price, is_available) VALUES
('Grilled Chicken', 'Juicy grilled chicken with herbs', 'Main Course', 15.99, TRUE),
('Caesar Salad', 'Fresh romaine lettuce with caesar dressing', 'Salad', 8.99, TRUE),
('Margherita Pizza', 'Classic pizza with tomato and mozzarella', 'Pizza', 12.50, TRUE),
('Beef Burger', 'Angus beef burger with cheese', 'Burgers', 11.99, TRUE),
('Chicken Wings', 'Spicy buffalo wings', 'Appetizers', 9.99, TRUE),
('Fish and Chips', 'Crispy battered fish with fries', 'Main Course', 14.99, TRUE),
('Pasta Carbonara', 'Creamy pasta with bacon', 'Pasta', 13.50, TRUE),
('Chocolate Cake', 'Rich chocolate layer cake', 'Desserts', 6.99, TRUE);

-- Update orders table to reference foods table (optional - for future use)
-- ALTER TABLE orders ADD COLUMN food_id INT DEFAULT NULL AFTER food_name;
-- ALTER TABLE orders ADD FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE SET NULL;
