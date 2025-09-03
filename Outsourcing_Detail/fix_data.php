<?php
require 'dp.php';

echo "<h1>Fix Outsourcing Data</h1>";

try {
    // Check if table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'outsourcing_detail'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'outsourcing_detail' exists</p>";
        
        // Get current data
        $data = $mysqli->query("SELECT * FROM outsourcing_detail");
        $updatedCount = 0;
        
        while ($row = $data->fetch_assoc()) {
            $id = $row['id'];
            $vendorInvValue = (float)$row['vendor_inv_value'];
            
            // Calculate TDS (2% of vendor_inv_value)
            $tdsDed = $vendorInvValue * 0.02;
            
            // Calculate Net Payable (vendor_inv_value * 1.18 - tds_ded)
            $netPayble = ($vendorInvValue * 1.18) - $tdsDed;
            
            // Calculate Pending Payment (net_payble - payment_value)
            $pendingPayment = $netPayble - (float)$row['payment_value'];
            
            // Update the record
            $updateSql = "UPDATE outsourcing_detail SET 
                tds_ded = $tdsDed,
                net_payble = $netPayble,
                pending_payment = $pendingPayment
                WHERE id = $id";
            
            if ($mysqli->query($updateSql)) {
                $updatedCount++;
                echo "<p style='color: green;'>✅ Updated ID $id: Vendor Inv: ₹" . number_format($vendorInvValue, 2) . 
                     ", TDS: ₹" . number_format($tdsDed, 2) . 
                     ", Net Payable: ₹" . number_format($netPayble, 2) . 
                     ", Pending: ₹" . number_format($pendingPayment, 2) . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update ID $id: " . $mysqli->error . "</p>";
            }
        }
        
        echo "<p style='color: green;'>✅ Successfully updated $updatedCount records</p>";
        
        // Show totals after fix
        $totals = $mysqli->query("SELECT 
            SUM(vendor_inv_value) as total_vendor_inv,
            SUM(tds_ded) as total_tds,
            SUM(net_payble) as total_net_payable,
            SUM(payment_value) as total_payment_value,
            SUM(pending_payment) as total_pending_payment,
            COUNT(*) as total_entries
            FROM outsourcing_detail");
        
        if ($totals && $totals->num_rows > 0) {
            $totalRow = $totals->fetch_assoc();
            echo "<h3>Updated Totals:</h3>";
            echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
            echo "<p><strong>Total Vendor Inv Value:</strong> ₹" . number_format($totalRow['total_vendor_inv'], 2) . "</p>";
            echo "<p><strong>Total TDS:</strong> ₹" . number_format($totalRow['total_tds'], 2) . "</p>";
            echo "<p><strong>Total Net Payable:</strong> ₹" . number_format($totalRow['total_net_payable'], 2) . "</p>";
            echo "<p><strong>Total Payment Value:</strong> ₹" . number_format($totalRow['total_payment_value'], 2) . "</p>";
            echo "<p><strong>Total Pending Payment:</strong> ₹" . number_format($totalRow['total_pending_payment'], 2) . "</p>";
            echo "<p><strong>Total Entries:</strong> " . $totalRow['total_entries'] . "</p>";
            echo "</div>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Table 'outsourcing_detail' does not exist</p>";
        echo "<p><a href='setup_database.php'>Run Database Setup First</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Outsourcing Page (Should now show correct data)</a></p>";
echo "<p><a href='test_data.php'>Test Data</a></p>";
?>
