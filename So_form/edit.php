<?php
require 'db.php';

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM so_form WHERE id=$id");
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("UPDATE so_form SET project=?, cost_centre=?, po_no=?, po_value=?, billed_till_date=?, vendor_name=?, vendor_po_no=?, po_to_vendor_value=?, vendor_invoicing_till_date=?, margin_till_date=?, variance_in_gm=? WHERE id=?");
    $stmt->bind_param(
        "sssddsssddsi",
        $_POST['project'],
        $_POST['cost_centre'],
        $_POST['po_no'],
        $_POST['po_value'],
        $_POST['billed_till_date'],
        $_POST['vendor_name'],
        $_POST['vendor_po_no'],
        $_POST['po_to_vendor_value'],
        $_POST['vendor_invoicing_till_date'],
        $_POST['margin_till_date'],
        $_POST['variance_in_gm'],
        $id
    );
    $stmt->execute();
    $stmt->close();
    header("Location: view.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit SO Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e0f4ff; }
        .btn-custom {
            background-color: skyblue;
            color: black;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: black;
            color: white;
        }
        .form-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            border: 2px solid black;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">Edit SO Entry</h2>
    <div class="form-container">
        <form method="post">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Project</label>
                    <input type="text" name="project" class="form-control" value="<?= htmlspecialchars($data['project']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cost Centre</label>
                    <input type="text" name="cost_centre" class="form-control" value="<?= htmlspecialchars($data['cost_centre']) ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">PO No</label>
                    <input type="text" name="po_no" class="form-control" value="<?= htmlspecialchars($data['po_no']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">PO Value</label>
                    <input type="number" step="0.01" name="po_value" class="form-control" value="<?= $data['po_value'] ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Billed Till Date</label>
                    <input type="number" step="0.01" name="billed_till_date" class="form-control" value="<?= $data['billed_till_date'] ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Vendor Name</label>
                    <input type="text" name="vendor_name" class="form-control" value="<?= htmlspecialchars($data['vendor_name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Vendor PO No</label>
                    <input type="text" name="vendor_po_no" class="form-control" value="<?= htmlspecialchars($data['vendor_po_no']) ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">PO to Vendor Value</label>
                    <input type="number" step="0.01" name="po_to_vendor_value" class="form-control" value="<?= $data['po_to_vendor_value'] ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vendor Invoicing Till Date</label>
                    <input type="number" step="0.01" name="vendor_invoicing_till_date" class="form-control" value="<?= $data['vendor_invoicing_till_date'] ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Margin Till Date</label>
                    <input type="text" name="margin_till_date" class="form-control" value="<?= htmlspecialchars($data['margin_till_date']) ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Variance in GM</label>
                <input type="text" name="variance_in_gm" class="form-control" value="<?= htmlspecialchars($data['variance_in_gm']) ?>">
            </div>
            <button type="submit" class="btn btn-custom">Update</button>
            <a href="view.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>
