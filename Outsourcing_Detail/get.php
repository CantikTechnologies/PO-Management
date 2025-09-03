<?php
header('Content-Type: application/json');
require_once 'dp.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(null);
    exit;
}

$sql = "SELECT * FROM outsourcing_details WHERE outsourcing_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$stmt->close();
$conn->close();

echo json_encode($data);