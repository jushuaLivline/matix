$(function () {
  const sessionSupplyInstructionData =
    document.getElementById("supplyInstructionData") || {};
  const sessionData = JSON.parse(sessionSupplyInstructionData.dataset.info);
  const buttonActions = {
    "data-clear-button": clearData,
    "data-edit-button": enableInputs,
    "data-update-button": updateData,
    "data-insert-button": storeData,
    "data-bulk-save-button": bulkSavingData,
    "data-delete-button": confirmDelete,
    "data-cancel-button": cancelEdit,
  };

  // Loop through the button actions and add event listeners for all matching elements
  Object.entries(buttonActions).forEach(([selector, handler]) => {
    document.querySelectorAll(`[${selector}]`).forEach((button) => {
      button.addEventListener("click", function () {
        handler(this);
      });

      if (
        selector == "data-bulk-save-button" &&
        Object.values(sessionData).length > 0
      ) {
        button.classList.remove("btn-disabled");
        button.removeAttribute("disabled");
      }
    });
  });

  function enableInputs(button) {
    // Enable inputs in the same row
    const row = button.closest("tr");
    const inputs = row.querySelectorAll("input");
    const modalButtons = row.querySelectorAll("[data-modal-button]");
    inputs.forEach((input) => {
      input.disabled = false;
    });

    modalButtons.forEach((button) => {
      button.disabled = false;
    });

    // Hide "EditDelete" div and show "UdpateUndo" div
    const editDeleteDiv = row.querySelector("#EditDelete");
    const updateUndoDiv = row.querySelector("#UdpateUndo");
    editDeleteDiv.style.display = "none";
    updateUndoDiv.style.display = "flex";
  }
  function clearData(button) {
    var tr = button.closest("tr");
    var inputs = tr.getElementsByTagName("input");

    for (var i = 0; i < inputs.length; i++) {
      inputs[i].value = "";
    }
  }
  function updateData(button) {
    // Get the row and kanban data ID
    const row = button.closest("tr");
    const dataId = row.getAttribute("data-instruction-id");

    // Get the input values to update
    const productCodeInput = row.querySelector('input[name="product_code"]');
    const productNameInput = row.querySelector('input[name="product_name"]');
    const instructionDateInput = row.querySelector(
      'input[name="instruction_date"]'
    );
    const instructionNumberInput = row.querySelector(
      'input[name="instruction_no"]'
    );
    const instructionKanbanQuantityInput = row.querySelector(
      'input[name="instruction_kanban_quantity"]'
    );

    const inputs = [
      productCodeInput,
      productNameInput,
      instructionDateInput,
      instructionNumberInput,
      instructionKanbanQuantityInput,
    ];

    $('.error_message').each(elem => {
      $(elem).html('');
    });

    // Get the supplier code input element
    const supplierCodeInput = document.getElementById("supplier_code");

    // Prepare the data to send in the request
    const data = {
      supplier_code: supplierCodeInput.value,
      temp_data_id: dataId,
      material_number: productCodeInput.value,
      product_code: productCodeInput.value,
      product_name: productNameInput.value,
      instruction_date: instructionDateInput.value,
      instruction_no: instructionNumberInput.value,
      instruction_kanban_quantity: instructionKanbanQuantityInput.value,
    };

    if (
      confirm("支給材端数指示情報を更新します、よろしいでしょうか？")
    ) {
      // Get the CSRF token from the meta tag
      const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
      // Send an AJAX request to update the session data
      $.ajax({
        url: "/material/fraction/" + dataId,
        method: "PUT",
        data: data,
        headers: {
          "X-CSRF-TOKEN": csrfToken,
        },
        success: function (response) {
          // Reload the page after a delay
          reloadSection('.material-fraction-table');
         
           // Show the success message with a delay
           $("#successUpdate")
           .delay(500)
           .fadeIn(100, function () {
             $(this).delay(1000);
           });

          // Disable inputs in the same row
          const inputs = row.querySelectorAll("input");
          inputs.forEach((input) => {
            input.disabled = true;
          });

          // Hide "UpdateUndo" div and show "EditDelete" div
          const editDeleteDiv = row.querySelector("#EditDelete");
          const updateUndoDiv = row.querySelector("#UdpateUndo");
          editDeleteDiv.style.display = "flex";
          updateUndoDiv.style.display = "none";

          const modalButtons = row.querySelectorAll("[data-modal-button]");
          modalButtons.forEach((button) => {
            button.disabled = true;
          });
        },
        error: function (xhr, status, error) {
          // Handle the error response
          if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors;

            // Clear previous errors
            $(".input-error").removeClass("input-error");
            $(".error_msg").remove();

            // Display errors
            Object.keys(errors).forEach(function (field) {
              const fieldName = field.replace(/\.\d+$/, ""); // handles array fields

              const input = $(row).find(`[id="${fieldName}"]`);
              const errorContainer = $(row).find(` #${fieldName}_error`);
              const supplierCode = $("#supplier_code_error");
              if (input.length) {
                // input.addClass('input-error');
              }
              if (errors["supplier_code"]) {
                supplierCode.html(
                  `<div class="error_msg text-danger">${errors["supplier_code"][0]}</div>`
                );
              }
              if (errorContainer.length) {
                errorContainer.html(
                  `<div class="error_msg text-danger">${errors[field][0]}</div>`
                );
              }
              // Scroll to the page
              $('html, body').animate({
                scrollTop: 0
              }, 300);
            });
          } else {
            alert("サーバーエラーが発生しました。");
          }
        },
      });
    }
  }
  function cancelEdit(button) {
    if (confirm("キャンセルしますか？")) {
      // Disable inputs in the same row
      const row = button.closest("tr");
      const inputs = row.querySelectorAll("input");
      inputs.forEach((input) => {
        input.disabled = true;
        input.value = input.getAttribute("data-old-value");
      });

      // Hide "UdpateUndo" div and show "EditDelete" div
      const editDeleteDiv = row.querySelector("#EditDelete");
      const updateUndoDiv = row.querySelector("#UdpateUndo");
      editDeleteDiv.style.display = "flex";
      updateUndoDiv.style.display = "none";
    }
  }
  function storeData() {
    // if(!validateInputs()) return;

    var productCode = $("#product_code").val();
    var productName = $("#product_name").val();
    var instructionDate = $("#date_instruction").val();
    var instructionNumber = $("#number_instruction").val();
    var instructionKanbanQuantity = $("#instruction_kanban_quantity").val();
    // Get the supplier code input element
    const supplierCodeInput = document.getElementById("supplier_code");
    // Get the supplier code input value
    const supplierCode = supplierCodeInput.value;

    var newData = {
      material_number: productCode,
      order_classification: 3,
      instruction_date: instructionDate,
      instruction_no: instructionNumber,
      instruction_kanban_quantity: instructionKanbanQuantity,

      //additional parameters
      product_name: productName,
      supplier_code : supplierCode,
      product_code : productCode,
    };

    $('.error_message').each(elem => {
      $(elem).html('');
    });
    
    if (
      confirm(
        "支給材端数指示情報を追加します、よろしいでしょうか？"
      )
    ) {
      // Get the CSRF token from the meta tag
      var csrfToken = $('meta[name="csrf-token"]').attr("content");
      $.ajax({
        url: "/material/fraction/store_session",
        method: "POST",
        data: newData,
        headers: {
          "X-CSRF-TOKEN": csrfToken,
        },
        success: function (response) {
          // Show the success message with a delay
          $("#successInputs")
            .delay(500)
            .fadeIn(100, function () {
              $(this).delay(1000);
            });

          // Reload the page after a delay
          reloadSection('.material-fraction-table');
        
        },
        error: function (xhr, status, error) {
          if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors;

            // Clear previous errors
            $(".input-error").removeClass("input-error");
            $(".error_msg").remove();

            // Display errors
            Object.keys(errors).forEach(function (field) {
              const fieldName = field.replace(/\.\d+$/, ""); // handles array fields

              const input = $(`table tfoot [id="${fieldName}"]`);
              const errorContainer = $(`table tfoot #${fieldName}_error`);
              const supplierCode = $("#supplier_code_error");
              if (input.length) {
                // input.addClass('input-error');
              }
              if (errors["supplier_code"]) {
                supplierCode.html(
                  `<div class="error_msg text-danger">${errors["supplier_code"][0]}</div>`
                );
              }
              if (errorContainer.length) {
                errorContainer.html(
                  `<div class="error_msg text-danger">${errors[field][0]}</div>`
                );
              }

              // Scroll to the page
              $('html, body').animate({
                scrollTop: 0
              }, 300);
               
            });
          } else {
            alert("サーバーエラーが発生しました。");
          }
        },
      });
    }
  }
  function bulkSavingData(button) {
    // if(!validateInputs()) return;

    // Get the supplier code input element
    const supplierCodeInput = document.getElementById("supplier_code");

    // Get the supplier code input value
    const supplierCode = supplierCodeInput.value;

    // Get the session data from the server-side
    const sessionSupplyInstructionData =
      document.getElementById("supplyInstructionData") || {};
    const sessionData = JSON.parse(sessionSupplyInstructionData.dataset.info);

    // Prepare the data to send in the request
    const data = {
      supplier_code: supplierCode,
      session_data: sessionData,
    };

    $('.error_message').each(elem => {
      $(elem).html('');
    });

    if (!confirm('支給材端数指示を登録します、よろしいでしょうか？')) return false;

    // Send an AJAX request to the server to save the data
    $.ajax({
      url: "/material/fraction/",
      method: "POST",
      data: data,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (response) {
        window.onbeforeunload = function () {
          sessionStorage.removeItem("previousDate");
          sessionStorage.removeItem("latestNumber");
          sessionStorage.removeItem("selectedDate");
          sessionStorage.removeItem("selectedCount");
        };

       
        // Show the success message with a delay
        $("#successInputs")
        .delay(500)
        .fadeIn(100, function () {
          $(this).delay(1000);
        });

        // scroll to top
        $('html, body').animate({
          scrollTop: 0
        }, 500);

      },
      error: function (xhr, status, error) {
        // Handle the error response
        alert("An error occurred while storing the data.");
      },
    });
  }

  function confirmDelete(button) {
    if (
      confirm("支給材端数指示情報を削除します、よろしいでしょうか？")
    ) {
      const row = button.closest("tr");
      const dataId = row.getAttribute("data-instruction-id");
      // Get the CSRF token from the meta tag
      var csrfToken = $('meta[name="csrf-token"]').attr("content");

      fetch(`/material/fraction/cancel_session/${dataId}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": csrfToken, // Include the CSRF token in the request headers
        },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Error deleting data");
          }
          return response.json();
        })
        .then((responseData) => {
          // Handle the response data
          alert("端数指示情報の削除が完了しました");

          // Remove the table row from the DOM
          row.remove();

          const getTableRow = document.querySelectorAll(
            ".table-bordered tbody tr"
          );
          const insertButton = document.querySelector(
            "[data-bulk-save-button]"
          );
          if (getTableRow.length < 2) {
            insertButton.classList.add("btn-disabled");
            insertButton.setAttribute("disabled", true);
          }
        })
        .catch((error) => {
          // Handle the error
          alert("Error deleting data: " + error);
        });
    }
  }

  function validateInputs() {
    const fields = [
      { selector: "#product_code", value: $("#product_code").val() },
      { selector: "#date_instruction", value: $("#date_instruction").val() },
      {
        selector: "#number_instruction",
        value: $("#number_instruction").val(),
      },
      {
        selector: "#instruction_kanban_quantity",
        value: $("#instruction_kanban_quantity").val(),
      },
      { selector: "#supplier_code", value: $("#supplier_code").val() },
    ];

    let hasError = false;
    $("#warningInputs").hide();

    // First, remove all previous error classes
    fields.forEach((field) => $(field.selector).removeClass("input-error"));

    // Then, validate inputs and add error class if needed
    fields.forEach((field) => {
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
