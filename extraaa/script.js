const samplePOData = [
    {
        id: 1,
        projectDescription: "Raptakos Resource Deployment - Anuj Kushwaha",
        costCenter: "Raptakos PT",
        sowNumber: "FC2024-497",
        startDate: "2025-01-16",
        endDate: "2025-03-31",
        poNumber: "4500095281",
        poDate: "2025-01-27",
        poValue: 175500,
        billingFrequency: "Monthly",
        targetGM: 0.05,
        pendingAmount: 3775,
        poStatus: "Closed",
        vendorName: "VRATA TECH SOLUTIONS PRIVATE LIMITED"
    },
    {
        id: 2,
        projectDescription: "Panasonic Resource Deployment - Shankar Bhuyan",
        costCenter: "GGN-NTT-PANASONIC-PT-01",
        sowNumber: "FC2024-391",
        startDate: "2024-11-25",
        endDate: "2025-11-24",
        poNumber: "4500095649",
        poDate: "2025-02-06",
        poValue: 1999992,
        billingFrequency: "Monthly",
        targetGM: 0.1,
        pendingAmount: 671684.25,
        poStatus: "Open",
        vendorName: "BCT Consulting Private Limited"
    },
    {
        id: 3,
        projectDescription: "HMSI Resource Deployment- Dotnet - Alok Kumar",
        costCenter: "GGN-NTT-HMSI-DOTNET-PT-01",
        sowNumber: "FC2025-080",
        startDate: "2025-04-22",
        endDate: "2026-03-31",
        poNumber: "4500098672",
        poDate: "2025-05-29",
        poValue: 1043120,
        billingFrequency: "Monthly",
        targetGM: 0.05,
        pendingAmount: 736320,
        poStatus: "Open",
        vendorName: "CANTICLE TECHNOLOGIES PRIVATE LIMITED"
    },
    {
        id: 4,
        projectDescription: "Sun Pharma - Website AMC",
        costCenter: "Sun Pharma PT",
        sowNumber: "FC2024-465",
        startDate: "2024-09-12",
        endDate: "2025-10-31",
        poNumber: "4500095362",
        poDate: "2025-01-26",
        poValue: 1068054,
        billingFrequency: "Monthly",
        targetGM: 0.05,
        pendingAmount: 240116,
        poStatus: "Open",
        vendorName: "CARMATEC IT SOLUTIONS PRIVATE LIMITED"
    },
    {
        id: 5,
        projectDescription: "HMSI Resource Deployment- Shubham Mishra",
        costCenter: "HMSI Bot Support-RPA",
        sowNumber: "FC2024-416",
        startDate: "2024-11-01",
        endDate: "2025-09-30",
        poNumber: "4500094574",
        poDate: "2025-01-01",
        poValue: 1452000,
        billingFrequency: "Monthly",
        targetGM: 0.05,
        pendingAmount: 532200,
        poStatus: "Open",
        vendorName: "BUSYBOTS PRIVATE LIMITED"
    }
];

// Global variables
let allPOs = [...samplePOData];
let filteredPOs = [...allPOs];

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount || 0);
}

function formatPercentage(value) {
    return ((value || 0) * 100).toFixed(1) + '%';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function getStatusColor(status) {
    return status?.toLowerCase() === 'open' ? 'status-open' : 'status-closed';
}

// DOM elements
const addPOBtn = document.getElementById('addPOBtn');
const poFormModal = document.getElementById('poFormModal');
const closeModal = document.getElementById('closeModal');
const cancelBtn = document.getElementById('cancelBtn');
const poForm = document.getElementById('poForm');
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const poTableBody = document.getElementById('poTableBody');
const noDataMessage = document.getElementById('noDataMessage');

// Statistics elements
const totalPOsEl = document.getElementById('totalPOs');
const totalPOValueEl = document.getElementById('totalPOValue');
const totalPendingEl = document.getElementById('totalPending');
const avgTargetGMEl = document.getElementById('avgTargetGM');
const poDescriptionEl = document.getElementById('poDescription');

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    renderPOs();
    updateStatistics();
    setupEventListeners();
});

function setupEventListeners() {
    // Modal controls
    addPOBtn.addEventListener('click', openModal);
    closeModal.addEventListener('click', closeModalHandler);
    cancelBtn.addEventListener('click', closeModalHandler);
    
    // Form submission
    poForm.addEventListener('submit', handleFormSubmit);
    
    // Search and filter
    searchInput.addEventListener('input', handleSearch);
    statusFilter.addEventListener('change', handleStatusFilter);
    
    // Close modal on outside click
    poFormModal.addEventListener('click', function(e) {
        if (e.target === poFormModal) {
            closeModalHandler();
        }
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && poFormModal.style.display === 'block') {
            closeModalHandler();
        }
    });
}

