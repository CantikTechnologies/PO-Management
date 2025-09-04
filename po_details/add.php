<?php include '../db.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
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

    $sql = "INSERT INTO purchase_orders (project_description,cost_center,sow_number,start_date,end_date,po_number,po_date,po_value,billing_frequency,target_gm,po_status,remarks,vendor_name) VALUES ('{$project}','{$cost_center}','{$sow}',".
            ($start?"'{$start}'":"NULL").",".
            ($end?"'{$end}'":"NULL").",'{$po_number}',".
            ($po_date?"'{$po_date}'":"NULL").",{$po_value},'{$billing}',".
            ($target_gm?"{$target_gm}":"NULL").",'{$status}','{$remarks}','{$vendor}')";

    if ($conn->query($sql)){
        header('Location: list.php'); exit;
    } else echo "Error: ". $conn->error;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add PO</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <h2>Add PO</h2>
    <form method="post">
      <label>Project Description<input name="project_description" required></label>
      <label>Cost Center<input name="cost_center" required></label>
      <label>SOW Number<input name="sow_number"></label>
      <label>Start Date<input type="date" name="start_date"></label>
      <label>End Date<input type="date" name="end_date"></label>
      <label>PO Number<input name="po_number" required></label>
      <label>PO Date<input type="date" name="po_date"></label>
      <label>PO Value<input type="number" step="0.01" name="po_value"></label>
      <label>Billing Frequency<select name="billing_frequency"><option>Monthly</option><option>Quarterly</option><option>Yearly</option><option>Other</option></select></label>
      <label>Target GM (%)<input type="number" step="0.01" name="target_gm"></label>
      <label>Status<select name="po_status"><option>Active</option><option>Closed</option><option>Open</option><option>Cancelled</option></select></label>
      <label>Remarks<textarea name="remarks"></textarea></label>
      <label>Vendor Name<input name="vendor_name" required></label>
      <button type="submit">Save</button>
      <a href="list.php" class="btn muted">Cancel</a>
    </form>
  </div>
</body>
</html>
