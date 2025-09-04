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
          <th>ID</th>
          <th>PO Number</th>
          <th>Project</th>
          <th>Cost Center</th>
          <th>PO Value</th>
          <th>Pending Amount</th>
          <th>Status</th>
          <th>Vendor</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // ✅ Query using the VIEW (best practice)
        $res = $conn->query("
          SELECT *
          FROM v_purchase_orders
          ORDER BY po_id DESC
        ");

        if ($res && $res->num_rows > 0) {
          while ($row = $res->fetch_assoc()) {
            echo '<tr>';
            echo '<td>'.htmlspecialchars($row['po_id']).'</td>';
            echo '<td>'.htmlspecialchars($row['po_number']).'</td>';
            echo '<td>'.htmlspecialchars($row['project_description']).'</td>';
            echo '<td>'.htmlspecialchars($row['cost_center']).'</td>';
            echo '<td>'.htmlspecialchars($row['po_value']).'</td>';
            echo '<td>'.htmlspecialchars($row['pending_amount_in_po']).'</td>';
            echo '<td>'.htmlspecialchars($row['po_status']).'</td>';
            echo '<td>'.htmlspecialchars($row['vendor_name']).'</td>';
            echo '<td>
                    <a href="edit.php?id='.$row['po_id'].'">Edit</a> | 
                    <a href="delete.php?id='.$row['po_id'].'" onclick="return confirm(\'Delete?\')">Delete</a>
                  </td>';
            echo '</tr>';
          }
        } else {
          echo '<tr><td colspan="9">No purchase orders found.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
