<?php include '../db.php';
$id = intval($_GET['id'] ?? 0); if (!$id) header('Location:list.php');
if ($_SERVER['REQUEST_METHOD']==='POST'){
    $vendor_invoice_no = $conn->real_escape_string($_POST['vendor_invoice_no']);
    $vendor_invoice_date = $_POST['vendor_invoice_date']?:null;
    $vendor_invoice_value = $_POST['vendor_invoice_value']?:0;
    $payment_status = $conn->real_escape_string($_POST['payment_status']);
    $payment_value = $_POST['payment_value']?:null;
    $payment_date = $_POST['payment_date']?:null;
    $remarks = $conn->real_escape_string($_POST['remarks']);

    $sql = "UPDATE outsourcing_details SET vendor_invoice_no='{$vendor_invoice_no}', vendor_invoice_date=".($vendor_invoice_date?"'{$vendor_invoice_date}'":"NULL").", vendor_invoice_value={$vendor_invoice_value}, payment_status='{$payment_status}', payment_value=".($payment_value?"{$payment_value}":"NULL").", payment_date=".($payment_date?"'{$payment_date}'":"NULL").", remarks='{$remarks}' WHERE outsourcing_id={$id}";
    if ($conn->query($sql)) header('Location:list.php'); else echo $conn->error;
}
$res = $conn->query("SELECT outd.*, po.po_number FROM outsourcing_details outd JOIN purchase_orders po ON po.po_id=outd.po_id WHERE outsourcing_id={$id}");
$row = $res->fetch_assoc(); if (!$row) header('Location:list.php');
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Edit Outsourcing</title><link rel="stylesheet" href="../assets/style.css"></head>
<body>
  <div class="container">
    <h2>Edit Outsourcing</h2>
    <form method="post">
      <label>Customer PO Number<input value="<?=$row['po_number']?>" disabled></label>
      <label>Vendor Invoice No<input name="vendor_invoice_no" value="<?=htmlspecialchars($row['vendor_invoice_no'])?>"></label>
      <label>Vendor Invoice Date<input type="date" name="vendor_invoice_date" value="<?=$row['vendor_invoice_date']?>"></label>
      <label>Vendor Invoice Value<input type="number" step="0.01" name="vendor_invoice_value" value="<?=$row['vendor_invoice_value']?>"></label>
      <label>Payment Status<input name="payment_status" value="<?=htmlspecialchars($row['payment_status'])?>"></label>
      <label>Payment Value<input type="number" step="0.01" name="payment_value" value="<?=$row['payment_value']?>"></label>
      <label>Payment Date<input type="date" name="payment_date" value="<?=$row['payment_date']?>"></label>
      <label>Remarks<textarea name="remarks"><?=htmlspecialchars($row['remarks'])?></textarea></label>
      <button type="submit">Update</button>
      <a href="list.php" class="btn muted">Cancel</a>
    </form>
  </div>
</body>
</html>
