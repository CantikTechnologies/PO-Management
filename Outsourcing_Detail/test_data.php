<?php
require 'dp.php';

echo "<h1>Outsourcing Detail Data Test</h1>";

try {
    // Check if table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'outsourcing_detail'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'outsourcing_detail' exists</p>";
        
        // Check table structure
        echo "<h3>Table Structure:</h3>";
        $columns = $mysqli->query("SHOW COLUMNS FROM outsourcing_detail");
        echo "<ul>";
        while ($col = $columns->fetch_assoc()) {
            echo "<li>" . $col['Field'] . " - " . $col['Type'] . "</li>";
        }
        echo "</ul>";
        
        // Get record count
        $count = $mysqli->query("SELECT COUNT(*) as total FROM outsourcing_detail");
        $total = $count->fetch_assoc()['total'];
        echo "<p><strong>Total records: $total</strong></p>";
        
        // Show sample data
        if ($total > 0) {
            echo "<h3>Sample Data:</h3>";
            $data = $mysqli->query("SELECT * FROM outsourcing_detail LIMIT 5");
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
        } else {
            echo "<p style='color: orange;'>⚠ No records found in outsourcing_detail table</p>";
        }
        
        // Test the list.php query
        echo "<h3>Testing list.php Query:</h3>";
        $sql = "SELECT o.*, 
                       b.payment_recpt_date AS payment_status_ntt
                FROM outsourcing_detail o
                LEFT JOIN billing_details b
                  ON b.against_vendor_inv_number = o.vendor_inv_number
                ORDER BY o.created_at ASC";
        
        $res = $mysqli->query($sql);
        if ($res) {
            $rows = [];
            while($r = $res->fetch_assoc()){
                $rows[] = $r;
            }
            echo "<p style='color: green;'>✅ Query executed successfully, found " . count($rows) . " records</p>";
            
            if (count($rows) > 0) {
                echo "<h4>Query Results:</h4>";
                echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                $first = true;
                foreach ($rows as $row) {
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
        } else {
            echo "<p style='color: red;'>❌ Query failed: " . $mysqli->error . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Table 'outsourcing_detail' does not exist</p>";
        echo "<p><a href='setup_database.php'>Run Database Setup</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Outsourcing Page</a></p>";
echo "<p><a href='setup_database.php'>Setup Database</a></p>";
?>
