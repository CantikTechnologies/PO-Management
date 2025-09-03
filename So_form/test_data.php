<?php
require 'db.php';

echo "<h2>Testing SO Form Data</h2>";

// Test 1: Check database connection
echo "<h3>1. Database Connection Test</h3>";
if ($conn) {
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Test 2: Check if po_details table has data
echo "<h3>2. PO Details Table Test</h3>";
$poResult = $conn->query("SELECT COUNT(*) as count FROM po_details");
if ($poResult) {
    $poCount = $poResult->fetch_assoc()['count'];
    echo "✅ PO Details table has {$poCount} records<br>";
    
    if ($poCount > 0) {
        $samplePO = $conn->query("SELECT * FROM po_details LIMIT 1")->fetch_assoc();
        echo "Sample PO: " . json_encode($samplePO, JSON_PRETTY_PRINT) . "<br>";
    }
} else {
    echo "❌ Error querying PO Details table<br>";
}

// Test 3: Check if billing_details table has data
echo "<h3>3. Billing Details Table Test</h3>";
$billingResult = $conn->query("SELECT COUNT(*) as count FROM billing_details");
if ($billingResult) {
    $billingCount = $billingResult->fetch_assoc()['count'];
    echo "✅ Billing Details table has {$billingCount} records<br>";
} else {
    echo "❌ Error querying Billing Details table<br>";
}

// Test 4: Check if outsourcing_detail table has data
echo "<h3>4. Outsourcing Detail Table Test</h3>";
$outsourcingResult = $conn->query("SELECT COUNT(*) as count FROM outsourcing_detail");
if ($outsourcingResult) {
    $outsourcingCount = $outsourcingResult->fetch_assoc()['count'];
    echo "✅ Outsourcing Detail table has {$outsourcingCount} records<br>";
} else {
    echo "❌ Error querying Outsourcing Detail table<br>";
}

// Test 5: Test the actual SO data query
echo "<h3>5. SO Data Query Test</h3>";
try {
    $sql = "
        SELECT 
            p.id,
            p.project_description as project,
            p.cost_center as cost_centre,
            p.po_number as customer_po_no,
            p.po_value as billed_po_no,
            p.target_gm,
            p.vendor_name,
            p.po_status,
            p.start_date,
            p.end_date,
            p.po_date,
            p.billing_frequency,
            p.remarks
        FROM po_details p
        ORDER BY p.po_date DESC
        LIMIT 3
    ";
    
    $result = $conn->query($sql);
    if ($result) {
        echo "✅ SO Data query successful<br>";
        echo "Found " . $result->num_rows . " records<br>";
        
        while ($row = $result->fetch_assoc()) {
            echo "PO: {$row['customer_po_no']} - {$row['project']}<br>";
        }
    } else {
        echo "❌ SO Data query failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Error in SO Data query: " . $e->getMessage() . "<br>";
}

$conn->close();
?>
