<?php
require './dp.php';
// Join with billing details to fetch Payment Status from NTT (payment receipt date)
// Also lookup Remaining Balance in PO from po_details (by matching Customer PO to po_number)
$sql = "SELECT o.*, 
               o.payment_status_from_ntt AS payment_status_ntt
        FROM outsourcing_detail o
        ORDER BY o.id ASC";
$res = $mysqli->query($sql);
$rows = [];
while ($r = $res->fetch_assoc()) {
    // Convert Excel serial dates to readable format
    if ($r['cantik_po_date'] && is_numeric($r['cantik_po_date'])) {
        $r['cantik_po_date'] = date('Y-m-d', ($r['cantik_po_date'] - 25569) * 86400);
    }
    if ($r['vendor_inv_date'] && is_numeric($r['vendor_inv_date'])) {
        $r['vendor_inv_date'] = date('Y-m-d', ($r['vendor_inv_date'] - 25569) * 86400);
    }
    if ($r['payment_date'] && is_numeric($r['payment_date'])) {
        $r['payment_date'] = date('Y-m-d', ($r['payment_date'] - 25569) * 86400);
    }
    
    // Ensure numeric values are properly formatted
    $r['vendor_inv_value'] = (float)$r['vendor_inv_value'];
    $r['tds_ded'] = (float)$r['tds_ded'];
    $r['net_payable'] = (float)$r['net_payble']; // Map database column to display field
    $r['payment_value'] = (float)$r['payment_value'];
    $r['pending_payment'] = (float)$r['pending_payment'];
    
    $rows[] = $r;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Outsourcing Details - View</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    :root {
      --primary-color: #2563eb;
      --primary-hover: #1d4ed8;
      --secondary-color: #64748b;
      --success-color: #059669;
      --warning-color: #d97706;
      --danger-color: #dc2626;
      --light-bg: #f8fafc;
      --border-color: #e2e8f0;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }

    body {
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: var(--text-primary);
      line-height: 1.6;
    }

    .page-header {
      background: linear-gradient(135deg, var(--warning-color) 0%, #b45309 100%);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
      border-radius: 0 0 20px 20px;
      box-shadow: var(--shadow-lg);
    }

    .page-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin: 0;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .page-subtitle {
      font-size: 1.1rem;
      opacity: 0.9;
      margin: 0.5rem 0 0 0;
      font-weight: 400;
    }

    .back-btn {
      background: rgba(255,255,255,0.2);
      border: 2px solid rgba(255,255,255,0.3);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 50px;
      text-decoration: none;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      font-weight: 500;
    }

    .back-btn:hover {
      background: rgba(255,255,255,0.3);
      border-color: rgba(255,255,255,0.5);
      color: white;
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .search-container {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border-color);
      position: relative;
      overflow: hidden;
    }

    .search-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--warning-color), var(--primary-color));
    }

    .search-label {
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 1rem;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .search-label i {
      color: var(--warning-color);
    }

    .search-input {
      border: 2px solid var(--border-color);
      border-radius: 15px;
      padding: 1rem 1.5rem;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: var(--light-bg);
    }

    .search-input:focus {
      outline: none;
      border-color: var(--warning-color);
      background: white;
      box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.1);
      transform: translateY(-1px);
    }

    .result-count {
      background: var(--light-bg);
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      color: var(--text-secondary);
      font-weight: 500;
      border: 1px solid var(--border-color);
    }

    .table-container {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border-color);
      overflow: hidden;
    }

    .table {
      margin: 0;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    .table thead th {
      background: linear-gradient(135deg, var(--text-primary) 0%, var(--text-secondary) 100%);
      color: white;
      border: none;
      padding: 1.25rem 1rem;
      font-weight: 600;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: relative;
    }

    .table thead th::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, var(--warning-color), var(--primary-color));
    }

    .table tbody tr {
      transition: all 0.2s ease;
      border-left: 4px solid transparent;
    }

    .table tbody tr:hover {
      background: var(--light-bg);
      border-left-color: var(--warning-color);
      transform: translateX(5px);
      box-shadow: var(--shadow-sm);
    }

    .table tbody td {
      padding: 1.25rem 1rem;
      border-color: var(--border-color);
      vertical-align: middle;
    }

    .table tbody tr:nth-child(even) {
      background: rgba(248, 250, 252, 0.5);
    }

    .no-results {
      text-align: center;
      padding: 4rem 2rem;
      color: var(--text-secondary);
    }

    .no-results i {
      font-size: 4rem;
      color: var(--secondary-color);
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    .no-results h5 {
      color: var(--text-primary);
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .no-results p {
      color: var(--text-secondary);
      margin: 0;
    }

    .currency-value {
      font-family: 'Monaco', 'Menlo', monospace;
      font-weight: 600;
      color: var(--success-color);
    }

    .po-number {
      font-family: 'Monaco', 'Menlo', monospace;
      font-weight: 600;
      color: var(--primary-color);
    }

    .vendor-name {
      font-weight: 600;
      color: var(--text-primary);
    }

    .date-value {
      color: var(--text-secondary);
      font-size: 0.9rem;
    }

    .project-details {
      font-weight: 600;
      color: var(--text-primary);
    }

    @media (max-width: 768px) {
      .page-title {
        font-size: 2rem;
      }
      
      .search-container,
      .table-container {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
      }
      
      .table-responsive {
        border-radius: 15px;
      }
    }
  </style>
</head>
<body>
  <!-- Page Header -->
  <div class="page-header">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h1 class="page-title">
            <i class="fas fa-users-cog me-3"></i>
            Outsourcing Details
          </h1>
          <p class="page-subtitle">Complete overview of all outsourcing entries</p>
        </div>
        <div class="col-md-4 text-md-end">
          <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>
            Back to Entries
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <!-- Search Container -->
    <div class="search-container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <label for="searchInput" class="search-label">
            <i class="fas fa-search"></i>
            Search Outsourcing Entries
          </label>
          <input type="text" id="searchInput" class="form-control search-input" 
                 placeholder="Search by project, vendor, PO number, invoice...">
        </div>
        <div class="col-md-6 text-md-end">
          <span class="result-count" id="resultCount">
            <i class="fas fa-info-circle me-2"></i>
            Loading...
          </span>
        </div>
      </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
      <div class="table-responsive">
        <table class="table table-hover" id="outsourcingTable">
          <thead>
            <tr>
              <th><i class="fas fa-hashtag me-2"></i>#</th>
              <th><i class="fas fa-project-diagram me-2"></i>Project Details</th>
              <th><i class="fas fa-building me-2"></i>Cost Center</th>
              <th><i class="fas fa-file-invoice me-2"></i>Customer PO</th>
              <th><i class="fas fa-user-tie me-2"></i>Vendor Name</th>
              <th><i class="fas fa-file-contract me-2"></i>Cantik PO No</th>
              <th><i class="fas fa-calendar me-2"></i>Cantik PO Date</th>
              <th><i class="fas fa-rupee-sign me-2"></i>Cantik PO Value</th>
              <th><i class="fas fa-balance-scale me-2"></i>Remaining Balance</th>
              <th><i class="fas fa-clock me-2"></i>Invoice Frequency</th>
              <th><i class="fas fa-receipt me-2"></i>Vendor Inv Number</th>
              <th><i class="fas fa-calendar-alt me-2"></i>Vendor Inv Date</th>
              <th><i class="fas fa-rupee-sign me-2"></i>Vendor Inv Value</th>
              <th><i class="fas fa-percentage me-2"></i>TDS Deduction</th>
              <th><i class="fas fa-money-bill-wave me-2"></i>Net Payable</th>
              <th><i class="fas fa-check-circle me-2"></i>Payment Status</th>
              <th><i class="fas fa-rupee-sign me-2"></i>Payment Value</th>
              <th><i class="fas fa-calendar-check me-2"></i>Payment Date</th>
              <th><i class="fas fa-hourglass-half me-2"></i>Pending Payment</th>
              <th><i class="fas fa-comment me-2"></i>Remarks</th>
            </tr>
          </thead>
          <tbody>
          <?php $row_num = 1; foreach ($rows as $r): ?>
            <tr data-search="<?=strtolower(htmlspecialchars(($r['project_details'] ?? '') . ' ' . ($r['vendor_name'] ?? '') . ' ' . ($r['customer_po'] ?? '') . ' ' . ($r['cantik_po_no'] ?? '') . ' ' . ($r['cost_center'] ?? '') . ' ' . ($r['vendor_inv_number'] ?? '')))?>">
              <td><span class="badge bg-secondary"><?=htmlspecialchars($row_num)?></span></td>
              <td class="project-details"><?=htmlspecialchars($r['project_details'])?></td>
              <td><span class="badge bg-light text-dark"><?=htmlspecialchars($r['cost_center'])?></span></td>
              <td><span class="badge bg-primary"><?=htmlspecialchars($r['customer_po'] ?? '')?></span></td>
              <td class="vendor-name"><?=htmlspecialchars($r['vendor_name'])?></td>
              <td class="po-number"><?=htmlspecialchars($r['cantik_po_no'])?></td>
              <td class="date-value"><?=htmlspecialchars($r['cantik_po_date'] ?? '')?></td>
              <td class="currency-value">₹<?=number_format($r['cantik_po_value'] ?? 0,2)?></td>
              <td class="currency-value"><?= ($r['remaining_bal_in_po'] ?? 0) ? ('₹'.number_format($r['remaining_bal_in_po'],2)) : '' ?></td>
              <td><?=htmlspecialchars($r['vendor_invoice_frequency'] ?? '')?></td>
              <td class="po-number"><?=htmlspecialchars($r['vendor_inv_number'] ?? '')?></td>
              <td class="date-value"><?=htmlspecialchars($r['vendor_inv_date'] ?? '')?></td>
              <td class="currency-value">₹<?=number_format($r['vendor_inv_value'] ?? 0,2)?></td>
              <td class="currency-value">₹<?=number_format($r['tds_ded'] ?? 0,2)?></td>
              <td class="currency-value">₹<?=number_format($r['net_payable'] ?? 0,2)?></td>
              <td><?=htmlspecialchars($r['payment_status_ntt'] ?? '')?></td>
              <td class="currency-value">₹<?=number_format($r['payment_value'] ?? 0,2)?></td>
              <td class="date-value"><?=htmlspecialchars($r['payment_date'] ?? '')?></td>
              <td class="currency-value">₹<?=number_format($r['pending_payment'] ?? 0,2)?></td>
              <td><?=htmlspecialchars($r['remarks'] ?? '')?></td>
            </tr>
          <?php $row_num++; endforeach; ?>
          </tbody>
        </table>
        
        <!-- No Results Message -->
        <div class="no-results" id="noResults">
          <i class="fas fa-search"></i>
          <h5>No outsourcing entries found</h5>
          <p>Try adjusting your search terms or check the spelling</p>
        </div>
      </div>
    </div>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('outsourcingTable');
    const rows = table.querySelectorAll('tbody tr');
    const resultCount = document.getElementById('resultCount');
    const noResults = document.getElementById('noResults');
    
    function updateSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        
        rows.forEach(row => {
            const searchData = row.getAttribute('data-search');
            if (searchData && searchData.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update result count
        resultCount.innerHTML = `<i class="fas fa-info-circle me-2"></i>Showing ${visibleCount} of ${rows.length} entries`;
        
        // Show/hide no results message
        if (visibleCount === 0 && searchTerm !== '') {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }
    
    // Search on input
    searchInput.addEventListener('input', updateSearch);
    
    // Initialize result count
    resultCount.innerHTML = `<i class="fas fa-info-circle me-2"></i>Showing ${rows.length} of ${rows.length} entries`;
    
    // Clear search on Escape key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInput.value = '';
            updateSearch();
        }
    });
});
</script>
</body>
</html>