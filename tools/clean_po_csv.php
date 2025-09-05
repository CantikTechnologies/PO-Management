<?php
// Clean and normalize PO_Detail.csv into PO_Detail_clean.csv ready for so_form import

header('Content-Type: text/plain');

$inputPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'PO_Detail.csv';
$outputPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'PO_Detail_clean.csv';

if (!file_exists($inputPath)) {
  http_response_code(404);
  echo "Input CSV not found at: $inputPath\n";
  exit;
}

function cleanNumber(?string $v): string {
  if ($v === null) return '';
  $v = trim($v);
  if ($v === '' || $v === '-' || stripos($v, '#N/A') !== false || stripos($v, '#VALUE') !== false || stripos($v, '#DIV/0') !== false) return '';
  // Remove commas and extra spaces
  $v = str_replace([',', ' '], '', $v);
  // Handle percentages like 5% or 100.00%
  if (substr($v, -1) === '%') {
    $v = rtrim($v, '%');
  }
  // If still non-numeric, return empty
  if (!is_numeric($v)) return '';
  return $v;
}

function cleanText(?string $v): string {
  if ($v === null) return '';
  $v = trim(preg_replace("/\s+/", ' ', $v));
  if ($v === '-' || stripos($v, '#N/A') !== false) return '';
  return $v;
}

$in = new SplFileObject($inputPath, 'r');
$in->setFlags(SplFileObject::READ_CSV | SplFileObject::DROP_NEW_LINE);

$out = fopen($outputPath, 'w');
// Target headers for so_form table
$headers = [
  'project_name','cost_center','customer_po_no','customer_po_value','billed_till_date','remaining_balance_po',
  'vendor_name','vendor_po_no','vendor_po_value','vendor_invoicing_till_date','remaining_vendor_balance',
  'sale_margin_till_date','target_gm','variance_in_gm'
];
fputcsv($out, $headers);

$rowIndex = 0;
foreach ($in as $row) {
  if ($row === [null] || $row === false) continue; // skip empty
  $rowIndex++;
  // Skip first two noise rows
  if ($rowIndex <= 2) continue;
  // Header row (third) - skip
  if ($rowIndex === 3) continue;

  // Some rows may have more/less columns due to stray commas; pad/trim to at least 14
  // We expect the columns as provided in the file's header order
  $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

  // Map fields by known positions from the provided header (row 3)
  $project_name = cleanText($row[0] ?? '');
  $cost_center = cleanText($row[1] ?? '');
  $customer_po_no = cleanText($row[2] ?? '');
  $customer_po_value = cleanNumber($row[3] ?? '');
  $billed_till_date = cleanNumber($row[4] ?? '');
  $remaining_balance_po = cleanNumber($row[5] ?? '');
  $vendor_name = cleanText($row[6] ?? '');
  $vendor_po_no = cleanText($row[7] ?? '');
  $vendor_po_value = cleanNumber($row[8] ?? '');
  $vendor_invoicing_till_date = cleanNumber($row[9] ?? '');
  $remaining_vendor_balance = cleanNumber($row[10] ?? '');
  $sale_margin_till_date = cleanNumber($row[11] ?? '');
  $target_gm = cleanNumber($row[12] ?? '');
  $variance_in_gm = cleanNumber($row[13] ?? '');

  // Skip completely empty lines
  $joined = $project_name.$cost_center.$customer_po_no.$customer_po_value.$billed_till_date.$vendor_name;
  if ($joined === '') continue;

  fputcsv($out, [
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
    $variance_in_gm,
  ]);
}

fclose($out);
echo "Created: $outputPath\n";
?>


