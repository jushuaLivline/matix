document.addEventListener("DOMContentLoaded", function () {
    window.enableInputs = function(button) {
        // Enable inputs in the same row
        const row = button.closest('tr');
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            // Store original value before enabling input
            if (!input.hasAttribute('data-original-value')) {
                input.setAttribute('data-original-value', input.value);
            }
            input.disabled = false;
        });
    

        inputs.forEach(input => {
            if (input.getAttribute('data-name') !== 'product_name') {
                input.disabled = false; // Enable all except 'product_name'
            }
        });

        // Hide "EditDelete" div and show "UdpateUndo" div
        const editDeleteDiv = row.querySelector('#EditDelete');
        const updateUndoDiv = row.querySelector('#UdpateUndo');
        editDeleteDiv.style.display = 'none';
        updateUndoDiv.style.display = 'flex';
    }

    window.updateData = function(button) {
        // Get the row and data ID
        const row = button.closest('tr');
        const instructionDataId = row.getAttribute('data-temp-id');

        // Get the input values to update
        const productCodeInput = row.querySelector('input[name="product_code_' + instructionDataId + '"]');
        const productNameInput = row.querySelector('input[name="product_name_' + instructionDataId + '"]');
        const instructionDateInput = row.querySelector('input[name="instruction_date"]');
        const instructionNumberInput = row.querySelector('input[name="instruction_number"]');
        const instructionKanbanQuantityInput = row.querySelector('input[name="instruction_kanban_quantity"]');
        // Get the supplier code input element
        const process_code = document.getElementById('process_code');

        // Prepare the data to send in the request
        const data = {
            instruction_data_id: instructionDataId,
            process_code: process_code.value,
            product_code: productCodeInput.value,
            product_name: productNameInput.value,
            instruction_date: instructionDateInput.value,
            instruction_number: instructionNumberInput.value,
            instruction_kanban_quantity: instructionKanbanQuantityInput.value
        };

        document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

        // Get the CSRF token from the meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        if(!confirm('端数指示入力情報を更新します、よろしいでしょうか？')) return;

        // Send an AJAX request to update the session data
        $.ajax({
            url: '/outsource/fraction/temp-update-data',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                $('#successUpdate').fadeIn(500, function() {
                    $(this).delay(500);
                });

                // Disable inputs in the same row
                const inputs = row.querySelectorAll('input');
                inputs.forEach(input => {
                    input.disabled = true;
                });

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
                        const processCode = $('#process_code_error');
                        if (input.length) {
                            // input.addClass('input-error');
                        }
    
                        if (errorContainer.length) {
                            errorContainer.html(`<div class="error_msg text-danger">${errors[field][0]}</div>`);
                        }
                        if (errors['process_code']) {
                            processCode.html(`<div class="error_msg text-danger">${errors['process_code'][0]}</div>`);
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
            document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })
        }
    }
        
    window.storeData = function(button) {
        var partNumber = $('#product_code').val();
        var partName = $('#product_name').val();
        var instructionDate = $('#date_instruction').val();
        var instructionNumber = $('#number_instruction').val();
        var instructionKanbanQuantity = $('#instruction_kanban_quantity').val();
        const row = button.closest('tr');
        
        // Get the supplier code input element
        const process_code = document.getElementById('process_code');

        // Get the supplier code input value
        const processCode = process_code.value;

        var newData = {
            'process_code': processCode,
            'product_code': partNumber,
            'product_name': partName,
            'instruction_date': instructionDate,
            'instruction_number': instructionNumber,
            'instruction_kanban_quantity': instructionKanbanQuantity
        };

        document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

        // Get the CSRF token from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        if(!confirm('端数指示情報を追加します、よろしいでしょうか？')) return;

        $.ajax({
            url: '/outsource/fraction/temp-data',
            method: 'POST',
            data: newData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                // Show the success message with a delay
                $('#successInputs').delay(1000).fadeIn(400, function() {
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
                        const processCode = $('#process_code_error');
                        if (input.length) {
                            // input.addClass('input-error');
                        }
    
                        if (errorContainer.length) {
                            errorContainer.html(`<div class="error_msg text-danger">${errors[field][0]}</div>`);
                        }
                        if (errors['process_code']) {
                            processCode.html(`<div class="error_msg text-danger">${errors['process_code'][0]}</div>`);
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
        // Get the supplier code input element
        const processCodeInput = document.getElementById('process_code');

        // Get the supplier code input value
        const processCode = processCodeInput.value;

        // Get the session data from the server-side
        // const sessionData = {!! json_encode(session('sessionData', [])) !!};
        const sessionData = window.sessionData || {};

        // Validate the supplier code input value
        if (!processCode) {
            $('#warningInputs').show();
            processCodeInput.classList.add('input-error');
            return; // Stop execution if inputs are missing
        }

        // Prepare the data to send in the request
        const data = {
            process_code: processCode,
            session_data: sessionData
        };

        if (!confirm('端数指示情報を登録します、よろしいでしょうか？')) return false;
        
        // Send an AJAX request to the server to save the data
        $.ajax({
            url: '/outsource/fraction',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                sessionData.length = 0;
                
                // Show the success message with a delay
                $('#successInputs').delay(300).fadeIn(100, function() {
                    $(this).delay(300);
                });

                // scroll to top
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
                // Clear the inputs in the form
                $('#process_code, #supplier_name').val('')
                $('.table-fraction tbody tr').each(function() {
                   $(this).delay(300).remove();
                });
            },
            error: function(xhr, status, error) {
                // Handle the error response
                alert('An error occurred while storing the data.');
            }
        });
    }

    window.clearData = function(button) {
            var tr = button.closest('tr');
            var inputs = tr.getElementsByTagName('input');
            
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].value = '';
            }
    }

    window.confirmDelete = function (button) {
        if (confirm('端数指示情報を削除します、よろしいでしょうか？')) {
            const row = button.closest('tr');
            const dataId = row.getAttribute('data-temp-id');
    
            fetch(`/outsource/fraction/delete-data/${dataId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error deleting data');
                }
                return response.json();
            })
            .then(responseData => {
                alert('端数指示情報の削除が完了しました');
                row.remove();
                bulkSavingButton();
            })
            .catch(error => {
                alert('Error deleting data: ' + error);
            });
        }
    };
    
    $(document).ready(function () {
        // Adding comment so that it will reflect on dev
        function fetchProcessName(processCode) {
            if (processCode) {
                $.ajax({
                    url: '/outsource/fraction/get-process-name',
                    type: 'POST',
                    data: {
                        process_code: processCode,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === "success") {
                            $('#supplier_name').val(response.process_name);
                        } else {
                            alert(response.message);
                            $('#supplier_name').val("");
                        }
                    }
                });
            } else {
                $('#supplier_name').val('');
            }
        }
    
        $('#process_code').on('input', function () {
            fetchProcessName($(this).val());
        });
    
        var initialProcessCode = $('#process_code').val();
        if (initialProcessCode) {
            fetchProcessName(initialProcessCode);
        }
    });    
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