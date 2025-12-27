# Staff Management & Image Upload Instructions

## 1. Create Profile Images Folder
✅ Already created: `uploads/profiles/`

## 2. Update Database
Run this SQL in phpMyAdmin:

```sql
-- Add new columns to users table
ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL AFTER email;
ALTER TABLE users ADD COLUMN role ENUM('admin', 'staff') DEFAULT 'staff' AFTER profile_image;
ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER role;
ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL AFTER phone;
ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER address;

-- Update admin user
UPDATE users SET role = 'admin' WHERE username = 'admin';
```

## 3. Upload Admin Image
1. Find a profile image for admin
2. Rename it to `admin.jpg` or `admin.png`
3. Upload to: `c:\xampp\htdocs\RMS\uploads\profiles\`
4. Run this SQL:
```sql
UPDATE users SET profile_image = 'uploads/profiles/admin.jpg' WHERE username = 'admin';
```

## 4. Add Staff Members (Optional)
```sql
INSERT INTO users (username, password, full_name, email, profile_image, role, phone, status) VALUES
('john_chef', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1MEOVkCnKZ8WFqBKCJlKCJDv.Qnj/Aa', 'John Smith', 'john@restaurant.com', 'uploads/profiles/john.jpg', 'staff', '+254712345678', 'active');
```
Password for staff: `admin123`

## 5. Logout and Login Again
After updating the database, logout and login again to see your profile image!

---

**Files Modified:**
- ✅ `includes/sidebar.php` - Shows profile image if available
- ✅ `index.php` - Fetches profile_image on login
- ✅ `assets/css/style.css` - Added avatar image styling
- ✅ Logo made bigger and centered
- ✅ RMS text removed from sidebar

**SQL File:** `update_staff_system.sql`
