// PO Details Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('poForm');
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (form.checkValidity()) {
            submitForm();
        } else {
            form.classList.add('was-validated');
        }
    });
    
    // Initialize form with current date defaults
    initializeForm();
});

// Initialize form with default values
function initializeForm() {
    const today = new Date();
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const poDate = document.getElementById('po_date');
    
    if (startDate) startDate.value = today.toISOString().split('T')[0];
    if (endDate) endDate.value = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    if (poDate) poDate.value = today.toISOString().split('T')[0];
    
    // Set default target GM to 5%
    const targetGm = document.getElementById('target_gm');
    if (targetGm) targetGm.value = '0.0500';
}

// Submit form data
function submitForm() {
    const form = document.getElementById('poForm');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    submitBtn.disabled = true;
    
    // Convert dates to Excel serial numbers
    const startDate = formData.get('start_date');
    const endDate = formData.get('end_date');
    const poDate = formData.get('po_date');
    
    if (startDate) formData.set('start_date', dateToExcelSerial(startDate));
    if (endDate) formData.set('end_date', dateToExcelSerial(endDate));
    if (poDate) formData.set('po_date', dateToExcelSerial(poDate));
    
    fetch('save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message || 'PO Details saved successfully!', 'success');
            resetForm();
        } else {
            showMessage(data.error || 'Failed to save PO Details', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while saving. Please try again.', 'error');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Convert date string to Excel serial number
function dateToExcelSerial(dateString) {
    const date = new Date(dateString);
    const excelEpoch = new Date(1900, 0, 1);
    const timeDiff = date.getTime() - excelEpoch.getTime();
    const daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
    return daysDiff + 2; // Excel starts from 1, but 1900-01-01 is day 1, so we add 2
}

// Convert Excel serial number to date string
function excelSerialToDate(serial) {
    const excelEpoch = new Date(1900, 0, 1);
    const date = new Date(excelEpoch.getTime() + (serial - 2) * 24 * 60 * 60 * 1000);
    return date.toISOString().split('T')[0];
}

// Reset form to initial state
function resetForm() {
    const form = document.getElementById('poForm');
    form.reset();
    form.classList.remove('was-validated');
    
    // Reset hidden fields
    document.getElementById('poId').value = '';
    
    // Re-initialize with current dates
    initializeForm();
    
    // Remove any validation styling
    const inputs = form.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

// Show message notification
function showMessage(message, type = 'info') {
    const container = document.getElementById('messageContainer');
    const content = document.getElementById('messageContent');
    const text = document.getElementById('messageText');
    
    // Set message content and type
    text.textContent = message;
    content.className = `message-content ${type}`;
    
    // Show container
    container.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        hideMessage();
    }, 5000);
}

// Hide message notification
function hideMessage() {
    const container = document.getElementById('messageContainer');
    container.style.display = 'none';
}

// Navigate to view page
function showViewPage() {
    window.location.href = 'view.php';
}

// Navigate to setup page
function showSetupPage() {
    window.location.href = 'setup_database.php';
}

// Edit PO function (for future use)
function editPO(id) {
    // Fetch PO details and populate form
    fetch(`get.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateForm(data.data);
            } else {
                showMessage(data.error || 'Failed to fetch PO details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while fetching PO details', 'error');
        });
}

// Populate form with existing data
function populateForm(data) {
    const form = document.getElementById('poForm');
    
    // Set form values
    document.getElementById('poId').value = data.id;
    document.getElementById('project_description').value = data.project_description || '';
    document.getElementById('cost_center').value = data.cost_center || '';
    document.getElementById('sow_number').value = data.sow_number || '';
    document.getElementById('po_number').value = data.po_number || '';
    document.getElementById('po_value').value = data.po_value || '';
    document.getElementById('billing_frequency').value = data.billing_frequency || '';
    document.getElementById('target_gm').value = data.target_gm || '';
    document.getElementById('po_status').value = data.po_status || 'Active';
    document.getElementById('vendor_name').value = data.vendor_name || '';
    document.getElementById('remarks').value = data.remarks || '';
    
    // Convert Excel dates to regular dates
    if (data.start_date) {
        document.getElementById('start_date').value = excelSerialToDate(data.start_date);
    }
    if (data.end_date) {
        document.getElementById('end_date').value = excelSerialToDate(data.end_date);
    }
    if (data.po_date) {
        document.getElementById('po_date').value = excelSerialToDate(data.po_date);
    }
    
    // Update form header
    const header = form.querySelector('.form-header h3');
    if (header) {
        header.innerHTML = '<i class="fas fa-edit me-2"></i>Edit PO Details';
    }
    
    // Update submit button
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update PO Details';
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Delete PO function (for future use)
function deletePO(id, poNumber) {
    if (confirm(`Are you sure you want to delete PO ${poNumber}? This action cannot be undone.`)) {
        fetch('delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message || 'PO deleted successfully!', 'success');
                // Refresh the page or remove the row from table
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showMessage(data.error || 'Failed to delete PO', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while deleting PO', 'error');
        });
    }
}

// Search functionality (for view page)
function searchPOs(searchTerm) {
    const rows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const searchData = row.getAttribute('data-search');
        if (searchData && searchData.toLowerCase().includes(searchTerm.toLowerCase())) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update result count
    const resultCount = document.getElementById('resultCount');
    if (resultCount) {
        resultCount.innerHTML = `<i class="fas fa-info-circle me-2"></i>Showing ${visibleCount} of ${rows.length} entries`;
    }
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (noResults) {
        if (visibleCount === 0 && searchTerm !== '') {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }
}

// Format currency values
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 2
    }).format(amount);
}

// Format percentage values
function formatPercentage(value) {
    return (parseFloat(value) * 100).toFixed(2) + '%';
}

// Validate form fields
function validateField(field) {
    const value = field.value.trim();
    const isValid = field.checkValidity();
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
    }
    
    return isValid;
}

// Add real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-control');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const form = document.getElementById('poForm');
        if (form) {
            form.dispatchEvent(new Event('submit'));
        }
    }
    
    // Escape to reset form
    if (e.key === 'Escape') {
        resetForm();
    }
});

// Auto-save draft functionality (optional)
let autoSaveTimer;
function setupAutoSave() {
    const form = document.getElementById('poForm');
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                saveDraft();
            }, 2000); // Auto-save after 2 seconds of inactivity
        });
    });
}

function saveDraft() {
    const form = document.getElementById('poForm');
    const formData = new FormData(form);
    
    // Save to localStorage
    const draft = {};
    for (let [key, value] of formData.entries()) {
        draft[key] = value;
    }
    
    localStorage.setItem('po_draft', JSON.stringify(draft));
    console.log('Draft saved automatically');
}

function loadDraft() {
    const draft = localStorage.getItem('po_draft');
    if (draft) {
        const data = JSON.parse(draft);
        Object.keys(data).forEach(key => {
            const field = document.getElementById(key);
            if (field) {
                field.value = data[key];
            }
        });
        console.log('Draft loaded');
    }
}

// Initialize auto-save if enabled
if (typeof(Storage) !== "undefined") {
    setupAutoSave();
    // Uncomment the line below to enable auto-loading of drafts
    // loadDraft();
}
