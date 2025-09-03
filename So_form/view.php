<?php
require 'db.php';

// Determine which SO column references the PO number
$dbRes = $conn->query('SELECT DATABASE() AS db');
$dbName = $dbRes && ($dbRow = $dbRes->fetch_assoc()) ? $dbRow['db'] : null;
$poCol = 'po_no';
if ($dbName) {
  // Check for exact column names in so_form
  $check = $conn->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='so_form' AND COLUMN_NAME='po_no' LIMIT 1");
  if ($check) {
    $check->bind_param('s', $dbName);
    $check->execute();
    $res = $check->get_result();
    $existsPoNo = $res && $res->num_rows > 0;
    $check->close();
    if (!$existsPoNo) {
      $check2 = $conn->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='so_form' AND COLUMN_NAME='customer_po_no' LIMIT 1");
      if ($check2) {
        $check2->bind_param('s', $dbName);
        $check2->execute();
        $res2 = $check2->get_result();
        if ($res2 && $res2->num_rows > 0) { $poCol = 'customer_po_no'; }
        $check2->close();
      }
    }
  }
}

$sql = "SELECT s.*, p.po_value AS po_value_po, p.target_gm AS po_target_gm, p.vendor_name AS po_vendor_name
         FROM so_form s LEFT JOIN po_details p ON p.po_number = s." . $poCol . " ORDER BY s.id DESC";
$result = $conn->query($sql);
$rows = [];
while ($r = $result->fetch_assoc()) $rows[] = $r;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SO Form - View</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .table-responsive {
      overflow-x: auto;
    }
    .table th, .table td {
      white-space: nowrap;
      min-width: 120px;
    }
    .table th:nth-child(1), .table td:nth-child(1) { min-width: 60px; } /* ID column */
    .table th:nth-child(2), .table td:nth-child(2) { min-width: 200px; } /* Project */
    .table th:nth-child(3), .table td:nth-child(3) { min-width: 120px; } /* Cost Centre */
    .table th:nth-child(4), .table td:nth-child(4) { min-width: 120px; } /* PO No */
    .table th:nth-child(5), .table td:nth-child(5) { min-width: 120px; } /* PO Value */
    .table th:nth-child(6), .table td:nth-child(6) { min-width: 120px; } /* Billed */
    .table th:nth-child(7), .table td:nth-child(7) { min-width: 150px; } /* Vendor Name */
    .table th:nth-child(8), .table td:nth-child(8) { min-width: 120px; } /* Vendor PO */
    .table th:nth-child(9), .table td:nth-child(9) { min-width: 120px; } /* PO to Vendor */
    .table th:nth-child(10), .table td:nth-child(10) { min-width: 120px; } /* Vendor Inv */
    .table th:nth-child(11), .table td:nth-child(11) { min-width: 120px; } /* Margin */
    .table th:nth-child(12), .table td:nth-child(12) { min-width: 120px; } /* Variance */
  </style>
</head>
<body class="p-4">
<div class="container-fluid">
  <h2 class="mb-4">All SO Form Entries - Complete View</h2>
  <a class="btn btn-outline-dark mb-3" href="index.php">Back to Entries</a>
  <div class="table-container">
    <div class="table-responsive">
      <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Project Name</th>
            <th>Cost Centre</th>
            <th>Customer PO No</th>
            <th>Customer PO Value</th>
            <th>Billed till date</th>
            <th>Remaining Balance in PO</th>
            <th>Vendor Name</th>
            <th>Vendor PO No</th>
            <th>Vendor PO Value</th>
            <th>Vendor Invocing Till Date</th>
            <th>Remaining Balance in PO</th>
            <th>Sale Margin Till date</th>
            <th>Target GM</th>
            <th>Varinace in GM</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['id'])?></td>
            <td><?=htmlspecialchars($r['project'])?></td>
            <td><?=htmlspecialchars($r['cost_centre'])?></td>
            <td><?=htmlspecialchars($r[$poCol] ?? '')?></td>
            <td><?=number_format((float)($r['po_value_po'] ?? 0),2)?></td>
            <td><?=number_format((float)($r['billed_po_no'] ?? 0),2)?></td>
            <?php $remainCust = isset($r['remaining_balance_in_po']) ? (float)$r['remaining_balance_in_po'] : max(0, (float)($r['po_value_po'] ?? 0) - (float)($r['billed_po_no'] ?? 0)); ?>
            <td><?=number_format($remainCust,2)?></td>
            <td><?=htmlspecialchars($r['vendor_name'] ?: ($r['po_vendor_name'] ?? ''))?></td>
            <td><?=htmlspecialchars($r['cantik_po_no'])?></td>
            <td><?=number_format((float)($r['vendor_po_value'] ?? 0),2)?></td>
            <td><?=number_format((float)$r['vendor_invoicing_till_date'],2)?></td>
            <?php $remainVendor = max(0, (float)($r['vendor_po_value'] ?? 0) - (float)$r['vendor_invoicing_till_date']); ?>
            <td><?=number_format($remainVendor,2)?></td>
            <?php $marginPct = isset($r['margin_till_date']) ? (float)preg_replace('/[^\d.\-]/','', (string)$r['margin_till_date']) : 0; ?>
            <td><?=number_format($marginPct,2)?>%</td>
            <?php 
              $tgRaw = isset($r['target_gm']) && $r['target_gm'] !== '' ? (string)$r['target_gm'] : ((string)($r['po_target_gm'] ?? ''));
              $tgNum = is_numeric(str_replace('%','', $tgRaw)) ? (float)str_replace('%','', $tgRaw) : 0.0;
            ?>
            <td><?=number_format($tgNum,2)?>%</td>
            <?php $varPct = isset($r['variance_in_gm']) ? (float)preg_replace('/[^\d.\-]/','', (string)$r['variance_in_gm']) : 0; ?>
            <td><?=number_format($varPct,2)?>%</td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
