<?php
require 'db.php';

echo "<h1>Reset Billing Data with Correct Values</h1>";

try {
    // Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'billing_details'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'billing_details' exists</p>";
        
        // Clear existing data
        $conn->query("DELETE FROM billing_details");
        echo "<p style='color: orange;'>🗑️ Cleared existing data</p>";
        
        // Insert correct data with proper calculations
        $correctData = [
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'customer_po' => '4500095281',
                'remaining_balance_in_po' => 0.00,
                'cantik_invoice_no' => 'CTPL/24-25/1312',
                'cantik_invoice_date' => '2025-02-05',
                'cantik_inv_value_taxable' => 35225.00,
                'tds' => 704.50, // 35225 * 0.02
                'receivable' => 40861.00, // (35225 * 1.18) - 704.50
                'against_vendor_inv_number' => 'MAH/464/24-25',
                'payment_recpt_date' => '2025-06-03',
                'payment_advise_no' => '',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ],
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'customer_po' => '4500095281',
                'remaining_balance_in_po' => 0.00,
                'cantik_invoice_no' => 'CTPL/24-25/1521',
                'cantik_invoice_date' => '2025-03-19',
                'cantik_inv_value_taxable' => 68250.00,
                'tds' => 1365.00, // 68250 * 0.02
                'receivable' => 79170.00, // (68250 * 1.18) - 1365.00
                'against_vendor_inv_number' => 'MAH/558/24-25',
                'payment_recpt_date' => '2025-04-16',
                'payment_advise_no' => '',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ],
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'customer_po' => '4500095281',
                'remaining_balance_in_po' => 0.00,
                'cantik_invoice_no' => 'CTPL/25-26/128',
                'cantik_invoice_date' => '2025-04-28',
                'cantik_inv_value_taxable' => 68250.00,
                'tds' => 1365.00, // 68250 * 0.02
                'receivable' => 79170.00, // (68250 * 1.18) - 1365.00
                'against_vendor_inv_number' => 'MAH/650/24-25',
                'payment_recpt_date' => '2025-05-28',
                'payment_advise_no' => '',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ],
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'customer_po' => '4500098831',
                'remaining_balance_in_po' => 0.00,
                'cantik_invoice_no' => 'CTPL/25-26/306',
                'cantik_invoice_date' => '2025-06-21',
                'cantik_inv_value_taxable' => 68250.00,
                'tds' => 1365.00, // 68250 * 0.02
                'receivable' => 79170.00, // (68250 * 1.18) - 1365.00
                'against_vendor_inv_number' => 'M/2526/Jun/024',
                'payment_recpt_date' => '2025-07-21',
                'payment_advise_no' => '1400005222',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ],
            [
                'project_details' => 'Raptakos Resource Deployment - Anuj Kushwaha',
                'cost_center' => 'Raptakos PT',
                'customer_po' => '4500098831',
                'remaining_balance_in_po' => 0.00,
                'cantik_invoice_no' => 'CTPL/25-26/307',
                'cantik_invoice_date' => '2025-06-21',
                'cantik_inv_value_taxable' => 68250.00,
                'tds' => 1365.00, // 68250 * 0.02
                'receivable' => 79170.00, // (68250 * 1.18) - 1365.00
                'against_vendor_inv_number' => 'M/2526/Jun/025',
                'payment_recpt_date' => '2025-07-21',
                'payment_advise_no' => '1400005222',
                'vendor_name' => 'VRATA TECH SOLUTIONS PRIVATE LIMITED'
            ]
        ];
        
        // Insert the correct data
        $insertSql = "INSERT INTO billing_details (
            project_details, cost_center, customer_po, remaining_balance_in_po, cantik_invoice_no, 
            cantik_invoice_date, cantik_inv_value_taxable, tds, receivable, 
            against_vendor_inv_number, payment_recpt_date, payment_advise_no, vendor_name
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insertSql);
        if ($stmt) {
            $insertedCount = 0;
            foreach ($correctData as $data) {
                $stmt->bind_param("sssdsdddssss", 
                    $data['project_details'],
                    $data['cost_center'],
                    $data['customer_po'],
                    $data['remaining_balance_in_po'],
                    $data['cantik_invoice_no'],
                    $data['cantik_invoice_date'],
                    $data['cantik_inv_value_taxable'],
                    $data['tds'],
                    $data['receivable'],
                    $data['against_vendor_inv_number'],
                    $data['payment_recpt_date'],
                    $data['payment_advise_no'],
                    $data['vendor_name']
                );
                
                if ($stmt->execute()) {
                    $insertedCount++;
                    echo "<p style='color: green;'>✅ Inserted: " . $data['cantik_invoice_no'] . " - TDS: ₹" . $data['tds'] . ", Receivable: ₹" . $data['receivable'] . "</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to insert: " . $data['cantik_invoice_no'] . " - " . $stmt->error . "</p>";
                }
            }
            $stmt->close();
            
            echo "<p style='color: green;'>✅ Successfully inserted $insertedCount records</p>";
            
            // Show final totals
            $totals = $conn->query("SELECT 
                SUM(cantik_inv_value_taxable) as total_taxable,
                SUM(tds) as total_tds,
                SUM(receivable) as total_receivable,
                COUNT(*) as total_entries
                FROM billing_details");
            
            if ($totals && $totals->num_rows > 0) {
                $totalRow = $totals->fetch_assoc();
                echo "<h3>Final Totals (Should Match Excel):</h3>";
                echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
                echo "<p><strong>Total Taxable Value:</strong> ₹" . number_format($totalRow['total_taxable'], 2) . " (Expected: ₹308,225.00)</p>";
                echo "<p><strong>Total TDS:</strong> ₹" . number_format($totalRow['total_tds'], 2) . " (Expected: ₹6,164.50)</p>";
                echo "<p><strong>Total Receivable:</strong> ₹" . number_format($totalRow['total_receivable'], 2) . " (Expected: ₹357,541.00)</p>";
                echo "<p><strong>Total Entries:</strong> " . $totalRow['total_entries'] . " (Expected: 5)</p>";
                echo "</div>";
                
                // Check if totals match expected values
                $expectedTaxable = 308225.00;
                $expectedTDS = 6164.50;
                $expectedReceivable = 357541.00;
                
                if (abs($totalRow['total_taxable'] - $expectedTaxable) < 0.01) {
                    echo "<p style='color: green;'>✅ Taxable total matches Excel!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Taxable total doesn't match Excel</p>";
                }
                
                if (abs($totalRow['total_tds'] - $expectedTDS) < 0.01) {
                    echo "<p style='color: green;'>✅ TDS total matches Excel!</p>";
                } else {
                    echo "<p style='color: red;'>❌ TDS total doesn't match Excel</p>";
                }
                
                if (abs($totalRow['total_receivable'] - $expectedReceivable) < 0.01) {
                    echo "<p style='color: green;'>✅ Receivable total matches Excel!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Receivable total doesn't match Excel</p>";
                }
            }
            
        } else {
            echo "<p style='color: red;'>❌ Failed to prepare insert statement: " . $conn->error . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Table 'billing_details' does not exist</p>";
        echo "<p><a href='setup_database.php'>Run Database Setup First</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Billing Page (Should now show correct data)</a></p>";
echo "<p><a href='view.php'>View All Entries</a></p>";
echo "<p><a href='check_data.php'>Check Current Data</a></p>";
?>
