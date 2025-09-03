document.addEventListener('DOMContentLoaded', () => {
    const addBtn = document.getElementById('addBillingBtn');
    const modal = document.getElementById('billingFormModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const form = document.getElementById('billingForm');

    addBtn.addEventListener('click', () => openForm());
    closeModal.addEventListener('click', () => closeForm());
    cancelBtn.addEventListener('click', () => closeForm());
    window.addEventListener('click', (e) => { if (e.target === modal) closeForm(); });

    form.addEventListener('submit', onSubmit);

    loadBilling();
    loadStats();
});

function openForm(entry) {
    const modal = document.getElementById('billingFormModal');
    const idEl = document.getElementById('billingId');
    document.getElementById('projectDetails').value = entry?.project_details || '';
    document.getElementById('costCenter').value = entry?.cost_center || '';
    document.getElementById('customerPO').value = entry?.customer_po || '';
    document.getElementById('cantikInvoiceNo').value = entry?.cantik_invoice_no || '';
    document.getElementById('cantikInvoiceDate').value = entry?.cantik_invoice_date || '';
    document.getElementById('cantikInvValueTaxable').value = entry?.cantik_inv_value_taxable || '';
    document.getElementById('tds').value = entry?.tds || '';
    document.getElementById('receivable').value = entry?.receivable || '';
    document.getElementById('againstVendorInvNumber').value = entry?.against_vendor_inv_number || '';
    document.getElementById('paymentRecptDate').value = entry?.payment_recpt_date || '';
    document.getElementById('paymentAdviseNo').value = entry?.payment_advise_no || '';
    document.getElementById('vendorName').value = entry?.vendor_name || '';
    document.getElementById('remainingBalanceInPO').value = entry?.remaining_balance_in_po || '';
    idEl.value = entry?.id || '';
    modal.style.display = 'flex';
}

function closeForm() {
    const modal = document.getElementById('billingFormModal');
    document.getElementById('billingForm').reset();
    document.getElementById('billingId').value = '';
    modal.style.display = 'none';
}

function recalc() {
    const taxable = parseFloat(document.getElementById('cantikInvValueTaxable').value) || 0;
    const tds = +(taxable * 0.02).toFixed(2);
    const receivable = +((taxable * 1.18) - tds).toFixed(2);
    document.getElementById('tds').value = tds;
    document.getElementById('receivable').value = receivable;
}

async function onSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);

    // convert date to Y-m-d if provided
    const dateEl = document.getElementById('cantikInvoiceDate');
    if (dateEl && dateEl.value) data.set('cantik_invoice_date', dateEl.value);

    const resp = await fetch('save.php', { method: 'POST', body: data });
    const json = await resp.json();
    if (json.success) {
        closeForm();
        loadBilling();
        loadStats();
    } else {
        alert(json.message || 'Failed to save');
    }
}

async function loadBilling() {
    const tbody = document.getElementById('billingTableBody');
    tbody.innerHTML = '<tr><td colspan="10">Loading...</td></tr>';
    try {
        const resp = await fetch('list.php?ts=' + Date.now());
        const json = await resp.json();
        if (!json.success) throw new Error(json.message || 'Failed');
        const rows = json.data || [];
        tbody.innerHTML = rows.map(r => {
            return `
            <tr>
                <td>${escapeHtml(r.project_details || '')}</td>
                <td>${escapeHtml(r.cost_center || '')}</td>
                <td>${escapeHtml(r.customer_po || '')}</td>
                <td>${escapeHtml(r.cantik_invoice_no || '')}</td>
                <td>${escapeHtml(r.cantik_invoice_date || '')}</td>
                <td>₹${formatNumber(+r.cantik_inv_value_taxable || 0)}</td>
                <td>₹${formatNumber(+r.tds || 0)}</td>
                <td>₹${formatNumber(+r.receivable || 0)}</td>
                <td>${escapeHtml(r.vendor_name || '')}</td>
                <td>
                    <button class="btn-secondary" onclick='editRow(${r.id})'>Edit</button>
                </td>
            </tr>`;
        }).join('');
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="10">Failed to load</td></tr>';
        console.error(e);
    }
}

async function loadStats() {
    try {
        const resp = await fetch('totals.php?ts=' + Date.now());
        const json = await resp.json();
        if (!json.success) return;
        const d = json.data;
        document.getElementById('totalEntries').textContent = d.total_entries || 0;
        document.getElementById('totalTaxable').textContent = '₹' + formatNumber(d.total_taxable || 0);
        document.getElementById('totalTDS').textContent = '₹' + formatNumber(d.total_tds || 0);
        document.getElementById('totalReceivable').textContent = '₹' + formatNumber(d.total_receivable || 0);
    } catch (e) {
        console.error(e);
    }
}

function editRow(id) {
    fetch('get.php?id=' + id).then(r => r.json()).then(r => {
        if (!r || !r.id) return;
        openForm(r);
    });
}

function escapeHtml(s) {
    return (s || '').toString().replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));
}

function formatNumber(n) {
    try { return (+n).toLocaleString('en-IN', { maximumFractionDigits: 2 }); } catch { return n; }
}

window.recalc = recalc;

