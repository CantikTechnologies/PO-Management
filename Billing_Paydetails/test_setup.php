<?php
// Test file to verify billing database setup
require 'db.php';

echo "<h1>Billing Database Test</h1>";

try {
    // Test database connection
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Database connection failed: " . $conn->connect_error . "</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'billing_details'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'billing_details' exists</p>";
        
        // Check table structure
        $columns = $conn->query("SHOW COLUMNS FROM billing_details");
        $existingColumns = [];
        while ($col = $columns->fetch_assoc()) {
            $existingColumns[] = $col['Field'];
        }
        
        echo "<h3>Table Columns:</h3>";
        echo "<ul>";
        foreach ($existingColumns as $col) {
            echo "<li>$col</li>";
        }
        echo "</ul>";
        
        // Check if required columns exist
        $requiredColumns = ['remaining_balance_in_po', 'payment_advise_no'];
        foreach ($requiredColumns as $reqCol) {
            if (in_array($reqCol, $existingColumns)) {
                echo "<p style='color: green;'>✅ Column '$reqCol' exists</p>";
            } else {
                echo "<p style='color: red;'>❌ Column '$reqCol' missing</p>";
            }
        }
        
        // Get record count
        $count = $conn->query("SELECT COUNT(*) as total FROM billing_details");
        $total = $count->fetch_assoc()['total'];
        echo "<p><strong>Total records: $total</strong></p>";
        
        // Show sample data
        if ($total > 0) {
            echo "<h3>Sample Data:</h3>";
            $data = $conn->query("SELECT * FROM billing_details LIMIT 3");
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            
            $first = true;
            while ($row = $data->fetch_assoc()) {
                if ($first) {
                    echo "<tr>";
                    foreach ($row as $key => $value) {
                        echo "<th>$key</th>";
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
        
        // Test calculations
        echo "<h3>Calculations Test:</h3>";
        $testTaxable = 35225.00;
        $testTDS = $testTaxable * 0.02;
        $testReceivable = ($testTaxable * 1.18) - $testTDS;
        
        echo "<p><strong>Test Calculation:</strong></p>";
        echo "<p>Taxable: ₹" . number_format($testTaxable, 2) . "</p>";
        echo "<p>TDS (2%): ₹" . number_format($testTDS, 2) . "</p>";
        echo "<p>Receivable (Taxable * 1.18 - TDS): ₹" . number_format($testReceivable, 2) . "</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Table 'billing_details' does not exist</p>";
        echo "<p><a href='setup_database.php'>Run Database Setup</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Billing Page</a></p>";
echo "<p><a href='setup_database.php'>Run Database Setup</a></p>";
echo "<p><a href='view.php'>View All Entries</a></p>";
?>
