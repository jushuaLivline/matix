$(function () {
  let debounceTimer; // Store the timer
  const sessionsessionDefectProcessData = document.getElementById("sessionDefectProcessData") || {};
  const sessionData = JSON.parse(sessionsessionDefectProcessData.dataset.info);
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
  
  document.querySelectorAll('input[name="quantity"]').forEach(input => {
    input.addEventListener('input', function() {
      clearTimeout(debounceTimer); // Clear previous timer
  
      debounceTimer = setTimeout(() => { // Wait 500ms before executing
        const row = this.closest('tr');
        if (!row) return;
        
        const getProductCode = row.querySelector('input[name="product_code"]');
        const processingUnitPrice = row.querySelector('input[name="processing_unit_price"]');
        const quantity = this.value;
        const subTotalInput = row.querySelector('input[name="subTotal"]');
        
        if ( getProductCode.value.trim() == ''){
          processingUnitPrice.value = 0;
          subTotalInput.value = 0;
          return;
        } 
  
        const productCode = getProductCode.value.trim();
       
     

        if (!productCode || !quantity || !processingUnitPrice || !subTotalInput) return;

        console.log("Fetching data for:", productCode, "Quantity:", quantity, );
  
        // Perform AJAX request here (only after the user stops typing)
        $.ajax({
          url: `/outsource/defect/process/get-product-unit-price/${productCode}`, // Your route
          method: 'GET',
          success: function(response) {
             // compute subtotal
          
            processingUnitPrice.value = response.unit_price;
            subTotalInput.value = quantity * response.unit_price;
            // Update UI with the retrieved data
            console.log("Product Unit Price:", response);
          },
          error: function() {
            console.error("Failed to fetch product unit price");
          }
        });
      }, 500); // Adjust delay as needed
    });
  });


  function enableInputs(button) {
    // Enable inputs in the same row
    const row = button.closest('tr');
    const inputs = row.querySelectorAll('input');
    const selects = row.querySelectorAll('select');
    const modalButtons = row.querySelectorAll('[data-modal-button]')
    inputs.forEach(input => {
        input.disabled = false;
    });

    selects.forEach(selects => {
      selects.disabled = false;
    });


    modalButtons.forEach(button => {
      button.disabled = false;
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
    const dataId = row.getAttribute('data-id');

    // Get the input values to update
    const processCodeInput = document.querySelector('input[name="process_code"]');
    const processNameInput = document.querySelector('input[name="process_name"]');
    const productCodeInput = row.querySelector('input[name="product_code"]');
    const productNameInput = row.querySelector('input[name^="product_name"]');
    const quantityInput = row.querySelector('input[name="quantity"]');
    const disposalDateInput = document.querySelector('input[name="disposal_date"]');
    const processingUnitPriceInput = row.querySelector('input[name="processing_unit_price"]');
    const subTotalInput = row.querySelector('input[name="subTotal"]');
    const slipNoInput = row.querySelector('input[name="slip_no"]');


    const inputs = [
      processCodeInput,
      processNameInput,
      productCodeInput,
      productNameInput,
      quantityInput,
      disposalDateInput,
      processingUnitPriceInput,
      subTotalInput,
      slipNoInput,
    ].filter(input => input !== null); // Remove null values;
    

    // Prepare the data to send in the request 
    const data = {
        id: dataId,
        process_code: processCodeInput.value,
        process_name: processNameInput.value,
        product_code: productCodeInput.value,
        product_name: productNameInput.value,
        quantity: quantityInput.value,
        disposal_date: disposalDateInput.value,
        processing_unit_price: processingUnitPriceInput.value,
        subTotal: subTotalInput.value,
        slip_no: slipNoInput.value,
        
    };
    if(confirm('加工不良実績を更新します、よろしいでしょうか？')){
      // Get the CSRF token from the meta tag
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      // Send an AJAX request to update the session data
      $.ajax({
          url: '/outsource/defect/process/update_session/' + dataId,
          method: 'PUT',
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
  
              // Hide "UpdateUndo" div and show "EditDelete" div
              const editDeleteDiv = row.querySelector('#EditDelete');
              const updateUndoDiv = row.querySelector('#UdpateUndo');
              editDeleteDiv.style.display = 'flex';
              updateUndoDiv.style.display = 'none';

              const modalButtons = row.querySelectorAll('[data-modal-button]')
              modalButtons.forEach(button => {
                button.disabled = true;
              });
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
                  const disposalDate = $('#disposal_date_error');
                  const processCode = $('#process_code_error');
                  if (input.length) {
                      // input.addClass('input-error');
                  }

                  if (errorContainer.length) {
                      errorContainer.html(`<div class="error_msg text-danger">${errors[field][0]}</div>`);
                  }
                  if (errors['disposal_date']) {
                    disposalDate.html(`<div class="error_msg text-danger">${errors['disposal_date'][0]}</div>`);
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

  }
  function cancelEdit(button) {
    if (confirm('キャンセルしますか？')) {
        // Disable inputs in the same row
        const row = button.closest('tr');
        const inputs = row.querySelectorAll('input');
        const select = row.querySelectorAll('select');
        inputs.forEach(input => {
            input.disabled = true;
            input.value = input.getAttribute('data-old-value');
        });

        select.forEach(select => {
          select.disabled = true;
          const oldValue = select.getAttribute("data-old-value"); // Get previous value
          // Set the option with the matching value as selected
          if (oldValue) {
            select.querySelectorAll("option").forEach(option => {
                option.selected = option.value === oldValue;
            });
        }
        });
        // Hide "UdpateUndo" div and show "EditDelete" div
        const editDeleteDiv = row.querySelector('#EditDelete');
        const updateUndoDiv = row.querySelector('#UdpateUndo');
        editDeleteDiv.style.display = 'flex';
        updateUndoDiv.style.display = 'none';
        document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })
    }
  }
  function storeData(button) {

    var processCode = $('#process_code').val();
    var processName = $('#process_name').val();
    var productCode = $('#product_code').val();
    var productName = $('#product_name').val();
    var quantity = $('#quantity').val();
    var disposalDate = $('#disposal_date').val();
    var processingUnitPrice = $('#processing_unit_price').val();
    var subTotal = $('#subTotal').val();
    var slipNo = $('#slip_no').val();
    const row = button.closest('tr');
    // Get the supplier code input element
    // const processCodeInput = document.getElementById('supplier_code');
    // const supplierCode = processCodeInput.value;


    document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

    var newData = {
        'process_code': processCode,
        'process_name': processName,
        'product_code': productCode,
        'product_name': productName,
        'quantity': quantity,
        'disposal_date': disposalDate,
        'processing_unit_price': processingUnitPrice,
        'subTotal': subTotal,
        'slip_no': slipNo,

        //additional parameters
        // 'supplier_code' : supplierCode,
    };

    if(confirm('加工不良実績を保存します、よろしいでしょうか？')){
      // Get the CSRF token from the meta tag
      var csrfToken = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
          url: '/outsource/defect/process/store_session',
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
                    const disposalDate = $('#disposal_date_error');
                    const processCode = $('#process_code_error');
                    if (input.length) {
                        // input.addClass('input-error');
                    }

                    if (errorContainer.length) {
                        errorContainer.html(`<div class="error_msg text-danger">${errors[field][0]}</div>`);
                    }
                    if (errors['disposal_date']) {
                      disposalDate.html(`<div class="error_msg text-danger">${errors['disposal_date'][0]}</div>`);
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
    };

  }
  function bulkSavingData(button) {

    // Get the session data from the server-side
    const sessionsessionDefectProcessData = document.getElementById("sessionDefectProcessData") || {};
    const sessionData = JSON.parse(sessionsessionDefectProcessData.dataset.info);

    // Prepare the data to send in the request
    const data = {
        session_data: sessionData
    };

    document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

    // Send an AJAX request to the server to save the data
    $.ajax({
        url: '/outsource/defect/process',
        method: 'POST',
        data:  JSON.stringify(data),
        contentType: 'application/json',
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
          $('#successInputs').delay(300).fadeIn(100, function() {
              $(this).delay(300);
          });

          // scroll top
          $('html, body').animate({ scrollTop: 0 }, 'slow');

          $('#process_code, #process_name').val('');
          $('table tbody tr').delay(300).empty();
        },
        error: function(xhr, status, error) {
            // Handle the error response
            alert('An error occurred while storing the data.');
        }
    });
  }

  function confirmDelete(button) {

    if (confirm('加工不良実績を削除します、よろしいでしょうか？')) {
        const row = button.closest('tr');
        const dataId = row.getAttribute('data-id');
        // Get the CSRF token from the meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        fetch(`/outsource/defect/process/cancel_session/${dataId}`, {
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

          document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })
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
        { selector: "#process_code", value: $("#process_code").val() },
        { selector: "#disposal_date", value: $("#disposal_date").val() },
        { selector: "#quantity", value: $("#quantity").val() },
        { selector: "#slip_no", value: $("#slip_no").val() },
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
