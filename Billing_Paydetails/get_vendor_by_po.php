<?php
// Return vendor name for a given Customer PO by looking up Outsourcing Details (VLOOKUP equivalent)
header('Content-Type: application/json');

try {
    require_once '../Outsourcing_Detail/dp.php'; // provides $mysqli

    if (!isset($mysqli) || !$mysqli) {
        throw new Exception('Database connection not available');
    }

    $po = isset($_GET['po']) ? trim($_GET['po']) : '';
    if ($po === '') {
        echo json_encode(['success' => true, 'vendor_name' => '']);
        exit;
    }

    $stmt = $mysqli->prepare('SELECT vendor_name FROM outsourcing_detail WHERE ntt_po = ? ORDER BY id DESC LIMIT 1');
    if (!$stmt) {
        throw new Exception('Failed to prepare statement');
    }
    $stmt->bind_param('s', $po);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    $vendor = $row && isset($row['vendor_name']) ? $row['vendor_name'] : '';
    echo json_encode(['success' => true, 'vendor_name' => $vendor]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>


