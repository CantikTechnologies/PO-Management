<?php include '../db.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location:list.php'); exit; }

if ($_SERVER['REQUEST_METHOD']==='POST'){
    // update
    $project = $conn->real_escape_string($_POST['project_description']);
    $cost_center = $conn->real_escape_string($_POST['cost_center']);
    $sow = $conn->real_escape_string($_POST['sow_number']);
    $start = $_POST['start_date']?:null;
    $end = $_POST['end_date']?:null;
    $po_number = $conn->real_escape_string($_POST['po_number']);
    $po_date = $_POST['po_date']?:null;
    $po_value = $_POST['po_value']?:0;
    $billing = $conn->real_escape_string($_POST['billing_frequency']);
    $target_gm = $_POST['target_gm']?:null;
    $status = $conn->real_escape_string($_POST['po_status']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $vendor = $conn->real_escape_string($_POST['vendor_name']);

    $sql = "UPDATE purchase_orders SET project_description='{$project}', cost_center='{$cost_center}', sow_number='{$sow}', ";
    $sql .= $start?"start_date='{$start}',":"start_date=NULL,";
    $sql .= $end?"end_date='{$end}',":"end_date=NULL,";
    $sql .= "po_number='{$po_number}', po_date=".($po_date?"'{$po_date}'":"NULL").", po_value={$po_value}, billing_frequency='{$billing}', target_gm=".($target_gm?"{$target_gm}":"NULL").", po_status='{$status}', remarks='{$remarks}', vendor_name='{$vendor}' WHERE po_id={$id}";

    if ($conn->query($sql)) header('Location:list.php'); else echo $conn->error;
}

$res = $conn->query("SELECT * FROM purchase_orders WHERE po_id={$id}");
$row = $res->fetch_assoc();
if (!$row) { header('Location:list.php'); exit; }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit PO</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <h2>Edit PO</h2>
    <form method="post">
      <label>Project Description<input name="project_description" value="<?=htmlspecialchars($row['project_description'])?>" required></label>
      <label>Cost Center<input name="cost_center" value="<?=htmlspecialchars($row['cost_center'])?>" required></label>
      <label>SOW Number<input name="sow_number" value="<?=htmlspecialchars($row['sow_number'])?>"></label>
      <label>Start Date<input type="date" name="start_date" value="<?=$row['start_date']?>"></label>
      <label>End Date<input type="date" name="end_date" value="<?=$row['end_date']?>"></label>
      <label>PO Number<input name="po_number" value="<?=htmlspecialchars($row['po_number'])?>" required></label>
      <label>PO Date<input type="date" name="po_date" value="<?=$row['po_date']?>"></label>
      <label>PO Value<input type="number" step="0.01" name="po_value" value="<?=$row['po_value']?>"></label>
      <label>Billing Frequency<select name="billing_frequency"><option<?= $row['billing_frequency']=='Monthly'?' selected':''?>>Monthly</option><option<?= $row['billing_frequency']=='Quarterly'?' selected':''?>>Quarterly</option><option<?= $row['billing_frequency']=='Yearly'?' selected':''?>>Yearly</option><option<?= $row['billing_frequency']=='Other'?' selected':''?>>Other</option></select></label>
      <label>Target GM (%)<input type="number" step="0.01" name="target_gm" value="<?=$row['target_gm']?>"></label>
      <label>Status<select name="po_status"><option<?= $row['po_status']=='Active'?' selected':''?>>Active</option><option<?= $row['po_status']=='Closed'?' selected':''?>>Closed</option><option<?= $row['po_status']=='Open'?' selected':''?>>Open</option><option<?= $row['po_status']=='Cancelled'?' selected':''?>>Cancelled</option></select></label>
      <label>Remarks<textarea name="remarks"><?=htmlspecialchars($row['remarks'])?></textarea></label>
      <label>Vendor Name<input name="vendor_name" value="<?=htmlspecialchars($row['vendor_name'])?>" required></label>
      <button type="submit">Update</button>
      <a href="list.php" class="btn muted">Cancel</a>
    </form>
  </div>
</body>
</html>
