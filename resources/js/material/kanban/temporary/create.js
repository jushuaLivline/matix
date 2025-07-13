document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.sessionSupplyTempData !== 'undefined') {
        window.sessionSupplyTempData.forEach(function(dataItem) {
            calculateRowSubtotal(dataItem.id);

            document.getElementById('number_of_accomodated_' + dataItem.id).addEventListener('input', function() {
                calculateRowSubtotal(dataItem.id);
            });

            document.getElementById('instruction_kanban_quantity_' + dataItem.id).addEventListener('input', function() {
                calculateRowSubtotal(dataItem.id);
            });
        });
    }

    /**
     * Calculates the subtotal for a row based on the number of accommodated items and the instruction kanban quantity.
     * Updates the subtotal input field with the calculated value.
     *
     * @param {string} id - The unique identifier for the row elements.
     */
    function calculateRowSubtotal(id) {
        var numberInput = document.getElementById('number_of_accomodated_' + id);
        var quantityInput = document.getElementById('instruction_kanban_quantity_' + id);
        var subtotalInput = document.getElementById('subTotal_' + id);

        if (numberInput && quantityInput && subtotalInput) {
            var number = parseInt(numberInput.value) || 0;
            var quantity = parseInt(quantityInput.value) || 0;
            subtotalInput.value = number * quantity;
        }
    }

    // Default row calculation
    var numberInputNew = document.getElementById('number_of_accomodated_new');
    var quantityInputNew = document.getElementById('instruction_kanban_quantity_new');
    var subtotalInputNew = document.getElementById('subTotal_new');

    function calculateNewSubtotal() {
        subtotalInputNew.value = (parseInt(numberInputNew.value) || 0) * (parseInt(quantityInputNew.value) || 0);
    }

    numberInputNew.addEventListener('input', calculateNewSubtotal);
    quantityInputNew.addEventListener('input', calculateNewSubtotal);

    // Set default date on `date_instruction` field
    const dateInput = document.getElementById('date_instruction');
    if (dateInput) {
        const today = new Date();
        const formattedDate = today.getFullYear().toString() +
                              (today.getMonth() + 1).toString().padStart(2, '0') +
                              today.getDate().toString().padStart(2, '0');
        dateInput.value = formattedDate;
    }
});

