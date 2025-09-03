<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
$request_uri = $_SERVER['REQUEST_URI'];
$path_parts = explode('/', trim($request_uri, '/'));
$current_dir = $path_parts[1] ?? '2dashboard-app'; // Default to dashboard

// Debug information
error_log("Navigation loaded - Current page: " . $current_page);
error_log("Navigation loaded - Current directory: " . $current_dir);
?>
<link rel="stylesheet" href="../shared/nav.css?v=4">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


<!-- Navigation Debug: Current dir = <?= $current_dir ?> -->
<header class="navbar">
  <div class="logo">
    <a href="../2dashboard-app/dashboard.php">
      <img src="../2dashboard-app/cantik_logo.png" alt="Cantik Logo" onerror="this.src='../2dashboard-app/cantik_logo.png'; this.onerror=null;">
    </a>
  </div>

  <div class="nav-toggle" id="nav-toggle">
      <i class="fas fa-bars"></i>
  </div>

  <nav class="nav-links" id="nav-links">
    <a href="../2dashboard-app/dashboard.php" <?= ($current_dir == '2dashboard-app') ? 'class="active"' : '' ?>> Dashboard</a>
    
    <div class="dropdown">
      <a href="../Tracker Updates/index.php" class="drop-link <?= ($current_dir == 'Tracker Updates') ? 'active' : '' ?>"> Tracker Updates</a>
      <div class="dropdown-menu">
        <a href="../Tracker Updates/view.php">View Tasks</a>
        <a href="../Tracker Updates/index.php?action=add">Add New Task</a>
      </div>
    </div>

    <div class="dropdown">
      <a href="../PO_Details/index.php" class="drop-link <?= ($current_dir == 'PO_Details') ? 'active' : '' ?>"> PO Details</a>
      <div class="dropdown-menu">
        <a href="../PO_Details/view.php">View POs</a>
        <a href="../PO_Details/index.php?action=add">Add New PO</a>
      </div>
    </div>
    
    <div class="dropdown">
      <a href="../Billing_Paydetails/index.php" class="drop-link <?= ($current_dir == 'Billing_Paydetails') ? 'active' : '' ?>"> Billing & Payments</a>
      <div class="dropdown-menu">
        <a href="../Billing_Paydetails/view.php">View Bills</a>
        <a href="../Billing_Paydetails/index.php?action=add">Add New Bill</a>
      </div>
    </div>
    
    <div class="dropdown">
      <a href="../Outsourcing_Detail/index.php" class="drop-link <?= ($current_dir == 'Outsourcing_Detail') ? 'active' : '' ?>"> Outsourcing</a>
      <div class="dropdown-menu">
        <a href="../Outsourcing_Detail/view.php">View Details</a>
        <a href="../Outsourcing_Detail/index.php?action=add">Add New Detail</a>
      </div>
    </div>
    
    <div class="dropdown">
      <a href="../So_form/index.php" class="drop-link <?= ($current_dir == 'So_form') ? 'active' : '' ?>"> SO Form</a>
      <div class="dropdown-menu">
        <a href="../So_form/view.php">View SOs</a>
        <a href="../So_form/index.php?action=add">Add New SO</a>
      </div>
    </div>
  </nav>

  <div class="nav-actions">
    <a href="../1Login_signuppage/logout.php" class="btn-logout">Logout</a>
  </div>
</header>

<script>
    const navToggle = document.getElementById('nav-toggle');
    const navLinks = document.getElementById('nav-links');

    navToggle.addEventListener('click', () => {
        navLinks.classList.toggle('open');
    });
</script>
