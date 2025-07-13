var csrfToken = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function(){
    //Keep on displaying flash message after registration
    let message = sessionStorage.getItem('successMessage');
    if (message) {
        $("#card p").text(message);
        $("#card").fadeIn(500);
        
        sessionStorage.removeItem('successMessage');
    }

    //Calculate Sub total
    $(document).on('input', '.calculate_subtotal', function (event) {
        event.stopImmediatePropagation()
        const row = $(this).closest('tr')
        const number_of_accomodated = row.find('#number_of_accomodated').val()
            ?? row.find('[name="number_of_accomodated"]').val()
        const instruction_kanban_quantity = row.find('#instruction_kanban_quantity').val()
            ?? row.find('[name="instruction_kanban_quantity"]').val()
        const subtotal = number_of_accomodated * instruction_kanban_quantity
        row.find('#subTotal').val(subtotal)
        row.find('.subTotal').val(subtotal)
    })

    function checkAndSendAjax(row) {
        let management_no = row.find("input[name='management_no']").val();
        let product_code = row.find("input[name='product_code']").val();
    
        if (management_no && product_code) {
            sendAjaxCall(management_no, product_code, row);
        }
    }

    function sendAjaxCall(management_no, product_code, row) {
        $.ajax({
            url: '/outsource/kanban/temporary/kanban-fetch-uniform-capacity',
            method: 'POST',
            data: {
                management_no: management_no,
                product_code: product_code
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "JSON",
            beforeSend:function(){
                row.find("[name='management_no']").removeClass('input-error');
                row.find("[name='product_code']").removeClass('input-error');
                $("#error-message").addClass('d-none');
            },
            success: function (response) {
                if(response.status == "success"){
                    console.log("Updating inputs with:", response.result);
                    row.find("[name='uniform_number']").val(response.result.printed_jersey_number);
                    row.find("[name='number_of_accomodated']").val(response.result.number_of_accomodated);
                }else{
                    row.find("[name='management_no']").addClass('input-error');
                    row.find("[name='product_code']").addClass('input-error');
                    $("#error-message").text(response.message).removeClass("d-none");
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }

    $(document).on("input change", "input[name='management_no'], input[name='product_code']", function () {
        let row = $(this).closest("tr");
        checkAndSendAjax(row);
    });
});

window.clearData = function(button) {
    var tr = button.closest('tr');
    var inputs = tr.getElementsByTagName('input');
    
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].value = '';
    }

    $("#warningManagementNo").hide();
}

window.enableInputs = function(button) {
    // Enable inputs in the same row
    const row = button.closest('tr');
    const inputs = row.querySelectorAll('input');

    const btnSubmit = row.querySelector(".btnSubmitCustom");
    if (btnSubmit) btnSubmit.disabled = false;

    inputs.forEach(input => {
        // Store original value before enabling input
        if (!input.hasAttribute('data-original-value')) {
            input.setAttribute('data-original-value', input.value);
        }
        input.disabled = false;
    });

    // Hide "EditDelete" div and show "UdpateUndo" div
    const editDeleteDiv = row.querySelector('#EditDelete');
    const updateUndoDiv = row.querySelector('#UdpateUndo');
    editDeleteDiv.style.display = 'none';
    updateUndoDiv.style.display = 'flex';
}

window.updateData = function(button) {
    // Get the row and kanban data ID
    const row = button.closest('tr');
    const kanbanDataId = row.getAttribute('data-temp-kanban-id');
    const instructionDate = $('#date_instruction').val();
    const cached = row.hasAttribute('cached')

    // Get the input values to update
    const managementNoInput = row.querySelector('input[name="management_no"]');
    const productCodeInput = row.querySelector('input[name="product_code"]');
    const productNameInput = row.querySelector('input[name="product_name"]');
    const uniformNumberInput = row.querySelector('input[name="uniform_number"]');
    const instructionNumberInput = row.querySelector('input[name="instruction_number"]');
    const numberOfAccommodatedInput = row.querySelector('input[name="number_of_accomodated"]');
    const instructionKanbanQuantityInput = row.querySelector('input[name="instruction_kanban_quantity"]');


    document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

    // Prepare the data to send in the request
    const data = {
        kanban_data_id: kanbanDataId,
        management_no: managementNoInput.value,
        product_code: productCodeInput.value,
        product_name: productNameInput.value,
        uniform_number: uniformNumberInput.value,
        instruction_date: instructionDate,
        instruction_number: instructionNumberInput.value,
        number_of_accomodated: numberOfAccommodatedInput.value,
        instruction_kanban_quantity: instructionKanbanQuantityInput.value,
        arrival_quantity: instructionKanbanQuantityInput.value * numberOfAccommodatedInput.value
    };

    if(!confirm('臨時かんばん入力情報を更新します、よろしいでしょうか？')) return;

    // Send an AJAX request to update the session data
    $.ajax({
        url: `/outsource/kanban/temporary/${cached ? 'kanban-temp-update-data' : 'kanban-update-data'}`,
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            // Handle the success response
            $('#successUpdate').fadeIn(500, function() {
                $(this).delay(500);
            });

            // Disable inputs in the same row
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                input.disabled = true;
            });

            const btnSubmit = row.querySelector(".btnSubmitCustom");
            if (btnSubmit) btnSubmit.disabled = true;

            // Hide "UpdateUndo" div and show "EditDelete" div
            const editDeleteDiv = row.querySelector('#EditDelete');
            const updateUndoDiv = row.querySelector('#UdpateUndo');
            editDeleteDiv.style.display = 'flex';
            updateUndoDiv.style.display = 'none';
        },
        error: function(xhr, status, error) {
            // Handle the error response
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                // Clear previous errors
                $('.input-error').removeClass('input-error');
                $('.error_msg').remove();

                // Display errors
                Object.keys(errors).forEach(function (field) {
                    const fieldName = field.replace(/\.\d+$/, ''); // handles array fields

                    const input = $(row).find(`[id="${fieldName}"]`);
                    const errorContainer = $(row).find(`#${fieldName}_error`);
                    const dateInstruction = $('#instruction_date_error');
                    if (input.length) {
                        // input.addClass('input-error');
                    }

                    if (errorContainer.length) {
                        errorContainer.html(`<div class="error_msg text-danger">${errors[field][0]}</div>`);
                    }
                    if (errors['instruction_date']) {
                        dateInstruction.html(`<div class="error_msg text-danger">${errors['instruction_date'][0]}</div>`);
                    }

                    // Scroll to the page
                    $('html, body').animate({
                        scrollTop: 0
                    }, 300);
                });
            } else {
                alert('サーバーエラーが発生しました。');
            }
        }
    });
}

