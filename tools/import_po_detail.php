<?php
// Import PO_Detail_clean.csv into so_form table
// Run via browser: http://localhost/PO_3/tools/import_po_detail.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once '../db.php'; // provides $conn (mysqli)

header('Content-Type: text/plain');

if (!$conn) {
  http_response_code(500);
  echo "DB connection not available.\n";
  exit;
}

$csvPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'PO_Detail_clean.csv';
if (!file_exists($csvPath)) {
  http_response_code(404);
  echo "CSV not found: $csvPath\nRun cleaner first: http://localhost/PO_3/tools/clean_po_csv.php\n";
  exit;
}

$handle = fopen($csvPath, 'r');
if ($handle === false) {
  http_response_code(500);
  echo "Failed to open CSV.\n";
  exit;
}

// Read header
$header = fgetcsv($handle);
if (!$header) {
  echo "Empty CSV.\n";
  exit;
}

// Prepare insert statement
$stmt = $conn->prepare(
  "INSERT INTO so_form_import (
      project_name, cost_center, customer_po_no, customer_po_value, billed_till_date,
      remaining_balance_po, vendor_name, vendor_po_no, vendor_po_value, vendor_invoicing_till_date,
      remaining_vendor_balance, sale_margin_till_date, target_gm, variance_in_gm
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
);

if (!$stmt) {
  echo "Prepare failed: {$conn->error}\n";
  exit;
}

$rows = 0; $inserted = 0; $skipped = 0;
while (($row = fgetcsv($handle)) !== false) {
  $rows++;
  // Ensure at least 14 columns
  for ($i = 0; $i < 14; $i++) {
    if (!isset($row[$i])) $row[$i] = '';
  }
  list($project_name,
       $cost_center,
       $customer_po_no,
       $customer_po_value,
       $billed_till_date,
       $remaining_balance_po,
       $vendor_name,
       $vendor_po_no,
       $vendor_po_value,
       $vendor_invoicing_till_date,
       $remaining_vendor_balance,
       $sale_margin_till_date,
       $target_gm,
       $variance_in_gm) = $row;

  // Basic skip if no required identifiers
  if ($project_name === '' && $customer_po_no === '' && $vendor_name === '') {
    $skipped++;
    continue;
  }

  $stmt->bind_param(
    'ssssssssssssss',
    $project_name,
    $cost_center,
    $customer_po_no,
    $customer_po_value,
    $billed_till_date,
    $remaining_balance_po,
    $vendor_name,
    $vendor_po_no,
    $vendor_po_value,
    $vendor_invoicing_till_date,
    $remaining_vendor_balance,
    $sale_margin_till_date,
    $target_gm,
    $variance_in_gm
  );

  if (!$stmt->execute()) {
    echo "Row $rows insert failed: {$stmt->error}\n";
    $skipped++;
  } else {
    $inserted++;
  }
}

fclose($handle);

echo "Processed rows: $rows\n";
echo "Inserted rows: $inserted\n";
echo "Skipped rows: $skipped\n";
echo "Done.\n";
?>


