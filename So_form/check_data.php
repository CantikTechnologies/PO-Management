<?php
require 'db.php';

echo "<h2>Data Check</h2>";

// Get PO number from po_details
$poResult = $conn->query("SELECT po_number FROM po_details LIMIT 1");
if ($poResult && $poResult->num_rows > 0) {
    $poData = $poResult->fetch_assoc();
    $poNumber = $poData['po_number'];
    echo "<h3>PO Number from po_details: {$poNumber}</h3>";
    
    // Check billing_details for this PO
    echo "<h4>Billing Details for PO {$poNumber}:</h4>";
    $billingResult = $conn->query("SELECT * FROM billing_details WHERE customer_po = '{$poNumber}'");
    if ($billingResult && $billingResult->num_rows > 0) {
        echo "Found {$billingResult->num_rows} billing records<br>";
        while ($row = $billingResult->fetch_assoc()) {
            echo "ID: {$row['id']}, Project: {$row['project_details']}, Amount: {$row['cantik_inv_value_taxable']}<br>";
        }
    } else {
        echo "No billing records found for PO {$poNumber}<br>";
    }
    
    // Check outsourcing_detail for this PO
    echo "<h4>Outsourcing Details for PO {$poNumber}:</h4>";
    $outsourcingResult = $conn->query("SELECT * FROM outsourcing_detail WHERE ntt_po = '{$poNumber}'");
    if ($outsourcingResult && $outsourcingResult->num_rows > 0) {
        echo "Found {$outsourcingResult->num_rows} outsourcing records<br>";
        while ($row = $outsourcingResult->fetch_assoc()) {
            echo "ID: {$row['id']}, Project: {$row['project_details']}, Value: {$row['cantik_po_value']}<br>";
        }
    } else {
        echo "No outsourcing records found for PO {$poNumber}<br>";
    }
    
    // Check if outsourcing_detail has customer_po column
    echo "<h4>Checking outsourcing_detail table structure:</h4>";
    $columnsResult = $conn->query("SHOW COLUMNS FROM outsourcing_detail LIKE 'customer_po'");
    if ($columnsResult && $columnsResult->num_rows > 0) {
        echo "customer_po column exists in outsourcing_detail<br>";
        $outsourcingResult2 = $conn->query("SELECT * FROM outsourcing_detail WHERE customer_po = '{$poNumber}'");
        if ($outsourcingResult2 && $outsourcingResult2->num_rows > 0) {
            echo "Found {$outsourcingResult2->num_rows} outsourcing records with customer_po<br>";
        } else {
            echo "No outsourcing records found with customer_po = {$poNumber}<br>";
        }
    } else {
        echo "customer_po column does NOT exist in outsourcing_detail<br>";
        echo "Available columns that might contain PO info:<br>";
        $allColumns = $conn->query("SHOW COLUMNS FROM outsourcing_detail");
        while ($col = $allColumns->fetch_assoc()) {
            echo "- {$col['Field']}<br>";
        }
    }
    
} else {
    echo "No PO data found<br>";
}

$conn->close();
?>
