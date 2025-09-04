<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoices</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <h2>Invoices</h2>
    <a class="btn" href="add.php">+ Add Invoice</a>
    <a href="../index.php" class="btn muted">Dashboard</a>
    <table>
      <thead>
        <tr>
          <th>ID</th><th>PO Number</th><th>Invoice No</th><th>Date</th><th>Taxable</th><th>TDS</th><th>Receivable</th><th>Vendor Inv No</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $res = $conn->query("SELECT inv.*, po.po_number FROM invoices inv JOIN purchase_orders po ON po.po_id=inv.po_id ORDER BY inv.invoice_id DESC");
      while ($r = $res->fetch_assoc()){
          echo '<tr>';
          echo '<td>'.$r['invoice_id'].'</td>';
          echo '<td>'.$r['po_number'].'</td>';
          echo '<td>'.$r['cantik_invoice_no'].'</td>';
          echo '<td>'.$r['cantik_invoice_date'].'</td>';
          echo '<td>'.$r['taxable_value'].'</td>';
          echo '<td>'.$r['tds'].'</td>';
          echo '<td>'.$r['receivable'].'</td>';
          echo '<td>'.$r['vendor_invoice_no'].'</td>';
          echo '<td><a href="edit.php?id='.$r['invoice_id'].'">Edit</a> | <a href="delete.php?id='.$r['invoice_id'].'" onclick="return confirm(\'Delete?\')">Delete</a></td>';
          echo '</tr>';
      }
      ?>
      </tbody>
    </table>
  </div>
</body>
</html>