// Modal functions
function openModal() {
    poFormModal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModalHandler() {
    poFormModal.style.display = 'none';
    document.body.style.overflow = 'auto';
    poForm.reset();
}

// Form handling
function handleFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(poForm);
    const newPO = {
        id: allPOs.length + 1,
        projectDescription: formData.get('projectDescription'),
        costCenter: formData.get('costCenter'),
        sowNumber: formData.get('sowNumber'),
        startDate: formData.get('startDate'),
        endDate: formData.get('endDate'),
        poNumber: formData.get('poNumber'),
        poDate: formData.get('poDate'),
        poValue: parseFloat(formData.get('poValue')) || 0,
        billingFrequency: formData.get('billingFrequency'),
        targetGM: parseFloat(formData.get('targetGM')) || 0,
        pendingAmount: parseFloat(formData.get('pendingAmount')) || 0,
        poStatus: formData.get('poStatus') || 'Open',
        vendorName: formData.get('vendorName')
    };
    
    allPOs.push(newPO);
    filteredPOs = [...allPOs];
    
    renderPOs();
    updateStatistics();
    closeModalHandler();
    
    // Show success message
    showNotification('Purchase Order added successfully!', 'success');
}

// Search and filter functions
function handleSearch() {
    applyFilters();
}

function handleStatusFilter() {
    applyFilters();
}

function applyFilters() {
    const searchTerm = searchInput.value.toLowerCase();
    const statusValue = statusFilter.value;
    
    filteredPOs = allPOs.filter(po => {
        const matchesSearch = !searchTerm || 
            (po.projectDescription && po.projectDescription.toLowerCase().includes(searchTerm)) ||
            (po.poNumber && po.poNumber.toLowerCase().includes(searchTerm)) ||
            (po.vendorName && po.vendorName.toLowerCase().includes(searchTerm)) ||
            (po.costCenter && po.costCenter.toLowerCase().includes(searchTerm));
        
        const matchesStatus = !statusValue || po.poStatus === statusValue;
        
        return matchesSearch && matchesStatus;
    });
    
    renderPOs();
}

// Render functions
function renderPOs() {
    if (filteredPOs.length === 0) {
        poTableBody.innerHTML = '';
        noDataMessage.style.display = 'block';
        return;
    }
    
    noDataMessage.style.display = 'none';
    
    poTableBody.innerHTML = filteredPOs.map(po => `
        <tr>
            <td>
                <div class="project-cell">
                    <div class="project-title">${po.projectDescription || '-'}</div>
                    <div class="cost-center">${po.costCenter || '-'}</div>
                </div>
            </td>
            <td>
                <div class="po-number-cell">${po.poNumber || '-'}</div>
                <div class="sow-number">${po.sowNumber || '-'}</div>
            </td>
            <td>
                <div class="vendor-cell">${po.vendorName || '-'}</div>
            </td>
            <td>
                <div class="value-cell">${formatCurrency(po.poValue)}</div>
                <div class="billing-frequency">${po.billingFrequency || '-'}</div>
            </td>
            <td>
                <div class="pending-amount">${formatCurrency(po.pendingAmount)}</div>
            </td>
            <td>
                <div class="target-gm">${formatPercentage(po.targetGM)}</div>
            </td>
            <td>
                <span class="status-badge ${getStatusColor(po.poStatus)}">${po.poStatus || 'Open'}</span>
            </td>
            <td>
                <div class="period-cell">
                    <div class="start-date">${formatDate(po.startDate)}</div>
                    <div class="end-date">to ${formatDate(po.endDate)}</div>
                </div>
            </td>
        </tr>
    `).join('');
}

function updateStatistics() {
    const totalPOs = allPOs.length;
    const totalPOValue = allPOs.reduce((sum, po) => sum + (po.poValue || 0), 0);
    const totalPending = allPOs.reduce((sum, po) => sum + (po.pendingAmount || 0), 0);
    const avgTargetGM = totalPOs > 0 ? allPOs.reduce((sum, po) => sum + (po.targetGM || 0), 0) / totalPOs : 0;
    const totalActive = allPOs.filter(po => po.poStatus === 'Open').length;
    const totalClosed = allPOs.filter(po => po.poStatus === 'Closed').length;
    
    totalPOsEl.textContent = totalPOs;
    totalPOValueEl.textContent = formatCurrency(totalPOValue);
    totalPendingEl.textContent = formatCurrency(totalPending);
    avgTargetGMEl.textContent = formatPercentage(avgTargetGM);
    poDescriptionEl.textContent = `${totalActive} active, ${totalClosed} closed`;
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? 'hsl(var(--success))' : 'hsl(var(--info))'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-elegant);
        z-index: 1001;
        font-weight: 500;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}