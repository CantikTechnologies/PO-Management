<?php
require 'dp.php';

echo "<h1>Test Outsourcing API</h1>";

try {
    // Test the list.php query directly
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
    
    echo "<h3>Raw Data from Database:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Vendor Inv Value</th><th>TDS Ded</th><th>Net Payble</th><th>Payment Value</th><th>Pending Payment</th></tr>";
    
    foreach ($rows as $row) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>₹" . number_format($row['vendor_inv_value'], 2) . "</td>";
        echo "<td>₹" . number_format($row['tds_ded'], 2) . "</td>";
        echo "<td>₹" . number_format($row['net_payble'], 2) . "</td>";
        echo "<td>₹" . number_format($row['payment_value'], 2) . "</td>";
        echo "<td>₹" . number_format($row['pending_payment'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>JSON Output (what the API returns):</h3>";
    echo "<pre>" . json_encode(['success'=>true,'data'=>$rows], JSON_PRETTY_PRINT) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Outsourcing Page</a></p>";
echo "<p><a href='fix_data.php'>Fix Data</a></p>";
?>
