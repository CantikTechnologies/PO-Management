async function fetchEntries(){
  const res = await fetch('list.php');
  const rows = await res.json();
  const tbody = document.querySelector('#entriesTable tbody');
  tbody.innerHTML = '';
  rows.forEach((r, idx) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${idx+1}</td>
      <td>${escapeHtml(r.project_details || '')}</td>
      <td>${formatNum(r.cantik_inv_value_taxable)}</td>
      <td>${formatNum(r.tds)}</td>
      <td>${formatNum(r.receivable)}</td>
      <td>${escapeHtml(r.vendor_name || '')}</td>
      <td>
        <button class="btn btn-sm btn-outline-primary me-1" onclick="editEntry(${r.id})">Edit</button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteEntry(${r.id})">Delete</button>
      </td>`;
    tbody.appendChild(tr);
  });
}

function formatNum(n){
  return parseFloat(n).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
}

function escapeHtml(s){
  if(!s) return '';
  return s.replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;');
}

function recalc(){
  const taxable = parseFloat(document.getElementById('cantik_inv_value_taxable').value)||0;
  const tds = parseFloat((taxable * 0.02).toFixed(2));
  const receivable = parseFloat((taxable - tds).toFixed(2));
  document.getElementById('tds').value = tds;
  document.getElementById('receivable').value = receivable;
}

// submit form
document.getElementById('entryForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const form = new FormData(this);
  // ensure tds & receivable are available
  if(!form.get('tds') || form.get('tds')===''){
    const taxable = parseFloat(form.get('cantik_inv_value_taxable')||0);
    form.set('tds', (taxable*0.02).toFixed(2));
    form.set('receivable', (taxable - taxable*0.02).toFixed(2));
  }
  const res = await fetch('save.php', { method: 'POST', body: form });
  const j = await res.json();
  if (j.success) {
    resetForm();
    fetchEntries();
    alert('Saved successfully');
  } else {
    alert('Error saving data');
  }
});

function resetForm(){
  document.getElementById('entryForm').reset();
  document.getElementById('entryId').value = '';
  document.getElementById('tds').value = '';
  document.getElementById('receivable').value = '';
}
document.getElementById('resetBtn').addEventListener('click', resetForm);

async function editEntry(id){
  const res = await fetch('get.php?id='+id);
  const r = await res.json();
  document.getElementById('entryId').value = r.id;
  document.getElementById('project_details').value = r.project_details;
  document.getElementById('cost_center').value = r.cost_center;
  document.getElementById('customer_po').value = r.customer_po;
  document.getElementById('cantik_invoice_no').value = r.cantik_invoice_no;
  document.getElementById('cantik_invoice_date').value = r.cantik_invoice_date;
  document.getElementById('cantik_inv_value_taxable').value = r.cantik_inv_value_taxable;
  document.getElementById('tds').value = r.tds;
  document.getElementById('receivable').value = r.receivable;
  document.getElementById('against_vendor_inv_number').value = r.against_vendor_inv_number;
  document.getElementById('payment_recpt_date').value = r.payment_recpt_date;
  document.getElementById('payment_advise_no').value = r.payment_advise_no;
  document.getElementById('vendor_name').value = r.vendor_name;
  window.scrollTo({top:0, behavior:'smooth'});
}

async function deleteEntry(id){
  if(!confirm('Delete entry #'+id+' ?')) return;
  const form = new FormData(); form.append('id', id);
  const res = await fetch('delete.php',{method:'POST', body: form});
  const j = await res.json();
  if(j.success){ fetchEntries(); alert('Deleted'); } else { alert('Delete failed'); }
}

window.addEventListener('DOMContentLoaded', () => { fetchEntries(); });