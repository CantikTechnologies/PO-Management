<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Details Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="po_details_new.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../shared/nav.php'; ?>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-info">
                    <h1 class="header-title">PO Details Management</h1>
                    <p class="header-subtitle">Manage Purchase Order details, project information, and vendor relationships</p>
                </div>
                <div class="header-actions">
                    <button class="btn-primary" onclick="window.location.href='view.php'">
                        <span class="btn-icon"><i class="fas fa-eye"></i></span>
                        View All POs
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Form Container -->
        <div class="form-container">
            <div class="form-header">
                <h3><i class="fas fa-plus-circle me-2"></i>Add New PO Details</h3>
                <p>Enter the complete information for the new Purchase Order</p>
            </div>

            <form id="poForm" class="needs-validation" novalidate>
                <input type="hidden" id="poId" name="id">
                
                <div class="form-grid">
                    <!-- Project Information -->
                    <div class="form-group form-group-full">
                        <label for="project_description" class="form-label">
                            <i class="fas fa-project-diagram me-2"></i>Project Description *
                        </label>
                        <textarea class="form-control" id="project_description" name="project_description" 
                                  rows="3" required placeholder="Enter detailed project description"></textarea>
                        <div class="invalid-feedback">Project description is required</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="cost_center" class="form-label">
                            <i class="fas fa-building me-2"></i>Cost Center *
                        </label>
                        <input type="text" class="form-control" id="cost_center" name="cost_center" 
                               required placeholder="Enter cost center">
                        <div class="invalid-feedback">Cost center is required</div>
                    </div>

                    <div class="form-group">
                        <label for="sow_number" class="form-label">
                            <i class="fas fa-file-alt me-2"></i>SOW Number *
                        </label>
                        <input type="text" class="form-control" id="sow_number" name="sow_number" 
                               required placeholder="Enter SOW number">
                        <div class="invalid-feedback">SOW number is required</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="po_number" class="form-label">
                            <i class="fas fa-hashtag me-2"></i>PO Number *
                        </label>
                        <input type="text" class="form-control" id="po_number" name="po_number" 
                               required placeholder="Enter PO number">
                        <div class="invalid-feedback">PO number is required</div>
                    </div>

                    <div class="form-group">
                        <label for="start_date" class="form-label">
                            <i class="fas fa-calendar-plus me-2"></i>Start Date *
                        </label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                        <div class="invalid-feedback">Start date is required</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date" class="form-label">
                            <i class="fas fa-calendar-minus me-2"></i>End Date *
                        </label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                        <div class="invalid-feedback">End date is required</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="po_date" class="form-label">
                            <i class="fas fa-calendar me-2"></i>PO Date *
                        </label>
                        <input type="date" class="form-control" id="po_date" name="po_date" required>
                        <div class="invalid-feedback">PO date is required</div>
                    </div>

                    <div class="form-group">
                        <label for="po_value" class="form-label">
                            <i class="fas fa-rupee-sign me-2"></i>PO Value *
                        </label>
                        <input type="number" class="form-control" id="po_value" name="po_value" 
                               step="0.01" min="0" required placeholder="0.00">
                        <div class="invalid-feedback">PO value is required</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="billing_frequency" class="form-label">
                            <i class="fas fa-clock me-2"></i>Billing Frequency *
                        </label>
                        <select class="form-control" id="billing_frequency" name="billing_frequency" required>
                            <option value="">Select frequency</option>
                            <option value="Weekly">Weekly</option>
                            <option value="Bi-weekly">Bi-weekly</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Quarterly">Quarterly</option>
                            <option value="Annually">Annually</option>
                        </select>
                        <div class="invalid-feedback">Billing frequency is required</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="target_gm" class="form-label">
                            <i class="fas fa-percentage me-2"></i>Target GM % *
                        </label>
                        <input type="number" class="form-control" id="target_gm" name="target_gm" 
                               step="0.0001" min="0" max="1" required placeholder="0.0500">
                        <div class="invalid-feedback">Target GM is required (0.0500 = 5%)</div>
                    </div>

                    <div class="form-group">
                        <label for="po_status" class="form-label">
                            <i class="fas fa-toggle-on me-2"></i>PO Status
                        </label>
                        <select class="form-control" id="po_status" name="po_status">
                            <option value="Active">Active</option>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="On Hold">On Hold</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="vendor_name" class="form-label">
                            <i class="fas fa-user-tie me-2"></i>Vendor Name
                        </label>
                        <input type="text" class="form-control" id="vendor_name" name="vendor_name" 
                               placeholder="Enter vendor name">
                    </div>

                    <div class="form-group form-group-full">
                        <label for="remarks" class="form-label">
                            <i class="fas fa-comment me-2"></i>Remarks
                        </label>
                        <textarea class="form-control" id="remarks" name="remarks" 
                                  rows="3" placeholder="Enter any additional remarks or notes"></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save me-2"></i>Save PO Details
                    </button>
                    <button type="button" class="btn-secondary" onclick="resetForm()">
                        <i class="fas fa-undo me-2"></i>Reset Form
                    </button>
                </div>
            </form>
        </div>

        <!-- Success/Error Messages -->
        <div id="messageContainer" class="message-container" style="display: none;">
            <div id="messageContent" class="message-content">
                <span id="messageText"></span>
                <button type="button" class="btn-close" onclick="hideMessage()"></button>
            </div>
        </div>
    </div>

    <script src="po_details_new.js?v=2"></script>
</body>
</html>