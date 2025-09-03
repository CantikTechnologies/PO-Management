# PO Details Management System

A comprehensive web-based system for managing Purchase Order (PO) details, project information, and vendor relationships.

## Features

- **Add/Edit PO Details**: Complete form for creating and updating purchase orders
- **View All POs**: Comprehensive table view with search and filtering capabilities
- **Database Management**: Easy setup and management of the PO details database
- **Summary Statistics**: Dashboard with totals, breakdowns, and analytics
- **Modern UI**: Responsive design with Bootstrap 5 and custom CSS
- **Excel Date Support**: Handles Excel serial date numbers for compatibility

## Files Structure

```
PO_Details/
├── index.php              # Main form page for adding/editing POs
├── view.php               # View all POs in table format
├── save.php               # API endpoint for saving PO data
├── delete.php             # API endpoint for deleting POs
├── get.php                # API endpoint for retrieving PO by ID
├── list.php               # API endpoint for listing POs with filters
├── totals.php             # Summary statistics and analytics
├── setup_database.php     # Database setup and table creation
├── test_db.php            # Database connection testing
├── db.php                 # Database connection configuration
├── po_details_new.css     # Main stylesheet
├── po_details_new.js      # JavaScript functionality
├── cantik_logo.png        # Company logo
└── README.md              # This file
```

## Database Schema

The system uses the `po_details` table with the following structure:

- `id`: Auto-increment primary key
- `project_description`: Detailed project description (required)
- `cost_center`: Cost center identifier (required)
- `sow_number`: Statement of Work number (required)
- `start_date`: Project start date (Excel serial number)
- `end_date`: Project end date (Excel serial number)
- `po_number`: Unique PO number (required, unique)
- `po_date`: PO creation date (Excel serial number)
- `po_value`: PO monetary value (required)
- `billing_frequency`: Billing frequency (Weekly, Bi-weekly, Monthly, etc.)
- `target_gm`: Target gross margin percentage (0.0500 = 5%)
- `pending_amount`: Pending/remaining amount
- `po_status`: PO status (Active, Pending, Completed, Cancelled, On Hold)
- `remarks`: Additional notes and comments
- `vendor_name`: Vendor/supplier name
- `created_at`: Record creation timestamp
- `updated_at`: Record update timestamp

## Setup Instructions

1. **Database Setup**: Run `setup_database.php` to create the required table and sample data
2. **Connection Test**: Use `test_db.php` to verify database connectivity
3. **Start Using**: Navigate to `index.php` to begin adding PO details

## API Endpoints

### Save PO Details
- **URL**: `save.php`
- **Method**: POST
- **Purpose**: Create new PO or update existing one

### Delete PO
- **URL**: `delete.php`
- **Method**: POST
- **Purpose**: Delete PO by ID

### Get PO Details
- **URL**: `get.php?id={id}`
- **Method**: GET
- **Purpose**: Retrieve PO details by ID

### List POs
- **URL**: `list.php`
- **Method**: GET
- **Parameters**:
  - `limit`: Number of records to return
  - `offset`: Starting position for pagination
  - `status`: Filter by PO status
  - `cost_center`: Filter by cost center
  - `vendor`: Filter by vendor name

## Usage

### Adding a New PO
1. Navigate to the main page (`index.php`)
2. Fill in all required fields (marked with *)
3. Set appropriate dates and values
4. Click "Save PO Details"

### Viewing All POs
1. Click "View All POs" button from the main page
2. Use the search box to filter results
3. Click edit/delete buttons for individual POs

### Database Management
1. Click "Setup Database" button to initialize the system
2. Use "Test Database" to verify connectivity
3. View summary statistics in the totals page

## Technical Details

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Framework**: Bootstrap 5.3.0
- **Icons**: Font Awesome 6.4.0
- **Date Handling**: Excel serial number conversion for compatibility

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Security Features

- SQL injection prevention using prepared statements
- Input validation and sanitization
- CSRF protection through form tokens
- Error handling without exposing sensitive information

## Customization

The system can be easily customized by:
- Modifying the CSS variables in `po_details_new.css`
- Adding new fields to the database schema
- Extending the JavaScript functionality
- Customizing the form validation rules

## Support

For technical support or customization requests, please contact the development team.

## Version History

- **v1.0.0**: Initial release with basic PO management functionality
- Includes form handling, database operations, and view capabilities
