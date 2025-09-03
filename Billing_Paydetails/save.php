<?php
header('Content-Type: application/json');
require_once 'db.php';

// Accept fields from current UI
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$project_details = $_POST['project_details'] ?? '';
$cost_center = $_POST['cost_center'] ?? '';
$customer_po = $_POST['customer_po'] ?? '';
$cantik_invoice_no = $_POST['cantik_invoice_no'] ?? '';
$cantik_invoice_date = $_POST['cantik_invoice_date'] ?? '';
$cantik_inv_value_taxable = isset($_POST['cantik_inv_value_taxable']) ? (float)$_POST['cantik_inv_value_taxable'] : 0;
$against_vendor_inv_number = $_POST['against_vendor_inv_number'] ?? '';
$payment_recpt_date = $_POST['payment_recpt_date'] ?? '';
$payment_advise_no = $_POST['payment_advise_no'] ?? '';
$vendor_name = $_POST['vendor_name'] ?? '';

if (!$project_details || $cantik_inv_value_taxable <= 0) {
    echo json_encode(['success' => false, 'message' => 'Project and a valid taxable value are required.']);
    exit;
}

// Compute TDS (2%) and Receivable ((taxable*1.18)-tds)
$tds = round($cantik_inv_value_taxable * 0.02, 2);
$receivable = round(($cantik_inv_value_taxable * 1.18) - $tds, 2);

// Normalize dates (Y-m-d) or NULL
function normDate($s) { return $s ? date('Y-m-d', strtotime($s)) : null; }
$cantik_invoice_date = normDate($cantik_invoice_date);
$payment_recpt_date = normDate($payment_recpt_date);

try {
    if ($id > 0) {
        $sql = "UPDATE billing_details SET project_details=?, cost_center=?, customer_po=?, cantik_invoice_no=?, cantik_invoice_date=?, cantik_inv_value_taxable=?, tds=?, receivable=?, against_vendor_inv_number=?, payment_recpt_date=?, payment_advise_no=?, vendor_name=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssdddssssi', $project_details, $cost_center, $customer_po, $cantik_invoice_no, $cantik_invoice_date, $cantik_inv_value_taxable, $tds, $receivable, $against_vendor_inv_number, $payment_recpt_date, $payment_advise_no, $vendor_name, $id);
        $ok = $stmt->execute();
        $msg = 'Billing entry updated';
    } else {
        $sql = "INSERT INTO billing_details (project_details, cost_center, customer_po, cantik_invoice_no, cantik_invoice_date, cantik_inv_value_taxable, tds, receivable, against_vendor_inv_number, payment_recpt_date, payment_advise_no, vendor_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssdddssss', $project_details, $cost_center, $customer_po, $cantik_invoice_no, $cantik_invoice_date, $cantik_inv_value_taxable, $tds, $receivable, $against_vendor_inv_number, $payment_recpt_date, $payment_advise_no, $vendor_name);
        $ok = $stmt->execute();
        $msg = 'Billing entry created';
    }

    if ($ok) {
        echo json_encode(['success' => true, 'message' => $msg]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
