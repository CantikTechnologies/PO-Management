<?php include '../db.php';
$id = intval($_GET['id'] ?? 0);
if ($id) $conn->query("DELETE FROM outsourcing_details WHERE outsourcing_id={$id}");
header('Location:list.php'); exit;
