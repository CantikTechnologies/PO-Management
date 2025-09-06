<?php
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: ../1Login_signuppage/login.php');
  exit();
}
include '../db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Invoices</title>
  <link rel="stylesheet" href="../assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
  <div class="container">
    <?php include '../shared/nav.php'; ?>
    <main>
      <div class="page-header">
        <h1>Invoices</h1>
        <a class="btn" href="add.php">+ Add New Invoice</a>
      </div>

      <form method="get" class="card" style="margin-bottom: 16px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label for="project_filter">Filter by Project:</label>
            <select name="project" id="project_filter" onchange="this.form.submit()">
              <option value="">All Projects</option>
              <?php
              $projects = $conn->query("SELECT DISTINCT po.project_description FROM invoices inv JOIN purchase_orders po ON po.po_id = inv.po_id WHERE po.project_description IS NOT NULL AND po.project_description != '' ORDER BY po.project_description");
              if ($projects && $projects->num_rows > 0) {
                while ($proj = $projects->fetch_assoc()) {
                  $selected = (isset($_GET['project']) && $_GET['project'] === $proj['project_description']) ? 'selected' : '';
                  echo '<option value="' . htmlspecialchars($proj['project_description']) . '" ' . $selected . '>' . htmlspecialchars($proj['project_description']) . '</option>';
                }
              }
              ?>
            </select>
          </div>
          <div>
            <label for="search_input">Search:</label>
            <input type="text" name="q" id="search_input" placeholder="Search by PO number, invoice no, vendor inv no..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" />
          </div>
        </div>
        <div class="search-bar">
          <button class="btn" type="submit">Search</button>
          <?php if (!empty($_GET['q']) || !empty($_GET['project'])): ?>
            <a class="btn muted" href="list.php">Reset</a>
          <?php endif; ?>
        </div>
      </form>

      <div class="card">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Project Details</th>
                <th>Cost Center</th>
                <th>Customer PO</th>
                <th>Remaining Balance in PO</th>
                <th>Cantik Invoice No</th>
                <th>Cantik Invoice Date</th>
                <th>Cantik Inv Value Taxable</th>
                <th>TDS</th>
                <th>Receivable</th>
                <th>Against Vendor Inv Number</th>
                <th>Payment Recpt Date</th>
                <th>Payment Advise No.</th>
                <th>Vendor Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $res = null;
              $search = isset($_GET['q']) ? trim($_GET['q']) : '';
              $project_filter = isset($_GET['project']) ? trim($_GET['project']) : '';

              if ($search !== '' || $project_filter !== '') {
                $where_conditions = [];
                $params = [];
                $param_types = '';

                if ($project_filter !== '') {
                  $where_conditions[] = "po.project_description = ?";
                  $params[] = $project_filter;
                  $param_types .= 's';
                }

                if ($search !== '') {
                  $like = '%' . $search . '%';
                  $where_conditions[] = "(po.po_number LIKE ? OR inv.cantik_invoice_no LIKE ? OR inv.vendor_invoice_no LIKE ?)";
                  $params = array_merge($params, [$like, $like, $like]);
                  $param_types .= 'sss';
                }

                $where_clause = implode(' AND ', $where_conditions);
                $stmt = $conn->prepare("SELECT inv.*, po.po_number, po.cost_center, po.vendor_name, po.project_description, po.pending_amount_in_po FROM invoices inv JOIN purchase_orders po ON po.po_id = inv.po_id WHERE $where_clause ORDER BY inv.invoice_id DESC");
                $stmt->bind_param($param_types, ...$params);
                $stmt->execute();
                $res = $stmt->get_result();
              } else {
                $res = $conn->query("SELECT inv.*, po.po_number, po.cost_center, po.vendor_name, po.project_description, po.pending_amount_in_po FROM invoices inv JOIN purchase_orders po ON po.po_id = inv.po_id ORDER BY inv.invoice_id DESC");
              }
              if ($res && $res->num_rows > 0) {
                while ($r = $res->fetch_assoc()) {
                  $fmt = fn($v) => $v === null || $v === '' ? '-' : htmlspecialchars((string)$v);
                  echo '<tr>';
                  echo '<td>' . $fmt($r['project_description'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['cost_center'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['po_number'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['pending_amount_in_po'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['cantik_invoice_no'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['cantik_invoice_date'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['taxable_value'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['tds'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['receivable'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['vendor_invoice_no'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['payment_receipt_date'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['payment_advise_no'] ?? '') . '</td>';
                  echo '<td>' . $fmt($r['vendor_name'] ?? '') . '</td>';
                  echo '<td class="actions">
                          <a href="edit.php?id=' . htmlspecialchars((string)($r['invoice_id'] ?? '')) . '" class="btn muted">Edit</a>
                          <a href="delete.php?id=' . htmlspecialchars((string)($r['invoice_id'] ?? '')) . '" class="btn danger" onclick=\'return confirm("Are you sure you want to delete this invoice? This action cannot be undone.")\'>Delete</a>
                        </td>';
                  echo '</tr>';
                }
              } else {
                echo '<tr><td colspan="14">No invoices found.</td></tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
  <script src="../assets/script.js"></script>
</body>
</html>
