$(function(){
    window.enableInputs = function(button) {

        // Enable inputs in the same row
        const row = button.closest('tr');
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            input.disabled = false;
        });
    
        // Enable the buttons in the same row
        const buttons = row.querySelectorAll('.btnSubmitCustom');
        buttons.forEach(btn => {
            btn.disabled = false;
        });
    
        // Hide "EditDelete" div and show "UdpateUndo" div
        const editDeleteDiv = row.querySelector('#EditDelete');
        const updateUndoDiv = row.querySelector('#UdpateUndo');
        editDeleteDiv.style.display = 'none';
        updateUndoDiv.style.display = 'flex';
    }
    
    window.updateData = function(button) {
        // Get the row and supply_material_order_id
        const row = button.closest('tr');
        const supplyMaterialArrivalId = row.getAttribute('data-supply-material-arrival-id');
    
        // // Get the input values to update from the current row in the table results
        const arrivalDateInput = row.querySelector('input[name^="arrival_date"]');
        const flightNumberInput = row.querySelector('input[name^="flight_no"]');
        const deliveryNumberInput = row.querySelector('input[name^="delivery_no"]');
        const materialNumberInput = row.querySelector('input[name^="product_code"]');
        const productNumberInput = row.querySelector('input[name^="part_number"]');
        const arrivalQuantityInput = row.querySelector('input[name^="arrival_quantity"]');
        
        // Valitate the form
        formValidaton(row);
        const isValid = $("#orderArrivalForm").valid();
        if (!isValid) {
            return;
        }

        // Prepare the data to send in the request
        const data = {
            columns: {
                id: supplyMaterialArrivalId,
                arrival_day: arrivalDateInput.value,
                flight_no: flightNumberInput.value,
                delivery_no: deliveryNumberInput.value,
                material_no: materialNumberInput.value,
                product_number: productNumberInput.value,
                arrival_quantity: arrivalQuantityInput.value,
            }
        };
        
      
        document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

        // Send an AJAX request to the API endpoint
        fetch('/api/supply-material-arival/' + supplyMaterialArrivalId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(responseData => {
                // Handle the response data
                alert('入荷実績情報の更新が完了しました');
    
                // Disable inputs in the same row
                const inputs = row.querySelectorAll('input');
                inputs.forEach(input => {
                    input.disabled = true;
                });
    
                location.reload();
                // Hide "UdpateUndo" div and show "EditDelete" div
                const editDeleteDiv = row.querySelector('#EditDelete');
                const updateUndoDiv = row.querySelector('#UdpateUndo');
                editDeleteDiv.style.display = 'flex';
                updateUndoDiv.style.display = 'none';
            })
            .catch(error => {
                console.error(error);
                // Handle error if needed
                alert('登録に必要ないくつかの情報が入力されていません！');
            });
    }
    
    window.cancelEdit = function(button) {
        if (confirm('キャンセルしますか？')) {
            // Get the row index from the button's data attribute
            var rowIndex = button.getAttribute('data-row-index');
    
            // Select all inputs within the same row
            var rowInputs = document.querySelectorAll(`input[id*="_${rowIndex}"]`);
    
            // Restore the original values from the data-original-value attribute
            rowInputs.forEach(function (input) {
                var originalValue = input.getAttribute('data-original-value');
                input.value = originalValue; // Set the value back to the original
            });
    
            // Disable inputs in the same row
            const row = button.closest('tr');
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                input.disabled = true;
            });
    
            const enableButtons = (enabled) => {
                // get all buttons in the current row that has 
                // class btnSubmitCustom and disable it
                const buttons = row.querySelectorAll('.btnSubmitCustom');
                buttons.forEach(btn => {
                    btn.disabled = !enabled;
                });
            };
            
            document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

            // Call the function to Disable buttons
            enableButtons(false);
    
            // Hide "UdpateUndo" div and show "EditDelete" div
            const editDeleteDiv = row.querySelector('#EditDelete');
            const updateUndoDiv = row.querySelector('#UdpateUndo');
            editDeleteDiv.style.display = 'flex';
            updateUndoDiv.style.display = 'none';
        }
    }
    
    window.confirmDelete = function(button) {
        if (confirm('選択された行の入荷実績情報を削除します、よろしいでしょうか？')) {
            const row = $(button).closest('tr');
            const supplyMaterialArrivalId = row.data('supply-material-arrival-id');

            document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

            fetch('/api/supply-material-arival/' + supplyMaterialArrivalId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            })
                .then(response => response.json())
                .then(responseData => {
                    // Handle the response data
                    //alert('支給材情報は正常に削除されました');
                    const flashmessage = document.getElementById("flash-message");
                    flashmessage.classList.add("show");
    
                    setTimeout(() => {
                        flashmessage.classList.remove("show");
                    }, 3000);
                    row.remove();
                })
                .catch(error => {
                    // Handle the error
                    alert('Error deleting SupplyMaterialArrival: ' + error);
                });
        }
    }
    
    window.resetForm = function() {
        let resetButton = document.getElementById("resetForm");
        let form = document.getElementById("form_request");
    
        if (resetButton && form) {
            resetButton.addEventListener("click", function () {
                form.reset();
                form.querySelectorAll("input[type='text']").forEach(input => {
                    input.value = "";
                });
            });
        } else {
            console.error("resetButton or form_request not found!");
        }
    }
    
    function setRequestDefaultDateOfArrival() {
        let input = document.getElementById("arrival_day_from");
    
        if (input && !input.value) {
            let today = new Date();
            let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            let formattedDate = firstDay.getFullYear().toString() +
                                String(firstDay.getMonth() + 1).padStart(2, '0') +
                                String(firstDay.getDate()).padStart(2, '0');
    
            input.value = formattedDate;
        }
    
        let input2 = document.getElementById("arrival_day_to");
    
        if (input2 && !input2.value) {
            let today = new Date();
            let lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
            let formattedDate = lastDay.getFullYear().toString() +
                                String(lastDay.getMonth() + 1).padStart(2, '0') +
                                String(lastDay.getDate()).padStart(2, '0');
    
            input2.value = formattedDate;
        }
    }
    resetForm();
})


