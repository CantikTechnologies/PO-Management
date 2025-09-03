<?php
require 'dp.php';

echo "<h1>Debug Outsourcing Data</h1>";

try {
    // Check if table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'outsourcing_detail'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'outsourcing_detail' exists</p>";
        
        // Get current data
        $data = $mysqli->query("SELECT * FROM outsourcing_detail ORDER BY id");
        
        echo "<h3>Raw Database Data:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Vendor Inv Number</th><th>Vendor Inv Value</th><th>TDS Ded</th><th>Net Payble</th><th>Payment Value</th><th>Pending Payment</th></tr>";
        
        while ($row = $data->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['vendor_inv_number'] . "</td>";
            echo "<td>" . $row['vendor_inv_value'] . " (type: " . gettype($row['vendor_inv_value']) . ")</td>";
            echo "<td>" . $row['tds_ded'] . " (type: " . gettype($row['tds_ded']) . ")</td>";
            echo "<td>" . $row['net_payble'] . " (type: " . gettype($row['net_payble']) . ")</td>";
            echo "<td>" . $row['payment_value'] . " (type: " . gettype($row['payment_value']) . ")</td>";
            echo "<td>" . $row['pending_payment'] . " (type: " . gettype($row['pending_payment']) . ")</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test the list.php query exactly as it runs
        echo "<h3>Testing list.php Query:</h3>";
        $sql = "SELECT * FROM outsourcing_detail ORDER BY created_at ASC";
        $res = $mysqli->query($sql);
        $rows = [];
        
        while($r = $res->fetch_assoc()){
            // Convert Excel serial dates to readable format
            if ($r['cantik_po_date'] && is_numeric($r['cantik_po_date'])) {
                $r['cantik_po_date'] = date('Y-m-d', ($r['cantik_po_date'] - 25569) * 86400);
            }
            if ($r['vendor_inv_date'] && is_numeric($r['vendor_inv_date'])) {
                $r['vendor_inv_date'] = date('Y-m-d', ($r['vendor_inv_date'] - 25569) * 86400);
            }
            if ($r['payment_date'] && is_numeric($r['payment_date'])) {
                $r['payment_date'] = date('Y-m-d', ($r['payment_date'] - 25569) * 86400);
            }
            
            // ensure numeric formatting
            $r['vendor_inv_value'] = (float)$r['vendor_inv_value'];
            $r['tds_ded'] = (float)$r['tds_ded'];
            $r['net_payble'] = (float)$r['net_payble'];
            $r['payment_value'] = (float)$r['payment_value'];
            $r['pending_payment'] = (float)$r['pending_payment'];
            
            // Add empty fields for compatibility
            $r['payment_status_ntt'] = '';
            $r['remaining_balance_in_po'] = '';
            
            $rows[] = $r;
        }
        
        echo "<h4>Processed Data (what JavaScript receives):</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Vendor Inv Number</th><th>Vendor Inv Value</th><th>TDS Ded</th><th>Net Payble</th><th>Payment Value</th><th>Pending Payment</th></tr>";
        
        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['vendor_inv_number'] . "</td>";
            echo "<td>" . $row['vendor_inv_value'] . " (type: " . gettype($row['vendor_inv_value']) . ")</td>";
            echo "<td>" . $row['tds_ded'] . " (type: " . gettype($row['tds_ded']) . ")</td>";
            echo "<td>" . $row['net_payble'] . " (type: " . gettype($row['net_payble']) . ")</td>";
            echo "<td>" . $row['payment_value'] . " (type: " . gettype($row['payment_value']) . ")</td>";
            echo "<td>" . $row['pending_payment'] . " (type: " . gettype($row['pending_payment']) . ")</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show JSON output
        echo "<h4>JSON Output:</h4>";
        echo "<pre>" . json_encode(['success'=>true,'data'=>$rows], JSON_PRETTY_PRINT) . "</pre>";
        
    } else {
        echo "<p style='color: red;'>❌ Table 'outsourcing_detail' does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Outsourcing Page</a></p>";
?>
