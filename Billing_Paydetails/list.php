<?php
header('Content-Type: application/json');
require_once 'db.php';

try {
    $q = $conn->query("SELECT * FROM billing_details ORDER BY id DESC");
    $rows = [];
    while ($r = $q->fetch_assoc()) {
        // format fields
        if (!empty($r['cantik_invoice_date']) && is_numeric($r['cantik_invoice_date'])) {
            // if it is an Excel serial date, convert; else pass string
            if ($r['cantik_invoice_date'] > 10000) {
                $r['cantik_invoice_date'] = date('Y-m-d', ($r['cantik_invoice_date'] - 25569) * 86400);
            }
        }
        $r['cantik_inv_value_taxable'] = (float)($r['cantik_inv_value_taxable'] ?? 0);
        $r['tds'] = (float)($r['tds'] ?? 0);
        $r['receivable'] = (float)($r['receivable'] ?? 0);
        $rows[] = $r;
    }
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
