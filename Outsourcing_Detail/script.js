// script.js
(function(){
  const inv = document.getElementById('vendor_inv_value');
  const tds = document.getElementById('tds_ded');
  const net = document.getElementById('net_payable');
  const payv = document.getElementById('payment_value');
  const pending = document.getElementById('pending_payment');

  function fmt(n){
    return (isFinite(n)?(Math.round(n*100)/100).toFixed(2):'0.00');
  }

  function recompute(){
    const v = parseFloat(inv.value||0);
    const pv = parseFloat(payv.value||0);
    const t = +(v * 0.02);
    const n = +(v * 1.18) - t;
    const p = n - pv;

    tds.value = fmt(t);
    net.value = fmt(n);
    pending.value = fmt(p);
  }

  if(inv) inv.addEventListener('input', recompute);
  if(payv) payv.addEventListener('input', recompute);
  // initial compute
  recompute();
})();