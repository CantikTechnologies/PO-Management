<?php
require 'db.php';

echo "<h1>Check Current Billing Data</h1>";

try {
    // Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'billing_details'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'billing_details' exists</p>";
        
        // Get all records
        $result = $conn->query("SELECT * FROM billing_details ORDER BY id");
        if ($result && $result->num_rows > 0) {
            echo "<h3>Current Data:</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Invoice No</th><th>Invoice Date</th><th>Taxable</th><th>TDS</th><th>Receivable</th><th>Payment Date</th><th>Payment Advise</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['cantik_invoice_no'] . "</td>";
                echo "<td>" . $row['cantik_invoice_date'] . "</td>";
                echo "<td>₹" . number_format($row['cantik_inv_value_taxable'], 2) . "</td>";
                echo "<td>₹" . number_format($row['tds'], 2) . "</td>";
                echo "<td>₹" . number_format($row['receivable'], 2) . "</td>";
                echo "<td>" . $row['payment_recpt_date'] . "</td>";
                echo "<td>" . ($row['payment_advise_no'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Show totals
            $totals = $conn->query("SELECT 
                SUM(cantik_inv_value_taxable) as total_taxable,
                SUM(tds) as total_tds,
                SUM(receivable) as total_receivable,
                COUNT(*) as total_entries
                FROM billing_details");
            
            if ($totals && $totals->num_rows > 0) {
                $totalRow = $totals->fetch_assoc();
                echo "<h3>Current Totals:</h3>";
                echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
                echo "<p><strong>Total Taxable Value:</strong> ₹" . number_format($totalRow['total_taxable'], 2) . "</p>";
                echo "<p><strong>Total TDS:</strong> ₹" . number_format($totalRow['total_tds'], 2) . "</p>";
                echo "<p><strong>Total Receivable:</strong> ₹" . number_format($totalRow['total_receivable'], 2) . "</p>";
                echo "<p><strong>Total Entries:</strong> " . $totalRow['total_entries'] . "</p>";
                echo "</div>";
            }
            
        } else {
            echo "<p style='color: orange;'>⚠ No records found</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Table 'billing_details' does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='fix_existing_data.php'>Fix Existing Data</a></p>";
echo "<p><a href='setup_database.php'>Run Database Setup</a></p>";
echo "<p><a href='index.php'>← Back to Billing Page</a></p>";
?>
