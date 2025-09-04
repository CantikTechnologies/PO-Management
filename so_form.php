<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>SO Form Report</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container">
    <h2>SO Form - Summary Report</h2>
    <a href="index.php" class="btn">Back</a>
    <table>
      <thead>
        <tr>
          <th>Cost Centre</th>
          <th>Customer PO No</th>
          <th>Customer PO Value</th>
          <th>Billed Till Date</th>
          <th>Remaining Balance (PO)</th>
          <th>Vendor Name</th>
          <th>Vendor PO No</th>
          <th>Vendor PO Value</th>
          <th>Vendor Invoicing Till Date</th>
          <th>Remaining Vendor Balance</th>
          <th>Sale Margin (%)</th>
          <th>Target GM (%)</th>
          <th>Variance GM (%)</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $res = $conn->query("SELECT * FROM so_form");
      while ($r = $res->fetch_assoc()) {
          echo "<tr>";
          echo "<td>{$r['cost_center']}</td>";
          echo "<td>{$r['customer_po_no']}</td>";
          echo "<td>{$r['customer_po_value']}</td>";
          echo "<td>{$r['billed_till_date']}</td>";
          echo "<td>{$r['remaining_balance_in_po']}</td>";
          echo "<td>{$r['vendor_name']}</td>";
          echo "<td>{$r['vendor_po_no']}</td>";
          echo "<td>{$r['vendor_po_value']}</td>";
          echo "<td>{$r['vendor_invoicing_till_date']}</td>";
          echo "<td>{$r['remaining_balance_in_vendor_po']}</td>";
          echo "<td>{$r['sale_margin_till_date']}</td>";
          echo "<td>{$r['target_gm']}</td>";
          echo "<td>{$r['variance_in_gm']}</td>";
          echo "</tr>";
      }
      ?>
      </tbody>
    </table>
  </div>
</body>
</html>
