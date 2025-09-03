<?php
// Start output buffering to prevent any unwanted output
ob_start();

// Suppress all error output
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

try {
    // Include database connection
    require_once 'db.php';
    
    // Check if database connection is available
    if (!isset($conn) || $conn === null) {
        throw new Exception('Database connection not available');
    }
    
    // Check if ID is provided
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('PO ID is required');
    }
    
    $id = intval($_GET['id']);
    
    // Get PO details by ID
    $sql = "SELECT * FROM po_details WHERE id = ?";
    $stmt = $pdo->prepare("SELECT * FROM po_details WHERE po_id = ?");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    if (!$stmt->bind_param("i", $id)) {
        throw new Exception('Failed to bind params: ' . $stmt->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('PO not found with the specified ID');
    }
    
    $poData = $result->fetch_assoc();
    $stmt->close();
    
    // Clear any output buffer
    ob_end_clean();
    
    // Return success response with PO data
    echo json_encode([
        'success' => true,
        'data' => $poData
    ]);
    
} catch (Exception $e) {
    // Clear any output buffer
    ob_end_clean();
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Close database connection if available
if (isset($conn) && $conn !== null) {
    $conn->close();
}
?>
