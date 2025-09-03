<?php
require 'db.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID is required']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM so_form WHERE so_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Entry deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Entry not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting entry']);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
