<?php
header('Content-Type: application/json');
require_once 'dp.php';

$sql = "SELECT billing_id, invoice_number FROM billing_payment_details ORDER BY invoice_date DESC";
$result = $conn->query($sql);

$invoices = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $invoices[] = $row;
    }
}

$conn->close();

echo json_encode(['success' => true, 'data' => $invoices]);
