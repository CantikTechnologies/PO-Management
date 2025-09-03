<?php
header('Content-Type: application/json');
require_once 'db.php';

$so_id = isset($_POST['so_id']) ? $_POST['so_id'] : null;
$po_id = isset($_POST['po_id']) ? $_POST['po_id'] : null;
$so_number = isset($_POST['so_number']) ? $_POST['so_number'] : null;
$so_date = isset($_POST['so_date']) ? $_POST['so_date'] : null;
$so_value = isset($_POST['so_value']) ? $_POST['so_value'] : null;

if (empty($po_id) || empty($so_number) || empty($so_value)) {
    echo json_encode(['success' => false, 'message' => 'PO, SO Number, and SO Value are required.']);
    exit;
}

if ($so_id) {
    // Update
    $sql = "UPDATE so_form SET po_id = ?, so_number = ?, so_date = ?, so_value = ? WHERE so_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isssi', $po_id, $so_number, $so_date, $so_value, $so_id);
    $message = 'Sales Order updated successfully';
} else {
    // Insert
    $sql = "INSERT INTO so_form (po_id, so_number, so_date, so_value) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $po_id, $so_number, $so_date, $so_value);
    $message = 'Sales Order saved successfully';
}

if ($stmt->execute()) {
    if (empty($so_id)) {
        $so_id = $conn->insert_id;
    }
    echo json_encode(['success' => true, 'message' => $message, 'id' => $so_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();