window.cancelEdit = function(button) {
    if (confirm('キャンセルしますか？')) {
        const row = button.closest('tr');
        const inputs = row.querySelectorAll('input');

        row.querySelectorAll('[type="text"]').forEach(input => {
            input.classList.remove('input-error');
        });

        const btnSubmit = row.querySelector(".btnSubmitCustom");
        if (btnSubmit) btnSubmit.disabled = true;

        $("#error-message").addClass('d-none');
        
        // Restore the original values before disabling the inputs
        inputs.forEach(input => {
            if (input.hasAttribute('data-original-value')) {
                input.value = input.getAttribute('data-original-value'); // Restore value
            }

            input.disabled = true; // Disable input
        });

        // Hide "UpdateUndo" div and show "EditDelete" div
        const editDeleteDiv = row.querySelector('#EditDelete');
        const updateUndoDiv = row.querySelector('#UdpateUndo');
        editDeleteDiv.style.display = 'flex';
        updateUndoDiv.style.display = 'none';
    }
};

window.storeData = function(button) {
    var managementNumber = $('#management_no').val();
    var productCode = $('#product_code').val();
    var productName = $('#product_name').val();
    var uniformNumber = $('#uniform_number').val();
    var instructionDate = $('#date_instruction').val();
    var instructionNumber = $('#number_instruction').val();
    var numberOfAccommodated = $('#number_of_accomodated').val();
    var instructionKanbanQuantity = $('#instruction_kanban_quantity').val();
    var arrivalQuantity = $('#subTotal').val();
    const row = button.closest('tr');

    // Get the supplier code input element
    const supplierCodeInput = document.getElementById('supplier_code');

    // Get the supplier code input value
    const supplierCode = supplierCodeInput.value;

    document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

    var newData = {
        'supplier_code' : supplierCode,
        'management_no': managementNumber,
        'product_code': productCode,
        'product_name': productName,
        'uniform_number': uniformNumber || null,
        'instruction_date': instructionDate,
        'instruction_number': instructionNumber,
        'number_of_accomodated': numberOfAccommodated,
        'instruction_kanban_quantity': instructionKanbanQuantity,
        'arrival_quantity': arrivalQuantity,
        'order_classification': 2
    };

    if(!confirm('臨時かんばん入力情報を登録します、よろしいでしょうか？')) return;

    $.ajax({
        url: '/outsource/kanban/temporary/kanban-temp-data',
        method: 'POST',
        data: newData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            $('#warningInputs').hide();
            $('#warningManagementNo').hide();

            // Show the success message with a delay
            $('#successInputs').fadeIn(500, function() {
                $(this).delay(500);
            });

            // Reload the page after a delay
            setTimeout(function() {
                location.reload();
            }, 1000);
        },
        error: function(xhr, status, error) {
            
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                // Clear previous errors
                $('.input-error').removeClass('input-error');
                $('.error_msg').remove();

                // Display errors
                Object.keys(errors).forEach(function (field) {
                    const fieldName = field.replace(/\.\d+$/, ''); // handles array fields

                    const input = $(row).find(`[id="${fieldName}"]`);
                    const errorContainer = $(row).find(`#${fieldName}_error`);
                    const dateInstruction = $('#instruction_date_error');
                    if (input.length) {
                        // input.addClass('input-error');
                    }

                    if (errorContainer.length) {
                        errorContainer.html(`<div class="error_msg text-danger">${errors[field][0]}</div>`);
                    }
                    if (errors['instruction_date']) {
                        dateInstruction.html(`<div class="error_msg text-danger">${errors['instruction_date'][0]}</div>`);
                    }

                    // Scroll to the page
                    $('html, body').animate({
                        scrollTop: 0
                    }, 300);
                });
            } else {
                alert('サーバーエラーが発生しました。');
            }
        }
    });
}

