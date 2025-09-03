<?php
header('Content-Type: application/json');
require_once 'dp.php';

// Use 'id' from the form which corresponds to outsourcing_id
$outsourcing_id = isset($_POST['id']) ? $_POST['id'] : null;
$billing_id = isset($_POST['billing_id']) ? $_POST['billing_id'] : null;
$vendor_name = isset($_POST['vendor_name']) ? $_POST['vendor_name'] : null;
$work_description = isset($_POST['work_description']) ? $_POST['work_description'] : null;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : null;

if (empty($billing_id) || empty($vendor_name) || $amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invoice, Vendor Name, and a valid Amount are required.']);
    exit;
}

if ($outsourcing_id) {
    // Update
    $sql = "UPDATE outsourcing_details SET billing_id = ?, vendor_name = ?, work_description = ?, amount = ?, payment_status = ? WHERE outsourcing_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issdsi', $billing_id, $vendor_name, $work_description, $amount, $payment_status, $outsourcing_id);
    $message = 'Outsourcing record updated successfully';
} else {
    // Insert
    $sql = "INSERT INTO outsourcing_details (billing_id, vendor_name, work_description, amount, payment_status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issds', $billing_id, $vendor_name, $work_description, $amount, $payment_status);
    $message = 'Outsourcing record saved successfully';
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => $message]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();