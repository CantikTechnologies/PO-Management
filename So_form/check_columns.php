<?php
require 'db.php';

echo "<h2>Database Column Check</h2>";

// Check billing_details table columns
echo "<h3>Billing Details Table Columns:</h3>";
$result = $conn->query("DESCRIBE billing_details");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Column: {$row['Field']} - Type: {$row['Type']}<br>";
    }
} else {
    echo "Error getting billing_details columns<br>";
}

echo "<br>";

// Check outsourcing_detail table columns
echo "<h3>Outsourcing Detail Table Columns:</h3>";
$result = $conn->query("DESCRIBE outsourcing_detail");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Column: {$row['Field']} - Type: {$row['Type']}<br>";
    }
} else {
    echo "Error getting outsourcing_detail columns<br>";
}

echo "<br>";

// Check po_details table columns
echo "<h3>PO Details Table Columns:</h3>";
$result = $conn->query("DESCRIBE po_details");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Column: {$row['Field']} - Type: {$row['Type']}<br>";
    }
} else {
    echo "Error getting po_details columns<br>";
}

$conn->close();
?>
