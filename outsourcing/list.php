<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Outsourcing</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <h2>Outsourcing Details</h2>
    <a class="btn" href="add.php">+ Add Record</a>
    <a href="../index.php" class="btn muted">Dashboard</a>
    <table>
      <thead>
        <tr>
          <th>ID</th><th>PO No</th><th>Vendor Inv No</th><th>Vendor Inv Date</th><th>Vendor Inv Value</th><th>TDS Ded</th><th>Net Payable</th><th>Payment Value</th><th>Pending</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $res = $conn->query("SELECT outd.*, po.po_number FROM outsourcing_details outd JOIN purchase_orders po ON po.po_id=outd.po_id ORDER BY outd.outsourcing_id DESC");
      while ($r = $res->fetch_assoc()){
          echo '<tr>';
          echo '<td>'.$r['outsourcing_id'].'</td>';
          echo '<td>'.$r['po_number'].'</td>';
          echo '<td>'.$r['vendor_invoice_no'].'</td>';
          echo '<td>'.$r['vendor_invoice_date'].'</td>';
          echo '<td>'.$r['vendor_invoice_value'].'</td>';
          echo '<td>'.$r['tds_ded'].'</td>';
          echo '<td>'.$r['net_payable'].'</td>';
          echo '<td>'.$r['payment_value'].'</td>';
          echo '<td>'.$r['pending_payment'].'</td>';
          echo '<td><a href="edit.php?id='.$r['outsourcing_id'].'">Edit</a> | <a href="delete.php?id='.$r['outsourcing_id'].'" onclick="return confirm(\'Delete?\')">Delete</a></td>';
          echo '</tr>';
      }
      ?>
      </tbody>
    </table>
  </div>
</body>
</html>
