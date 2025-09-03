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

try {
    // Get total counts and values
    $totalPOs = $conn->query("SELECT COUNT(*) as count FROM po_details")->fetch_assoc()['count'];
    $totalValue = $conn->query("SELECT SUM(po_value) as total FROM po_details")->fetch_assoc()['total'];
    $totalPending = $conn->query("SELECT SUM(pending_amount) as total FROM po_details")->fetch_assoc()['total'];
    
    // Get status breakdown
    $statusBreakdown = $conn->query("SELECT po_status, COUNT(*) as count, SUM(po_value) as value FROM po_details GROUP BY po_status ORDER BY count DESC");
    
    // Get cost center breakdown
    $costCenterBreakdown = $conn->query("SELECT cost_center, COUNT(*) as count, SUM(po_value) as value FROM po_details GROUP BY cost_center ORDER BY value DESC");
    
    // Get vendor breakdown
    $vendorBreakdown = $conn->query("SELECT vendor_name, COUNT(*) as count, SUM(po_value) as value FROM po_details WHERE vendor_name IS NOT NULL AND vendor_name != '' GROUP BY vendor_name ORDER BY value DESC LIMIT 10");
    
    // Get recent POs
    $recentPOs = $conn->query("SELECT * FROM po_details ORDER BY created_at DESC LIMIT 5");
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PO Details - Summary & Totals</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="po_details_new.css">
  <style>
    .stats-card {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 4px 20px -2px rgba(0,0,0,0.1);
      border: 1px solid #e2e8f0;
      margin-bottom: 2rem;
    }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    
    .stat-label {
      color: #64748b;
      font-size: 1rem;
      font-weight: 500;
    }
    
    .stat-card-primary .stat-number {
      color: #2563eb;
    }
    
    .stat-card-success .stat-number {
      color: #059669;
    }
    
    .stat-card-warning .stat-number {
      color: #d97706;
    }
    
    .stat-card-info .stat-number {
      color: #0891b2;
    }
    
    .breakdown-table {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .breakdown-table th {
      background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
      color: white;
      border: none;
      padding: 1rem;
      font-weight: 600;
    }
    
    .breakdown-table td {
      padding: 1rem;
      border-color: #e2e8f0;
      vertical-align: middle;
    }
    
    .breakdown-table tbody tr:nth-child(even) {
      background: #f8fafc;
    }
    
    .page-header {
      background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
      border-radius: 0 0 20px 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
            <i class="fas fa-chart-bar me-3"></i>
            PO Details Summary & Totals
          </h1>
          <p class="page-subtitle">Comprehensive overview of all Purchase Orders</p>
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
    <?php if (isset($error)): ?>
      <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Error: <?= htmlspecialchars($error) ?>
      </div>
    <?php else: ?>
      <!-- Statistics Cards -->
      <div class="row">
        <div class="col-md-3">
          <div class="stats-card stat-card-primary">
            <div class="stat-number"><?= number_format($totalPOs) ?></div>
            <div class="stat-label">Total POs</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stats-card stat-card-success">
            <div class="stat-number"><?= formatCurrency($totalValue) ?></div>
            <div class="stat-label">Total PO Value</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stats-card stat-card-warning">
            <div class="stat-number"><?= formatCurrency($totalPending) ?></div>
            <div class="stat-label">Total Pending Amount</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stats-card stat-card-info">
            <div class="stat-number"><?= formatCurrency($totalValue - $totalPending) ?></div>
            <div class="stat-label">Total Utilized Amount</div>
          </div>
        </div>
      </div>

      <!-- Breakdown Tables -->
      <div class="row">
        <!-- Status Breakdown -->
        <div class="col-md-6">
          <div class="stats-card">
            <h4><i class="fas fa-toggle-on me-2"></i>PO Status Breakdown</h4>
            <div class="breakdown-table">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Value</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $statusBreakdown->fetch_assoc()): ?>
                    <tr>
                      <td>
                        <span class="badge bg-light text-dark"><?= htmlspecialchars($row['po_status']) ?></span>
                      </td>
                      <td><?= number_format($row['count']) ?></td>
                      <td class="fw-bold text-success"><?= formatCurrency($row['value']) ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Cost Center Breakdown -->
        <div class="col-md-6">
          <div class="stats-card">
            <h4><i class="fas fa-building me-2"></i>Cost Center Breakdown</h4>
            <div class="breakdown-table">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Cost Center</th>
                    <th>Count</th>
                    <th>Value</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $costCenterBreakdown->fetch_assoc()): ?>
                    <tr>
                      <td>
                        <span class="badge bg-info"><?= htmlspecialchars($row['cost_center']) ?></span>
                      </td>
                      <td><?= number_format($row['count']) ?></td>
                      <td class="fw-bold text-success"><?= formatCurrency($row['value']) ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Vendor Breakdown -->
      <div class="row">
        <div class="col-12">
          <div class="stats-card">
            <h4><i class="fas fa-user-tie me-2"></i>Top Vendors by Value</h4>
            <div class="breakdown-table">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>Vendor Name</th>
                    <th>PO Count</th>
                    <th>Total Value</th>
                    <th>Average PO Value</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $vendorBreakdown->fetch_assoc()): ?>
                    <tr>
                      <td class="fw-bold"><?= htmlspecialchars($row['vendor_name']) ?></td>
                      <td><?= number_format($row['count']) ?></td>
                      <td class="fw-bold text-success"><?= formatCurrency($row['value']) ?></td>
                      <td class="text-muted"><?= formatCurrency($row['value'] / $row['count']) ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent POs -->
      <div class="row">
        <div class="col-12">
          <div class="stats-card">
            <h4><i class="fas fa-clock me-2"></i>Recent Purchase Orders</h4>
            <div class="breakdown-table">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th>PO Number</th>
                    <th>Project Description</th>
                    <th>Cost Center</th>
                    <th>PO Value</th>
                    <th>Status</th>
                    <th>Created Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $recentPOs->fetch_assoc()): ?>
                    <tr>
                      <td class="fw-bold text-primary"><?= htmlspecialchars($row['po_number']) ?></td>
                      <td>
                        <div class="fw-bold"><?= htmlspecialchars(substr($row['project_description'], 0, 50)) ?><?= strlen($row['project_description']) > 50 ? '...' : '' ?></div>
                      </td>
                      <td><span class="badge bg-light text-dark"><?= htmlspecialchars($row['cost_center']) ?></span></td>
                      <td class="fw-bold text-success"><?= formatCurrency($row['po_value']) ?></td>
                      <td>
                        <span class="badge bg-light text-dark"><?= htmlspecialchars($row['po_status']) ?></span>
                      </td>
                      <td class="text-muted"><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
