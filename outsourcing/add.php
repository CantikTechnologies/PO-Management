<?php 
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Fix: match form field name
    $customer_po_number = $conn->real_escape_string($_POST['customer_po_number']);

    // Look up PO ID
    $res = $conn->query("SELECT po_id FROM purchase_orders WHERE po_number='{$customer_po_number}' LIMIT 1");
    $po = $res->fetch_assoc();
    if (!$po) { 
        echo "PO not found for number: {$customer_po_number}"; 
        exit; 
    }
    $po_id = $po['po_id'];

    // Collect form fields
    $cantik_po_no = $conn->real_escape_string($_POST['cantik_po_no']);
    $cantik_po_date = !empty($_POST['cantik_po_date']) ? $_POST['cantik_po_date'] : null;
    $cantik_po_value = $_POST['cantik_po_value'] ?: 0;
    $vendor_invoice_no = $conn->real_escape_string($_POST['vendor_invoice_no']);
    $vendor_invoice_date = !empty($_POST['vendor_invoice_date']) ? $_POST['vendor_invoice_date'] : null;
    $vendor_invoice_value = $_POST['vendor_invoice_value'] ?: 0;
    $payment_date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : null;
    $remarks = $conn->real_escape_string($_POST['remarks']);

    // ✅ Removed payment_value (auto-calculated in DB)
    // ✅ Removed payment_status (will be derived later)
    $sql = "INSERT INTO outsourcing_details 
        (po_id, cantik_po_no, cantik_po_date, cantik_po_value, 
         vendor_invoice_no, vendor_invoice_date, vendor_invoice_value, 
         payment_date, remarks) 
        VALUES (
            {$po_id},
            '{$cantik_po_no}',
            " . ($cantik_po_date ? "'{$cantik_po_date}'" : "NULL") . ",
            {$cantik_po_value},
            '{$vendor_invoice_no}',
            " . ($vendor_invoice_date ? "'{$vendor_invoice_date}'" : "NULL") . ",
            {$vendor_invoice_value},
            " . ($payment_date ? "'{$payment_date}'" : "NULL") . ",
            '{$remarks}'
        )";

    if ($conn->query($sql)) {
        header('Location: list.php');
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Outsourcing</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <h2>Add Outsourcing Record</h2>
    <form method="post">
      <label>Customer PO Number</label>
      <input type="text" name="customer_po_number" required><br>

      <label>Cantik PO No</label>
      <input type="text" name="cantik_po_no" required><br>

      <label>Cantik PO Date</label>
      <input type="date" name="cantik_po_date"><br>

      <label>Cantik PO Value</label>
      <input type="number" step="0.01" name="cantik_po_value" required><br>

      <label>Vendor Invoice No</label>
      <input type="text" name="vendor_invoice_no" required><br>

      <label>Vendor Invoice Date</label>
      <input type="date" name="vendor_invoice_date"><br>

      <label>Vendor Invoice Value</label>
      <input type="number" step="0.01" name="vendor_invoice_value" required><br>

      <label>Payment Date</label>
      <input type="date" name="payment_date"><br>

      <label>Remarks</label>
      <textarea name="remarks"></textarea><br>

      <button type="submit">Save</button>
      <button type="button" onclick="window.location='list.php'">Cancel</button>
    </form>
  </div>
</body>
</html>
