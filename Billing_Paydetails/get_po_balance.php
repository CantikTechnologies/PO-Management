<?php
require 'db.php';
header('Content-Type: application/json');

$po_number = $_GET['po_number'] ?? '';

if (empty($po_number)) {
    echo json_encode(['success' => false, 'error' => 'PO number is required.']);
    exit;
}

$sql = "SELECT pending_amount FROM po_details WHERE po_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $po_number);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'pending_amount' => $row['pending_amount']]);
} else {
    echo json_encode(['success' => false, 'error' => 'PO number not found.']);
}

$stmt->close();
$conn->close();
?>