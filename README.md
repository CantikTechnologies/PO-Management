# PO Management System

A comprehensive Purchase Order Management System built with PHP and MySQL for managing purchase orders, invoices, and outsourcing details.

## 🚀 Features

- **Purchase Order Management**: Create, edit, and track purchase orders
- **Invoice Management**: Handle customer invoices with automatic TDS and receivable calculations
- **Outsourcing Management**: Track vendor invoices and payments
- **Dashboard**: Real-time overview of all system metrics
- **SO Form**: Consolidated reporting across all modules
- **User Authentication**: Secure login/signup system
- **Finance Tasks**: Task management for finance operations
- **Tracker Updates**: Project tracking and updates

## 📋 System Requirements

- **Web Server**: Apache/Nginx
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher (MariaDB 10.4+)
- **XAMPP**: Recommended for local development

## 🛠️ Installation

### 1. Clone/Download the Project
```bash
# If using git
git clone <repository-url>
# Or download and extract to your web server directory
```

### 2. Database Setup
1. Start your XAMPP/WAMP server
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Create a new database named `po_management_2`
4. Import the database structure:
   ```sql
   -- Import the file: po_management_2.sql
   ```

### 3. Database Configuration
Update the database connection settings in `db.php`:
```php
$host = "localhost";
$user = "root";        // Your MySQL username
$pass = "";            // Your MySQL password
$db   = "po_management_2";
```

### 4. Web Server Setup
- Place the project folder in your web server's document root
- For XAMPP: `C:\xampp\htdocs\PO-Management\`
- For WAMP: `C:\wamp64\www\PO-Management\`

### 5. Access the Application
Open your browser and navigate to:
```
http://localhost/PO-Management/
```

## 👤 Default Login Credentials

**Admin Account:**
- **Email**: admin@123.com
- **Password**: 12345

> ⚠️ **Important**: Change these default credentials after first login for security purposes.

## 📁 Project Structure

```
PO-Management/
├── 1Login_signuppage/          # Authentication system
│   ├── login.php              # Login page
│   ├── signup.php             # Registration page
│   ├── logout.php             # Logout functionality
│   └── db.php                 # Database connection
├── assets/                    # Static assets
│   ├── style.css             # Main stylesheet
│   ├── script.js             # JavaScript functionality
│   └── csv file/             # Sample CSV files
├── invoices/                  # Invoice management
│   ├── add.php               # Add new invoice
│   ├── edit.php              # Edit invoice
│   ├── list.php              # List all invoices
│   └── delete.php            # Delete invoice
├── outsourcing/              # Outsourcing management
│   ├── add.php               # Add outsourcing record
│   ├── edit.php              # Edit outsourcing record
│   ├── list.php              # List outsourcing records
│   └── delete.php            # Delete outsourcing record
├── po_details/               # Purchase Order management
│   ├── add.php               # Add new PO
│   ├── edit.php              # Edit PO
│   ├── list.php              # List all POs
│   └── delete.php            # Delete PO
├── shared/                   # Shared components
│   ├── nav.php               # Navigation component
│   └── nav.css               # Navigation styles
├── Tracker Updates/          # Project tracking system
│   ├── index.php             # Tracker dashboard
│   ├── add.php               # Add new task
│   └── view_tasks.php        # View all tasks
├── tools/                    # Utility tools
│   ├── import_po_detail.php  # CSV import tool
│   └── clean_po_csv.php      # CSV cleaning tool
├── index.php                 # Main dashboard
├── so_form.php              # SO Form report
├── db.php                   # Database configuration
└── README.md                # This file
```

## 🗄️ Database Schema

### Main Tables

1. **purchase_orders**: Core PO data
   - PO details, project information, vendor details
   - Automatic pending amount calculation

2. **invoices**: Customer invoices
   - Automatic TDS (2%) and receivable calculations
   - Links to purchase orders

3. **outsourcing_details**: Vendor invoices
   - Vendor invoice tracking and payment status
   - Automatic net payable calculations

4. **finance_tasks**: Task management
   - Action items and status tracking
   - Cost center and owner assignments

5. **users_login_signup**: User authentication
   - Secure password hashing
   - User management

### Key Features

- **Automatic Calculations**: TDS, receivables, and pending amounts
- **Foreign Key Constraints**: Data integrity between related tables
- **Triggers**: Automatic updates when invoices are added
- **Views**: Optimized queries for reporting

## 🔧 Configuration

### Database Connection
Edit `db.php` to match your database settings:
```php
$host = "localhost";     // Database host
$user = "root";          // Database username
$pass = "";              // Database password
$db   = "po_management_2"; // Database name
```

### Security Settings
- Change default admin credentials
- Update database passwords
- Configure proper file permissions
- Enable HTTPS in production

## 📊 Key Features Explained

### Dashboard
- Real-time statistics for POs, invoices, and outsourcing
- Quick action buttons for common tasks
- Recent activity feed

### Purchase Orders
- Complete PO lifecycle management
- Project and cost center tracking
- Vendor management
- Status tracking (Open, Active, Closed, Cancelled, On Hold)

### Invoices
- Automatic TDS calculation (2%)
- Receivable amount calculation (Taxable + 18% GST - TDS)
- Payment tracking
- Vendor invoice correlation

### Outsourcing
- Vendor invoice management
- Payment status tracking
- Net payable calculations
- Pending payment tracking

### SO Form
- Consolidated reporting view
- Margin analysis
- Variance tracking against target GM

## 🛡️ Security Features

- Password hashing using PHP's `password_hash()`
- Session management
- SQL injection prevention with prepared statements
- Input validation and sanitization
- Access control through authentication

## 🔄 Import/Export Features

### CSV Import
- Import PO details from CSV files
- Data cleaning and validation tools
- Bulk data processing

### Sample Files
- `PO_Detail_template.csv`: Template for PO imports
- `Billing_and_Pay.csv`: Sample billing data
- `Outsourcing.csv`: Sample outsourcing data

## 🚨 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `db.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Login Issues**
   - Check if user exists in `users_login_signup` table
   - Verify password hashing
   - Clear browser cache and cookies

3. **Permission Errors**
   - Check file permissions (755 for directories, 644 for files)
   - Ensure web server has read/write access

4. **CSS/JS Not Loading**
   - Check file paths in HTML
   - Clear browser cache
   - Verify file permissions

### Debug Mode
Enable PHP error reporting for debugging:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📈 Performance Optimization

- Database indexes on frequently queried columns
- Optimized SQL queries with proper joins
- Cached calculations using generated columns
- Efficient pagination for large datasets

## 🔮 Future Enhancements

- [ ] Advanced reporting and analytics
- [ ] Email notifications
- [ ] Document upload functionality
- [ ] API endpoints for integration
- [ ] Mobile-responsive design improvements
- [ ] Advanced user role management
- [ ] Audit trail functionality

## 📞 Support

For technical support or questions:
- Check the troubleshooting section above
- Review the database schema for data structure
- Examine the code comments for implementation details

## 📄 License

This project is proprietary software. All rights reserved.

---

**Version**: 2.0  
**Last Updated**: September 2025  
**Compatibility**: PHP 7.4+, MySQL 5.7+
