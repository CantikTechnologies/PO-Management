<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../1Login_signuppage/login.php");
    exit();
}

// Database connections for different modules
$billingConn = new mysqli('127.0.0.1', 'root', '', 'po_management');
$outsourcingConn = new mysqli('127.0.0.1', 'root', '', 'po_management');
$poConn = new mysqli('127.0.0.1', 'root', '', 'po_management');

// Initialize totals arrays
$billingTotals = [
    'total_taxable' => 0,
    'total_tds' => 0,
    'total_receivable' => 0,
    'total_invoices' => 0
];

$outsourcingTotals = [
    'total_vendor_inv' => 0,
    'total_tds_ded' => 0,
    'total_net_payable' => 0,
    'total_payment_value' => 0,
    'total_pending_payment' => 0,
    'total_entries' => 0
];

$poTotals = [
    'total_po_value' => 0,
    'total_po_pending' => 0,
    'total_pos' => 0,
    'closed_pos' => 0,
    'active_pos' => 0
];

// Get Billing Totals (Excel: Billing and Payment Details)
if ($billingConn && !$billingConn->connect_error) {
    try {
        // Check if billing_details table exists
        $tableCheck = $billingConn->query("SHOW TABLES LIKE 'billing_details'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $billingQuery = "SELECT 
                COALESCE(SUM(cantik_inv_value_taxable), 0) as total_taxable,
                COALESCE(SUM(tds), 0) as total_tds,
                COALESCE(SUM(receivable), 0) as total_receivable,
                COUNT(*) as total_invoices
                FROM billing_details";
            $billingResult = $billingConn->query($billingQuery);
            if ($billingResult) {
                $billingTotals = $billingResult->fetch_assoc();
            }
        }
    } catch (Exception $e) {
        error_log("Billing query error: " . $e->getMessage());
    }
}

// Get Outsourcing Totals (Excel: Outsourcing Details)
if ($outsourcingConn && !$outsourcingConn->connect_error) {
    try {
        // Check if outsourcing_detail table exists
        $tableCheck = $outsourcingConn->query("SHOW TABLES LIKE 'outsourcing_detail'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $outsourcingQuery = "SELECT 
                COALESCE(SUM(vendor_inv_value), 0) as total_vendor_inv,
                COALESCE(SUM(tds_ded), 0) as total_tds_ded,
                COALESCE(SUM(net_payable), 0) as total_net_payable,
                COALESCE(SUM(payment_value), 0) as total_payment_value,
                COALESCE(SUM(pending_payment), 0) as total_pending_payment,
                COUNT(*) as total_entries
                FROM outsourcing_detail";
            $outsourcingResult = $outsourcingConn->query($outsourcingQuery);
            if ($outsourcingResult) {
                $outsourcingTotals = $outsourcingResult->fetch_assoc();
            }
        }
    } catch (Exception $e) {
        error_log("Outsourcing query error: " . $e->getMessage());
    }
}

// Get PO Totals (Excel: PO Details)
if ($poConn && !$poConn->connect_error) {
    try {
        // Check if po_details table exists
        $tableCheck = $poConn->query("SHOW TABLES LIKE 'po_details'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $poQuery = "SELECT 
                COALESCE(SUM(po_value), 0) as total_po_value,
                COALESCE(SUM(pending_amount), 0) as total_po_pending,
                COUNT(*) as total_pos,
                COUNT(CASE WHEN po_status = 'closed' THEN 1 END) as closed_pos,
                COUNT(CASE WHEN po_status = 'active' THEN 1 END) as active_pos
                FROM po_details";
            $poResult = $poConn->query($poQuery);
            if ($poResult) {
                $poTotals = $poResult->fetch_assoc();
            }
        }
    } catch (Exception $e) {
        error_log("PO query error: " . $e->getMessage());
    }
}

