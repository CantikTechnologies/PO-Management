<?php
require 'dp.php';

echo "<h1>Fix Payment Data in Outsourcing</h1>";

try {
    // Check if table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'outsourcing_detail'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'outsourcing_detail' exists</p>";
        
        // Update the data based on the correct information provided
        $updates = [
            // Entry 1: MAH/464/24-25 - Unpaid
            [
                'id' => 1,
                'vendor_inv_number' => 'MAH/464/24-25',
                'payment_status' => 'Unpaid',
                'payment_value' => 0.00,
                'payment_date' => null,
                'pending_payment' => 38915.00
            ],
            // Entry 2: MAH/558/24-25 - Paid
            [
                'id' => 2,
                'vendor_inv_number' => 'MAH/558/24-25',
                'payment_status' => 'Paid',
                'payment_value' => 75400.00,
                'payment_date' => '2025-04-19',
                'pending_payment' => 0.00
            ],
            // Entry 3: MAH/650/24-25 - Paid
            [
                'id' => 3,
                'vendor_inv_number' => 'MAH/650/24-25',
                'payment_status' => 'Paid',
                'payment_value' => 75400.00,
                'payment_date' => '2025-05-28',
                'pending_payment' => 0.00
            ],
            // Entry 4: M/2526/Jun/024 - Paid
            [
                'id' => 4,
                'vendor_inv_number' => 'M/2526/Jun/024',
                'payment_status' => 'Paid',
                'payment_value' => 75400.00,
                'payment_date' => '2025-08-06',
                'pending_payment' => 0.00
            ],
            // Entry 5: M/2526/Jun/025 - Paid
            [
                'id' => 5,
                'vendor_inv_number' => 'M/2526/Jun/025',
                'payment_status' => 'Paid',
                'payment_value' => 75400.00,
                'payment_date' => '2025-08-06',
                'pending_payment' => 0.00
            ]
        ];
        
        $updatedCount = 0;
        foreach ($updates as $update) {
            $sql = "UPDATE outsourcing_detail SET 
                payment_status_from_ntt = '" . $mysqli->real_escape_string($update['payment_status']) . "',
                payment_value = " . $update['payment_value'] . ",
                payment_date = " . ($update['payment_date'] ? "'" . $update['payment_date'] . "'" : 'NULL') . ",
                pending_payment = " . $update['pending_payment'] . "
                WHERE id = " . $update['id'];
            
            if ($mysqli->query($sql)) {
                $updatedCount++;
                echo "<p style='color: green;'>✅ Updated ID {$update['id']} ({$update['vendor_inv_number']}): " . 
                     "Status: {$update['payment_status']}, Payment: ₹" . number_format($update['payment_value'], 2) . 
                     ", Pending: ₹" . number_format($update['pending_payment'], 2) . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update ID {$update['id']}: " . $mysqli->error . "</p>";
            }
        }
        
        echo "<p style='color: green;'>✅ Successfully updated $updatedCount records</p>";
        
        // Show updated totals
        $totals = $mysqli->query("SELECT 
            SUM(vendor_inv_value) as total_vendor_inv,
            SUM(tds_ded) as total_tds,
            SUM(net_payble) as total_net_payable,
            SUM(payment_value) as total_payment_value,
            SUM(pending_payment) as total_pending_payment,
            COUNT(*) as total_entries,
            SUM(CASE WHEN payment_status_from_ntt = 'Paid' THEN 1 ELSE 0 END) as paid_entries,
            SUM(CASE WHEN payment_status_from_ntt = 'Unpaid' THEN 1 ELSE 0 END) as unpaid_entries
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
            echo "<p><strong>Paid Entries:</strong> " . $totalRow['paid_entries'] . "</p>";
            echo "<p><strong>Unpaid Entries:</strong> " . $totalRow['unpaid_entries'] . "</p>";
            echo "</div>";
        }
        
        // Show detailed breakdown
        echo "<h3>Detailed Breakdown:</h3>";
        $details = $mysqli->query("SELECT 
            vendor_inv_number,
            vendor_inv_value,
            net_payble,
            payment_status_from_ntt,
            payment_value,
            pending_payment
            FROM outsourcing_detail 
            ORDER BY id");
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Vendor Inv Number</th><th>Vendor Inv Value</th><th>Net Payable</th><th>Status</th><th>Payment Value</th><th>Pending Payment</th></tr>";
        
        while ($row = $details->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['vendor_inv_number'] . "</td>";
            echo "<td>₹" . number_format($row['vendor_inv_value'], 2) . "</td>";
            echo "<td>₹" . number_format($row['net_payble'], 2) . "</td>";
            echo "<td>" . ($row['payment_status_from_ntt'] ?: 'Unpaid') . "</td>";
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
echo "<p><a href='index.php'>← Back to Outsourcing Page (Should now show correct payment data)</a></p>";
echo "<p><a href='test_api.php'>Test API</a></p>";
?>
