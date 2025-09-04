<?php
// Determine active route relative to project root
$request_uri = $_SERVER['REQUEST_URI'] ?? '/PO_3/index.php';
$isActive = function(string $needle) use ($request_uri) {
  return strpos($request_uri, $needle) !== false ? 'class="active"' : '';
};
?>
<link rel="stylesheet" href="shared/nav.css?v=1">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<header class="navbar">
  <div class="logo">
    <a href="index.php" class="brand">
      <i class="fas fa-receipt"></i>
      <span>PO Management</span>
    </a>
  </div>

  <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation">
    <i class="fas fa-bars"></i>
  </button>

  <nav class="nav-links" id="nav-links">
    <a href="index.php" <?= $isActive('/PO_3/index.php'); ?>>Dashboard</a>

    <div class="dropdown">
      <a href="po_details/list.php" class="drop-link <?= $isActive('/po_details/') ? 'active' : '' ?>">PO Details</a>
      <div class="dropdown-menu">
        <a href="po_details/list.php">View POs</a>
        <a href="po_details/add.php">Add New PO</a>
      </div>
    </div>

    <div class="dropdown">
      <a href="invoices/list.php" class="drop-link <?= $isActive('/invoices/') ? 'active' : '' ?>">Invoices</a>
      <div class="dropdown-menu">
        <a href="invoices/list.php">View Invoices</a>
        <a href="invoices/add.php">Add New Invoice</a>
      </div>
    </div>

    <div class="dropdown">
      <a href="outsourcing/list.php" class="drop-link <?= $isActive('/outsourcing/') ? 'active' : '' ?>">Outsourcing</a>
      <div class="dropdown-menu">
        <a href="outsourcing/list.php">View Records</a>
        <a href="outsourcing/add.php">Add New Record</a>
      </div>
    </div>

    <a href="so_form.php" <?= $isActive('/so_form.php'); ?>>SO Form</a>
  </nav>

  <div class="nav-actions">
    <a href="1Login_signuppage/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
