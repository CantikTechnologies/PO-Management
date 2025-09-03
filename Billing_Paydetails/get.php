<?php
require 'db.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { echo json_encode([]); exit; }

$q = $conn->prepare('SELECT * FROM billing_details WHERE id = ?');
$q->bind_param('i', $id);
$q->execute();
$res = $q->get_result();
$row = $res->fetch_assoc();
if ($row && !empty($row['cantik_invoice_date']) && is_numeric($row['cantik_invoice_date']) && $row['cantik_invoice_date'] > 10000) {
    $row['cantik_invoice_date'] = date('Y-m-d', ($row['cantik_invoice_date'] - 25569) * 86400);
}
echo json_encode($row ?: []);
?>
<?php
header('Content-Type: application/json');
require_once 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(null);
    exit;
}

$sql = "SELECT b.*, s.po_id 
        FROM billing_payment_details b 
        JOIN so_form s ON b.so_id = s.so_id 
        WHERE b.billing_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$stmt->close();
$conn->close();

echo json_encode($data);
