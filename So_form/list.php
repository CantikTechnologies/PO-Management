<?php
require_once 'db.php';

$sql = "SELECT so.so_id, so.so_number, so.so_date, so.so_value, po.po_number 
        FROM so_form so 
        LEFT JOIN po_details po ON so.po_id = po.po_id 
        ORDER BY so.so_date DESC";

$result = $conn->query($sql);

echo "<table class='table table-bordered table-striped'>";
echo "<thead>";
echo "<tr>";
echo "<th>SO Number</th>";
echo "<th>SO Date</th>";
echo "<th>SO Value</th>";
echo "<th>Linked PO Number</th>";
echo "<th>Actions</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['so_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['so_date']) . "</td>";
        echo "<td style='text-align: right;'>" . htmlspecialchars(number_format($row['so_value'], 2)) . "</td>";
        echo "<td>" . htmlspecialchars($row['po_number']) . "</td>";
        echo "<td>";
        echo "<button class='btn btn-sm btn-primary' onclick='editSO(" . $row['so_id'] . ")'>Edit</button> ";
        echo "<button class='btn btn-sm btn-danger' onclick='deleteSO(" . $row['so_id'] . ")'>Delete</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo '<tr><td colspan="5" class="text-center">No Sales Orders found.</td></tr>';
}

echo "</tbody>";
echo "</table>";

$conn->close();
