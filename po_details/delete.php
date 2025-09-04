<?php include '../db.php';
$id = intval($_GET['id'] ?? 0);
if ($id) {
    $conn->query("DELETE FROM purchase_orders WHERE po_id={$id}");
}
header('Location:list.php'); exit;
