<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PO Details</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<h2>Purchase Order Details</h2>
<table>
  <tr>
    <th>PO Number</th>
    <th>Project</th>
    <th>Cost Center</th>
    <th>PO Value</th>
    <th>Status</th>
    <th>Vendor</th>
  </tr>
  <?php
  $result = $conn->query("SELECT * FROM purchase_orders");
  while($row = $result->fetch_assoc()) {
      echo "<tr>
        <td>{$row['po_number']}</td>
        <td>{$row['project_description']}</td>
        <td>{$row['cost_center']}</td>
        <td>{$row['po_value']}</td>
        <td>{$row['po_status']}</td>
        <td>{$row['vendor_name']}</td>
      </tr>";
  }
  ?>
</table>
</body>
</html>
