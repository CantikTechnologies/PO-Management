<?php
// Test version of dashboard without login requirement
// This is for debugging purposes only

// Database connections for different modules
$billingConn = new mysqli('127.0.0.1', 'root', '', 'po_management');
$outsourcingConn = new mysqli('127.0.0.1', 'root', '', 'po_management');
$poConn = new mysqli('127.0.0.1', 'root', '', 'po_management');

// Get Billing Totals (Excel: Billing and Payment Details)
$billingTotals = [];
if ($billingConn && !$billingConn->connect_error) {
    // Check if billing_details table exists
    $tableCheck = $billingConn->query("SHOW TABLES LIKE 'billing_details'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $billingQuery = "SELECT 
            SUM(cantik_inv_value_taxable) as total_taxable,
            SUM(tds) as total_tds,
            SUM(receivable) as total_receivable,
            COUNT(*) as total_invoices
            FROM billing_details";
        $billingResult = $billingConn->query($billingQuery);
        if ($billingResult) {
            $billingTotals = $billingResult->fetch_assoc();
        }
    }
}

// Get Outsourcing Totals (Excel: Outsourcing Details)
$outsourcingTotals = [];
if ($outsourcingConn && !$outsourcingConn->connect_error) {
    // Check if outsourcing_detail table exists
    $tableCheck = $outsourcingConn->query("SHOW TABLES LIKE 'outsourcing_detail'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $outsourcingQuery = "SELECT 
            SUM(vendor_inv_value) as total_vendor_inv,
            SUM(tds_ded) as total_tds_ded,
            SUM(net_payable) as total_net_payable,
            SUM(payment_value) as total_payment_value,
            SUM(pending_payment) as total_pending_payment,
            COUNT(*) as total_entries
            FROM outsourcing_detail";
        $outsourcingResult = $outsourcingConn->query($outsourcingQuery);
        if ($outsourcingResult) {
            $outsourcingTotals = $outsourcingResult->fetch_assoc();
        }
    }
}

// Get PO Totals (Excel: PO Details)
$poTotals = [];
if ($poConn && !$poConn->connect_error) {
    // Check if po_details table exists
    $tableCheck = $poConn->query("SHOW TABLES LIKE 'po_details'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $poQuery = "SELECT 
            SUM(po_value) as total_po_value,
            SUM(pending_amount) as total_po_pending,
            COUNT(*) as total_pos,
            COUNT(CASE WHEN po_status = 'closed' THEN 1 END) as closed_pos,
            COUNT(CASE WHEN po_status = 'active' THEN 1 END) as active_pos
            FROM po_details";
        $poResult = $poConn->query($poQuery);
        if ($poResult) {
            $poTotals = $poResult->fetch_assoc();
        }
    }
}

// Calculate derived metrics
$totalOutstanding = ($poTotals['total_po_value'] ?? 0) - ($billingTotals['total_taxable'] ?? 0);
$totalNetReceivable = ($billingTotals['total_receivable'] ?? 0) - ($outsourcingTotals['total_payment_value'] ?? 0);
$profitabilityRatio = ($billingTotals['total_taxable'] ?? 0) > 0 ? 
    (($billingTotals['total_taxable'] ?? 0) - ($outsourcingTotals['total_vendor_inv'] ?? 0)) / ($billingTotals['total_taxable'] ?? 0) * 100 : 0;

// Normalize outsourcing derived figures for display consistency
$outsVendorInv = (float)($outsourcingTotals['total_vendor_inv'] ?? 0);
$outsNetPayable = (float)($outsourcingTotals['total_net_payable'] ?? 0);
$outsPaid = (float)($outsourcingTotals['total_payment_value'] ?? 0);
$outsPending = $outsNetPayable - $outsPaid;
$outsOverpaid = $outsPending < 0 ? abs($outsPending) : 0;
$outsPendingDisplay = $outsPending > 0 ? $outsPending : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Financial Dashboard - PO Management (TEST)</title>
  
  <!-- Import Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="../shared/nav.css?v=4">
  <link rel="stylesheet" href="style.css?v=3">
