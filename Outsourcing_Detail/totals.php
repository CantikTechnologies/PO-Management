<?php
require 'dp.php';
header('Content-Type: application/json');

try {
    // Check if table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'outsourcing_detail'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        // Table doesn't exist, return zeros
        echo json_encode([
            'success' => true,
            'data' => [
                'total_entries' => 0,
                'total_vendor_inv_value' => 0.0,
                'total_net_payable' => 0.0,
                'total_payment_value' => 0.0,
                'total_pending_payment' => 0.0
            ]
        ]);
        exit;
    }

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
    
    // Calculate total pending payment (Excel-style: sum of max(0, net_payble - payment_value))
    $q5 = $mysqli->query("SELECT COALESCE(SUM(GREATEST(0, net_payble - payment_value)), 0) as s FROM outsourcing_detail");
    $pending = ($q5 && ($r = $q5->fetch_assoc())) ? floatval($r['s']) : 0.0;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_entries' => $total,
            'total_vendor_inv_value' => $inv,
            'total_net_payable' => $netPayable,
            'total_payment_value' => $paymentValue,
            'total_pending_payment' => $pending
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
