# 🔧 Quick Fix for Login Issue

If you're having trouble logging in with the default credentials, follow these steps:

## Option 1: Re-import Database (Recommended)

1. **Drop existing database** (if you already imported it):
   - Go to `http://localhost/phpmyadmin`
   - Select `restaurant_management` database
   - Click "Drop" tab
   - Confirm deletion

2. **Re-import the updated database.sql**:
   - Click "Import" tab
   - Choose the `database.sql` file
   - Click "Go"

3. **Try logging in**:
   - Username: `admin`
   - Password: `admin123`

## Option 2: Update Password Directly in phpMyAdmin

1. **Go to phpMyAdmin**: `http://localhost/phpmyadmin`

2. **Select database**: Click on `restaurant_management`

3. **Open users table**: Click on `users` table

4. **Click SQL tab** and run this query:
   ```sql
   UPDATE users 
   SET password = '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1MEOVkCnKZ8WFqBKCJlKCJDv.Qnj/Aa' 
   WHERE username = 'admin';
   ```

5. **Try logging in again**:
   - Username: `admin`
   - Password: `admin123`

## Option 3: Generate New Hash

1. **Access the hash generator**:
   - Go to `http://localhost/RMS/generate_hash.php`
   - Copy the generated hash

2. **Update in phpMyAdmin**:
   - Go to `restaurant_management` → `users` table
   - Click "Edit" on the admin row
   - Paste the new hash in the `password` field
   - Click "Go"

## Option 4: Create New Admin User

Run this in phpMyAdmin SQL tab:

```sql
-- Delete old admin if exists
DELETE FROM users WHERE username = 'admin';

-- Insert new admin with working password
INSERT INTO users (username, password, full_name, email) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1MEOVkCnKZ8WFqBKCJlKCJDv.Qnj/Aa', 'System Administrator', 'admin@restaurant.com');
```

## Verify Login

After trying any of the above options:

1. Go to `http://localhost/RMS`
2. Enter:
   - **Username**: `admin`
   - **Password**: `admin123`
3. Click "Login"

## Still Not Working?

Check these:

1. **Database imported correctly?**
   - Verify `users` table exists in `restaurant_management` database
   - Check if admin user exists: `SELECT * FROM users WHERE username = 'admin';`

2. **Apache and MySQL running?**
   - Check XAMPP Control Panel
   - Both should show green "Running" status

3. **Correct database credentials in config.php?**
   - Open `config.php`
   - Verify DB_NAME is `restaurant_management`
   - Verify DB_USER is `root`
   - Verify DB_PASS is empty (or your MySQL password)

4. **PHP errors?**
   - Check browser console (F12)
   - Check Apache error log: `C:\xampp\apache\logs\error.log`

## Contact Support

If none of these work, check:
- XAMPP is properly installed
- PHP version is 7.4 or higher
- MySQL is running
- No firewall blocking localhost

---

**The password hash has been updated in database.sql**. Please re-import the database to fix the login issue.
