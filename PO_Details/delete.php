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
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('PO ID is required');
    }
    
    $id = intval($_POST['id']);
    
    // First, get the PO details to confirm deletion
    $getSql = "SELECT po_number, project_description FROM po_details WHERE id = ?";
    $getStmt = $conn->prepare($getSql);
    if (!$getStmt) {
        throw new Exception('Failed to prepare get statement: ' . $conn->error);
    }
    
    if (!$getStmt->bind_param("i", $id)) {
        throw new Exception('Failed to bind get params: ' . $getStmt->error);
    }
    
    if (!$getStmt->execute()) {
        throw new Exception('Failed to execute get: ' . $getStmt->error);
    }
    
    $result = $getStmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('PO not found with the specified ID');
    }
    
    $poData = $result->fetch_assoc();
    $poNumber = $poData['po_number'];
    $projectDescription = $poData['project_description'];
    
    $getStmt->close();
    
    // Delete the PO
    $stmt = $pdo->prepare("DELETE FROM po_details WHERE po_id = ?");
    if (!$deleteStmt) {
        throw new Exception('Failed to prepare delete statement: ' . $conn->error);
    }
    
    if (!$deleteStmt->bind_param("i", $id)) {
        throw new Exception('Failed to bind delete params: ' . $deleteStmt->error);
    }
    
    if (!$deleteStmt->execute()) {
        throw new Exception('Failed to execute delete: ' . $deleteStmt->error);
    }
    
    if ($deleteStmt->affected_rows === 0) {
        throw new Exception('No PO was deleted. The PO may have already been removed.');
    }
    
    $deleteStmt->close();
    
    // Clear any output buffer
    ob_end_clean();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => "PO '{$poNumber}' for project '{$projectDescription}' has been deleted successfully.",
        'deleted_id' => $id
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
