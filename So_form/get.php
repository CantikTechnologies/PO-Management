<?php
require 'db.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID is required']);
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $conn->prepare("SELECT * FROM so_form WHERE so_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $entry = $result->fetch_assoc();
    
    if ($entry) {
        // Convert decimal values back to percentage for display
        $entry['margin_till_date'] = isset($entry['margin_till_date']) ? round($entry['margin_till_date'] * 100, 2) : 0;
        $entry['variance_in_gm'] = isset($entry['variance_in_gm']) ? round($entry['variance_in_gm'] * 100, 2) : 0;
        
        echo json_encode([
            'success' => true,
            'data' => $entry
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Entry not found'
        ]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
