$(document).ready(function() {
    loadPoNumbers();
    loadBillingTable();

    $('#billing-form').on('submit', function(e) {
        e.preventDefault();
        saveBilling();
    });

    $('#po_number').on('change', function() {
        var po_id = $(this).val();
        if (po_id) {
            $.ajax({
                url: 'get_details_for_billing.php',
                type: 'GET',
                data: { po_id: po_id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $('#balance-info').html('SO Value: ' + data.so_value + ' | Billed: ' + data.billed_amount + ' | Balance: ' + data.balance);
                    } else {
                        $('#balance-info').html('<span class="text-danger">Could not fetch details.</span>');
                    }
                }
            });
        } else {
            $('#balance-info').html('');
        }
    });
});

function loadPoNumbers() {
    $.ajax({
        url: 'get_details_for_billing.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var poSelect = $('#po_number');
                poSelect.empty();
                poSelect.append('<option value="">-- Select PO --</option>');
                $.each(response.data, function(index, po) {
                    poSelect.append('<option value="' + po.po_id + '">' + po.po_number + '</option>');
                });
            }
        }
    });
}

function loadBillingTable() {
    $('#billing-table-container').load('list.php');
}

function saveBilling() {
    var formData = {
        billing_id: $('#billing_id').val(),
        po_id: $('#po_number').val(), // This is the po_id
        invoice_number: $('#invoice_number').val(),
        invoice_date: $('#invoice_date').val(),
        invoice_amount: $('#invoice_amount').val(),
        payment_status: $('#payment_status').val(),
        payment_date: $('#payment_date').val()
        // TDS will be calculated on the backend
    };

    $.ajax({
        url: 'save.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                clearForm();
                loadBillingTable();
                $('#po_number').trigger('change'); // Refresh balance info
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function editBilling(billing_id) {
    $.ajax({
        url: 'get.php?id=' + billing_id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response) {
                $('#billing_id').val(response.billing_id);
                $('#po_number').val(response.po_id).trigger('change'); // Set dropdown and trigger change to load balance
                $('#invoice_number').val(response.invoice_number);
                $('#invoice_date').val(response.invoice_date);
                $('#invoice_amount').val(response.invoice_amount);
                $('#payment_status').val(response.payment_status);
                $('#payment_date').val(response.payment_date);
            }
        }
    });
}

function deleteBilling(billing_id) {
    if (confirm('Are you sure?')) {
        $.ajax({
            url: 'delete.php',
            type: 'POST',
            data: { id: billing_id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    loadBillingTable();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function clearForm() {
    $('#billing_id').val('');
    $('#billing-form')[0].reset();
    $('#balance-info').html('');
}