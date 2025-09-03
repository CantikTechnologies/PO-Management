<?php
header('Content-Type: application/json');
require_once 'db.php';

$po_id = isset($_GET['po_id']) ? $_GET['po_id'] : null;

if (!$po_id) {
    // Mode 1: Get all POs for the dropdown
    $sql = "SELECT po_id, po_number FROM po_details ORDER BY po_date DESC";
    $result = $conn->query($sql);
    $pos = [];
    while($row = $result->fetch_assoc()) {
        $pos[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $pos]);
} else {
    // Mode 2: Get calculated details for a specific PO
    try {
        $so_value = 0;
        $total_billed = 0;

        // Get SO Value
        $sql_so = "SELECT so_value FROM so_form WHERE po_id = ?";
        $stmt_so = $conn->prepare($sql_so);
        $stmt_so->bind_param('i', $po_id);
        $stmt_so->execute();
        $result_so = $stmt_so->get_result();
        if ($result_so->num_rows > 0) {
            $so_value = $result_so->fetch_assoc()['so_value'];
        }
        $stmt_so->close();

        // Get Total Billed Amount
        $sql_billed = "SELECT SUM(b.invoice_amount) as total_billed 
                       FROM billing_payment_details b 
                       JOIN so_form s ON b.so_id = s.so_id 
                       WHERE s.po_id = ?";
        $stmt_billed = $conn->prepare($sql_billed);
        $stmt_billed->bind_param('i', $po_id);
        $stmt_billed->execute();
        $result_billed = $stmt_billed->get_result();
        if ($result_billed->num_rows > 0) {
            $billed_row = $result_billed->fetch_assoc();
            $total_billed = $billed_row['total_billed'] ? $billed_row['total_billed'] : 0;
        }
        $stmt_billed->close();

        $balance = $so_value - $total_billed;

        echo json_encode([
            'success' => true,
            'data' => [
                'so_value' => $so_value,
                'billed_amount' => $total_billed,
                'balance' => $balance
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

$conn->close();
