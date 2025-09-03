<?php
require 'dp.php';

echo "<h1>Test Outsourcing Stats</h1>";

try {
    // Check if table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'outsourcing_detail'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table 'outsourcing_detail' exists</p>";
        
        // Get total entries count
        $q1 = $mysqli->query("SELECT COUNT(*) as c FROM outsourcing_detail");
        $total = ($q1 && ($r = $q1->fetch_assoc())) ? intval($r['c']) : 0;
        
        // Get total vendor invoice value
        $q2 = $mysqli->query("SELECT COALESCE(SUM(vendor_inv_value), 0) as s FROM outsourcing_detail");
        $inv = ($q2 && ($r = $q2->fetch_assoc())) ? floatval($r['s']) : 0.0;
        
        // Get total net payable
        $q3 = $mysqli->query("SELECT COALESCE(SUM(net_payble), 0) as s FROM outsourcing_detail");
        $netPayable = ($q3 && ($r = $q3->fetch_assoc())) ? floatval($r['s']) : 0.0;
        
        // Get total payment value
        $q4 = $mysqli->query("SELECT COALESCE(SUM(payment_value), 0) as s FROM outsourcing_detail");
        $paymentValue = ($q4 && ($r = $q4->fetch_assoc())) ? floatval($r['s']) : 0.0;
        
        // Calculate total pending payment
        $q5 = $mysqli->query("SELECT COALESCE(SUM(GREATEST(0, net_payble - payment_value)), 0) as s FROM outsourcing_detail");
        $pending = ($q5 && ($r = $q5->fetch_assoc())) ? floatval($r['s']) : 0.0;
        
        echo "<h3>Stats Summary:</h3>";
        echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 10px; margin: 10px 0;'>";
        echo "<p><strong>Total Entries:</strong> " . $total . "</p>";
        echo "<p><strong>Total Vendor Inv Value:</strong> ₹" . number_format($inv, 2) . "</p>";
        echo "<p><strong>Total Net Payable:</strong> ₹" . number_format($netPayable, 2) . "</p>";
        echo "<p><strong>Total Payment Value:</strong> ₹" . number_format($paymentValue, 2) . "</p>";
        echo "<p><strong>Total Pending Payment:</strong> ₹" . number_format($pending, 2) . "</p>";
        echo "</div>";
        
        // Test the totals.php API
        echo "<h3>Testing totals.php API:</h3>";
        $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/totals.php';
        echo "<p><strong>API URL:</strong> <a href='$apiUrl' target='_blank'>$apiUrl</a></p>";
        
        $apiResponse = file_get_contents($apiUrl);
        if ($apiResponse) {
            $apiData = json_decode($apiResponse, true);
            if ($apiData && $apiData['success']) {
                echo "<p style='color: green;'>✅ API Response Success:</p>";
                echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
                echo json_encode($apiData, JSON_PRETTY_PRINT);
                echo "</pre>";
            } else {
                echo "<p style='color: red;'>❌ API Response Error:</p>";
                echo "<pre style='background: #ffe6e6; padding: 10px; border-radius: 5px;'>";
                echo $apiResponse;
                echo "</pre>";
            }
        } else {
            echo "<p style='color: red;'>❌ Failed to fetch API response</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Table 'outsourcing_detail' does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Outsourcing Page (Should now show stats grid)</a></p>";
?>
