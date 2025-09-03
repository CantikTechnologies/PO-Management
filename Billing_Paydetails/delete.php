<?php
header('Content-Type: application/json');
require_once 'db.php';

$billing_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($billing_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Billing ID.']);
    exit;
}

try {
    $sql = "DELETE FROM billing_payment_details WHERE billing_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $billing_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Billing record deleted successfully']);
        } else {
            throw new Exception('Billing record not found or already deleted.');
        }
    } else {
        throw new Exception($stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
