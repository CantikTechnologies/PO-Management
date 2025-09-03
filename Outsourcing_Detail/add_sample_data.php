<?php
require 'dp.php';

echo "<h1>Add Sample Outsourcing Data</h1>";

try {
    // Check if table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'outsourcing_detail'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'outsourcing_detail' exists</p>";
        
        // Clear existing data
        $mysqli->query("DELETE FROM outsourcing_detail");
        echo "<p style='color: orange;'>🗑️ Cleared existing data</p>";
        
        // Insert sample data that matches your Excel data
        $sampleData = [
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'ntt_po' => '4500095281',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED',
                'cantik_po_no' => 'CTPL/24-25/1312',
                'cantik_po_date' => '2025-02-05',
                'cantik_po_value' => 35225.00,
                'vendor_inv_frequency' => 'Monthly',
                'vendor_inv_number' => 'MAH/464/24-25',
                'vendor_inv_date' => '2025-02-05',
                'vendor_inv_value' => 35225.00,
                'tds_ded' => 704.50,
                'net_payable' => 40861.00,
                'payment_status' => 'Unpaid',
                'payment_value' => 0.00,
                'payment_date' => null,
                'pending_payment' => 40861.00,
                'remarks' => 'Sample entry 1'
            ],
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'ntt_po' => '4500095281',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED',
                'cantik_po_no' => 'CTPL/24-25/1521',
                'cantik_po_date' => '2025-03-19',
                'cantik_po_value' => 68250.00,
                'vendor_inv_frequency' => 'Monthly',
                'vendor_inv_number' => 'MAH/558/24-25',
                'vendor_inv_date' => '2025-03-19',
                'vendor_inv_value' => 68250.00,
                'tds_ded' => 1365.00,
                'net_payable' => 79170.00,
                'payment_status' => 'Unpaid',
                'payment_value' => 0.00,
                'payment_date' => null,
                'pending_payment' => 79170.00,
                'remarks' => 'Sample entry 2'
            ],
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'ntt_po' => '4500095281',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED',
                'cantik_po_no' => 'CTPL/25-26/128',
                'cantik_po_date' => '2025-04-28',
                'cantik_po_value' => 68250.00,
                'vendor_inv_frequency' => 'Monthly',
                'vendor_inv_number' => 'MAH/650/24-25',
                'vendor_inv_date' => '2025-04-28',
                'vendor_inv_value' => 68250.00,
                'tds_ded' => 1365.00,
                'net_payable' => 79170.00,
                'payment_status' => 'Unpaid',
                'payment_value' => 0.00,
                'payment_date' => null,
                'pending_payment' => 79170.00,
                'remarks' => 'Sample entry 3'
            ],
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'ntt_po' => '4500098831',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED',
                'cantik_po_no' => 'CTPL/25-26/306',
                'cantik_po_date' => '2025-06-21',
                'cantik_po_value' => 68250.00,
                'vendor_inv_frequency' => 'Monthly',
                'vendor_inv_number' => 'M/2526/Jun/024',
                'vendor_inv_date' => '2025-06-21',
                'vendor_inv_value' => 68250.00,
                'tds_ded' => 1365.00,
                'net_payable' => 79170.00,
                'payment_status' => 'Paid',
                'payment_value' => 79170.00,
                'payment_date' => '2025-07-21',
                'pending_payment' => 0.00,
                'remarks' => 'Sample entry 4 - Paid'
            ],
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'ntt_po' => '4500098831',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED',
                'cantik_po_no' => 'CTPL/25-26/307',
                'cantik_po_date' => '2025-06-21',
                'cantik_po_value' => 68250.00,
                'vendor_inv_frequency' => 'Monthly',
                'vendor_inv_number' => 'M/2526/Jun/025',
                'vendor_inv_date' => '2025-06-21',
                'vendor_inv_value' => 68250.00,
                'tds_ded' => 1365.00,
                'net_payable' => 79170.00,
                'payment_status' => 'Paid',
                'payment_value' => 79170.00,
                'payment_date' => '2025-07-21',
                'pending_payment' => 0.00,
                'remarks' => 'Sample entry 5 - Paid'
            ]
        ];
        
        // Insert the sample data
        $insertedCount = 0;
        foreach ($sampleData as $data) {
            $sql = "INSERT INTO outsourcing_detail (
                project_details, cost_center, ntt_po, vendor_name, cantik_po_no, 
                cantik_po_date, cantik_po_value, vendor_inv_frequency, vendor_inv_number, 
                vendor_inv_date, vendor_inv_value, tds_ded, net_payable, payment_status, 
                payment_value, payment_date, pending_payment, remarks
            ) VALUES (
                '" . $mysqli->real_escape_string($data['project_details']) . "',
                '" . $mysqli->real_escape_string($data['cost_center']) . "',
                '" . $mysqli->real_escape_string($data['ntt_po']) . "',
                '" . $mysqli->real_escape_string($data['vendor_name']) . "',
                '" . $mysqli->real_escape_string($data['cantik_po_no']) . "',
                '" . $data['cantik_po_date'] . "',
                " . $data['cantik_po_value'] . ",
                '" . $mysqli->real_escape_string($data['vendor_inv_frequency']) . "',
                '" . $mysqli->real_escape_string($data['vendor_inv_number']) . "',
                '" . $data['vendor_inv_date'] . "',
                " . $data['vendor_inv_value'] . ",
                " . $data['tds_ded'] . ",
                " . $data['net_payable'] . ",
                '" . $mysqli->real_escape_string($data['payment_status']) . "',
                " . $data['payment_value'] . ",
                " . ($data['payment_date'] ? "'" . $data['payment_date'] . "'" : 'NULL') . ",
                " . $data['pending_payment'] . ",
                '" . $mysqli->real_escape_string($data['remarks']) . "'
            )";
            
            if ($mysqli->query($sql)) {
                $insertedCount++;
                echo "<p style='color: green;'>✅ Inserted: " . $data['vendor_inv_number'] . " - Vendor Inv: ₹" . $data['vendor_inv_value'] . ", Net Payable: ₹" . $data['net_payable'] . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to insert: " . $data['vendor_inv_number'] . " - " . $mysqli->error . "</p>";
            }
        }
        
        echo "<p style='color: green;'>✅ Successfully inserted $insertedCount records</p>";
        
        // Show totals
        $totals = $mysqli->query("SELECT 
            SUM(vendor_inv_value) as total_vendor_inv,
            SUM(net_payable) as total_net_payable,
            SUM(payment_value) as total_payment_value,
            SUM(pending_payment) as total_pending_payment,
            COUNT(*) as total_entries
            FROM outsourcing_detail");
        
        if ($totals && $totals->num_rows > 0) {
            $totalRow = $totals->fetch_assoc();
            echo "<h3>Totals:</h3>";
            echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
            echo "<p><strong>Total Vendor Inv Value:</strong> ₹" . number_format($totalRow['total_vendor_inv'], 2) . "</p>";
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
echo "<p><a href='index.php'>← Back to Outsourcing Page (Should now show data)</a></p>";
echo "<p><a href='test_data.php'>Test Data</a></p>";
echo "<p><a href='setup_database.php'>Setup Database</a></p>";
?>
