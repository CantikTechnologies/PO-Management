<?php 
include 'db.php'; 
?>
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
      if ($res && $res->num_rows > 0) {
        while ($r = $res->fetch_assoc()) {
          echo "<tr>";
          echo "<td>".htmlspecialchars($r['cost_center'])."</td>";
          echo "<td>".htmlspecialchars($r['customer_po_no'])."</td>";
          echo "<td>".number_format($r['customer_po_value'],2)."</td>";
          echo "<td>".number_format($r['billed_till_date'],2)."</td>";
          echo "<td>".number_format($r['remaining_balance_po'],2)."</td>";
          echo "<td>".htmlspecialchars($r['vendor_name'])."</td>";
          echo "<td>".htmlspecialchars($r['vendor_po_no'])."</td>";
          echo "<td>".number_format($r['vendor_po_value'],2)."</td>";
          echo "<td>".number_format($r['vendor_invoicing_till_date'],2)."</td>";
          echo "<td>".number_format($r['remaining_vendor_balance'],2)."</td>";
          echo "<td>".number_format($r['sale_margin_till_date'],2)."%</td>";
          echo "<td>".number_format($r['target_gm'],2)."%</td>";
          echo "<td>".number_format($r['variance_in_gm'],2)."%</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='13'>No records found</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </div>
</body>
</html>
