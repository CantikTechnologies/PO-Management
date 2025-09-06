<?php 
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: 1Login_signuppage/login.php');
  exit();
}
include 'db.php'; 
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>SO Form Report</title>
  <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
  <div class="container so-form-container">
    <?php include 'shared/nav.php'; ?>
    <main>
      <div class="page-header">
        <h2>SO Form - Summary Report</h2>
        <a href="index.php" class="btn">Back</a>
      </div>

      <form method="get" class="card" style="margin: 16px 0;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label for="project_filter">Filter by Project:</label>
            <select name="project" id="project_filter" onchange="this.form.submit()">
              <option value="">All Projects</option>
              <?php
                             $projects = $conn->query("SELECT DISTINCT project_name FROM so_form WHERE project_name IS NOT NULL AND project_name != '' ORDER BY project_name");
              if ($projects && $projects->num_rows > 0) {
                while ($proj = $projects->fetch_assoc()) {
                  $selected = (isset($_GET['project']) && $_GET['project'] === $proj['project_name']) ? 'selected' : '';
                  echo '<option value="' . htmlspecialchars($proj['project_name']) . '" ' . $selected . '>' . htmlspecialchars($proj['project_name']) . '</option>';
                }
              }
              ?>
            </select>
          </div>
          <div>
            <label for="search_input">Search:</label>
            <input type="text" name="q" id="search_input" placeholder="Search by Cost Centre, Customer PO, Vendor, Vendor PO..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" />
          </div>
        </div>
        <div class="search-bar">
          <button class="btn" type="submit">Search</button>
          <?php if (!empty($_GET['q']) || !empty($_GET['project'])): ?>
            <a class="btn muted" href="so_form.php">Reset</a>
          <?php endif; ?>
        </div>
      </form>

      <div class="card">
        <div class="table-wrap">
          <table>
            <thead>
                             <tr>
                 <th>Project Description</th>
                 <th>Cost Centre</th>
                 <th>Customer PO No</th>
                 <th>Customer PO Value</th>
                 <th>Billed Till Date</th>
                 <th>Remaining Balance (PO)</th>
                 <th>Vendor Name</th>
                 <th>Vendor PO No</th>
                 <th>Vendor PO Value</th>
                 <th>Vendor Invoicing Till Date</th>
                 <th>Remaining Vendor Balance</th>
                 <th>Sale Margin (%)</th>
                 <th>Target GM (%)</th>
                 <th>Variance GM (%)</th>
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
                 $where_conditions[] = "project_name = ?";
                 $params[] = $project_filter;
                 $param_types .= 's';
               }

              if ($search !== '') {
                $like = '%' . $search . '%';
                $where_conditions[] = "(cost_center LIKE ? OR customer_po_no LIKE ? OR vendor_name LIKE ? OR vendor_po_no LIKE ?)";
                $params = array_merge($params, [$like, $like, $like, $like]);
                $param_types .= 'ssss';
              }

              $where_clause = implode(' AND ', $where_conditions);
              $stmt = $conn->prepare("SELECT * FROM so_form WHERE $where_clause ORDER BY cost_center, customer_po_no");
              $stmt->bind_param($param_types, ...$params);
              $stmt->execute();
              $res = $stmt->get_result();
            } else {
              $res = $conn->query("SELECT * FROM so_form ORDER BY cost_center, customer_po_no");
            }
                         if ($res && $res->num_rows > 0) {
               while ($r = $res->fetch_assoc()) {
                 echo "<tr>";
                 echo "<td>".htmlspecialchars($r['project_name'] ?? '')."</td>";
                 echo "<td>".htmlspecialchars($r['cost_center'])."</td>";
                 echo "<td>".htmlspecialchars($r['customer_po_no'])."</td>";
                 echo "<td>".number_format($r['customer_po_value'],2)."</td>";
                 echo "<td>".number_format($r['billed_till_date'],2)."</td>";
                 echo "<td>".number_format($r['remaining_balance_po'],2)."</td>";
                 echo "<td>".htmlspecialchars($r['vendor_name'])."</td>";
                 echo "<td>".htmlspecialchars($r['vendor_po_no'])."</td>";
                 echo "<td>".number_format($r['vendor_po_value'],2)."</td>";
                 echo "<td>".number_format($r['vendor_invoicing_till_date'],2)."</td>";
                 echo "<td>".number_format($r['remaining_vendor_balance'],2)."</td>";
                 echo "<td>".number_format($r['sale_margin_till_date'],2)."%</td>";
                 echo "<td>".number_format($r['target_gm'],2)."%</td>";
                 echo "<td>".number_format($r['variance_in_gm'],2)."%</td>";
                 echo "</tr>";
               }
             } else {
               echo "<tr><td colspan='14'>No records found</td></tr>";
             }
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
