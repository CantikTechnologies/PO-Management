<?php
require 'db.php';

// Function to convert Excel serial number to readable date
function excelSerialToDate($serial) {
    if (!$serial) return '-';
    $excelEpoch = new DateTime('1900-01-01');
    $date = clone $excelEpoch;
    $date->add(new DateInterval('P' . ($serial - 2) . 'D'));
    return $date->format('Y-m-d');
}

// Function to format currency
function formatCurrency($amount) {
    return '₹' . number_format($amount, 2);
}

// Function to format percentage
function formatPercentage($value) {
    return (floatval($value) * 100) . '%';
}

// Fetch PO entries
$sql = "SELECT * FROM po_details ORDER BY created_at DESC";
$res = $conn->query($sql);
$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PO Details - View All</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="po_details_new.css">
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
      background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
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
      background: linear-gradient(90deg, var(--primary-color), var(--success-color));
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
      color: var(--primary-color);
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
      border-color: var(--primary-color);
      background: white;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
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
      background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    }

    .table tbody tr {
      transition: all 0.2s ease;
      border-left: 4px solid transparent;
    }

    .table tbody tr:hover {
      background: var(--light-bg);
      border-left-color: var(--primary-color);
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

    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 25px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .status-active {
      background: rgba(5, 150, 105, 0.1);
      color: var(--success-color);
      border: 1px solid rgba(5, 150, 105, 0.3);
    }

    .status-pending {
      background: rgba(217, 119, 6, 0.1);
      color: var(--warning-color);
      border: 1px solid rgba(217, 119, 6, 0.3);
    }

    .status-completed {
      background: rgba(37, 99, 235, 0.1);
      color: var(--primary-color);
      border: 1px solid rgba(37, 99, 235, 0.3);
    }

    .status-cancelled {
      background: rgba(220, 38, 38, 0.1);
      color: var(--danger-color);
      border: 1px solid rgba(220, 38, 38, 0.3);
    }

    .status-hold {
      background: rgba(100, 116, 139, 0.1);
      color: var(--secondary-color);
      border: 1px solid rgba(100, 116, 139, 0.3);
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.8rem;
      border-radius: 20px;
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
            <i class="fas fa-file-contract me-3"></i>
            PO Details Overview
          </h1>
          <p class="page-subtitle">Complete overview of all Purchase Orders</p>
        </div>
        <div class="col-md-4 text-md-end">
          <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>
            Back to Form
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
            Search PO Details
          </label>
          <input type="text" id="searchInput" class="form-control search-input" 
                 placeholder="Search by project, PO number, vendor, cost center...">
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
        <table class="table table-hover" id="poTable">
          <thead>
            <tr>
              <th><i class="fas fa-hashtag me-2"></i>#</th>
              <th><i class="fas fa-project-diagram me-2"></i>Project Description</th>
              <th><i class="fas fa-building me-2"></i>Cost Center</th>
              <th><i class="fas fa-file-alt me-2"></i>SOW Number</th>
              <th><i class="fas fa-hashtag me-2"></i>PO Number</th>
              <th><i class="fas fa-calendar me-2"></i>Start Date</th>
              <th><i class="fas fa-calendar me-2"></i>End Date</th>
              <th><i class="fas fa-calendar me-2"></i>PO Date</th>
              <th><i class="fas fa-rupee-sign me-2"></i>PO Value</th>
              <th><i class="fas fa-clock me-2"></i>Billing Frequency</th>
              <th><i class="fas fa-percentage me-2"></i>Target GM</th>
              <th><i class="fas fa-money-bill-wave me-2"></i>Pending Amount</th>
              <th><i class="fas fa-toggle-on me-2"></i>Status</th>
              <th><i class="fas fa-user-tie me-2"></i>Vendor</th>
              <th><i class="fas fa-cogs me-2"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr data-search="<?=strtolower(htmlspecialchars($r['project_description'] . ' ' . $r['po_number'] . ' ' . $r['vendor_name'] . ' ' . $r['cost_center'] . ' ' . $r['sow_number']))?>">
              <td><span class="badge bg-secondary"><?=htmlspecialchars($r['id'])?></span></td>
              <td>
                <div class="fw-bold text-primary"><?=htmlspecialchars($r['project_description'])?></div>
                <?php if (!empty($r['remarks'])): ?>
                  <small class="text-muted"><?=htmlspecialchars(substr($r['remarks'], 0, 50))?><?=strlen($r['remarks']) > 50 ? '...' : ''?></small>
                <?php endif; ?>
              </td>
              <td><span class="badge bg-light text-dark"><?=htmlspecialchars($r['cost_center'])?></span></td>
              <td><span class="badge bg-info"><?=htmlspecialchars($r['sow_number'])?></span></td>
              <td class="po-number"><?=htmlspecialchars($r['po_number'])?></td>
              <td class="date-value"><?=excelSerialToDate($r['start_date'])?></td>
              <td class="date-value"><?=excelSerialToDate($r['end_date'])?></td>
              <td class="date-value"><?=excelSerialToDate($r['po_date'])?></td>
              <td class="currency-value"><?=formatCurrency($r['po_value'])?></td>
              <td><span class="badge bg-light text-dark"><?=htmlspecialchars($r['billing_frequency'])?></span></td>
              <td class="currency-value"><?=formatPercentage($r['target_gm'])?></td>
              <td class="currency-value"><?=formatCurrency($r['pending_amount'])?></td>
              <td>
                <span class="status-badge status-<?=strtolower(str_replace(' ', '-', $r['po_status']))?>">
                  <?=htmlspecialchars($r['po_status'])?>
                </span>
              </td>
              <td class="vendor-name"><?=htmlspecialchars($r['vendor_name'] ?: '-')?></td>
              <td>
                <div class="action-buttons">
                  <button class="btn btn-outline-primary btn-sm" onclick="editPO(<?=$r['id']?>)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-sm" onclick="deletePO(<?=$r['id']?>, '<?=htmlspecialchars($r['po_number'])?>')">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        
        <!-- No Results Message -->
        <div class="no-results" id="noResults">
          <i class="fas fa-search"></i>
          <h5>No PO details found</h5>
          <p>Try adjusting your search terms or check the spelling</p>
        </div>
      </div>
    </div>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('poTable');
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

// Edit PO function
function editPO(id) {
    window.location.href = `index.php?edit=${id}`;
}

// Delete PO function
function deletePO(id, poNumber) {
    if (confirm(`Are you sure you want to delete PO ${poNumber}? This action cannot be undone.`)) {
        fetch('delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'PO deleted successfully!');
                window.location.reload();
            } else {
                alert(data.error || 'Failed to delete PO');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting PO');
        });
    }
}
</script>
</body>
</html>