function isArrayEmpty(arr) {
    return Array.isArray(arr) && arr.length === 0;
}
// default input row
document.addEventListener('DOMContentLoaded', function () {

    // DECLARE ALL THE INPUTS FIELDS NEED FOR THE PROCESS
    const managementInput = document.getElementById('management_no');
    const manufacturerCode = document.getElementById('material_manufacturer_code');
    const productCodeInput = document.getElementById('product_code');
    const productName = document.getElementById('product_name');
    const uniformNumber = document.getElementById('uniform_number');
    const numberOfAccomodated = document.getElementById('number_of_accomodated_new');
    
    // Function to clear input fields
    function clearFields() {
        manufacturerCode.value = '';
        // productCodeInput.value = '';
        // productName.value = '';
        // uniformNumber.value = '';
        // numberOfAccomodated.value = '';

        // Trigger input event for numberOfAccomodated
        const accomodatedEvent = new Event('input', { bubbles: true });
        console.log("accomodatedEvent", accomodatedEvent)
        numberOfAccomodated.dispatchEvent(accomodatedEvent);
    }

    if (managementInput) {
        // Listen for input changes on the management_no field
        managementInput.addEventListener('input', function () {
            const managementValue = managementInput.value.trim();

            // Clear fields if input length is less than 5
            if (managementValue.length < 5) {
                clearFields();
                return;
            }
            
            // PROCESS ONLY IF HAS CHANGES ON THE ORIGINAL VALUE
            if (managementValue) {
                if (managementValue.length === 5) {
                  
                    // Simulate an AJAX call to fetch data based on management_no
                    fetch(`/material/kanban/temporary/fetch-kanban-details?management_no=${managementValue}`)
                        .then(response => response.json())
                        .then(data => {

                            if (!isArrayEmpty(data)) {
                                // ASSIGN ALL THE FETCH INFORMATION NEEDED ON RETURN OF AJAX CALL
                                manufacturerCode.value = data.material_manufacturer_code || '';
                                productCodeInput.value = data.product_code || '';
                                productName.value = data.product_name || '';
                                uniformNumber.value = data.uniform_number || '';
                                numberOfAccomodated.value = data.number_of_accomodated || '';
                                
                                // Trigger input event for numberOfAccomodated
                                const accomodatedEvent = new Event('input', { bubbles: true });
                                numberOfAccomodated.dispatchEvent(accomodatedEvent);
                            } else {
                                alert('この管理No.が存在しません')
                                clearFields();
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching kanban details:', error);
                            clearFields();
                        });
                }
                
            }
        });
    }

    if (productCodeInput) {
        // Listen for input changes on the product_code field
        productCodeInput.addEventListener('input', function () {
            const productValue = productCodeInput.value.trim();

            // process only when has changes
            if (productValue) {
                fetch(`/material/kanban/temporary/fetch-product-details?part_number=${productValue}`)
                    .then(response => response.json())
                    .then(data => {

                        // ASSIGN ALL THE FETCH INFORMATION NEEDED ON RETURN OF AJAX CALL
                        manufacturerCode.value = data.material_manufacturer_code || '';
                        productName.value = data.product_name || '';
                        uniformNumber.value = data.uniform_number || '';
                    })
                    .catch(error => {
                        console.error('Error fetching product details:', error);
                    });
            }
        });
    }
});

// Information on dynamic rows
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('tr[data-supply-material-order-id]');

    rows.forEach(row => {
        const managementInput = row.querySelector('[id^="management_no_"]');
        const productCodeInput = row.querySelector('[id^="product_code_"]');
        const numberOfAccomodated = row.querySelector('[id^="number_of_accomodated_"]');

        if (managementInput) {
            // Listen for input changes on the management_no field
            managementInput.addEventListener('input', function () {
                const managementValue = managementInput.value.trim();

                const rowId = managementInput.id.split('_').pop();


                // PROCESS ONLY IF HAS CHANGES ON THE ORIGINAL VALUE 
                if (managementValue) {
                    fetch(`/material/kanban/temporary/fetch-kanban-details?management_no=${managementValue}`)
                        .then(response => response.json())
                        .then(data => {

                            // ASSIGN ALL THE FETCH INFORMATION NEEDED ON RETURN OF AJAX CALL
                            var $manufacturerCode = document.getElementById(`material_manufacturer_code__${rowId}`),
                                $productCode = document.getElementById(`product_code_${rowId}`),
                                $productName = document.getElementById(`product_name_${rowId}`),
                                $uniformNumber = document.getElementById(`uniform_number_${rowId}`),
                                $numberOfAccomodated = document.getElementById(`number_of_accomodated_${rowId}`);

                            $manufacturerCode.value = data.material_manufacturer_code || '';
                            $productCode.value = data.product_code || $productCode.getAttribute('data-original-value');
                            $productName.value = data.product_name || $productName.getAttribute('data-original-value');
                            $uniformNumber.value = data.uniform_number || $uniformNumber.getAttribute('data-original-value');
                            $numberOfAccomodated.value = data.number_of_accomodated || '';

                            // Trigger input event for numberOfAccomodated
                            const accomodatedEvent = new Event('input', { bubbles: true });
                            numberOfAccomodated.dispatchEvent(accomodatedEvent);
                        })
                        .catch(error => {
                            console.error('Error fetching kanban details:', error);
                        });
                }
            });
        }

        if (productCodeInput) {
            // Listen for input changes on the product_code field
            productCodeInput.addEventListener('input', function () {
                const productValue = productCodeInput.value.trim();
                const rowId = productCodeInput.id.split('_')[1];  // Extract ID from input

                // PROCESS ONLY IF HAS CHANGES ON THE ORIGINAL VALUE 
                if (productValue) {
                    fetch(`/material/fetch-product-details?part_number=${productValue}`)
                        .then(response => response.json())
                        .then(data => {

                            // ASSIGN ALL THE FETCH INFORMATION NEEDED ON RETURN OF AJAX CALL
                            document.getElementById(`material_manufacturer_code__${rowId}`).value = data.material_manufacturer_code || '';
                            document.getElementById(`product_name_${rowId}`).value = data.product_name || '';
                            document.getElementById(`uniform_number_${rowId}`).value = data.uniform_number || '';
                        })
                        .catch(error => {
                            console.error('Error fetching product details:', error);
                        });
                }
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    window.enableInputs = function(button) {
        // Enable inputs in the same row except for specific ones
        const row = button.closest('tr');
        const inputs = row.querySelectorAll('input');

        inputs.forEach(input => {
            // Only enable inputs that don't match the excluded IDs
            if (!input.id.startsWith('material_manufacturer_code__') &&
                !input.id.startsWith('product_name_') &&
                !input.id.startsWith('uniform_number_') &&
                !input.id.startsWith('subTotal_')) {
                input.disabled = false;
            }
        });

        // Hide "EditDelete" div and show "UdpateUndo" div
        const editDeleteDiv = row.querySelector('#EditDelete');
        const updateUndoDiv = row.querySelector('#UdpateUndo');
        editDeleteDiv.style.display = 'none';
        updateUndoDiv.style.display = 'flex';
    }

    window.updateData = function(button) {
        // Get the row and kanban data ID from the closest <tr> element
        const row = button.closest('tr');
        const dataId = row.getAttribute('data-supply-material-order-id');


        // Get the input values within this row to prepare for the update
        const managementNoInput = row.querySelector('input[name^="management_no"]');
        const productCodeInput = row.querySelector('input[name^="product_code"]');
        const manufacturerCoded = row.querySelector('input[name^="material_manufacturer_code"]');
        const productNameInput = row.querySelector('input[name^="product_name"]');
        const uniformNumberInput = row.querySelector('input[name^="uniform_number"]');
        const instructionDateInput = row.querySelector('input[name^="instruction_date"]');
        const instructionNumberInput = row.querySelector('input[name^="instruction_no"]');
        const numberOfAccommodatedInput = row.querySelector('input[name^="number_of_accomodated"]');
        const instructionKanbanQuantityInput = row.querySelector('input[name^="instruction_kanban_quantity"]');

        row.querySelectorAll('.error_message').forEach(elem => {
            $(elem).html('');
        });

        const sessionKanbanData = window.sessionSupplyTempData;

        // Prepare the data object to send in the request
        const data = {
            temp_data_id: dataId,
            management_no: managementNoInput.value,
            material_manufacturer_code: manufacturerCoded.value,
            product_code: productCodeInput.value,
            product_name: productNameInput.value,
            uniform_number: uniformNumberInput.value,
            instruction_date: instructionDateInput.value,
            instruction_no: instructionNumberInput.value,
            number_of_accomodated: numberOfAccommodatedInput.value,
            instruction_kanban_quantity: instructionKanbanQuantityInput.value,
            arrival_quantity: instructionKanbanQuantityInput.value * numberOfAccommodatedInput.value, // Calculate the total arrival quantity
            session_data: sessionKanbanData
        };


        // Get the CSRF token from the meta tag to protect against CSRF attacks
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        $('#card').remove();

        if(!confirm('この内容で更新してもよろしいでしょうか？')) return;

        // Send an AJAX request to update the session data
        $.ajax({
            url: '/material/kanban/temporary/update-temporary-data', // The server URL for updating data
            method: 'POST', // Use POST method for sending data
            data: data, // Send the data object
            headers: {
                'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the headers for security
            },
            success: function(response) {
                // Remove old messages
                $("#success-message").remove();

                // Append new message
                $(".pageHeaderBox").after(`
                    <div id="card" style="background-color: #fff; margin-top: 20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <div style="text-align: left;">
                            <p style="font-size: 18px; color: #0d9c38;">
                                ${response.message}
                            </p>
                        </div>
                    </div>
                `);

                // Optionally hide the message after 3 seconds
                setTimeout(function() {
                    $("#success-message").fadeOut();
                }, 3000);
                
                // Disable the input fields in the same row after successful update
                const inputs = row.querySelectorAll('input');
                inputs.forEach(input => {
                    input.disabled = true;
                });

                // Hide the "UpdateUndo" div and show the "EditDelete" div
                const editDeleteDiv = row.querySelector('#EditDelete');
                const updateUndoDiv = row.querySelector('#UdpateUndo');
                editDeleteDiv.style.display = 'flex'; // Show the edit/delete controls
                updateUndoDiv.style.display = 'none'; // Hide the update/undo controls
            },
            error: function(xhr, status, error) {
                // Handle the error response if the update fails

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
                        if (input.length) {
                            // input.addClass('input-error');
                        }
            
                        if (errorContainer.length) {
                            errorContainer.html(`<div class="error_msg text-danger">${errors[field][0]}</div>`);
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
        // Confirm with the user if they really want to cancel the edit
        if (confirm('キャンセルしますか？')) {

            // Get the row index from the button's data attribute
            var rowIndex = button.getAttribute('data-row-index');

            // Select all input elements within the same row using the row index
            var rowInputs = document.querySelectorAll(`input[id*="_${rowIndex}"]`);

            // Loop through all inputs in the row and restore their original values
            rowInputs.forEach(function(input) {
                // Retrieve the original value stored in the 'data-original-value' attribute
                var originalValue = input.getAttribute('data-original-value');
                // Restore the input value to its original state
                input.value = originalValue;
            });

            // Disable all inputs in the same row to prevent further changes
            const row = button.closest('tr');
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                input.disabled = true; // Disable each input in the row
            });

            // Hide the "UpdateUndo" div and show the "EditDelete" div to change the interface state
            const editDeleteDiv = row.querySelector('#EditDelete');
            const updateUndoDiv = row.querySelector('#UdpateUndo');
            editDeleteDiv.style.display = 'flex'; // Show the "EditDelete" controls
            updateUndoDiv.style.display = 'none'; // Hide the "UpdateUndo" controls

            // Recalculate the subtotal for this row
            calculateRowSubtotal(rowIndex);
        }
    }

    window.storeData = function() {
        // Get the values of input fields from the form
        var managementNumber = $('#management_no').val();
        var productCode = $('#product_code').val();
        var materialManufacturerCode = $('#material_manufacturer_code').val();
        var productName = $('#product_name').val();
        var uniformNumber = $('#uniform_number').val();
        var instructionDate = $('#date_instruction').val();
        var instructionNumber = $('#number_instruction').val();

        var numberOfAccommodated = $('#number_of_accomodated_new').val();
        var instructionKanbanQuantity = $('#instruction_kanban_quantity_new').val();
        var numberOfAccomodated = $('#number_of_accomodated_new').val();
        var arrivalQuantity = $('#subTotal_new').val();

        // Prepare the data to send to the server
        var newData = {
            'management_no': managementNumber,
            'branch_number': null,
            'material_number': productCode,
            'product_code': productCode,
            'order_classification': 2,
            'material_manufacturer_code': materialManufacturerCode,
            'instruction_date': instructionDate,
            'instruction_no': instructionNumber,
            'lot': instructionNumber,
            'number_of_accomodated': numberOfAccommodated,
            'instruction_kanban_quantity': instructionKanbanQuantity,
            'arrival_quantity': arrivalQuantity,
            'where_to_use_department_code': null,
            'document_issue_date': null,

            // Additional parameters
            'product_name': productName,
            'uniform_number': uniformNumber
        };

        $('table tfoot .error_message').each(elem => {
            $(elem).html('');
        });

        // Get CSRF token for security
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Perform an AJAX request to store the data on the server
        $.ajax({
            url: '/material/kanban/temporary/save-temporary-data', // Endpoint to send the data
            method: 'POST',
            data: newData, // Data to be sent
            headers: {
                'X-CSRF-TOKEN': csrfToken // CSRF token to protect against cross-site request forgery
            },
            success: function(response) {
                // Display a success message after data is stored

                // Clear modal search value from session storage
                sessionStorage.removeItem('modal-search-value');

                location.reload();
            },
            error: function(xhr, status, error) {
                // Display an error message if the request fails
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
            
                    // Clear previous errors
                    $('.input-error').removeClass('input-error');
                    $('.error_msg').remove();
            
                    // Display errors
                    Object.keys(errors).forEach(function (field) {
                        const fieldName = field.replace(/\.\d+$/, ''); // handles array fields
            
                        const input = $(`table tfoot [id="${fieldName}"]`);
                        const errorContainer = $(`table tfoot #${fieldName}_error`);
                        if (input.length) {
                            // input.addClass('input-error');
                        }
            
                        if (errorContainer.length) {
                            errorContainer.html(`<div class="error_msg text-danger">${errors[field][0]}</div>`);
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
        // Get form field values
        var managementNumber = $('#management_no').val();
        var productCode = $('#product_code').val();
        var materialManufacturerCode = $('#material_manufacturer_code').val();
        var productName = $('#product_name').val();
        var uniformNumber = $('#uniform_number').val();
        var instructionDate = $('#date_instruction').val();
        var instructionNumber = $('#number_instruction').val();
        var numberOfAccommodated = $('#number_of_accomodated').val();
        var instructionKanbanQuantity = $('#instruction_kanban_quantity').val();
        var arrivalQuantity = $('#subTotal').val();

        // Get the session data from the server-side (using Blade to output session data in JavaScript)
        const sessionKanbanData = window.sessionSupplyTempData;

        // Prepare data for the AJAX request
        const data = {
            session_data: sessionKanbanData  // Sending the session data to the server
        };

        $('.error_message').each(elem => {
            $(elem).html('');
        });
        bulkSavingButton();

        // Send AJAX request to the server to save the data
        $.ajax({
            url: '/material/kanban/temporary',  // The server endpoint to handle the data
            method: 'POST',  // POST request to store data
            data: data,  // Data to be sent
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // CSRF token for security
            },
            success: function(response) {
                
                localStorage.setItem("successMessage", "臨時かんばんの登録が完了いたしました。");
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle the error response
                alert('An error occurred while storing the data.');  // Error alert
            }
        });
    }

    window.confirmDelete = function(button) {
        if (confirm('内示情報を削除します、よろしいでしょうか？')) {
            const row = button.closest('tr');
            const dataId = row.getAttribute('data-supply-material-order-id');

          

            fetch(`/material/kanban/temporary/remove-temporary-data/${dataId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Include the CSRF token in the request headers
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error deleting data');
                    }

                    $("#success-message").remove();
                    
                    // Append new message
                    $(".pageHeaderBox").after(`
                        <div id="card" style="background-color: #fff; margin-top: 20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                            <div style="text-align: left;">
                                <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                                    の削除が完了いたしました。
                                </p>
                            </div>
                        </div>
                    `);

                    // Optionally hide the message after 3 seconds
                    setTimeout(function() {
                        $("#success-message").fadeOut();
                    }, 3000);

                  

                    return response.json();
                })
                .then(responseData => {
                    
                    // Remove the table row from the DOM
                    row.remove();
                    bulkSavingButton();
                })
                .catch(error => {
                    // Handle the error
                    alert('Error deleting data: ' + error);
                });
        }
    }

    window.clearData = function(button) {
        // Get the closest 'tr' element to the clicked button (the row containing the button)
        var tr = button.closest('tr');
        
        // Get all 'input' elements within the row
        var inputs = tr.getElementsByTagName('input');

        // Loop through each input element in the row
        for (var i = 0; i < inputs.length; i++) {
            // Skip clearing the input with id="date_instruction"
            if (inputs[i].id === 'date_instruction') {
                continue;  // Skip this input and move to the next one
            }
            
            // Clear the value of the other input fields
            inputs[i].value = '';
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    let successMessage = localStorage.getItem("successMessage");
    if (successMessage) {
        $(".pageHeaderBox").after(`
            <div id="card" style="background-color: #fff; margin-top: 20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <div style="text-align: left;">
                    <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                        ${successMessage}
                    </p>
                </div>
            </div>
        `);
        localStorage.removeItem("successMessage"); // Clear it after displaying
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