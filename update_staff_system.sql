-- ============================================
-- Updated Database Schema with Staff Management and Images
-- ============================================

-- Update users table to include profile images
ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL AFTER email;
ALTER TABLE users ADD COLUMN role ENUM('admin', 'staff') DEFAULT 'staff' AFTER profile_image;
ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER role;
ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL AFTER phone;
ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER address;

-- Update admin user with image path (you'll need to upload admin image to uploads/profiles/)
UPDATE users SET profile_image = 'uploads/profiles/admin.jpg', role = 'admin' WHERE username = 'admin';

-- Insert sample staff members (update image paths after uploading images)
INSERT INTO users (username, password, full_name, email, profile_image, role, phone, status) VALUES
('john_chef', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1MEOVkCnKZ8WFqBKCJlKCJDv.Qnj/Aa', 'John Smith', 'john@restaurant.com', 'uploads/profiles/john.jpg', 'staff', '+254712345678', 'active'),
('mary_waiter', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1MEOVkCnKZ8WFqBKCJlKCJDv.Qnj/Aa', 'Mary Johnson', 'mary@restaurant.com', 'uploads/profiles/mary.jpg', 'staff', '+254723456789', 'active'),
('david_cashier', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1MEOVkCnKZ8WFqBKCJlKCJDv.Qnj/Aa', 'David Wilson', 'david@restaurant.com', 'uploads/profiles/david.jpg', 'staff', '+254734567890', 'active');

-- Note: Default password for all staff is 'admin123'
-- They should change it after first login
