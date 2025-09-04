<?php include '../db.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
    $po_number = $conn->real_escape_string($_POST['po_number']);
    // find po_id
    $res = $conn->query("SELECT po_id FROM purchase_orders WHERE po_number='{$po_number}' LIMIT 1");
    $po = $res->fetch_assoc();
    if (!$po) { echo "PO not found"; exit; }
    $po_id = $po['po_id'];
    $inv_no = $conn->real_escape_string($_POST['cantik_invoice_no']);
    $inv_date = $_POST['cantik_invoice_date']?:null;
    $taxable = $_POST['taxable_value']?:0;
    $vendor_inv = $conn->real_escape_string($_POST['vendor_invoice_no']);
    $payment_receipt_date = $_POST['payment_receipt_date']?:null;
    $payment_advise_no = $conn->real_escape_string($_POST['payment_advise_no']);
    $vendor_name = $conn->real_escape_string($_POST['vendor_name']);

    $sql = "INSERT INTO invoices (po_id,cantik_invoice_no,cantik_invoice_date,taxable_value,vendor_invoice_no,payment_receipt_date,payment_advise_no,vendor_name) VALUES ({$po_id},'{$inv_no}',".
           ($inv_date?"'{$inv_date}'":"NULL").",{$taxable},'{$vendor_inv}',".
           ($payment_receipt_date?"'{$payment_receipt_date}'":"NULL").",'{$payment_advise_no}','{$vendor_name}')";
    if ($conn->query($sql)) header('Location:list.php'); else echo $conn->error;
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Add Invoice</title><link rel="stylesheet" href="../assets/style.css"></head>
<body>
  <div class="container">
    <h2>Add Invoice</h2>
    <form method="post">
      <label>Customer PO Number<input name="po_number" required></label>
      <label>Cantik Invoice No<input name="cantik_invoice_no" required></label>
      <label>Cantik Invoice Date<input type="date" name="cantik_invoice_date"></label>
      <label>Taxable Value<input type="number" step="0.01" name="taxable_value" required></label>
      <label>Vendor Invoice No<input name="vendor_invoice_no"></label>
      <label>Payment Receipt Date<input type="date" name="payment_receipt_date"></label>
      <label>Payment Advise No<input name="payment_advise_no"></label>
      <label>Vendor Name<input name="vendor_name"></label>
      <button type="submit">Save</button>
      <a href="list.php" class="btn muted">Cancel</a>
    </form>
  </div>
</body>
</html>
