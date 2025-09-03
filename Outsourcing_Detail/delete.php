<?php
header('Content-Type: application/json');
require_once 'dp.php';

// The JS sends the id in the body of a FormData object
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid Outsourcing ID.']);
    exit;
}

try {
    $sql = "DELETE FROM outsourcing_details WHERE outsourcing_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Outsourcing record deleted successfully']);
        } else {
            throw new Exception('Record not found or already deleted.');
        }
    } else {
        throw new Exception($stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
