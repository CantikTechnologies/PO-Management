<?php
require 'dp.php';

echo "<h1>Check Net Payable Data</h1>";

try {
    // Check if table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'outsourcing_detail'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'outsourcing_detail' exists</p>";
        
        // Get current data
        $data = $mysqli->query("SELECT * FROM outsourcing_detail ORDER BY id");
        
        echo "<h3>Current Data:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Vendor Inv Number</th><th>Vendor Inv Value</th><th>TDS Ded</th><th>Net Payble</th><th>Payment Value</th><th>Pending Payment</th></tr>";
        
        while ($row = $data->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['vendor_inv_number'] . "</td>";
            echo "<td>₹" . number_format($row['vendor_inv_value'], 2) . "</td>";
            echo "<td>₹" . number_format($row['tds_ded'], 2) . "</td>";
            echo "<td>₹" . number_format($row['net_payble'], 2) . "</td>";
            echo "<td>₹" . number_format($row['payment_value'], 2) . "</td>";
            echo "<td>₹" . number_format($row['pending_payment'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if net_payble is NULL or 0
        $nullCheck = $mysqli->query("SELECT COUNT(*) as null_count FROM outsourcing_detail WHERE net_payble IS NULL OR net_payble = 0");
        $nullCount = $nullCheck->fetch_assoc()['null_count'];
        
        if ($nullCount > 0) {
            echo "<p style='color: orange;'>⚠ Found $nullCount records with NULL or 0 net_payble values</p>";
            
            // Fix the net_payble calculation
            echo "<h3>Fixing Net Payable Calculations:</h3>";
            $fixData = $mysqli->query("SELECT * FROM outsourcing_detail WHERE net_payble IS NULL OR net_payble = 0");
            $fixedCount = 0;
            
            while ($row = $fixData->fetch_assoc()) {
                $id = $row['id'];
                $vendorInvValue = (float)$row['vendor_inv_value'];
                $tdsDed = (float)$row['tds_ded'];
                
                // Calculate Net Payable (vendor_inv_value * 1.18 - tds_ded)
                $netPayble = ($vendorInvValue * 1.18) - $tdsDed;
                
                // Update the record
                $updateSql = "UPDATE outsourcing_detail SET net_payble = $netPayble WHERE id = $id";
                
                if ($mysqli->query($updateSql)) {
                    $fixedCount++;
                    echo "<p style='color: green;'>✅ Fixed ID $id: Vendor Inv: ₹" . number_format($vendorInvValue, 2) . 
                         ", TDS: ₹" . number_format($tdsDed, 2) . 
                         ", Net Payable: ₹" . number_format($netPayble, 2) . "</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to fix ID $id: " . $mysqli->error . "</p>";
                }
            }
            
            echo "<p style='color: green;'>✅ Fixed $fixedCount records</p>";
        } else {
            echo "<p style='color: green;'>✅ All net_payble values are properly calculated</p>";
        }
        
        // Show updated data
        echo "<h3>Updated Data:</h3>";
        $updatedData = $mysqli->query("SELECT * FROM outsourcing_detail ORDER BY id");
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Vendor Inv Number</th><th>Vendor Inv Value</th><th>TDS Ded</th><th>Net Payble</th><th>Payment Value</th><th>Pending Payment</th></tr>";
        
        while ($row = $updatedData->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['vendor_inv_number'] . "</td>";
            echo "<td>₹" . number_format($row['vendor_inv_value'], 2) . "</td>";
            echo "<td>₹" . number_format($row['tds_ded'], 2) . "</td>";
            echo "<td>₹" . number_format($row['net_payble'], 2) . "</td>";
            echo "<td>₹" . number_format($row['payment_value'], 2) . "</td>";
            echo "<td>₹" . number_format($row['pending_payment'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='color: red;'>❌ Table 'outsourcing_detail' does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Outsourcing Page</a></p>";
echo "<p><a href='test_api.php'>Test API</a></p>";
?>
