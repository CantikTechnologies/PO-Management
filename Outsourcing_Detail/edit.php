<?php
require './dp.php';
$id = intval($_GET['id'] ?? 0);
if(!$id) { header('Location: view.php'); exit; }

if($_SERVER['REQUEST_METHOD']==='POST'){
  // sanitize and cast values
  $project_details = $_POST['project_details'] ?? '';
  $cost_center = $_POST['cost_center'] ?? '';
  $ntt_po = $_POST['ntt_po'] ?? '';
  $vendor_name = $_POST['vendor_name'] ?? '';
  $cantik_po_no = $_POST['cantik_po_no'] ?? '';
  $cantik_po_date = $_POST['cantik_po_date'] ?: null;
  $cantik_po_value = floatval(str_replace(',', '', $_POST['cantik_po_value'] ?? 0));
  $vendor_inv_frequency = $_POST['vendor_inv_frequency'] ?? '';
  $vendor_inv_number = $_POST['vendor_inv_number'] ?? '';
  $vendor_inv_date = $_POST['vendor_inv_date'] ?: null;
  $vendor_inv_value = floatval(str_replace(',', '', $_POST['vendor_inv_value'] ?? 0));

  // Excel logic (server-side)
  $tds_ded = round($vendor_inv_value * 0.02, 2);
  $net_payable = round(($vendor_inv_value * 1.18) - $tds_ded, 2);
  $payment_status = $_POST['payment_status'] ?? '';
  $payment_value = floatval(str_replace(',', '', $_POST['payment_value'] ?? 0));
  $payment_date = $_POST['payment_date'] ?: null;
  $pending_payment = round($net_payable - $payment_value, 2);
  $remarks = $_POST['remarks'] ?? '';

  $stmt = $mysqli->prepare("UPDATE outsourcing_detail SET
    project_details=?,cost_center=?,ntt_po=?,vendor_name=?,cantik_po_no=?,cantik_po_date=?,cantik_po_value=?,
    vendor_inv_frequency=?,vendor_inv_number=?,vendor_inv_date=?,vendor_inv_value=?,tds_ded=?,net_payable=?,
    payment_status=?,payment_value=?,payment_date=?,pending_payment=?,remarks=?
    WHERE id=?");
  $stmt->bind_param('ssssssdsssdddsdsdsi',
    $project_details,$cost_center,$ntt_po,$vendor_name,$cantik_po_no,$cantik_po_date,$cantik_po_value,
    $vendor_inv_frequency,$vendor_inv_number,$vendor_inv_date,$vendor_inv_value,$tds_ded,$net_payable,
    $payment_status,$payment_value,$payment_date,$pending_payment,$remarks,$id);

  if($stmt->execute()){
    header('Location: view.php'); exit;
  } else {
    $error = $stmt->error;
  }
}

// fetch existing row
$stmt = $mysqli->prepare('SELECT * FROM outsourcing_detail WHERE id=?');
$stmt->bind_param('i',$id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if(!$row){ header('Location: view.php'); exit; }
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Entry #<?= $id ?></title>
  <link rel="stylesheet" href="./style.css">
</head>
<body>
  <div class="container form-card">
    <h2>Edit Entry #<?= $id ?></h2>
    <?php if(!empty($error)): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif;?>
    <form method="post" id="entryForm">
      <label>Project Details<textarea name="project_details"><?=htmlspecialchars($row['project_details'])?></textarea></label>
      <label>Cost Center<input name="cost_center" value="<?=htmlspecialchars($row['cost_center'])?>"></label>
      <label>NTT PO<input name="ntt_po" value="<?=htmlspecialchars($row['ntt_po'])?>"></label>
      <label>Vendor Name<input name="vendor_name" value="<?=htmlspecialchars($row['vendor_name'])?>"></label>
      <label>Cantik PO No<input name="cantik_po_no" value="<?=htmlspecialchars($row['cantik_po_no'])?>"></label>
      <label>Cantik PO Date<input type="date" name="cantik_po_date" value="<?=htmlspecialchars($row['cantik_po_date'])?>"></label>
      <label>Cantik PO Value<input name="cantik_po_value" value="<?=htmlspecialchars($row['cantik_po_value'])?>"></label>
      <label>Vendor Invoice Frequency<input name="vendor_inv_frequency" value="<?=htmlspecialchars($row['vendor_inv_frequency'])?>"></label>
      <label>Vendor Inv Number<input name="vendor_inv_number" value="<?=htmlspecialchars($row['vendor_inv_number'])?>"></label>
      <label>Vendor Inv Date<input type="date" name="vendor_inv_date" value="<?=htmlspecialchars($row['vendor_inv_date'])?>"></label>
      <label>Vendor Inv Value<input id="vendor_inv_value" name="vendor_inv_value" type="number" step="0.01" value="<?=htmlspecialchars($row['vendor_inv_value'])?>"></label>
      <label>TDS Deduction<input id="tds_ded" name="tds_ded" readonly value="<?=htmlspecialchars($row['tds_ded'])?>"></label>
      <label>Net Payable<input id="net_payable" name="net_payable" readonly value="<?=htmlspecialchars($row['net_payable'])?>"></label>
      <label>Payment Status<input name="payment_status" value="<?=htmlspecialchars($row['payment_status'])?>"></label>
      <label>Payment Value<input id="payment_value" name="payment_value" type="number" step="0.01" value="<?=htmlspecialchars($row['payment_value'])?>"></label>
      <label>Payment Date<input type="date" name="payment_date" value="<?=htmlspecialchars($row['payment_date'])?>"></label>
      <label>Pending Payment<input id="pending_payment" name="pending_payment" readonly value="<?=htmlspecialchars($row['pending_payment'])?>"></label>
      <label>Remarks<textarea name="remarks"><?=htmlspecialchars($row['remarks'])?></textarea></label>
      <div class="form-actions">
        <button type="submit" class="btn">Update</button>
        <a class="btn ghost" href="view.php">Cancel</a>
      </div>
    </form>
  </div>
  <script src="./script.js"></script>
</body>
</html>
