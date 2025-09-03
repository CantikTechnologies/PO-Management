document.addEventListener('DOMContentLoaded', function() {
    const addEntryBtn = document.getElementById('addEntryBtn');
    const modal = document.getElementById('entryFormModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const entryForm = document.getElementById('entryForm');
    const searchInput = document.getElementById('searchInput');

    addEntryBtn.addEventListener('click', () => showForm());
    closeModal.addEventListener('click', () => hideForm());
    cancelBtn.addEventListener('click', () => hideForm());
    window.addEventListener('click', (event) => {
        if (event.target == modal) {
            hideForm();
        }
    });

    entryForm.addEventListener('submit', saveEntry);
    searchInput.addEventListener('keyup', () => filterEntries(searchInput.value));

    loadEntries();
    loadStats();
});

let allEntries = [];

function showForm(entry = null) {
    const modal = document.getElementById('entryFormModal');
    const formTitle = document.getElementById('formTitle');
    const entryId = document.getElementById('entryId');
    const formGrid = document.querySelector('.form-grid');

    formTitle.textContent = entry ? 'Edit Outsourcing Entry' : 'Add New Outsourcing Entry';
    entryId.value = entry ? entry.outsourcing_id : '';

    // Define form fields
    const fields = [
        { name: 'billing_id', label: 'Invoice Number', type: 'select', required: true },
        { name: 'vendor_name', label: 'Vendor Name', type: 'text', required: true },
        { name: 'work_description', label: 'Work Description', type: 'text' },
        { name: 'amount', label: 'Amount', type: 'number', required: true },
        { name: 'payment_status', label: 'Payment Status', type: 'select', options: ['Pending', 'Paid'], required: true },
    ];

    formGrid.innerHTML = fields.map(field => {
        if (field.type === 'select') {
            return `
                <div class="form-group">
                    <label for="${field.name}">${field.label} ${field.required ? '*' : ''}</label>
                    <select id="${field.name}" name="${field.name}" ${field.required ? 'required' : ''}>
                        <!-- Options will be loaded dynamically -->
                    </select>
                </div>
            `;
        } else {
            return `
                <div class="form-group">
                    <label for="${field.name}">${field.label} ${field.required ? '*' : ''}</label>
                    <input type="${field.type}" id="${field.name}" name="${field.name}"
                           ${field.required ? 'required' : ''}
                           value="${entry ? (entry[field.name] || '') : ''}">
                </div>
            `;
        }
    }).join('');

    // Load invoices for the dropdown
    loadInvoices(entry ? entry.billing_id : null);
    
    // Set payment status dropdown if editing
    if (entry) {
        document.getElementById('payment_status').value = entry.payment_status;
    } else {
        // Create payment status options for new entry
        const paymentStatusSelect = document.getElementById('payment_status');
        paymentStatusSelect.innerHTML = '<option value="Pending">Pending</option><option value="Paid">Paid</option>';
    }

    modal.style.display = 'flex';
}

async function loadInvoices(selectedId = null) {
    try {
        const response = await fetch('get_invoices.php');
        const result = await response.json();
        if (result.success) {
            const select = document.getElementById('billing_id');
            select.innerHTML = '<option value="">-- Select Invoice --</option>';
            result.data.forEach(invoice => {
                const option = document.createElement('option');
                option.value = invoice.billing_id;
                option.textContent = invoice.invoice_number;
                if (selectedId && invoice.billing_id == selectedId) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading invoices:', error);
    }
}

function hideForm() {
    const modal = document.getElementById('entryFormModal');
    modal.style.display = 'none';
}

async function loadEntries() {
    try {
        const response = await fetch('list.php');
        const result = await response.json();
        if (result.success) {
            allEntries = result.data;
            renderTable(allEntries);
        } else {
            console.error('Failed to load entries:', result.error);
        }
    } catch (error) {
        console.error('Error loading entries:', error);
    }
}

async function loadStats() {
    try {
        const response = await fetch('totals.php?ts=' + Date.now());
        const result = await response.json();
        
        if (result.success) {
            const stats = result.data;
            console.log('Outsourcing stats loaded:', stats);
            
            const statsGrid = document.getElementById('statsGrid');
            statsGrid.innerHTML = `
                <div class="stat-card">
                    <div class="stat-header"><span class="stat-title">Total Entries</span><span class="stat-icon">&#128202;</span></div>
                    <div class="stat-content"><div class="stat-value">${stats.total_entries || 0}</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header"><span class="stat-title">Total Vendor Inv Value</span><span class="stat-icon">&#128176;</span></div>
                    <div class="stat-content"><div class="stat-value">₹${formatNumber(stats.total_vendor_inv_value || 0)}</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header"><span class="stat-title">Total Net Payable</span><span class="stat-icon">&#128176;</span></div>
                    <div class="stat-content"><div class="stat-value">₹${formatNumber(stats.total_net_payable || 0)}</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header"><span class="stat-title">Total Payment Value</span><span class="stat-icon">&#128176;</span></div>
                    <div class="stat-content"><div class="stat-value">₹${formatNumber(stats.total_payment_value || 0)}</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header"><span class="stat-title">Total Pending Payment</span><span class="stat-icon">&#9203;</span></div>
                    <div class="stat-content"><div class="stat-value">₹${formatNumber(stats.total_pending_payment || 0)}</div></div>
                </div>
            `;
        } else {
            console.error('Stats error:', result.error);
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

function renderTable(entries) {
    const tableBody = document.getElementById('entriesTableBody');
    const noDataMessage = document.getElementById('noDataMessage');

    if (entries.length === 0) {
        tableBody.innerHTML = '';
        noDataMessage.style.display = 'block';
        return;
    }

    noDataMessage.style.display = 'none';
    
    // Debug: Log the first entry to see what fields are available
    if (entries.length > 0) {
        console.log('First entry data:', entries[0]);
        console.log('net_payable value:', entries[0].net_payable);
    }
    
    tableBody.innerHTML = entries.map(entry => {
        // Calculate net_payable if it's missing or undefined
        let netPayable = entry.net_payable;
        if (netPayable === undefined || netPayable === null || netPayable === 0) {
            const vendorInvValue = parseFloat(entry.vendor_inv_value) || 0;
            const tdsDed = parseFloat(entry.tds_ded) || 0;
            netPayable = (vendorInvValue * 1.18) - tdsDed;
        }
        
        return `
        <tr>
            <td>${entry.project_details || ''}</td>
            <td>${entry.vendor_name || ''}</td>
            <td>₹${formatNumber(parseFloat(entry.vendor_inv_value) || 0)}</td>
            <td>₹${formatNumber(parseFloat(netPayable) || 0)}</td>
            <td>₹${formatNumber(parseFloat(entry.payment_value) || 0)}</td>
            <td>₹${formatNumber(parseFloat(entry.pending_payment) || 0)}</td>
            <td>
                <button class="btn-secondary btn-edit" data-id="${entry.id}">
                    <span style="margin-right: 4px;">✏️</span>Edit
                </button>
                <button class="btn-danger btn-delete" data-id="${entry.id}">
                    <span style="margin-right: 4px;">🗑️</span>Delete
                </button>
            </td>
        </tr>
        `;
    }).join('');

    // Add event listeners for edit and delete buttons
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', () => editEntry(button.dataset.id));
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', () => deleteEntry(button.dataset.id));
    });
}

function filterEntries(query) {
    const lowerCaseQuery = query.toLowerCase();
    const filteredEntries = allEntries.filter(entry => {
        return Object.values(entry).some(value => 
            String(value).toLowerCase().includes(lowerCaseQuery)
        );
    });
    renderTable(filteredEntries);
}

async function saveEntry(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const url = 'save.php';

    try {
        const response = await fetch(url, { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            hideForm();
            loadEntries();
            loadStats();
        } else {
            alert(`Error: ${result.error}`);
        }
    } catch (error) {
        console.error('Error saving entry:', error);
        alert('An error occurred while saving the entry.');
    }
}

function editEntry(id) {
    const entry = allEntries.find(e => e.id === id);
    if (entry) {
        showForm(entry);
    }
}

async function deleteEntry(id) {
    if (!confirm('Are you sure you want to delete this entry?')) {
        return;
    }

    const formData = new FormData();
    formData.append('id', id);

    try {
        const response = await fetch('delete.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            loadEntries();
            loadStats();
        } else {
            alert(`Error: ${result.error}`);
        }
    } catch (error) {
        console.error('Error deleting entry:', error);
        alert('An error occurred while deleting the entry.');
    }
}

// Utility function to format numbers with Indian locale
function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    return parseFloat(num).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
