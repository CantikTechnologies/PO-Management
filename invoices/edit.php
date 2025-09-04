<?php include '../db.php';
$id = intval($_GET['id'] ?? 0); if (!$id) header('Location:list.php');
if ($_SERVER['REQUEST_METHOD']==='POST'){
    $inv_no = $conn->real_escape_string($_POST['cantik_invoice_no']);
    $inv_date = $_POST['cantik_invoice_date']?:null;
    $taxable = $_POST['taxable_value']?:0;
    $vendor_inv = $conn->real_escape_string($_POST['vendor_invoice_no']);
    $payment_receipt_date = $_POST['payment_receipt_date']?:null;
    $payment_advise_no = $conn->real_escape_string($_POST['payment_advise_no']);
    $vendor_name = $conn->real_escape_string($_POST['vendor_name']);

    $sql = "UPDATE invoices SET cantik_invoice_no='{$inv_no}', cantik_invoice_date=".($inv_date?"'{$inv_date}'":"NULL").", taxable_value={$taxable}, vendor_invoice_no='{$vendor_inv}', payment_receipt_date=".($payment_receipt_date?"'{$payment_receipt_date}'":"NULL").", payment_advise_no='{$payment_advise_no}', vendor_name='{$vendor_name}' WHERE invoice_id={$id}";
    if ($conn->query($sql)) header('Location:list.php'); else echo $conn->error;
}

$res = $conn->query("SELECT inv.*, po.po_number FROM invoices inv JOIN purchase_orders po ON po.po_id=inv.po_id WHERE invoice_id={$id}");
$row = $res->fetch_assoc(); if (!$row) header('Location:list.php');
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Edit Invoice</title><link rel="stylesheet" href="../assets/style.css"></head>
<body>
  <div class="container">
    <h2>Edit Invoice</h2>
    <form method="post">
      <label>Customer PO Number<input name="po_number" value="<?=$row['po_number']?>" disabled></label>
      <label>Cantik Invoice No<input name="cantik_invoice_no" value="<?=htmlspecialchars($row['cantik_invoice_no'])?>"></label>
      <label>Cantik Invoice Date<input type="date" name="cantik_invoice_date" value="<?=$row['cantik_invoice_date']?>"></label>
      <label>Taxable Value<input type="number" step="0.01" name="taxable_value" value="<?=$row['taxable_value']?>"></label>
      <label>Vendor Invoice No<input name="vendor_invoice_no" value="<?=htmlspecialchars($row['vendor_invoice_no'])?>"></label>
      <label>Payment Receipt Date<input type="date" name="payment_receipt_date" value="<?=$row['payment_receipt_date']?>"></label>
      <label>Payment Advise No<input name="payment_advise_no" value="<?=htmlspecialchars($row['payment_advise_no'])?>"></label>
      <label>Vendor Name<input name="vendor_name" value="<?=htmlspecialchars($row['vendor_name'])?>"></label>
      <button type="submit">Update</button>
      <a href="list.php" class="btn muted">Cancel</a>
    </form>
  </div>
</body>
</html>