window.bulkSavingData = function(button) {
    var managementNumber = $('#management_no').val();
    var productCode = $('#product_code').val();
    var productName = $('#product_name').val();
    var uniformNumber = $('#uniform_number').val();
    var instructionDate = $('#date_instruction').val();
    var instructionNumber = $('#number_instruction').val();
    var numberOfAccommodated = $('#number_of_accomodated').val();
    var instructionKanbanQuantity = $('#instruction_kanban_quantity').val();

    // Get the supplier code input element
    const supplierCodeInput = document.getElementById('supplier_code');

    // Get the supplier code input value
    const supplierCode = supplierCodeInput.value;

    if (!instructionDate) {
        $('#warningInputs').show();

        if (!instructionDate) {
            $('#date_instruction').addClass('input-error');
        }

        return;
    }

    // Prepare the data to send in the request
    const data = {
        instruction_date: instructionDate,
        supplier_code: supplierCode,
        session_data: sessionKanbanData
    };

    //console.log(data);

    // Send an AJAX request to the server to save the data
    if (confirm("この内容で登録してもよろしいでしょうか？")) {
        $.ajax({
            url: '/outsource/kanban/temporary/kanban-store-data',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            beforeSend: function(){
                $("#date_instruction").removeClass('input-error');
            },
            success: function(response) {
                sessionKanbanData.length = 0;
                sessionStorage.setItem('successMessage', response.message);

                window.onbeforeunload = function() {
                    sessionStorage.removeItem('previousDate');
                    sessionStorage.removeItem('latestNumber');
                };

                $('#warningInputs').hide();

                // Reload the page after a delay
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr, status, error) {
                // Handle the error response
                alert('An error occurred while storing the data.');
            }
        });
    }
}

