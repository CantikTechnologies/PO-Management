<?php
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT po_id, po_number, po_date, vendor_name, total_po_value FROM po_details ORDER BY po_date DESC");
    $pos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grand_total = 0;

    echo "<table class='table table-bordered table-striped'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>PO Number</th>";
    echo "<th>PO Date</th>";
    echo "<th>Vendor Name</th>";
    echo "<th>Total PO Value</th>";
    echo "<th>Actions</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    if (count($pos) > 0) {
        foreach ($pos as $row) {
            $grand_total += $row['total_po_value'];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['po_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['po_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['vendor_name']) . "</td>";
            echo "<td style='text-align: right;'>" . htmlspecialchars(number_format($row['total_po_value'], 2)) . "</td>";
            echo "<td>";
            echo "<button class='btn btn-sm btn-primary' onclick='editPO(" . $row['po_id'] . ")'>Edit</button> ";
            echo "<button class='btn btn-sm btn-danger' onclick='deletePO(" . $row['po_id'] . ")'>Delete</button>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo '<tr><td colspan="5" class="text-center">No Purchase Orders found.</td></tr>';
    }

    echo "</tbody>";
    echo "<tfoot>";
    echo "<tr>";
    echo "<th colspan='3' style='text-align:right'>Grand Total:</th>";
    echo "<th style='text-align: right;'>" . htmlspecialchars(number_format($grand_total, 2)) . "</th>";
    echo "<th></th>";
    echo "</tr>";
    echo "</tfoot>";
    echo "</table>";

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error fetching PO list: " . $e->getMessage() . "</div>";
}