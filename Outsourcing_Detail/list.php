<?php
header('Content-Type: application/json');
require_once 'dp.php';

$sql = "SELECT 
            o.outsourcing_id, o.vendor_name, o.work_description, o.amount, o.payment_status,
            b.invoice_number,
            s.so_number,
            p.po_number
        FROM 
            outsourcing_details o
        JOIN 
            billing_payment_details b ON o.billing_id = b.billing_id
        JOIN 
            so_form s ON b.so_id = s.so_id
        JOIN 
            po_details p ON s.po_id = p.po_id
        ORDER BY 
            o.outsourcing_id DESC";

$result = $conn->query($sql);
$data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // The JS expects 'id' for the buttons, so we map outsourcing_id to id.
        $row['id'] = $row['outsourcing_id'];
        $data[] = $row;
    }
}

$conn->close();

echo json_encode(['success' => true, 'data' => $data]);