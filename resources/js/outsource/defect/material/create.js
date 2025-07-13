window.clearData = function(button) {
    var tr = button.closest('tr');
    var inputs = tr.getElementsByTagName('input');
    
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].value = '';
    }
}

window.enableInputs = function(button) {
    const row = button.closest('tr');
    if (!row) {
        console.error('No parent <tr> found for button:', button);
        return;
    }

    const selects = row.querySelectorAll('select');
    const inputs = row.querySelectorAll('input');
    
    //create and attribute and retain original values 
    selects.forEach(select => {
        select.setAttribute("data-original-value", select.value);
        select.disabled = false;
    });
    //create and attribute and retain original values 
    inputs.forEach(input => {
        input.setAttribute("data-original-value", input.value);
        input.disabled = false;
    });

    $("#grand_total").attr("data-original-value", $("#grand_total").text());
    
    const editDeleteDiv = row.querySelector('#EditDelete');
    const updateUndoDiv = row.querySelector('#UdpateUndo');
    
    if (editDeleteDiv && updateUndoDiv) {
        editDeleteDiv.style.display = 'none';
        updateUndoDiv.style.display = 'flex';
    } else {
        console.warn('Could not find #EditDelete or #UdpateUndo in row:', row);
    }
};

window.updateData = function(button) {
    const row = button.closest('tr');
    const itemId = row.getAttribute('data-id');
    
    const selectReason = row.querySelector('select');
    const quantityInput = row.querySelector('input[type="number"]');
    const processingRateSelect = row.querySelectorAll('select')[1];
    const subTotalInput = row.querySelector('input[type="text"]');
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const updatedData = {
        id: itemId,
        reason_code: selectReason.value,
        quantity: quantityInput.value,
        processing_rate: processingRateSelect.value,
        subTotal: subTotalInput.value,
    };

    document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

    
    // Send the updated data to the server for processing
    fetch('/outsource/defect/material/update/record', {
        method: 'POST', // or 'PUT' depending on your API
        headers: {
            // 'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(updatedData),
    })
    .then(response => {
        // Handle the response from the server (e.g., show a success message)
        if (response.ok) {
            alert('データは正常に更新されました');

            // Disable inputs in the same row
            const selects = row.querySelectorAll('select');
            const inputs = row.querySelectorAll('input');
            
            selects.forEach(select => {
                select.disabled = true;
            });

            inputs.forEach(input => {
                input.disabled = true;
            });

            // Hide "UdpateUndo" div and show "EditDelete" div
            const editDeleteDiv = row.querySelector('#EditDelete');
            const updateUndoDiv = row.querySelector('#UdpateUndo');
            editDeleteDiv.style.display = 'flex';
            updateUndoDiv.style.display = 'none';

            location.reload();
        } else {
            console.error('Failed to update data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

window.cancelEdit = function(button) {
    if (confirm('キャンセルしますか？')) {
        // Disable inputs in the same row
        const row = button.closest('tr');
        const selects = row.querySelectorAll('select');
        const inputs = row.querySelectorAll('input');
        
        //Disable and restore original value
        selects.forEach(select => {
            if (select.hasAttribute("data-original-value")) {
                select.value = select.getAttribute("data-original-value");
            }
            select.disabled = true;
        });
        //Disable and restore original value
        inputs.forEach(input => {
            if (input.hasAttribute("data-original-value")) {
                input.value = input.getAttribute("data-original-value");
            }
            input.disabled = true;
        });

        $("#grand_total").text($("#grand_total").data("original-value"));
        document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

        // Hide "UdpateUndo" div and show "EditDelete" div
        const editDeleteDiv = row.querySelector('#EditDelete');
        const updateUndoDiv = row.querySelector('#UdpateUndo');
        editDeleteDiv.style.display = 'flex';
        updateUndoDiv.style.display = 'none';
    }
}

window.confirmDelete = function(button) {
    if (confirm('内示情報を削除します、よろしいでしょうか？')) {
        const row = button.closest('tr');
        const dataId = row.getAttribute('data-id');

        console.log('id:', dataId);

        fetch(`/outsource/defect/material/delete-temp/${dataId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error deleting data');
            }
            return response.json();
        })
        .then(responseData => {
            alert('支給材情報は正常に削除されました');
            row.remove();
            calculateTotalSum();
            document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })
        })
        .catch(error => {
            // Handle the error
            alert('Error deleting data: ' + error);
        });
    }
}

// Computation of subtotal and total
function calculateTotalSum() {
    var totalSum = 0;
    $(".sub_total").each(function () {
        totalSum += parseFloat($(this).val()) || 0;
    });
    
    $("#grand_total").text(totalSum.toFixed(0));
}

$(document).ready(function(){
    // Function to calculate and update itemSubtotal
    function calculateItemSubtotal(rowId) {
        var row = $("#" + rowId);
        var processingUnitPrice = parseFloat($("#processing_unit_price").val()) || 0;
        var itemQuantity = parseFloat(row.find('.item_quantity').val()) || 0;
        var itemProcessRate = parseFloat(row.find('.processing_rate').val()) || 0;

        var itemSubtotal = (processingUnitPrice * itemQuantity * itemProcessRate)/100;

        // Update the itemSubtotal field
        row.find('.sub_total').val(itemSubtotal.toFixed(0));
        calculateTotalSum();
    }

    //Computation of subtotal and total
    $(document).on("change input", ".item_quantity, .processing_rate", function() {
        var rowId = $(this).closest('tr').attr('id');
        if (rowId) {
            calculateItemSubtotal(rowId);
        }
    });

    // $('#form').validate({
    //     rules: {
    //         return_date: { required: true },
    //         process_code: { required: true },
    //         product_code: { required: true },
    //         slip_no: { required: true },
    //     },
    //     messages: {
    //         return_date: { required: '入力してください' },
    //         process_code: { required: '入力してください' },
    //         product_number: { required: '入力してください' },
    //         slip_no: { required: '入力してください' },
    //     },
    //     errorElement : 'div',
    //     errorPlacement: function(error, element) {
    //         if($(element).closest('div')){
    //             $(element).closest('div').siblings('div').html(error);
    //         }else{
    //             $(element).closest('p').closest('div').siblings('div').html(error);
    //         }
    //     },
    // });

    //Before registering, validate the inputs first
    $("#validateAndProceed").on("click", function (event) {
        event.preventDefault(); // Stop default navigation

        if ($("#form").valid()) { 
            if(!confirm("加工不良情報を登録します。よろしいでしょうか？")) return false;
            // If form is valid, navigate to the link
            window.location.href = $(this).attr("href");
        }
    });

    function fetchQueryName(query, get, model, value, name) {
        if (value) {
            $.ajax({
                url: '/outsource/defect/material/fetch-query-name',
                type: 'POST',
                data: {
                    query: query,
                    get: get,
                    model: model,
                    value: value,
                    compare: 'process_order',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                beforeSend: function(){
                    $("#"+name).val('');
                },
                success: function (response) {
                    if (response.status === "success") {
                        $("#"+name).val(response.result);
                    } else {
                        alert(response.message);
                        $("#"+name).val('');
                    }
                }
            });
        } else {
            $("[name='"+name+"']").val('');
        }
    }

    $('.fetchQueryName').on('input', function () {
        let query = $(this).data('query');
        let get = $(this).data('query-get');
        let model = $(this).data('model');
        let value = $(this).val();
        let name = $(this).data('reference');
        fetchQueryName(query, get, model, value, name);
    });

    $('.fetchQueryName').each(function () {
        let value = $(this).val();
        if (value) {
            let query = $(this).data('query');
            let get = $(this).data('query-get');
            let model = $(this).data('model');
            let name = $(this).data('reference');
            fetchQueryName(query, get, model, value, name);
        }
    });
    
})