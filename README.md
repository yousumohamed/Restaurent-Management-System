# 🍽️ Restaurant Management System (RMS)

A comprehensive web-based Restaurant Management System built with PHP and MySQL, designed to run on XAMPP. This system automates restaurant operations including order management with image uploads, sales tracking, expense management, and profit/loss reporting.

![PHP](https://img.shields.io/badge/PHP-7.4+-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![License](https://img.shields.io/badge/License-MIT-green)

## 📋 Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Default Login](#default-login)
- [Folder Structure](#folder-structure)
- [Technologies Used](#technologies-used)
- [Screenshots](#screenshots)
- [Troubleshooting](#troubleshooting)
- [License](#license)

## ✨ Features

### 🔐 Authentication & Security
- Secure admin login system
- Session management
- Password hashing with bcrypt
- CSRF protection
- XSS prevention

### 📦 Order Management (WITH IMAGE UPLOAD)
- Add new orders with food images
- Upload and store food pictures (JPG, PNG, GIF)
- View all orders with image thumbnails
- Edit existing orders
- Delete orders (removes both database record and image file)
- Search orders by food name or customer name
- Filter orders by date range
- Pagination (20 records per page)
- Automatic total calculation (quantity × price)

### 💰 Sales Management
- Automatic sales calculation from orders
- Daily sales summary
- Sales breakdown by food items
- Top selling items analysis
- Average order value calculation

### 💸 Expense Management
- Add expenses with categories (Rent, Salaries, Ingredients, Utilities, etc.)
- View all expenses with filtering
- Filter by category and date range
- Edit and delete expenses
- Track expenses by date
- Expense breakdown by category

### 📈 Profit & Loss System
- Automatic calculation: Sales - Expenses
- Daily profit/loss tracking
- Monthly profit/loss analysis
- Profit margin calculation
- Color-coded profit (green) and loss (red) indicators
- Daily breakdown with status indicators

### 📄 Reports
- **Daily Reports**: Complete daily business summary with orders, expenses, and profit/loss
- **Monthly Reports**: Comprehensive monthly analysis with trends and breakdowns
- Top selling items
- Expense category analysis
- Print-friendly format
- Export capability

### 📊 Dashboard
- Real-time statistics
- Today's overview (orders, sales, expenses, profit/loss)
- Monthly overview
- Recent orders display
- Quick action buttons
- Visual widgets with icons

### 🎨 UI/UX Features
- **Warm Restaurant Colors**: Red, Orange, Yellow color scheme
- Responsive design (mobile, tablet, desktop)
- Clean and intuitive interface
- Modern card-based layout
- Smooth animations and transitions
- Easy navigation sidebar

## 💻 System Requirements

- **XAMPP** (Apache + MySQL + PHP)
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
  - Apache 2.4 or higher
- **Web Browser** (Chrome, Firefox, Edge, Safari)
- **Minimum 100MB** disk space

## 🚀 Installation

### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Install XAMPP on your computer
3. Start **Apache** and **MySQL** from XAMPP Control Panel

### Step 2: Setup Project Files

1. Copy the `RMS` folder to `C:\xampp\htdocs\`
2. The final path should be: `C:\xampp\htdocs\RMS\`

### Step 3: Create Database

1. Open your web browser
2. Go to `http://localhost/phpmyadmin`
3. Click on **"Import"** tab
4. Click **"Choose File"** and select `database.sql` from the RMS folder
5. Click **"Go"** to import the database
6. The database `restaurant_management` will be created automatically

**Alternative Method:**
1. In phpMyAdmin, click **"New"** to create a new database
2. Name it `restaurant_management`
3. Click **"Import"** and upload `database.sql`

### Step 4: Configure Database Connection

1. Open `config.php` in a text editor
2. Verify the database settings (default settings should work):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'restaurant_management');
   ```
3. If you have a different MySQL password, update `DB_PASS`

### Step 5: Access the System

1. Open your web browser
2. Go to `http://localhost/RMS`
3. You will see the login page
4. Use the default credentials (see below)

## 🔑 Default Login

**Username:** `admin`  
**Password:** `admin123`

> ⚠️ **Important:** Change the default password after first login for security!

## 📁 Folder Structure

```
RMS/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet with warm colors
│   └── js/
│       └── script.js          # JavaScript functions
├── expenses/
│   ├── add_expense.php        # Add new expense
│   ├── view_expenses.php      # View all expenses
│   ├── edit_expense.php       # Edit expense
│   └── delete_expense.php     # Delete expense
├── includes/
│   └── sidebar.php            # Navigation sidebar
├── orders/
│   ├── add_order.php          # Add new order with image
│   ├── view_orders.php        # View all orders
│   ├── edit_order.php         # Edit order
│   └── delete_order.php       # Delete order
├── profit_loss/
│   └── view.php               # Profit & loss analysis
├── reports/
│   ├── daily_report.php       # Daily business report
│   └── monthly_report.php     # Monthly business report
├── sales/
│   └── daily_sales.php        # Daily sales summary
├── uploads/                   # Uploaded order images
│   └── .htaccess              # Security configuration
├── config.php                 # Database configuration
├── functions.php              # Reusable functions
├── index.php                  # Login page
├── dashboard.php              # Main dashboard
├── logout.php                 # Logout handler
├── database.sql               # Database structure & sample data
├── README.md                  # This file
└── INSTALLATION_GUIDE.md      # Detailed installation guide
```

## 🛠️ Technologies Used

| Technology | Purpose |
|------------|---------|
| **PHP** | Backend server-side scripting |
| **MySQL** | Database management |
| **HTML5** | Structure and markup |
| **CSS3** | Styling and responsive design |
| **JavaScript** | Client-side interactivity |
| **XAMPP** | Local development environment |

## 📸 Screenshots

### Dashboard
The main dashboard displays today's and monthly statistics with visual widgets.

### Order Management
Add orders with food images, view thumbnails, search, and filter.

### Reports
Generate comprehensive daily and monthly reports with print functionality.

## 🔧 Troubleshooting

### Database Connection Error
- Ensure Apache and MySQL are running in XAMPP
- Check database credentials in `config.php`
- Verify database `restaurant_management` exists in phpMyAdmin

### Image Upload Not Working
- Check folder permissions for `uploads/` directory
- Ensure `uploads/` folder exists
- Verify file size is under 5MB
- Only JPG, PNG, GIF formats are allowed

### Page Not Found (404)
- Ensure project is in `C:\xampp\htdocs\RMS\`
- Access via `http://localhost/RMS` (not `http://localhost/RMS/index.php`)
- Check Apache is running

### Login Not Working
- Verify database was imported correctly
- Check if `users` table exists
- Use default credentials: admin / admin123

### Images Not Displaying
- Check image path in database
- Verify images exist in `uploads/` folder
- Check file permissions

## 🎯 Usage Tips

1. **Adding Orders**: Always add an image for better visual tracking
2. **Daily Routine**: Check daily reports at end of business day
3. **Monthly Analysis**: Review monthly reports for business insights
4. **Expense Tracking**: Record expenses immediately to maintain accuracy
5. **Backup**: Regularly export database from phpMyAdmin

## 🔒 Security Features

- Password hashing with bcrypt
- SQL injection prevention using prepared statements
- XSS protection with input sanitization
- CSRF token validation
- Secure file upload validation
- Session management
- .htaccess protection for uploads directory

## 📈 Future Enhancements

- Multi-user support with different roles
- Email notifications
- SMS integration
- Inventory management
- Table reservation system
- Online ordering integration
- Mobile app
- Advanced analytics and charts
- PDF export for reports
- Backup and restore functionality

## 📝 License

This project is open-source and available under the MIT License.

## 👨‍💻 Support

For issues or questions:
1. Check the [Troubleshooting](#troubleshooting) section
2. Review the `INSTALLATION_GUIDE.md`
3. Ensure all system requirements are met

## 🙏 Acknowledgments

Built with ❤️ for restaurant owners to simplify their daily operations.

---

**Version:** 1.0.0  
**Last Updated:** December 2025  
**Developed for:** XAMPP Environment
