<?php
require 'db.php';

echo "<h2>PO Details Database Setup</h2>";

try {
    // Check if table exists
    $checkTable = $conn->query("SHOW TABLES LIKE 'po_details'");
    
    if ($checkTable->num_rows == 0) {
        echo "<p>Creating po_details table...</p>";
        
        $createTable = "CREATE TABLE IF NOT EXISTS po_details (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_description VARCHAR(500) NOT NULL,
            cost_center VARCHAR(100) NOT NULL,
            sow_number VARCHAR(100) NOT NULL,
            start_date INT NOT NULL,
            end_date INT NOT NULL,
            po_number VARCHAR(50) UNIQUE NOT NULL,
            po_date INT NOT NULL,
            po_value DECIMAL(15,2) NOT NULL,
            billing_frequency VARCHAR(50) NOT NULL,
            target_gm DECIMAL(5,4) NOT NULL,
            pending_amount DECIMAL(15,2) DEFAULT 0,
            po_status VARCHAR(50) DEFAULT 'Active',
            remarks TEXT,
            vendor_name VARCHAR(200),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_po_number (po_number),
            INDEX idx_project (project_description),
            INDEX idx_cost_center (cost_center),
            INDEX idx_status (po_status)
        )";
        
        if ($conn->query($createTable)) {
            echo "<p style='color: green;'>✓ Table created successfully!</p>";
            
            // Insert sample data
            $sampleData = "INSERT INTO po_details (
                project_description, cost_center, sow_number, start_date, end_date,
                po_number, po_date, po_value, billing_frequency, target_gm,
                pending_amount, po_status, remarks, vendor_name
            ) VALUES 
            (
                'Raptakos Resource Deployment - Anuj Kushwaha',
                'Raptakos PT',
                'SOW-001',
                44927,
                45291,
                '4500095281',
                44927,
                500000.00,
                'Monthly',
                0.0500,
                0.00,
                'Active',
                'Initial PO for resource deployment',
                'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ),
            (
                'Digital Transformation Project - Mumbai Office',
                'Digital IT',
                'SOW-002',
                44958,
                45322,
                '4500095282',
                44958,
                750000.00,
                'Bi-weekly',
                0.0600,
                0.00,
                'Active',
                'Digital transformation initiative',
                'TECHNOLOGY PARTNERS INDIA'
            )";
            
            if ($conn->query($sampleData)) {
                echo "<p style='color: green;'>✓ Sample data inserted successfully!</p>";
            } else {
                echo "<p style='color: orange;'>⚠ Sample data insertion failed: " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Table creation failed: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✓ Table 'po_details' already exists!</p>";
    }
    
    // Show table structure
    echo "<h3>Table Structure:</h3>";
    $structure = $conn->query("DESCRIBE po_details");
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show current data count
    $count = $conn->query("SELECT COUNT(*) as total FROM po_details");
    $total = $count->fetch_assoc()['total'];
    echo "<p><strong>Total entries in table: " . $total . "</strong></p>";
    
    if ($total > 0) {
        echo "<h3>Sample Data:</h3>";
        $data = $conn->query("SELECT * FROM po_details LIMIT 3");
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        
        $first = true;
        while ($row = $data->fetch_assoc()) {
            if ($first) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<th>" . $key . "</th>";
                }
                echo "</tr>";
                $first = false;
            }
            
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . ($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php'>← Back to PO Details Page</a>";
?>
