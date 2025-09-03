<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Payment Management</title>
    <meta name="description" content="Modern billing and payment tracking system with real-time analytics" />
    <link rel="stylesheet" href="billing_details_new.css?v=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../shared/nav.php'; ?>  
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-info">
                    <h1 class="header-title">Billing Details</h1>
                    <p class="header-subtitle">Real-time billing and payment tracking and management</p>
                </div>
                <button class="btn-primary" id="addBillingBtn">
                    <span class="btn-icon">&#10133;</span>
                    Add New Billing
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Entries</span>
                    <span class="stat-icon">&#128196;</span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalEntries">0</div>
                    <div class="stat-description">All billing entries</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Taxable</span>
                    <span class="stat-icon">&#128176;</span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalTaxable">₹0</div>
                    <div class="stat-description">Across all invoices</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total TDS</span>
                    <span class="stat-icon">&#9203;</span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalTDS">₹0</div>
                    <div class="stat-description">Tax deducted at source</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Receivable</span>
                    <span class="stat-icon">&#128200;</span>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="totalReceivable">₹0</div>
                    <div class="stat-description">Amount receivable</div>
                </div>
            </div>
        </div>

        <!-- Billing Form Modal -->
        <div class="modal" id="billingFormModal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Add New Billing Entry</h2>
                    <button class="modal-close" id="closeModal">&times;</button>
                </div>
                <form id="billingForm" class="billing-form">
                    <input type="hidden" name="id" id="billingId">
                    <div class="form-grid">
                        <div class="form-group form-group-full">
                            <label for="projectDetails">Project Details *</label>
                            <input type="text" id="projectDetails" name="project_details" required 
                                   placeholder="e.g., Raptakos Resource Deployment - Anuj Kushwaha">
                        </div>
                        
                        <div class="form-group">
                            <label for="costCenter">Cost Center</label>
                            <input type="text" id="costCenter" name="cost_center" 
                                   placeholder="e.g., Raptakos PT">
                        </div>
                        
                        <div class="form-group">
                            <label for="customerPO">Customer PO</label>
                            <input type="text" id="customerPO" name="customer_po" 
                                   placeholder="e.g., 4500095281" oninput="handlePOChange()">
                        </div>
                        
                        <div class="form-group">
                            <label for="cantikInvoiceNo">Cantik Invoice No</label>
                            <input type="text" id="cantikInvoiceNo" name="cantik_invoice_no" 
                                   placeholder="e.g., INV-2024-001">
                        </div>
                        
                        <div class="form-group">
                            <label for="cantikInvoiceDate">Cantik Invoice Date</label>
                            <input type="date" id="cantikInvoiceDate" name="cantik_invoice_date">
                        </div>
                        
                        <div class="form-group">
                            <label for="cantikInvValueTaxable">Cantik Inv Value Taxable *</label>
                            <input type="number" id="cantikInvValueTaxable" name="cantik_inv_value_taxable" required step="0.01" 
                                   placeholder="e.g., 175500" oninput="recalc()">
                        </div>
                        
                        <div class="form-group">
                            <label for="tds">TDS</label>
                            <input type="number" id="tds" name="tds" step="0.01" readonly
                                   placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label for="receivable">Receivable</label>
                            <input type="number" id="receivable" name="receivable" step="0.01" readonly
                                   placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label for="againstVendorInvNumber">Against Vendor Inv Number</label>
                            <input type="text" id="againstVendorInvNumber" name="against_vendor_inv_number" 
                                   placeholder="e.g., VINV-2024-001">
                        </div>
                        
                        <div class="form-group">
                            <label for="paymentRecptDate">Payment Receipt Date</label>
                            <input type="date" id="paymentRecptDate" name="payment_recpt_date">
                        </div>
                        
                        <div class="form-group">
                            <label for="paymentAdviseNo">Payment Advise No</label>
                            <input type="text" id="paymentAdviseNo" name="payment_advise_no" 
                                   placeholder="e.g., 1400005222">
                        </div>
                        
                        <div class="form-group form-group-full">
                            <label for="vendorName">Vendor Name</label>
                            <input type="text" id="vendorName" name="vendor_name" 
                                   placeholder="e.g., VRATA TECH SOLUTIONS PRIVATE LIMITED">
                        </div>

                        <div class="form-group form-group-full">
                            <label for="remainingBalanceInPO">Remaining Balance in PO</label>
                            <input type="text" id="remainingBalanceInPO" name="remaining_balance_in_po" readonly
                                   placeholder="-">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
                        <button type="submit" class="btn-primary">Save Billing Entry</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Billing Table Section -->
        <div class="table-section">
            <div class="table-header">
                <div class="table-title">
                    <span class="table-icon">&#128195;</span>
                    <h3>Billing Entries</h3>
                </div>
                <p class="table-subtitle">Manage and track all your billing entries in one place</p>
            </div>
            
            <div class="table-filters">
                <div class="search-box">
                    <span class="search-icon">&#128269;</span>
                    <input type="text" id="searchInput" placeholder="Search entries by project, invoice, vendor..." 
                           class="search-input">
                </div>
                
                <select id="projectFilter" class="filter-select">
                    <option value="">All Projects</option>
                </select>
            </div>
            
            <div class="table-container">
                <table class="billing-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Cost Center</th>
                            <th>Customer PO</th>
                            <th>Invoice No</th>
                            <th>Invoice Date</th>
                            <th>Taxable Value</th>
                            <th>TDS</th>
                            <th>Receivable</th>
                            <th>Vendor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="billingTableBody">
                        <!-- Billing rows will be populated here by JavaScript -->
                    </tbody>
                </table>
                
                <div id="noDataMessage" class="no-data" style="display: none;">
                    No billing entries found matching your criteria
                </div>
            </div>
        </div>
    </div>

    <script src="billing_modern.js?v=1"></script>
</body>
</html>