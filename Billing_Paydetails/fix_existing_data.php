<?php
require 'db.php';

echo "<h1>Fix Existing Billing Data</h1>";

try {
    // Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'billing_details'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'billing_details' exists</p>";
        
        // Get all existing records
        $result = $conn->query("SELECT * FROM billing_details");
        if ($result && $result->num_rows > 0) {
            echo "<p>Found " . $result->num_rows . " records to fix...</p>";
            
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $taxable = floatval($row['cantik_inv_value_taxable']);
                
                // Calculate correct values
                $tds = round($taxable * 0.02, 2);
                $receivable = round(($taxable * 1.18) - $tds, 2);
                
                // Convert Excel serial date to proper date
                $invoiceDate = $row['cantik_invoice_date'];
                $paymentDate = $row['payment_recpt_date'];
                
                // If date looks like Excel serial number, convert it
                if (is_numeric($invoiceDate) && $invoiceDate > 40000) {
                    // Excel serial date conversion (Excel epoch starts from 1900-01-01)
                    $excelEpoch = new DateTime('1900-01-01');
                    $excelEpoch->add(new DateInterval('P' . (intval($invoiceDate) - 2) . 'D'));
                    $invoiceDate = $excelEpoch->format('Y-m-d');
                }
                
                if (is_numeric($paymentDate) && $paymentDate > 40000) {
                    $excelEpoch = new DateTime('1900-01-01');
                    $excelEpoch->add(new DateInterval('P' . (intval($paymentDate) - 2) . 'D'));
                    $paymentDate = $excelEpoch->format('Y-m-d');
                }
                
                // Update the record
                $updateSql = "UPDATE billing_details SET 
                    tds = ?, 
                    receivable = ?, 
                    cantik_invoice_date = ?, 
                    payment_recpt_date = ?,
                    remaining_balance_in_po = 0,
                    payment_advise_no = CASE 
                        WHEN customer_po = '4500098831' THEN '1400005222'
                        ELSE ''
                    END
                    WHERE id = ?";
                
                $stmt = $conn->prepare($updateSql);
                if ($stmt) {
                    $stmt->bind_param("ddssi", $tds, $receivable, $invoiceDate, $paymentDate, $id);
                    if ($stmt->execute()) {
                        echo "<p style='color: green;'>✅ Fixed record ID $id: TDS=₹$tds, Receivable=₹$receivable, Date=$invoiceDate</p>";
                    } else {
                        echo "<p style='color: red;'>❌ Failed to update record ID $id: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p style='color: red;'>❌ Failed to prepare statement for record ID $id: " . $conn->error . "</p>";
                }
            }
            
            echo "<h3>Data Fix Complete!</h3>";
            
            // Show updated totals
            $totals = $conn->query("SELECT 
                SUM(cantik_inv_value_taxable) as total_taxable,
                SUM(tds) as total_tds,
                SUM(receivable) as total_receivable,
                COUNT(*) as total_entries
                FROM billing_details");
            
            if ($totals && $totals->num_rows > 0) {
                $totalRow = $totals->fetch_assoc();
                echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
                echo "<h4>Updated Totals:</h4>";
                echo "<p><strong>Total Taxable Value:</strong> ₹" . number_format($totalRow['total_taxable'], 2) . "</p>";
                echo "<p><strong>Total TDS:</strong> ₹" . number_format($totalRow['total_tds'], 2) . "</p>";
                echo "<p><strong>Total Receivable:</strong> ₹" . number_format($totalRow['total_receivable'], 2) . "</p>";
                echo "<p><strong>Total Entries:</strong> " . $totalRow['total_entries'] . "</p>";
                echo "</div>";
            }
            
        } else {
            echo "<p style='color: orange;'>⚠ No records found to fix</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Table 'billing_details' does not exist</p>";
        echo "<p><a href='setup_database.php'>Run Database Setup First</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Billing Page</a></p>";
echo "<p><a href='view.php'>View All Entries</a></p>";
echo "<p><a href='test_setup.php'>Test Database</a></p>";
?>
