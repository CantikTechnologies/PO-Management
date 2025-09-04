<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>PO List</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <h2>Purchase Orders</h2>
    <a class="btn" href="add.php">+ Add PO</a>
    <a href="../index.php" class="btn muted">Dashboard</a>
    <table>
      <thead>
        <tr>
          <th>ID</th><th>PO Number</th><th>Project</th><th>Cost Center</th><th>PO Value</th><th>Status</th><th>Vendor</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $res = $conn->query("SELECT * FROM purchase_orders ORDER BY po_id DESC");
      while ($row = $res->fetch_assoc()) {
          echo '<tr>';
          echo '<td>'.$row['po_id'].'</td>';
          echo '<td>'.$row['po_number'].'</td>';
          echo '<td>'.$row['project_description'].'</td>';
          echo '<td>'.$row['cost_center'].'</td>';
          echo '<td>'.$row['po_value'].'</td>';
          echo '<td>'.$row['po_status'].'</td>';
          echo '<td>'.$row['vendor_name'].'</td>';
          echo '<td><a href="edit.php?id='.$row['po_id'].'">Edit</a> | <a href="delete.php?id='.$row['po_id'].'" onclick="return confirm(\'Delete?\')">Delete</a></td>';
          echo '</tr>';
      }
      ?>
      </tbody>
    </table>
  </div>
</body>
</html>
