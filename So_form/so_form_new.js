$(document).ready(function() {
    // Load the list of SOs and the PO dropdown when the page loads
    loadSOTable();
    loadPOs();

    // Handle form submission
    $('#so-form').on('submit', function(e) {
        e.preventDefault();
        saveSO();
    });

    // Handle clear button
    $('#clear-btn').on('click', function() {
        clearForm();
    });
});

// Load Purchase Orders into the dropdown
function loadPOs() {
    $.ajax({
        url: 'get_po_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var poSelect = $('#po_number');
                poSelect.empty();
                poSelect.append('<option value="">-- Select a Purchase Order --</option>');
                $.each(response.data, function(index, po) {
                    // Use po_id as the value and po_number as the text
                    poSelect.append('<option value="' + po.po_id + '">' + po.po_number + '</option>');
                });
            } else {
                console.error('Error loading Purchase Orders: ' + response.message);
            }
        },
        error: function() {
            console.error('Could not connect to the server to load Purchase Orders.');
        }
    });
}

// Load the table of Sales Orders
function loadSOTable() {
    $('#so-table-container').html('Loading...');
    $.ajax({
        url: 'list.php',
        type: 'GET',
        success: function(response) {
            $('#so-table-container').html(response);
        },
        error: function() {
            $('#so-table-container').html('<div class="alert alert-danger">Error loading list.</div>');
        }
    });
}

// Save (create or update) a Sales Order
function saveSO() {
    var formData = {
        so_id: $('#so_id').val(),
        po_id: $('#po_number').val(), // This is the po_id from the dropdown's value
        so_number: $('#so_number').val(),
        so_date: $('#so_date').val(),
        so_value: $('#so_value').val()
    };

    if (!formData.po_id || !formData.so_number || !formData.so_value) {
        alert('Please select a PO, and enter an SO Number and SO Value.');
        return;
    }

    $.ajax({
        url: 'save.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                clearForm();
                loadSOTable();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Could not connect to the server to save the SO.');
        }
    });
}

// Populate the form for editing a Sales Order
function editSO(so_id) {
    $.ajax({
        url: 'get.php?id=' + so_id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response) {
                $('#so_id').val(response.so_id);
                $('#po_number').val(response.po_id);
                $('#so_number').val(response.so_number);
                $('#so_date').val(response.so_date);
                $('#so_value').val(response.so_value);
                window.scrollTo(0, 0);
            } else {
                alert('Could not find SO data for editing.');
            }
        },
        error: function() {
            alert('Could not connect to the server to fetch SO data.');
        }
    });
}

// Delete a Sales Order
function deleteSO(so_id) {
    if (confirm('Are you sure you want to delete this Sales Order?')) {
        $.ajax({
            url: 'delete.php',
            type: 'POST',
            data: { id: so_id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    loadSOTable();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Could not connect to the server to delete the SO.');
            }
        });
    }
}

// Clear the form fields
function clearForm() {
    $('#so_id').val('');
    $('#so-form')[0].reset();
}