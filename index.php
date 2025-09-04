<?php
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: 1Login_signuppage/login.php');
  exit();
}
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>PO Management Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container">
    <?php include 'shared/nav.php'; ?>
    

    <main>
      <section class="card">
        <h2>Welcome</h2>
        <p>Use the menu to manage Purchase Orders, Invoices and Outsourcing records. The SO Form report aggregates data from all modules.</p>
      </section>
    </main>

    <footer>
      
    </footer>
  </div>
<script src="assets/script.js"></script>
</body>
</html>
