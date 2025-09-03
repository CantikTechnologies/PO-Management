<?php
session_start();
require 'db.php';

function getPONumbers($conn) {
    $result = $conn->query("SELECT DISTINCT po_number, project_description FROM po_details ORDER BY po_number");
    $po_numbers = [];
    while($row = $result->fetch_assoc()) {
        $po_numbers[] = $row;
    }
    return $po_numbers;
}

$po_numbers = getPONumbers($conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SO Form Report - PO Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="so_form_new.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../shared/nav.php'; ?>  
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-info">
                    <h1 class="header-title">SO Form Report</h1>
                    <p class="header-subtitle">Generate and view SO form reports</p>
                </div>
            </div>
        </div>

        <!-- Report Controls -->
        <div class="report-controls">
            <div class="form-group">
                <label for="po_no_select">Select a PO Number to Generate Report:</label>
                <select id="po_no_select">
                    <option value="">Select a PO</option>
                    <?php foreach($po_numbers as $po): ?>
                        <option value="<?= htmlspecialchars($po['po_number']) ?>"><?= htmlspecialchars($po['po_number']) ?> - <?= htmlspecialchars($po['project_description']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="searchInput">Search SOs:</label>
                <input type="text" id="searchInput" placeholder="Search by PO, Project, Vendor...">
            </div>
            <button class="btn-primary" id="loadSODataBtn">
                <span class="btn-icon">&#128260;</span>
                Load SO Data
            </button>
            <button class="btn-primary" id="saveReportBtn">
                <span class="btn-icon">&#128190;</span>
                Save Report Snapshot
            </button>
        </div>

        <!-- Report Content -->
        <div class="report-content-area" id="reportContentArea" style="display: none;">
            <div class="report-header">
                <h3 class="report-title" id="reportTitle"></h3>
            </div>
            <div class="report-grid-container">
                <div class="report-grid" id="report-content">
                    <!-- Report will be displayed here -->
                </div>
            </div>
        </div>

        <!-- SO Table -->
        <div class="table-section">
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="table-title">
                            <i class="fas fa-table me-2"></i>
                            <h3>SO Form Reports</h3>
                        </div>
                        <p class="table-subtitle">Comprehensive view of all SO form data</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="table-actions">
                            <span class="result-count" id="resultCount">
                                <i class="fas fa-info-circle me-2"></i>
                                Ready to load data...
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-container">
                <div class="table-responsive">
                    <div class="loading-indicator" id="loadingIndicator" style="display: none;">
                        <div class="spinner"></div>
                        <p>Loading data...</p>
                    </div>
                    <table class="table table-hover" id="soTable" style="display: table;">
                    <thead>
                        <tr>
                            <th><i class="fas fa-project-diagram me-2"></i>Project</th>
                            <th><i class="fas fa-building me-2"></i>Cost Center</th>
                            <th><i class="fas fa-file-invoice me-2"></i>Customer PO No</th>
                            <th><i class="fas fa-receipt me-2"></i>Billed PO No</th>
                            <th><i class="fas fa-balance-scale me-2"></i>Remaining Balance</th>
                            <th><i class="fas fa-user-tie me-2"></i>Vendor Name</th>
                            <th><i class="fas fa-file-contract me-2"></i>Cantik PO No</th>
                            <th><i class="fas fa-rupee-sign me-2"></i>Vendor PO Value</th>
                            <th><i class="fas fa-chart-line me-2"></i>Vendor Invoicing</th>
                            <th><i class="fas fa-percentage me-2"></i>Margin</th>
                            <th><i class="fas fa-bullseye me-2"></i>Target GM</th>
                            <th><i class="fas fa-chart-area me-2"></i>Variance in GM</th>
                        </tr>
                    </thead>
                    <tbody id="soTableBody">
                        <!-- SO data will be populated here by JavaScript -->
                    </tbody>
                </table>
                
                <!-- No Results Message -->
                <div class="no-results" id="noResults">
                    <i class="fas fa-search"></i>
                    <h5>No SO data found</h5>
                    <p>Click "Load SO Data" to fetch the latest information</p>
                </div>
            </div>
        </div>
    </div>

    <script src="so_form_new.js?v=3"></script>
</body>
</html>