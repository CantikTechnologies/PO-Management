<?php
require 'db.php';
header('Content-Type: application/json');

try {
    $tableCheck = $conn->query("SHOW TABLES LIKE 'billing_details'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        echo json_encode([
            'success' => true,
            'data' => [
                'total_entries' => 0,
                'total_taxable' => 0.0,
                'total_tds' => 0.0,
                'total_receivable' => 0.0
            ]
        ]);
        exit;
    }

    $q1 = $conn->query("SELECT COUNT(*) c FROM billing_details");
    $total = ($q1 && ($r=$q1->fetch_assoc())) ? (int)$r['c'] : 0;

    $q2 = $conn->query("SELECT COALESCE(SUM(cantik_inv_value_taxable),0) s FROM billing_details");
    $taxable = ($q2 && ($r=$q2->fetch_assoc())) ? (float)$r['s'] : 0.0;

    $q3 = $conn->query("SELECT COALESCE(SUM(tds),0) s FROM billing_details");
    $tds = ($q3 && ($r=$q3->fetch_assoc())) ? (float)$r['s'] : 0.0;

    $q4 = $conn->query("SELECT COALESCE(SUM(receivable),0) s FROM billing_details");
    $receivable = ($q4 && ($r=$q4->fetch_assoc())) ? (float)$r['s'] : 0.0;

    echo json_encode([
        'success' => true,
        'data' => [
            'total_entries' => $total,
            'total_taxable' => round($taxable, 2),
            'total_tds' => round($tds, 2),
            'total_receivable' => round($receivable, 2)
        ]
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
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
    
    // Check if billing_details table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'billing_details'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        // Table doesn't exist, return zeros
        echo json_encode([
            'success' => true,
            'data' => [
                'total_entries' => 0,
                'total_taxable' => 0.0,
                'total_tds' => 0.0,
                'total_receivable' => 0.0
            ]
        ]);
        exit;
    }
    
    // Calculate totals
    $sql = "SELECT 
                COUNT(*) as total_entries,
                SUM(COALESCE(cantik_inv_value_taxable, 0)) as total_taxable,
                SUM(COALESCE(tds, 0)) as total_tds,
                SUM(COALESCE(receivable, 0)) as total_receivable
            FROM billing_details";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception('Failed to calculate totals: ' . $conn->error);
    }
    
    $totals = $result->fetch_assoc();
    
    // Clear any output buffer
    ob_end_clean();
    
    // Return success response with raw numeric values
    echo json_encode([
        'success' => true,
        'data' => [
            'total_entries' => (int)($totals['total_entries'] ?? 0),
            'total_taxable' => (float)($totals['total_taxable'] ?? 0.0),
            'total_tds' => (float)($totals['total_tds'] ?? 0.0),
            'total_receivable' => (float)($totals['total_receivable'] ?? 0.0)
        ]
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