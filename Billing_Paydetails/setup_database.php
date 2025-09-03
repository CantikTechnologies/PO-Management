<?php
require 'db.php';

echo "<h2>Billing Database Setup</h2>";

try {
    // Check if table exists
    $checkTable = $conn->query("SHOW TABLES LIKE 'billing_details'");
    
    if ($checkTable->num_rows == 0) {
        echo "<p>Creating billing_details table...</p>";
        
        $createTable = "CREATE TABLE IF NOT EXISTS billing_details (
            id INT PRIMARY KEY AUTO_INCREMENT,
            project_details VARCHAR(255),
            cost_center VARCHAR(100),
            customer_po VARCHAR(50),
            remaining_balance_in_po DECIMAL(12,2) DEFAULT 0,
            cantik_invoice_no VARCHAR(50),
            cantik_invoice_date DATE,
            cantik_inv_value_taxable DECIMAL(12,2) DEFAULT 0,
            tds DECIMAL(12,2) DEFAULT 0,
            receivable DECIMAL(12,2) DEFAULT 0,
            against_vendor_inv_number VARCHAR(50),
            payment_recpt_date DATE,
            payment_advise_no VARCHAR(50),
            vendor_name VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($createTable)) {
            echo "<p style='color: green;'>✓ Table created successfully!</p>";
            
            // Insert sample data with correct calculations
            $sampleData = "INSERT INTO billing_details (
                project_details, cost_center, customer_po, remaining_balance_in_po, cantik_invoice_no, 
                cantik_invoice_date, cantik_inv_value_taxable, tds, receivable, 
                against_vendor_inv_number, payment_recpt_date, payment_advise_no, vendor_name
            ) VALUES 
            (
                'Raptakos Resource Deployment - Anuj Kushwaha',
                'Raptakos PT',
                '4500095281',
                0.00,
                'CTPL/24-25/1312',
                '2025-02-05',
                35225.00,
                704.50,
                40861.00,
                'MAH/464/24-25',
                '2025-06-03',
                '',
                'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ),
            (
                'Raptakos Resource Deployment - Anuj Kushwaha',
                'Raptakos PT',
                '4500095281',
                0.00,
                'CTPL/24-25/1521',
                '2025-03-19',
                68250.00,
                1365.00,
                79170.00,
                'MAH/558/24-25',
                '2025-04-16',
                '',
                'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ),
            (
                'Raptakos Resource Deployment - Anuj Kushwaha',
                'Raptakos PT',
                '4500095281',
                0.00,
                'CTPL/25-26/128',
                '2025-04-28',
                68250.00,
                1365.00,
                79170.00,
                'MAH/650/24-25',
                '2025-05-28',
                '',
                'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ),
            (
                'Raptakos Resource Deployment - Anuj Kushwaha',
                'Raptakos PT',
                '4500098831',
                0.00,
                'CTPL/25-26/306',
                '2025-06-21',
                68250.00,
                1365.00,
                79170.00,
                'M/2526/Jun/024',
                '2025-07-21',
                '1400005222',
                'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ),
            (
                'Raptakos Resource Deployment - Anuj Kushwaha',
                'Raptakos PT',
                '4500098831',
                0.00,
                'CTPL/25-26/307',
                '2025-06-21',
                68250.00,
                1365.00,
                79170.00,
                'M/2526/Jun/025',
                '2025-07-21',
                '1400005222',
                'VRATA TECH SOLUTIONS PRIVATE LIMITED'
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
        echo "<p style='color: green;'>✓ Table 'billing_details' already exists!</p>";
        
        // Check if we need to add missing columns
        $columns = $conn->query("SHOW COLUMNS FROM billing_details");
        $existingColumns = [];
        while ($col = $columns->fetch_assoc()) {
            $existingColumns[] = $col['Field'];
        }
        
        $requiredColumns = [
            'remaining_balance_in_po' => 'DECIMAL(12,2) DEFAULT 0',
            'payment_advise_no' => 'VARCHAR(50)'
        ];
        
        foreach ($requiredColumns as $colName => $colType) {
            if (!in_array($colName, $existingColumns)) {
                $alterQuery = "ALTER TABLE billing_details ADD COLUMN $colName $colType";
                if ($conn->query($alterQuery)) {
                    echo "<p style='color: green;'>✓ Added missing column: $colName</p>";
                } else {
                    echo "<p style='color: orange;'>⚠ Failed to add column $colName: " . $conn->error . "</p>";
                }
            }
        }
    }
    
    // Show table structure
    echo "<h3>Table Structure:</h3>";
    $structure = $conn->query("DESCRIBE billing_details");
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
    $count = $conn->query("SELECT COUNT(*) as total FROM billing_details");
    $total = $count->fetch_assoc()['total'];
    echo "<p><strong>Total entries in table: " . $total . "</strong></p>";
    
    if ($total > 0) {
        echo "<h3>Sample Data:</h3>";
        $data = $conn->query("SELECT * FROM billing_details LIMIT 3");
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
    
    // Show totals calculation
    echo "<h3>Totals Calculation:</h3>";
    $totals = $conn->query("SELECT 
        SUM(cantik_inv_value_taxable) as total_taxable,
        SUM(tds) as total_tds,
        SUM(receivable) as total_receivable,
        COUNT(*) as total_entries
        FROM billing_details");
    
    if ($totals && $totals->num_rows > 0) {
        $totalRow = $totals->fetch_assoc();
        echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
        echo "<p><strong>Total Taxable Value:</strong> ₹" . number_format($totalRow['total_taxable'], 2) . "</p>";
        echo "<p><strong>Total TDS:</strong> ₹" . number_format($totalRow['total_tds'], 2) . "</p>";
        echo "<p><strong>Total Receivable:</strong> ₹" . number_format($totalRow['total_receivable'], 2) . "</p>";
        echo "<p><strong>Total Entries:</strong> " . $totalRow['total_entries'] . "</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php'>← Back to Billing Page</a>";
?>
