<?php
// Database setup script for Tracker Updates
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "po_management";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Connection Successful!</h2>";
    
    // Create tracker_updates table
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS finance_tasks (
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
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_request_date (request_date),
        INDEX idx_cost_center (cost_center),
        INDEX idx_status (status_of_action),
        INDEX idx_action_owner (action_owner),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($createTableSQL);
    echo "<p>✅ Tracker updates table created successfully!</p>";
    
    // Check if table has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM finance_tasks");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        // Insert sample data
        $insertSQL = "
        INSERT INTO finance_tasks (action_requested_by, request_date, cost_center, action_required, action_owner, status_of_action, completion_date, remark) VALUES
        ('Naveen', '2025-06-25', 'Raptokos - PT', 'Vratatech - Raptokos PT One month payment to be released immediately', 'Sanjay', 'Pending', NULL, NULL),
        ('Naveen', '2025-06-25', 'Raptokos - PT', 'Renewal to be followed with Priya', 'Sneha', 'Pending', NULL, NULL),
        ('Naveen', '2025-06-25', 'BMW-OA', 'Renewal to be followed with Priya', 'Sneha', 'Pending', NULL, NULL),
        ('Maneesh', '2025-06-25', 'Finder Fees - PT', 'Xpheno GST payment to be released', 'Sanjay', 'Pending', NULL, NULL),
        ('Maneesh', '2025-06-25', 'Finder Fees - PT', 'PO # 4500092198 - Check billing status', 'Sanjay', 'Completed', '2025-06-26', ''),
        ('Maneesh', '2025-06-26', 'Finder Fees - PT', 'PO # 4500092198 - Check if payment has been made to vendor, else release PO', 'Akshay', 'Completed', '2025-06-27', 'Checked Invoice is pending from Vendor Auropro, Request Sanjay to issue PO once approved, hence the vendor can submit their invoice.'),
        ('Maneesh', '2025-06-26', 'HCIL PT', '25-26/10 - WinoVision Invoice - Get the CN Against the invoice', 'Sneha', 'Pending', NULL, NULL)
        ";
        
        $pdo->exec($insertSQL);
        echo "<p>✅ Sample data inserted successfully!</p>";
    } else {
        echo "<p>ℹ️ Table already contains {$result['count']} records.</p>";
    }
    
    // Show table structure
    echo "<h3>Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE finance_tasks");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><a href='index.php'>🚀 Go to Tracker Updates</a></p>";
    
} catch(PDOException $e) {
    echo "<h2>❌ Database Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    
    // Try to create database if it doesn't exist
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        try {
            $pdo = new PDO("mysql:host=$servername", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            echo "<p>✅ Database '$dbname' created successfully!</p>";
            echo "<p><a href='setup_database.php'>🔄 Refresh to continue setup</a></p>";
            
        } catch(PDOException $e2) {
            echo "<p>❌ Failed to create database: " . $e2->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Tracker Updates</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2, h3 {
            color: #333;
        }
        p {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        table {
            background: white;
            border-radius: 5px;
            overflow: hidden;
        }
        th {
            background: #f0f0f0;
            padding: 10px;
        }
        td {
            padding: 8px;
        }
        a {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        a:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <h1>🔧 Tracker Updates Database Setup</h1>
    <p>This script will set up the database and tables for the Tracker Updates system.</p>
</body>
</html> 