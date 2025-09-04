
<?php include '../db.php';
$id = intval($_GET['id'] ?? 0);
if ($id) $conn->query("DELETE FROM invoices WHERE invoice_id={$id}");
header('Location:list.php'); exit;
