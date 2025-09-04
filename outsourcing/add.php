<?php include '../db.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
    $po_number = $conn->real_escape_string($_POST['po_number']);
    $res = $conn->query("SELECT po_id FROM purchase_orders WHERE po_number='{$po_number}' LIMIT 1");
    $po = $res->fetch_assoc(); if (!$po){ echo "PO not found"; exit; }
    $po_id = $po['po_id'];
    $cantik_po_no = $conn->real_escape_string($_POST['cantik_po_no']);
    $cantik_po_date = $_POST['cantik_po_date']?:null;
    $cantik_po_value = $_POST['cantik_po_value']?:0;
    $vendor_invoice_no = $conn->real_escape_string($_POST['vendor_invoice_no']);
    $vendor_invoice_date = $_POST['vendor_invoice_date']?:null;
    $vendor_invoice_value = $_POST['vendor_invoice_value']?:0;
    $payment_status = $conn->real_escape_string($_POST['payment_status']);
    $payment_value = $_POST['payment_value']?:null;
    $payment_date = $_POST['payment_date']?:null;
    $remarks = $conn->real_escape_string($_POST['remarks']);

    $sql = "INSERT INTO outsourcing_details (po_id,cantik_po_no,cantik_po_date,cantik_po_value,vendor_invoice_no,vendor_invoice_date,vendor_invoice_value,payment_status,payment_value,payment_date,remarks) VALUES ({$po_id},'{$cantik_po_no}',".
           ($cantik_po_date?"'{$cantik_po_date}'":"NULL").",{$cantik_po_value},'{$vendor_invoice_no}',".
           ($vendor_invoice_date?"'{$vendor_invoice_date}'":"NULL").",{$vendor_invoice_value},'{$payment_status}',".
           ($payment_value?"{$payment_value}":"NULL").",".
           ($payment_date?"'{$payment_date}'":"NULL").",'{$remarks}')";
    if ($conn->query($sql)) header('Location:list.php'); else echo $conn->error;
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Add Outsourcing</title><link rel="stylesheet" href="../assets/style.css"></head>
<body>
  <div class="container">
    <h2>Add Outsourcing Record</h2>
    <form method="post">
      <label>Customer PO Number<input name="po_number" required></label>
      <label>Cantik PO No<input name="cantik_po_no"></label>
      <label>Cantik PO Date<input type="date" name="cantik_po_date"></label>
      <label>Cantik PO Value<input type="number" step="0.01" name="cantik_po_value"></label>
      <label>Vendor Invoice No<input name="vendor_invoice_no" required></label>
      <label>Vendor Invoice Date<input type="date" name="vendor_invoice_date"></label>
      <label>Vendor Invoice Value<input type="number" step="0.01" name="vendor_invoice_value" required></label>
      <label>Payment Status<input name="payment_status"></label>
      <label>Payment Value<input type="number" step="0.01" name="payment_value"></label>
      <label>Payment Date<input type="date" name="payment_date"></label>
      <label>Remarks<textarea name="remarks"></textarea></label>
      <button type="submit">Save</button>
      <a href="list.php" class="btn muted">Cancel</a>
    </form>
  </div>
</body>
</html>
