# 📖 Restaurant Management System - Installation Guide

Complete step-by-step installation guide for XAMPP environment.

## 📋 Prerequisites

Before you begin, ensure you have:
- A computer running Windows, macOS, or Linux
- At least 500MB free disk space
- Administrator/root access to install software
- Internet connection for downloading XAMPP

---

## 🔧 Step 1: Download and Install XAMPP

### Windows Installation

1. **Download XAMPP**
   - Visit [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)
   - Download XAMPP for Windows (PHP 7.4 or higher)
   - File size: approximately 150MB

2. **Run the Installer**
   - Double-click the downloaded `.exe` file
   - If Windows asks for permission, click **"Yes"**
   - Click **"Next"** on the welcome screen

3. **Select Components**
   - Ensure these are checked:
     - ✅ Apache
     - ✅ MySQL
     - ✅ PHP
     - ✅ phpMyAdmin
   - Click **"Next"**

4. **Choose Installation Folder**
   - Default: `C:\xampp`
   - Click **"Next"**

5. **Complete Installation**
   - Uncheck "Learn more about Bitnami" (optional)
   - Click **"Next"** and wait for installation
   - Click **"Finish"**

### macOS Installation

1. Download XAMPP for macOS from the official website
2. Open the `.dmg` file
3. Drag XAMPP to Applications folder
4. Open XAMPP from Applications

### Linux Installation

```bash
# Download XAMPP
wget https://www.apachefriends.org/xampp-files/[version]/xampp-linux-x64-[version]-installer.run

# Make it executable
chmod +x xampp-linux-x64-[version]-installer.run

# Run installer
sudo ./xampp-linux-x64-[version]-installer.run
```

---

## 🚀 Step 2: Start XAMPP Services

### Windows

1. **Open XAMPP Control Panel**
   - Search for "XAMPP Control Panel" in Start Menu
   - Or navigate to `C:\xampp\xampp-control.exe`

2. **Start Services**
   - Click **"Start"** button next to **Apache**
   - Click **"Start"** button next to **MySQL**
   - Both should show green "Running" status

3. **Verify Services**
   - Open browser and go to `http://localhost`
   - You should see XAMPP welcome page

### macOS/Linux

1. Open Terminal
2. Start XAMPP:
   ```bash
   sudo /Applications/XAMPP/xamppfiles/xampp start
   ```
3. Or use XAMPP Manager GUI

---

## 📦 Step 3: Setup Project Files

### Method 1: Manual Copy

1. **Locate htdocs Folder**
   - Windows: `C:\xampp\htdocs\`
   - macOS: `/Applications/XAMPP/htdocs/`
   - Linux: `/opt/lampp/htdocs/`

2. **Copy RMS Folder**
   - Copy the entire `RMS` folder
   - Paste it into the `htdocs` directory
   - Final path: `C:\xampp\htdocs\RMS\`

3. **Verify Files**
   - Ensure all files are present:
     - `index.php`
     - `config.php`
     - `database.sql`
     - `assets/` folder
     - `orders/` folder
     - etc.

### Method 2: Extract from ZIP

1. If you have RMS.zip:
   - Right-click the ZIP file
   - Select "Extract All"
   - Choose `C:\xampp\htdocs\` as destination
   - Click "Extract"

---

## 🗄️ Step 4: Create Database

### Using phpMyAdmin (Recommended)

1. **Access phpMyAdmin**
   - Open browser
   - Go to `http://localhost/phpmyadmin`
   - You should see phpMyAdmin interface

2. **Import Database**
   - Click on **"Import"** tab at the top
   - Click **"Choose File"** button
   - Navigate to `C:\xampp\htdocs\RMS\database.sql`
   - Select the file and click **"Open"**
   - Scroll down and click **"Go"** button
   - Wait for "Import has been successfully finished" message

3. **Verify Database**
   - Click on **"Databases"** tab
   - You should see `restaurant_management` in the list
   - Click on it to view tables:
     - `users`
     - `orders`
     - `expenses`

### Alternative: Manual Database Creation

1. **Create Database**
   - In phpMyAdmin, click **"New"** in left sidebar
   - Database name: `restaurant_management`
   - Collation: `utf8mb4_general_ci`
   - Click **"Create"**

2. **Import SQL File**
   - Select the newly created database
   - Click **"Import"** tab
   - Upload `database.sql`
   - Click **"Go"**

### Using MySQL Command Line

```bash
# Navigate to XAMPP MySQL bin directory
cd C:\xampp\mysql\bin

# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE restaurant_management;

# Exit MySQL
exit

# Import SQL file
mysql -u root restaurant_management < C:\xampp\htdocs\RMS\database.sql
```

---

## ⚙️ Step 5: Configure Database Connection

1. **Open config.php**
   - Navigate to `C:\xampp\htdocs\RMS\`
   - Open `config.php` with a text editor (Notepad++, VS Code, etc.)

2. **Check Database Settings**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Leave empty for default XAMPP
   define('DB_NAME', 'restaurant_management');
   ```

3. **Modify if Needed**
   - If you set a MySQL password, update `DB_PASS`
   - If using different database name, update `DB_NAME`
   - Save the file

---

## 🌐 Step 6: Access the System

1. **Open Web Browser**
   - Chrome, Firefox, Edge, or Safari

2. **Navigate to Application**
   - Type in address bar: `http://localhost/RMS`
   - Press Enter

