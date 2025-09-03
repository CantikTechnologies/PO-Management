<?php
require 'db.php';
header('Content-Type: application/json');

try {
    // Get all PO details with calculated fields
    $sql = "SELECT po_id, po_number FROM po_details ORDER BY po_date DESC";
    
    $result = $conn->query($sql);
    $poData = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $poNumber = $row['customer_po_no'];
            
            // Calculate billed amount from billing details (Excel: SUMIF(billing taxable))
            $billedSql = "
                SELECT SUM(cantik_inv_value_taxable) as total_billed
                FROM billing_details 
                WHERE customer_po = ?
            ";
            $billedStmt = $conn->prepare($billedSql);
            $billedStmt->bind_param("s", $poNumber);
            $billedStmt->execute();
            $billedResult = $billedStmt->get_result();
            $billedAmount = $billedResult->fetch_assoc()['total_billed'] ?? 0;
            
            // Calculate remaining balance in PO (Excel: PO Value - Billed Amount)
            $remainingBalance = $row['billed_po_no'] - $billedAmount;
            
            // Get outsourcing details for this PO (Excel: VLOOKUP to outsourcing details)
            // Note: outsourcing_detail uses 'ntt_po' column, not 'customer_po'
            $outsourcingSql = "
                SELECT 
                    cantik_po_no,
                    cantik_po_date,
                    cantik_po_value,
                    vendor_inv_value,
                    SUM(tds_ded) as total_tds_ded,
                    SUM(net_payable) as total_net_payable,
                    SUM(payment_value) as total_payment_value,
                    SUM(pending_payment) as total_pending_payment
                FROM outsourcing_detail 
                WHERE ntt_po = ?
                GROUP BY cantik_po_no
                LIMIT 1
            ";
            $outsourcingStmt = $conn->prepare($outsourcingSql);
            $outsourcingStmt->bind_param("s", $poNumber);
            $outsourcingStmt->execute();
            $outsourcingResult = $outsourcingStmt->get_result();
            $outsourcingData = $outsourcingResult->fetch_assoc();
            
            $cantikPoNo = $outsourcingData['cantik_po_no'] ?? '';
            $cantikPoDate = $outsourcingData['cantik_po_date'] ?? '';
            $cantikPoValue = $outsourcingData['cantik_po_value'] ?? 0;
            $vendorInvValue = $outsourcingData['vendor_inv_value'] ?? 0;
            $totalTdsDed = $outsourcingData['total_tds_ded'] ?? 0;
            $totalNetPayable = $outsourcingData['total_net_payable'] ?? 0;
            $totalPaymentValue = $outsourcingData['total_payment_value'] ?? 0;
            $totalPendingPayment = $outsourcingData['total_pending_payment'] ?? 0;
            
            // Calculate vendor invoicing till date (Excel: SUMIF from outsourcing details)
            // Note: outsourcing_detail uses 'ntt_po' column, not 'customer_po'
            $vendorInvoicingSql = "
                SELECT SUM(vendor_inv_value) as total_vendor_inv
                FROM outsourcing_detail 
                WHERE ntt_po = ?
            ";
            $vendorInvoicingStmt = $conn->prepare($vendorInvoicingSql);
            $vendorInvoicingStmt->bind_param("s", $poNumber);
            $vendorInvoicingStmt->execute();
            $vendorInvoicingResult = $vendorInvoicingStmt->get_result();
            $vendorInvoicingTillDate = $vendorInvoicingResult->fetch_assoc()['total_vendor_inv'] ?? 0;
            
            // Calculate remaining balance in PO for outsourcing (Excel: Cantik PO Value - Vendor Invoicing)
            $remainingBalanceOutsourcing = $cantikPoValue - $vendorInvoicingTillDate;
            
            // Calculate margin till date (Excel: (Billed - Vendor Cost) / Billed)
            $marginTillDate = $billedAmount > 0 ? (($billedAmount - $vendorInvoicingTillDate) / $billedAmount) * 100 : 0;
            
            // Calculate variance in GM (Excel: Target GM - Actual GM)
            $targetGM = floatval($row['target_gm'] ?? 0);
            $varianceInGM = $targetGM - $marginTillDate;
            
            $poData[] = [
                'id' => $row['id'],
                'project' => $row['project'],
                'cost_centre' => $row['cost_centre'],
                'customer_po_no' => $poNumber,
                'billed_po_no' => $row['billed_po_no'],
                'remaining_balance_in_po' => $remainingBalance,
                'vendor_name' => $row['vendor_name'],
                'cantik_po_no' => $cantikPoNo,
                'cantik_po_date' => $cantikPoDate,
                'cantik_po_value' => $cantikPoValue,
                'vendor_invoicing_till_date' => $vendorInvoicingTillDate,
                'remaining_balance_in_po_outsourcing' => $remainingBalanceOutsourcing,
                'margin_till_date' => round($marginTillDate, 2),
                'target_gm' => $targetGM,
                'variance_in_gm' => round($varianceInGM, 2),
                'po_status' => $row['po_status'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'po_date' => $row['po_date'],
                'billing_frequency' => $row['billing_frequency'],
                'remarks' => $row['remarks'],
                'billed_amount' => $billedAmount,
                'total_tds_ded' => $totalTdsDed,
                'total_net_payable' => $totalNetPayable,
                'total_payment_value' => $totalPaymentValue,
                'total_pending_payment' => $totalPendingPayment
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $poData
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>