window.confirmDelete = function(button) {
    if (confirm('臨時かんばん情報を削除します、よろしいでしょうか？')) {
        const row = button.closest('tr');
        const dataId = row.getAttribute('data-temp-kanban-id');

        fetch(`/outsource/kanban/temporary/kanban-delete-data/${dataId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error deleting data');
                }
                return response.json();
            })
            .then(responseData => {
                // Handle the response data
                alert('臨時かんばん情報は正常に削除されました');

                // Remove the table row from the DOM
                row.remove();
                bulkSavingButton()
            })
            .catch(error => {
                // Handle the error
                alert('Error deleting data: ' + error);
            });
    }
}

function searchByManagementNoAndInstructionDate(data) {
    // Get the CSRF token from the meta tag
    const csrfToken = $('meta[name="csrf-token"]').attr('content')

    $.ajax({
        url: 'temporary-kanban-entry/search-by-management-no-and-instruction-date',
        method: 'POST',
        data,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            response.forEach(data => {
                if ($(`tr[data-temp-kanban-id="${data.id}"]`).html()) return
                $('#outsourced-processings tr:last').before(`<tr data-temp-kanban-id="${data.id}">
                    <td>
                        <input type="text"
                            name="management_no"
                            value="${data.management_no}"
                            class="numberCharacter"
                            maxLength="5"
                            disabled>
                    </td>
                    <td class="text-center">
                        <div class="center">
                            <input type="text" id="product_code_${data.id}" name="product_code" 
                                value="${data.product_code}" 
                                class="searchOnInput ProductMaterial${data.id}"
                                disabled>
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                data-target="searchProductModal_${data.id}">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                    </td>
                    <td>
                        <input type="text"
                            id="product_name_${data.id}"
                            name="product_name"
                            value="${data.product.product_name}"
                            class="middle-name text-left"
                            readonly>
                    </td>
                    <td>
                        <input type="text" class="textCharacter" name="uniform_number" value="${data.instruction_number ?? ''}" readonly>
                    </td>
                    <td>
                        <input type="text" class="numberCharacter" name="instruction_number" value="${data.incoming_flight_number}" disabled>
                    </td>
                    <td>
                        <input type="text" class="numberCharacter calculate_subtotal" name="number_of_accomodated" value="${data.arrival_number ?? ''}" readonly>
                    </td>
                    <td>
                        <input type="text" class="numberCharacter calculate_subtotal" name="instruction_kanban_quantity" value="${data.instruction_kanban_quantity}" disabled>
                    </td>
                    <td class="tA-cn" style="align-content: center;">
                        <input type="text" class="numberCharacter subTotal" value="${data.arrival_quantity}" readonly style="text-align: center !important;">
                    </td>
                    <td>
                        <div class="center" id="EditDelete">
                            <button onclick="enableInputs(this)" class="btn btn-block btn-blue mr-2" id="edit">編集</button>
                            <button onclick="confirmDelete(this)" class="btn btn-block btn-orange" style="margin-left: 2px" id="delete">削除</button>
                        </div>

                        <div class="center" id="UdpateUndo" style="display: none;">
                            <button onclick="updateData(this)" class="btn btn-block btn-green" id="update">更新</button>
                            <button onclick="cancelEdit(this)" class="btn btn-block btn-gray" style="margin-left: 1px" id="undo">取消</button>
                        </div>
                    </td>
                </tr>`)
                $('#outsourced-processings tbody').append(`{!! view('partials.modals.masters._search', [
                    'modalId' => 'searchProductModal_${data.id}',
                    'searchLabel' => '品番',
                    'resultValueElementId' => 'product_code_${data.id}',
                    'resultNameElementId' => 'product_name_${data.id}',
                    'model' => 'ProductNumber',
                ])->render() !!}`)
            })
        },
        error: function(xhr, status, error) {
            alert('An error occurred while fetching data.')
        }
    })
}

let debounceId

$('#date_instruction').on('input change', function () {
    const instructionDateElement = $('#date_instruction');

    if (instructionDateElement.length === 0) {
        console.error("Element #date_instruction not found.");
        return;
    }

    const instruction_date = instructionDateElement.val()?.trim();
    const management_no = $(this).val()?.trim();

    clearTimeout(debounceId)
    if (!instruction_date || !management_no) return
    debounceId = setTimeout(() => searchByManagementNoAndInstructionDate({
        management_no,
        instruction_date,
    }), 500)
})

$('#management_no').on('input', function () {
    const instructionDateElement = $('#date_instruction');
    if (instructionDateElement.length === 0) {
        console.error("Element #date_instruction not found.");
        return;
    }

    const instruction_date = instructionDateElement.val()?.trim();
    const management_no = $(this).val()?.trim();

    clearTimeout(debounceId)
    if (!instruction_date || !management_no) return
    debounceId = setTimeout(() => searchByManagementNoAndInstructionDate({
        management_no,
        instruction_date,
    }), 500)
})

const managementInput = document.getElementById('management_no');
managementInput.addEventListener('input', function () {
    const managementValue = managementInput.value.trim();
    if (managementValue) {
        if (managementValue.length === 5) {
            $.ajax({
                url: "/outsource/ajax-check-kanban-management-no",
                type: "GET",
                data: { barcode: managementValue },
                success: function (response) {
                    if (!response.exists) {
                        $("#warningManagementNo").show();
                    } else {
                        $("#warningManagementNo").hide();
                    }                
                },
                error: function (error) {
                    console.log("Error fetching barcode data.");
                },
            });
        }

    }
});

function bulkSavingButton() {
    let cta = $('.btn-bulk-saving');
    console.log($(".contentInner table tbody tr").length)
    if($(".contentInner table tbody tr").length > 0) {
        cta.removeClass('btn-disabled');
        cta.removeAttr('disabled'); 
    }else{
        cta.addClass('btn-disabled');
        cta.attr('disabled', 'disabled');
    }
}