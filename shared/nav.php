<?php
// Determine active route relative to project root
$request_uri = $_SERVER['REQUEST_URI'] ?? '/PO_3/index.php';
$isActive = function(string $needle) use ($request_uri) {
  return strpos($request_uri, $needle) !== false ? 'class="active"' : '';
};
?>
<link rel="stylesheet" href="/PO_3/shared/nav.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<header class="navbar">
  <div class="logo">
    <a href="/PO_3/index.php" class="brand">
      <img src="/PO_3/assets/cantik_logo.png" alt="Cantik Homemade Logo" width="120">
    </a>
  </div>

  <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation">
    <i class="fas fa-bars"></i>
  </button>

  <nav class="nav-links" id="nav-links">
    <a href="/PO_3/index.php" <?= $isActive('/PO_3/index.php'); ?>>Dashboard</a>

    <div class="dropdown">
      <a href="/PO_3/po_details/list.php" class="drop-link <?= $isActive('/po_details/') ? 'active' : '' ?>">PO Details</a>
      <div class="dropdown-menu">
        <a href="/PO_3/po_details/list.php">View POs</a>
        <a href="/PO_3/po_details/add.php">Add New PO</a>
      </div>
    </div>

    <div class="dropdown">
      <a href="/PO_3/invoices/list.php" class="drop-link <?= $isActive('/invoices/') ? 'active' : '' ?>">Invoices</a>
      <div class="dropdown-menu">
        <a href="/PO_3/invoices/list.php">View Invoices</a>
        <a href="/PO_3/invoices/add.php">Add New Invoice</a>
      </div>
    </div>

    <div class="dropdown">
      <a href="/PO_3/outsourcing/list.php" class="drop-link <?= $isActive('/outsourcing/') ? 'active' : '' ?>">Outsourcing</a>
      <div class="dropdown-menu">
        <a href="/PO_3/outsourcing/list.php">View Records</a>
        <a href="/PO_3/outsourcing/add.php">Add New Record</a>
      </div>
    </div>

    <div class="dropdown">
      <a href="/PO_3/Tracker%20Updates/index.php" class="drop-link <?= $isActive('/Tracker%20Updates/') ? 'active' : '' ?>">Tracker Updates</a>
      <div class="dropdown-menu">
        <a href="/PO_3/Tracker%20Updates/index.php">View Updates</a>
        <a href="/PO_3/Tracker%20Updates/index.php#trackerFormModal">Add Update</a>
      </div>
    </div>

    <a href="/PO_3/so_form.php" <?= $isActive('/so_form.php'); ?>>SO Form</a>
  </nav>

  <div class="nav-actions">
    <a href="/PO_3/1Login_signuppage/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</header>

<script>
  const navToggle = document.getElementById('nav-toggle');
  const navLinks = document.getElementById('nav-links');
  if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => {
      navLinks.classList.toggle('open');
    });
  }
</script>