function formValidaton(row) {
    document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })
  
    $.validator.addMethod(
      "dateFormat",
      function (value, element) {
        if (this.optional(element)) return true;
  
        // Check if it's exactly 8 digits
        const regex = /^\d{8}$/;
        if (!regex.test(value)) return false;
  
        const year = parseInt(value.substring(0, 4), 10);
        const month = parseInt(value.substring(4, 6), 10);
        const day = parseInt(value.substring(6, 8), 10);
  
        // Check if month is valid
        if (month < 1 || month > 12) return false;
  
        // Handle leap year
        const isLeapYear =
          (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
        const daysInMonth = [
          31,
          isLeapYear ? 29 : 28,
          31,
          30,
          31,
          30,
          31,
          31,
          30,
          31,
          30,
          31,
        ];
  
        // Check if day is valid
        return day >= 1 && day <= daysInMonth[month - 1];
      },
      "正しい形式で入力してください"
    );
  
    $("#orderArrivalForm").validate({
      rules: {
        arrival_date: {
          required: true,
          digits: true,
          maxlength: 8,
          dateFormat: true,
        },
        flight_no: {
          required: true,
          digits: true,
          maxlength: 2,
        },
        arrival_quantity: {
            required: true,
            digits: true
        },
        delivery_no: {
            required: true,
            digits: true,
            maxlength: 20,
        },
        product_code: {
            required: true
        },
      },
      messages: {
        arrival_date: {
          required: "入荷日は必須です。",
          digits: "入荷日は整数で入力してください",
          maxlength: "入荷日8max文字以内で入力してください。",
          dateFormat: "正しい形式で入力してください",
        },
        flight_no: {
            required: "便No.は必須です。",
            digits: "便No.は整数で入力してください",
            maxlength: "便No.2max文字以内で入力してください",
          },
          arrival_quantity: {
            required: "入荷数は必須です。",
            digits: "入荷数は整数で入力してください",
          },
          delivery_no: {
            required: "納入Noは必須です。",
            digits: "納入Noは整数で入力してください",
            maxlength: "納入No20max文字以内で入力してください",
          },
          product_code: {
            required: "材料品番は必須です。",
          },
      },
      errorPlacement: function (error, element) {
        const fieldName = element.attr("id").replace(/_\d+$/, ""); // strip _559020
        const errorContainer = $(row).find(`#${fieldName}_error`);
        if (errorContainer.length) {
          errorContainer.html(
            `<div class="error_msg text-danger">${error.text()}</div>`
          );
        } else {
          error.insertAfter(element);
        }
      },
    });
  }