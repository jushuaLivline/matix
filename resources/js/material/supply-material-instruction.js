$(function () {
  const sessionSupplyInstructionData = document.getElementById("supplyInstructionData") || {};
  const sessionData = JSON.parse(sessionSupplyInstructionData.dataset.info);
  const buttonActions = {
    "data-clear-button": clearData,
    "data-edit-button": enableInputs,
    "data-update-button": updateData,
    "data-insert-button": storeData,
    "data-bulk-save-button": bulkSavingData,
    "data-delete-button": confirmDelete,
    "data-cancel-button": cancelEdit
  };
  
  // Loop through the button actions and add event listeners for all matching elements
  Object.entries(buttonActions).forEach(([selector, handler]) => {
    document.querySelectorAll(`[${selector}]`).forEach(button => {
      button.addEventListener("click", function () {
        handler(this);
      });
      
      if(selector == 'data-bulk-save-button' && Object.values(sessionData).length > 0){
        button.classList.remove('btn-disabled');
        button.removeAttribute('disabled');
      }
      
    });
  });
    

  function enableInputs(button) {
    // Enable inputs in the same row
    const row = button.closest('tr');
    const inputs = row.querySelectorAll('input');
    inputs.forEach(input => {
        input.disabled = false;
    });

    // Hide "EditDelete" div and show "UdpateUndo" div
    const editDeleteDiv = row.querySelector('#EditDelete');
    const updateUndoDiv = row.querySelector('#UdpateUndo');
    editDeleteDiv.style.display = 'none';
    updateUndoDiv.style.display = 'flex';
  }
  function clearData(button) {
        var tr = button.closest('tr');
        var inputs = tr.getElementsByTagName('input');
        
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = '';
        }
  }
  function updateData(button) {
    // Get the row and kanban data ID
    const row = button.closest('tr');
    const dataId = row.getAttribute('data-instruction-id');

    // Get the input values to update
    const productCodeInput = row.querySelector('input[name="product_code"]');
    const productNameInput = row.querySelector('input[name="product_name"]');
    const instructionDateInput = row.querySelector('input[name="instruction_date"]');
    const instructionNumberInput = row.querySelector('input[name="instruction_no"]');
    const instructionKanbanQuantityInput = row.querySelector('input[name="instruction_kanban_quantity"]');

    const inputs = [
      productCodeInput,
      productNameInput,
      instructionDateInput,
      instructionNumberInput,
      instructionKanbanQuantityInput
    ];
    
    let hasError = false;
    $("#warningInputs").hide();
    inputs.forEach(input => {
      if (input && input.value.trim() === "") {
        input.classList.add('input-error');
        hasError = true;
        $("#warningInputs").show();
      }else{
        input.classList.remove('input-error'); // Remove error class if input is valid
      }
    });
    
    if (hasError) return; // Stop execution if any input is empty


    // Get the supplier code input element
    const supplierCodeInput = document.getElementById('supplier_code');

    // Prepare the data to send in the request 
    const data = {
        supplier_code: supplierCodeInput.value,
        temp_data_id: dataId,
        material_number: productCodeInput.value,
        product_name: productNameInput.value,
        instruction_date: instructionDateInput.value,
        instruction_no: instructionNumberInput.value,
        instruction_kanban_quantity: instructionKanbanQuantityInput.value,
    };

    if(confirm('支給材端数指示情報を更新します、よろしいでしょうか？')){
      // Get the CSRF token from the meta tag
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      // Send an AJAX request to update the session data
      $.ajax({
          url: '/materials/instruction-temp-update-data',
          method: 'POST',
          data: data,
          headers: {
              'X-CSRF-TOKEN': csrfToken
          },
          success: function(response) {
              // Handle the success response
              location.reload();
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
              alert('An error occurred while updating the data.');
          }
      });
    }

  }
  function cancelEdit(button) {
    if (confirm('キャンセルしますか？')) {
        // Disable inputs in the same row
        const row = button.closest('tr');
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            input.disabled = true;
        });

        // Hide "UdpateUndo" div and show "EditDelete" div
        const editDeleteDiv = row.querySelector('#EditDelete');
        const updateUndoDiv = row.querySelector('#UdpateUndo');
        editDeleteDiv.style.display = 'flex';
        updateUndoDiv.style.display = 'none';
    }
  }
  function storeData() {
    if(!validateInputs()) return;

    var productCode = $('#product_code').val();
    var productName = $('#product_name').val();
    var instructionDate = $('#date_instruction').val();
    var instructionNumber = $('#number_instruction').val();
    var instructionKanbanQuantity = $('#instruction_kanban_quantity').val();
    // Get the supplier code input element
    const supplierCodeInput = document.getElementById('supplier_code');
    // Get the supplier code input value
    const supplierCode = supplierCodeInput.value;

   
    var newData = {
        'material_number': productCode,
        'order_classification': 3,
        'instruction_date': instructionDate,
        'instruction_no': instructionNumber,
        'instruction_kanban_quantity': instructionKanbanQuantity,

        //additional parameters
        'product_name': productName,
        'supplier_code_request' : supplierCode,
    };

    if(confirm('支給材端数指示情報を追加します、よろしいでしょうか？')){
      // Get the CSRF token from the meta tag
      var csrfToken = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
          url: '/materials/instruction-temp-data',
          method: 'POST',
          data: newData,
          headers: {
              'X-CSRF-TOKEN': csrfToken
          },
          success: function(response) {
              // Show the success message with a delay
              $('#successInputs').delay(500).fadeIn(100, function() {
                  $(this).delay(1000);
              });
  
              // Reload the page after a delay
              setTimeout(function() {
                  location.reload();
              }, 1500);
          },
          error: function(xhr, status, error) {
              alert('An error occurred while storing the data.');
          }
      });
    };

  }
  function bulkSavingData(button) {

    // if(!validateInputs()) return;

    // Get the supplier code input element
    const supplierCodeInput = document.getElementById('supplier_code');

    // Get the supplier code input value
    const supplierCode = supplierCodeInput.value;

    // Get the session data from the server-side
    const sessionSupplyInstructionData = document.getElementById("supplyInstructionData") || {};
    const sessionData = JSON.parse(sessionSupplyInstructionData.dataset.info);

    // Prepare the data to send in the request
    const data = {
        supplier_code: supplierCode,
        session_data: sessionData
    };

    // Send an AJAX request to the server to save the data
    $.ajax({
        url: '/materials/instruction-store-data',
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {

          window.onbeforeunload = function() {
              sessionStorage.removeItem('previousDate');
              sessionStorage.removeItem('latestNumber');
              sessionStorage.removeItem('selectedDate');
              sessionStorage.removeItem('selectedCount');
          };

          // Show the success message with a delay
          $('#successInputs').delay(500).fadeIn(100, function() {
              $(this).delay(1000);
          });

          // Reload the page after a delay
          setTimeout(function() {
              location.reload();
          }, 1500);
        },
        error: function(xhr, status, error) {
            // Handle the error response
            alert('An error occurred while storing the data.');
        }
    });
  }

  function confirmDelete(button) {

    if (confirm('支給材端数指示情報を削除します、よろしいでしょうか？')) {
        const row = button.closest('tr');
        const dataId = row.getAttribute('data-instruction-id');
        // Get the CSRF token from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        fetch(`/materials/instruction-temp-delete-data/${dataId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
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
          alert('支給材情報は正常に削除されました');

          // Remove the table row from the DOM
          row.remove();

          const getTableRow = document.querySelectorAll(".table-bordered tbody tr");
          const insertButton = document.querySelector("[data-bulk-save-button]");
          if(getTableRow.length < 2){
            insertButton.classList.add('btn-disabled');
            insertButton.setAttribute('disabled', true);
          }
        })
        .catch(error => {
            // Handle the error
            alert('Error deleting data: ' + error);
        });
    }
  }

  function validateInputs() {
    const fields = [
        { selector: "#product_code", value: $("#product_code").val() },
        { selector: "#date_instruction", value: $("#date_instruction").val() },
        { selector: "#number_instruction", value: $("#number_instruction").val() },
        { selector: "#instruction_kanban_quantity", value: $("#instruction_kanban_quantity").val() },
        { selector: "#supplier_code", value: $("#supplier_code").val() },
    ];

    let hasError = false;
    $("#warningInputs").hide();

    // First, remove all previous error classes
    fields.forEach(field => $(field.selector).removeClass("input-error"));

    // Then, validate inputs and add error class if needed
    fields.forEach(field => {
        if (!field.value) {
          $(field.selector).addClass("input-error");
          hasError = true;
        }
    });

    if (hasError) {
        $("#warningInputs").show();
        return false; // Stop execution if inputs are missing
    }
    return true; // Return true if validation passes
  }
  
});