3. **Login Page**
   - You should see the Restaurant Management System login page
   - If you see an error, check [Troubleshooting](#troubleshooting) section

4. **Login with Default Credentials**
   - Username: `admin`
   - Password: `admin123`
   - Click **"Login"**

5. **Success!**
   - You should now see the dashboard
   - Explore the system features

---

## 🔐 Step 7: Change Default Password (Recommended)

For security, change the default password:

1. **Access Database**
   - Go to `http://localhost/phpmyadmin`
   - Select `restaurant_management` database
   - Click on `users` table

2. **Generate New Password Hash**
   - Use online bcrypt generator or PHP:
   ```php
   <?php
   echo password_hash('your_new_password', PASSWORD_DEFAULT);
   ?>
   ```

3. **Update Password**
   - Click **"Edit"** on the admin user row
   - Replace the `password` field value with new hash
   - Click **"Go"**

---

## ✅ Step 8: Verify Installation

### Test Order Management

1. **Add a Test Order**
   - Click **"Add Order"** in sidebar
   - Fill in the form:
     - Customer Name: Test Customer
     - Food Name: Test Pizza
     - Quantity: 2
     - Price: 500
     - Order Date: Today's date
     - Upload a test image
   - Click **"Add Order"**

2. **View Orders**
   - Click **"View Orders"**
   - Verify your test order appears
   - Check if image thumbnail displays

### Test Expense Management

1. **Add a Test Expense**
   - Click **"Add Expense"**
   - Category: Ingredients
   - Description: Test expense
   - Amount: 1000
   - Date: Today
   - Click **"Add Expense"**

2. **View Expenses**
   - Click **"View Expenses"**
   - Verify expense appears

### Test Reports

1. **Daily Report**
   - Click **"Daily Report"**
   - Select today's date
   - Click **"Generate Report"**
   - Verify data displays correctly

2. **Dashboard**
   - Click **"Dashboard"**
   - Check all widgets show correct data

---

## 🔧 Troubleshooting

### Apache Won't Start

**Problem:** Port 80 is already in use

**Solution:**
1. Open XAMPP Control Panel
2. Click **"Config"** next to Apache
3. Select **"httpd.conf"**
4. Find line: `Listen 80`
5. Change to: `Listen 8080`
6. Save and restart Apache
7. Access via `http://localhost:8080/RMS`

**Alternative:** Stop Skype or other applications using port 80

### MySQL Won't Start

**Problem:** Port 3306 is already in use

**Solution:**
1. Check if MySQL service is already running
2. Stop other MySQL instances
3. Or change MySQL port in XAMPP config

### Database Connection Error

**Error:** "Connection failed: Access denied"

**Solution:**
1. Check `config.php` credentials
2. Verify MySQL is running
3. Test connection in phpMyAdmin
4. Ensure database exists

### Images Not Uploading

**Problem:** Upload fails or images don't display

**Solution:**
1. **Check Folder Permissions**
   - Right-click `uploads` folder
   - Properties → Security
   - Ensure "Everyone" has Write permissions

2. **Create uploads Folder**
   ```bash
   mkdir C:\xampp\htdocs\RMS\uploads
   ```

3. **Check PHP Settings**
   - Open `php.ini` (XAMPP Control → Apache Config → php.ini)
   - Find and set:
     ```ini
     upload_max_filesize = 10M
     post_max_size = 10M
     ```
   - Restart Apache

### Page Not Found (404)

**Problem:** Cannot access `http://localhost/RMS`

**Solution:**
1. Verify folder is in correct location: `C:\xampp\htdocs\RMS\`
2. Check Apache is running (green in XAMPP Control)
3. Try `http://localhost/RMS/index.php`
4. Clear browser cache

### Blank White Page

**Problem:** Page loads but shows nothing

**Solution:**
1. Enable error reporting in `config.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
2. Check PHP error log in `C:\xampp\apache\logs\error.log`
3. Verify all PHP files have proper syntax

---

## 📱 Accessing from Other Devices

### Same Network Access

1. **Find Your IP Address**
   - Windows: Open CMD, type `ipconfig`
   - Look for IPv4 Address (e.g., 192.168.1.100)

2. **Configure Apache**
   - Edit `httpd-xampp.conf`
   - Change:
     ```apache
     Require local
     ```
   - To:
     ```apache
     Require all granted
     ```

3. **Access from Other Device**
   - On phone/tablet, go to: `http://192.168.1.100/RMS`

---

## 🔄 Updating the System

1. **Backup Database**
   - phpMyAdmin → Export → Go
   - Save the SQL file

2. **Backup Uploads**
   - Copy `uploads/` folder to safe location

3. **Replace Files**
   - Copy new RMS files
   - Restore `uploads/` folder

4. **Update Database**
   - Import any new SQL changes

---

## 🎓 Next Steps

After successful installation:

1. ✅ Change default password
2. ✅ Add real orders and expenses
3. ✅ Customize categories if needed
4. ✅ Set up regular database backups
5. ✅ Train staff on system usage

---

## 📞 Support

If you encounter issues:

1. Check this guide thoroughly
2. Review error messages carefully
3. Check XAMPP error logs
4. Verify all prerequisites are met
5. Ensure XAMPP services are running

---

## 🎉 Congratulations!

You have successfully installed the Restaurant Management System!

Start managing your restaurant operations efficiently with automated order tracking, sales analysis, and profit/loss reporting.

---

**Installation Guide Version:** 1.0  
**Last Updated:** December 2025  
**Compatible with:** XAMPP 7.4+
