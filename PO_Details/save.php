<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = $_POST;

$id = isset($data['id']) ? $data['id'] : null;

// Basic validation
if (empty($data['po_number']) || empty($data['total_po_value'])) {
    echo json_encode(['success' => false, 'message' => 'PO Number and Total PO Value are required.']);
    exit;
}

try {
    if ($id) {
        // Update existing entry
        $sql = "UPDATE po_details SET po_number = ?, po_date = ?, vendor_name = ?, vendor_address = ?, gst_no = ?, total_po_value = ? WHERE po_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['po_number'],
            empty($data['po_date']) ? null : $data['po_date'],
            $data['vendor_name'],
            $data['vendor_address'],
            $data['gst_no'],
            $data['total_po_value'],
            $id
        ]);
        $message = 'PO Details updated successfully';
    } else {
        // Insert new entry
        $sql = "INSERT INTO po_details (po_number, po_date, vendor_name, vendor_address, gst_no, total_po_value) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['po_number'],
            empty($data['po_date']) ? null : $data['po_date'],
            $data['vendor_name'],
            $data['vendor_address'],
            $data['gst_no'],
            $data['total_po_value']
        ]);
        $id = $pdo->lastInsertId();
        $message = 'PO Details saved successfully';
    }

    echo json_encode(['success' => true, 'message' => $message, 'id' => $id]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}