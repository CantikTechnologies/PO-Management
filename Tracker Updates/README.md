# Tracker Updates System

A comprehensive tracking system for managing action items, requests, and their status updates in the PO Management system.

## Features

- **Action Tracking**: Track actions requested by team members
- **Cost Center Management**: Organize actions by cost centers
- **Status Updates**: Monitor progress with status tracking
- **Completion Tracking**: Record completion dates and remarks
- **Responsive Design**: Works on desktop and mobile devices
- **Search & Filter**: Find specific actions quickly
- **Real-time Updates**: Add, edit, and delete tracker entries

## Database Fields

The system tracks the following information:

1. **Action Requested By** - Who requested the action
2. **Request Date** - When the action was requested
3. **Cost Center** - Which cost center the action belongs to
4. **Action Required** - Description of what needs to be done
5. **Action Owner** - Who is responsible for completing the action
6. **Status of Action** - Current status (Pending, In Progress, Completed, On Hold)
7. **Completion Date** - When the action was completed (if applicable)
8. **Remark** - Additional notes or comments

## Setup Instructions

### 1. Database Setup

1. Make sure XAMPP is running (Apache and MySQL)
2. Navigate to `http://localhost/PO-MANAGEMENT/Tracker%20Updates/setup_database.php`
3. This will automatically create the database and tables
4. Sample data will be inserted for testing

### 2. Access the System

1. Navigate to `http://localhost/PO-MANAGEMENT/Tracker%20Updates/index.php`
2. The system will display all tracker updates
3. Use the "Add New Tracker" button to create new entries

## Sample Data

The system comes pre-loaded with sample data based on your requirements:

- **Naveen's requests** for Raptokos - PT and BMW-OA
- **Maneesh's requests** for Finder Fees - PT and HCIL PT
- Various statuses (Pending, Completed)
- Sample completion dates and remarks

## Usage

### Adding New Tracker Updates

1. Click "Add New Tracker" button
2. Fill in all required fields (marked with *)
3. Select appropriate status
4. Add completion date if completed
5. Include any relevant remarks
6. Click "Add Tracker"

### Editing Tracker Updates

1. Click the "Edit" button on any tracker entry
2. Modify the fields as needed
3. Update status and completion date if applicable
4. Click "Update Tracker"

### Deleting Tracker Updates

1. Click the "Delete" button on any tracker entry
2. Confirm the deletion
3. The entry will be permanently removed

### Searching and Filtering

- **Search**: Use the search box to find specific text
- **Status Filter**: Filter by status (Pending, In Progress, Completed, On Hold)

## File Structure

```
Tracker Updates/
├── index.php              # Main tracker interface
├── setup_database.php     # Database setup script
├── get_task.php          # API for fetching tracker data
├── styles.css            # Styling for the interface
├── view.js               # JavaScript functionality
├── database_setup.sql    # SQL schema
└── README.md             # This file
```

## Database Schema

```sql
CREATE TABLE tracker_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_requested_by VARCHAR(100) NOT NULL,
    request_date DATE NOT NULL,
    cost_center VARCHAR(100) NOT NULL,
    action_required TEXT NOT NULL,
    action_owner VARCHAR(100) NOT NULL,
    status_of_action ENUM('Pending', 'In Progress', 'Completed', 'On Hold') DEFAULT 'Pending',
    completion_date DATE NULL,
    remark TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Status Definitions

- **Pending**: Action has been requested but not yet started
- **In Progress**: Action is currently being worked on
- **Completed**: Action has been finished successfully
- **On Hold**: Action is temporarily suspended

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure XAMPP MySQL service is running
   - Check database credentials in PHP files

2. **Table Not Found**
   - Run `setup_database.php` to create tables
   - Check if database exists

3. **Permission Issues**
   - Ensure web server has read/write access to the directory

### Support

If you encounter any issues:
1. Check XAMPP error logs
2. Verify database connection settings
3. Ensure all required files are present

## Customization

### Adding New Statuses

To add new status values, modify the `status_of_action` ENUM in the database:

```sql
ALTER TABLE tracker_updates 
MODIFY COLUMN status_of_action 
ENUM('Pending', 'In Progress', 'Completed', 'On Hold', 'New Status') 
DEFAULT 'Pending';
```

### Modifying Fields

To add new fields or modify existing ones, update both the database schema and the PHP files accordingly.

## Security Notes

- The system uses prepared statements to prevent SQL injection
- Input validation is implemented for all form fields
- Session management is handled securely
- Always validate and sanitize user inputs in production

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Mobile browsers (responsive design)

---

**Last Updated**: December 2024  
**Version**: 2.0  
**Developer**: PO Management System
