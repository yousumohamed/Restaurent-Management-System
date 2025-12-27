-- ============================================
-- QUICK FIX: Update Admin Password
-- ============================================
-- Run this single command in phpMyAdmin SQL tab
-- This will update the existing admin user's password

UPDATE users 
SET password = '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1MEOVkCnKZ8WFqBKCJlKCJDv.Qnj/Aa' 
WHERE username = 'admin';

-- After running this, login with:
-- Username: admin
-- Password: admin123