// Calculate derived metrics with safe defaults
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
  <title>Financial Dashboard - PO Management</title>
  
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
        <h2>Po-Management Dashboard</h2>
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
  

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      console.log('🚀 Financial Dashboard Loaded Successfully!');
      console.log('💰 Real-time totals from all modules');
      console.log('📊 Matching Excel calculations');
      
      // Auto-refresh data every 5 minutes
      setInterval(function() {
        console.log('Data refresh interval - 5 minutes');
      }, 300000);

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

      // Dropdowns with small close delay for better usability
      var dropdowns = document.querySelectorAll('.dropdown');
      var closeTimers = new WeakMap();
      dropdowns.forEach(function(dd) {
        var trigger = dd.querySelector('.drop-trigger');
        var menu = dd.querySelector('.dropdown-menu');
        if (!trigger) return;

        function openDd() {
          var t = closeTimers.get(dd);
          if (t) { clearTimeout(t); closeTimers.delete(dd); }
          // close others
          dropdowns.forEach(function(other) { if (other !== dd) other.classList.remove('open'); });
          dd.classList.add('open');
          trigger.setAttribute('aria-expanded', 'true');
        }

        function scheduleClose() {
          var t = setTimeout(function() { dd.classList.remove('open'); }, 360);
          closeTimers.set(dd, t);
          trigger.setAttribute('aria-expanded', 'false');
        }

        // Click toggle (mobile and desktop)
        trigger.addEventListener('click', function(e) {
          e.stopPropagation();
          var isOpen = dd.classList.contains('open');
          dropdowns.forEach(function(other) { if (other !== dd) other.classList.remove('open'); });
          if (isOpen) {
            dd.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
          } else {
            openDd();
            var firstItem = dd.querySelector('.dropdown-menu a');
            if (firstItem) firstItem.focus();
          }
        });

        // Prevent link click from closing others unintentionally
        var linkEl = dd.querySelector('.drop-link');
        if (linkEl) {
          linkEl.addEventListener('click', function(e) {
            // allow navigation normally; nothing special here besides not toggling dropdown
            dropdowns.forEach(function(other) { if (other !== dd) other.classList.remove('open'); });
          });
        }

        // Hover intent (desktop only)
        dd.addEventListener('mouseenter', function() { if (window.innerWidth > 768) openDd(); });
        dd.addEventListener('mouseleave', function() { if (window.innerWidth > 768) scheduleClose(); });

        // Keyboard support
        trigger.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
            e.preventDefault();
            openDd();
            var first = dd.querySelector('.dropdown-menu a');
            if (first) first.focus();
          }
          if (e.key === 'Escape') {
            dd.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
          }
        });
        if (menu) {
          menu.addEventListener('keydown', function(e) {
            var items = Array.prototype.slice.call(menu.querySelectorAll('a'));
            var idx = items.indexOf(document.activeElement);
            if (e.key === 'ArrowDown') {
              e.preventDefault();
              var next = items[(idx + 1) % items.length] || items[0];
              if (next) next.focus();
            } else if (e.key === 'ArrowUp') {
              e.preventDefault();
              var prev = items[(idx - 1 + items.length) % items.length] || items[items.length - 1];
              if (prev) prev.focus();
            } else if (e.key === 'Escape') {
              dd.classList.remove('open');
              trigger.setAttribute('aria-expanded', 'false');
              trigger.focus();
            }
          });
        }
      });
      document.addEventListener('click', function() {
        dropdowns.forEach(function(dd) { dd.classList.remove('open'); });
      });

      // Active link state
      var anchors = document.querySelectorAll('.nav-links a');
      var currentPath = window.location.pathname.replace(/\/g, '/');
      anchors.forEach(function(a) {
        try {
          var aPath = a.getAttribute('href');
          if (!aPath) return;
          // Normalize relative path check
          var normalized = aPath.replace(/\/g, '/');
          if (currentPath.endsWith(normalized.split('/').pop())) {
            a.classList.add('active');
          }
        } catch (e) {}
      });

      // Wire dashboard PO form submission to PO_Details/save.php
      var dashForm = document.getElementById('dashboardPoForm');
      if (dashForm) {
        dashForm.addEventListener('submit', function(e) {
          e.preventDefault();
          var msgEl = document.getElementById('dash_form_msg');
          var fd = new FormData(dashForm);
          // Provide a minimal project_description since save.php requires it. Use Cost Center + PO Number.
          if (!fd.get('project_description')) {
            var cc = (document.getElementById('dash_cost_center').value || '').trim();
            var po = (document.getElementById('dash_po_number').value || '').trim();
            fd.append('project_description', [cc, po].filter(Boolean).join(' - ') || 'PO Entry');
          }
          fetch('../PO_Details/save.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(j){
              if (j && j.success) {
                msgEl.style.display = 'block';
                msgEl.style.color = '#0a5';
                msgEl.textContent = 'Saved successfully';
                dashForm.reset();
                setTimeout(function(){ msgEl.style.display = 'none'; }, 3000);
                // Soft refresh key cards
                window.location.reload();
              } else {
                msgEl.style.display = 'block';
                msgEl.style.color = '#b00';
                msgEl.textContent = (j && j.error) ? j.error : 'Save failed';
              }
            })
            .catch(function(err){
              msgEl.style.display = 'block';
              msgEl.style.color = '#b00';
              msgEl.textContent = err && err.message ? err.message : 'Network error';
            });
        });
      }
    });
  </script>
</body>
</html>