</head>
<body>
  <?php include '../shared/nav.php'; ?>

  <div class="content">
    <div class="dashboard-header">
        <h2>Po-Management Dashboard (TEST VERSION)</h2>
        <div class="filter-container">
            <div class="filter-dropdown">
                <label for="filter">Filter by:</label>
                <select name="filter" id="filter">
                    <option value="all">All Time</option>
                    <option value="monthly">This Month</option>
                    <option value="quarterly">This Quarter</option>
                    <option value="yearly">This Year</option>
                </select>
            </div>
            <button>Go</button>
        </div>
    </div>
    
    <button class="refresh-btn" onclick="location.reload()">
      &#x1F504; Refresh Data
    </button>
    
    <div class="financial-summary">
      <div class="summary-card">
        <h3>
          <span class="card-icon">💰</span>
          Billing & Receivables
        </h3>
        <div class="amount">₹<?= number_format($billingTotals['total_taxable'] ?? 0, 2) ?></div>
        <div class="label">Total Taxable Value</div>
        <div class="trend positive">
          TDS: ₹<?= number_format($billingTotals['total_tds'] ?? 0, 2) ?> | 
          Receivable: ₹<?= number_format($billingTotals['total_receivable'] ?? 0, 2) ?>
        </div>
      </div>
      
      <div class="summary-card">
        <h3>
          <span class="card-icon">🤝</span>
          Outsourcing Costs
        </h3>
        <div class="amount">₹<?= number_format($outsourcingTotals['total_vendor_inv'] ?? 0, 2) ?></div>
        <div class="label">Total Vendor Invoices</div>
        <div class="trend neutral">
          Net Payable: ₹<?= number_format($outsNetPayable, 2) ?> | 
          Pending: ₹<?= number_format($outsPendingDisplay, 2) ?><?= $outsOverpaid > 0 ? ' (Overpaid: ₹'.number_format($outsOverpaid, 2).')' : '' ?>
        </div>
      </div>
      
      <div class="summary-card">
        <h3>
          <span class="card-icon">📑</span>
          Purchase Orders
        </h3>
        <div class="amount">₹<?= number_format($poTotals['total_po_value'] ?? 0, 2) ?></div>
        <div class="label">Total PO Value</div>
        <div class="trend <?= ($poTotals['total_po_pending'] ?? 0) > 0 ? 'negative' : 'positive' ?>">
          Pending: ₹<?= number_format($poTotals['total_po_pending'] ?? 0, 2) ?> | 
          Active: <?= $poTotals['active_pos'] ?? 0 ?> POs
        </div>
      </div>
      
      <div class="summary-card">
        <h3>
          <span class="card-icon">📈</span>
          Profitability
        </h3>
        <div class="amount">₹<?= number_format(($billingTotals['total_taxable'] ?? 0) - ($outsourcingTotals['total_vendor_inv'] ?? 0), 2) ?></div>
        <div class="label">Gross Profit</div>
        <div class="trend <?= $profitabilityRatio > 0 ? 'positive' : 'negative' ?>">
          Margin: <?= number_format($profitabilityRatio, 1) ?>% | 
          Outstanding: ₹<?= number_format($totalOutstanding, 2) ?>
        </div>
      </div>
    </div>
    
    <div class="module-cards">
      <div class="module-card" onclick="window.location.href='../Billing_Paydetails/index.php'">
        <span class="card-icon">💰</span>
        <h3>Billing & Payments</h3>
        <p>Manage customer invoices, TDS, and receivables</p>
        <div class="stats">
          <div class="stat-item">
            <span class="stat-value"><?= $billingTotals['total_invoices'] ?? 0 ?></span>
            <span class="stat-label">Invoices</span>
          </div>
          <div class="stat-item">
            <span class="stat-value">₹<?= number_format($billingTotals['total_receivable'] ?? 0, 0) ?></span>
            <span class="stat-label">Receivable</span>
          </div>
        </div>
      </div>
      
      <div class="module-card" onclick="window.location.href='../Outsourcing_Detail/index.php'">
        <span class="card-icon">🤝</span>
        <h3>Outsourcing Details</h3>
        <p>Track vendor POs, invoices, and payment status</p>
        <div class="stats">
          <div class="stat-item">
            <span class="stat-value"><?= $outsourcingTotals['total_entries'] ?? 0 ?></span>
            <span class="stat-label">Entries</span>
          </div>
          <div class="stat-item">
            <span class="stat-value">₹<?= number_format($outsPendingDisplay, 0) ?></span>
            <span class="stat-label">Pending</span>
          </div>
        </div>
      </div>
      
      <div class="module-card" onclick="window.location.href='../PO_Details/index.php'">
        <span class="card-icon">📑</span>
        <h3>PO Details</h3>
        <p>Create and manage purchase orders with status tracking</p>
        <div class="stats">
          <div class="stat-item">
            <span class="stat-value"><?= $poTotals['total_pos'] ?? 0 ?></span>
            <span class="stat-label">Total POs</span>
          </div>
          <div class="stat-item">
            <span class="stat-value"><?= $poTotals['closed_pos'] ?? 0 ?></span>
            <span class="stat-label">Closed</span>
          </div>
        </div>
      </div>
      
      <div class="module-card" onclick="window.location.href='../So_form/index.php'">
        <span class="card-icon">📝</span>
        <h3>SO Form</h3>
        <p>Statement of Work with aggregated data from all modules</p>
        <div class="stats">
          <div class="stat-item">
            <span class="stat-value">₹<?= number_format($totalNetReceivable, 0) ?></span>
            <span class="stat-label">Net Receivable</span>
          </div>
          <div class="stat-item">
            <span class="stat-value"><?= number_format($profitabilityRatio, 1) ?>%</span>
            <span class="stat-label">Margin</span>
          </div>
        </div>
      </div>
      
      <div class="module-card" onclick="window.location.href='../Tracker Updates/index.php'">
        <span class="card-icon">📊</span>
        <h3>Tracker Updates</h3>
        <p>Project progress monitoring and milestone tracking</p>
        <div class="stats">
          <div class="stat-item">
            <span class="stat-value">📈</span>
            <span class="stat-label">Progress</span>
          </div>
          <div class="stat-item">
            <span class="stat-value">🎯</span>
            <span class="stat-label">Milestones</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Debug Information -->
    <div style="background: #f0f0f0; padding: 20px; margin: 20px 0; border-radius: 10px;">
      <h3>Debug Information</h3>
      <p><strong>Database Connection:</strong> 
        <?= ($billingConn && !$billingConn->connect_error) ? '✅ Connected' : '❌ Failed' ?>
      </p>
      <p><strong>Billing Table:</strong> 
        <?= isset($billingTotals['total_invoices']) ? '✅ Exists' : '❌ Missing' ?>
      </p>
      <p><strong>Outsourcing Table:</strong> 
        <?= isset($outsourcingTotals['total_entries']) ? '✅ Exists' : '❌ Missing' ?>
      </p>
      <p><strong>PO Details Table:</strong> 
        <?= isset($poTotals['total_pos']) ? '✅ Exists' : '❌ Missing' ?>
      </p>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      console.log('🚀 Financial Dashboard Loaded Successfully!');
      console.log('💰 Real-time totals from all modules');
      console.log('📊 Matching Excel calculations');
      
      // Mobile nav toggle
      var navToggle = document.querySelector('.nav-toggle');
      var navLinks = document.querySelector('.nav-links');
      if (navToggle && navLinks) {
        navToggle.addEventListener('click', function() {
          var expanded = this.getAttribute('aria-expanded') === 'true';
          this.setAttribute('aria-expanded', (!expanded).toString());
          navLinks.classList.toggle('open');
        });
      }
    });
  </script>
</body>
</html>
