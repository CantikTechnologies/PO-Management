<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'dp.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outsourcing Details - PO Management</title>
    <meta name="description" content="Modern outsourcing details tracking and management system with real-time analytics" />
    <link rel="stylesheet" href="outsourcing_detail_new.css?v=3">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../shared/nav.php'; ?>  
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-info">
                    <h1 class="header-title">Outsourcing Details</h1>
                    <p class="header-subtitle">Real-time outsourcing tracking and management</p>
                </div>
                <button class="btn-primary" id="addEntryBtn">
                    <span class="btn-icon">&#10133;</span>
                    Add New Entry
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid" id="statsGrid">
            <!-- Stats will be loaded here by JavaScript -->
        </div>

        <!-- Form Modal -->
        <div class="modal" id="entryFormModal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="formTitle">Add New Outsourcing Entry</h2>
                    <button class="modal-close" id="closeModal">&times;</button>
                </div>
                <form id="entryForm" class="po-form">
                    <input type="hidden" name="id" id="entryId">
                    <div class="form-grid">
                        <!-- Form fields will be populated here by JavaScript -->
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
                        <button type="submit" class="btn-primary">Save Entry</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <div class="table-header">
                <div class="table-title">
                    <span class="table-icon">&#128195;</span>
                    <h3>Outsourcing Entries</h3>
                </div>
                <p class="table-subtitle">Manage and track all your outsourcing entries in one place</p>
            </div>
            
            <div class="table-filters">
                <div class="search-box">
                    <span class="search-icon">&#128269;</span>
                    <input type="text" id="searchInput" placeholder="Search by project, vendor..." 
                           class="search-input">
                </div>
            </div>
            
            <div class="table-container">
                <table class="po-table">
                    <thead>
                        <tr>
                            <th>Project Details</th>
                            <th>Vendor Name</th>
                            <th>Vendor Inv Value</th>
                            <th>Net Payable</th>
                            <th>Payment Value</th>
                            <th>Pending Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="entriesTableBody">
                        <!-- Rows will be populated here by JavaScript -->
                    </tbody>
                </table>
                
                <div id="noDataMessage" class="no-data" style="display: none;">
                    No entries found matching your criteria
                </div>
            </div>
        </div>
    </div>

    <script src="outsourcing_detail_new.js?v=4"></script>
</body>
</html>
