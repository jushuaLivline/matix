$(function () {
  const sessionsupplyInspectionData =
    document.getElementById("supplyInspectionData") || {};
  const sessionData = JSON.parse(sessionsupplyInspectionData.dataset.info);

  const buttonActions = {
    "data-clear-button": clearData,
    "data-update-button": updateData,
    "data-insert-button": storeData,
    "data-bulk-save-button": bulkSavingData,
    "data-delete-button": confirmDelete,
    "data-cancel-edit-button": cancelEdit,
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

  function validateInputs() {
    const fields = [
      { selector: "#arrival_day", value: $("#arrival_day").val() },
      { selector: "#flight_no", value: $("#flight_no").val() },
      { selector: "#delivery_no", value: $("#delivery_no").val() },
      { selector: "#product_code", value: $("#product_code").val() },
      //   { selector: "#product_name", value: $("#product_name").val() },
      { selector: "#arrival_quantity", value: $("#arrival_quantity").val() },
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

  function validateInputsEdit() {
    let hasError = false;
    const warningMessage = $("#warningInputs");
    const inputFields = $(".edit_field"); // Select all elements with the class

    warningMessage.hide();
    inputFields.removeClass("input-error"); // Remove previous error classes

    inputFields.each(function () {
      if (!$(this).val().trim()) {
        // Check if input is empty or just spaces
        $(this).addClass("input-error");
        hasError = true;
      }
    });

    if (hasError) {
      warningMessage.show();
      return false; // Prevent form submission
    }

    return true; // Validation passed
  }

  function storeData(button) {
    const row = button.closest("tr");

    var arrivalDateInput = $("#arrival_day").val();
    var flightNumberInput = $("#flight_no").val();
    var deliveryNumberInput = $("#delivery_no").val();
    var materialNumberInput = $("#product_code").val();
    var productName = $("#product_name");
    var arrivalQuantityInput = $("#arrival_quantity").val();

    // if (!validateInputs()) return;

    // Prepare the data to send in the request
    var newData = {
      arrival_day: arrivalDateInput,
      flight_no: flightNumberInput,
      delivery_no: deliveryNumberInput,
      material_no: materialNumberInput,
      arrival_quantity: arrivalQuantityInput,
      voucher_class: 1,

      //other data
      product_name: productName.val().trim(),
    };

    $('.error_message').each(elem => {
      $(elem).html('');
    });

    if (
      confirm(
        "検収入力情報を追加します、よろしいでしょうか？"
      )
    ) {
      // Get the CSRF token from the meta tag
      var csrfToken = $('meta[name="csrf-token"]').attr("content");

      $.ajax({
        url: "/material/order/inspection/store_session",
        method: "POST",
        data: newData,
        headers: {
          "X-CSRF-TOKEN": csrfToken,
        },
        success: function (response) {
          $("#warningInputs").hide();
          $("#successInputs").fadeIn(1000, () => location.reload());
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
              if (input.length) {
                // input.addClass('input-error');
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

  function updateData(button) {
    // Get the row and supply-material-arrival-id
    const row = button.closest("tr");
    const dataId = row.getAttribute("data-inspection-input-id");

    // Get the input values to update
    const arrivalDateInput = row.querySelector('input[name="arrival_day"]');
    const flightNumberInput = row.querySelector('input[name="flight_no"]');
    const deliveryNumberInput = row.querySelector('input[name="delivery_no"]');
    const materialNumberInput = row.querySelector('input[name="material_no"]');
    const productNameInput = row.querySelector('input[name="product_name"]');
    const arrivalQuantityInput = row.querySelector(
      'input[name="arrival_quantity"]'
    );

    const inputs = [
      arrivalDateInput,
      flightNumberInput,
      deliveryNumberInput,
      materialNumberInput,
      productNameInput,
      arrivalQuantityInput,
    ];

    const data = {
      data_id: dataId,
      arrival_day: arrivalDateInput.value,
      flight_no: flightNumberInput.value,
      delivery_no: deliveryNumberInput.value,
      material_no: materialNumberInput.value,
      arrival_quantity: arrivalQuantityInput.value,

      //other data
      product_name: productNameInput.value.trim(),
    };

    $('.error_message').each(elem => {
      $(elem).html('');
    });

    if (
      confirm(
        "検収入力情報を更新します、よろしいでしょうか？"
      )
    ) {
      // Get the CSRF token from the meta tag
      const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

      // Send an AJAX request to update the session data
      $.ajax({
        url: "/material/order/inspection/" + dataId,
        method: "PUT",
        data: data,
        headers: {
          "X-CSRF-TOKEN": csrfToken,
        },
        success: function (response) {
          location.reload();

          $("#successUpdate").fadeIn(400, function () {
            // Show the success message with a delay
            $("#successUpdate")
              .delay(500)
              .fadeIn(100, function () {
                $(this).delay(1000);
              });
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
              const errorContainer = $(row).find(`#${fieldName}_error`);
              if (input.length) {
                // input.addClass('input-error');
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
    if (!validateInputsEdit()) return;

    // Get the session data from the server-side
    const sessionsupplyInspectionData =
      document.getElementById("supplyInspectionData") || {};
    const sessionData = JSON.parse(sessionsupplyInspectionData.dataset.info);

    // Prepare the data to send in the request
    const data = {
      session_data: sessionData,
    };

    $('.error_message').each(elem => {
      $(elem).html('');
    });

    if (!confirm('支給材検収情報を登録します、よろしいでしょうか？')) return false;

    // Send an AJAX request to the server to save the data
    $.ajax({
      url: "/material/order/inspection",
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
        $("#successInputs").fadeIn(400, function () {
          // Show the success message with a delay
          $("#successInputs")
            .delay(500)
            .fadeIn(100, function () {
              $(this).delay(1000);
            });
        });

       // scroll top
        $("html, body").animate(
          {scrollTop: 0,},
        500);

        // Reload the page after a delay
        $('form table tbody tr').each(function () {
          $(this).remove();
        });
      },
      error: function (xhr, status, error) {
        // Handle the error response
        alert("An error occurred while storing the data.");
      },
    });
  }

  function clearData(button) {
    var tr = button.closest("tr");
    var inputs = tr.getElementsByTagName("input");

    for (var i = 0; i < inputs.length; i++) {
      inputs[i].value = "";
    }
  }

  function cancelEdit(button) {
    if (confirm("キャンセルしますか？")) {
      // Disable inputs in the same row
      const row = button.closest("tr");
      const inputs = row.querySelectorAll("input");
      inputs.forEach((input) => {
        input.disabled = true;
        input.value = input.getAttribute("old");
      });

      // Hide "UdpateUndo" div and show "EditDelete" div
      const editDeleteDiv = row.querySelector(".EditDelete");
      const updateUndoDiv = row.querySelector(".updateUndo");
      editDeleteDiv.style.display = "flex";
      updateUndoDiv.style.display = "none";
    }
  }

  function confirmDelete(button) {
    if (
      confirm(
        "検収入力情報を削除します、よろしいでしょうか？"
      )
    ) {
      const row = button.closest("tr");
      const dataId = row.getAttribute("data-inspection-input-id");
      // Get the CSRF token from the meta tag
      var csrfToken = $('meta[name="csrf-token"]').attr("content");

      fetch(`/material/order/inspection/cancel_session/${dataId}`, {
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
          alert("検収情報の削除が完了しました");

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

  //----------------------------------------
  // FROM master/receipt-and-inspection-input/index.js
  $("#product_code").on("input", () => $("#product_name").val(""));

  $(".edit-material-number").on("input", (event) => {
    const id = $(event.target).attr("material");
    $(`#product_name_${id}`).val("");
  });

  const disableEditableColumns = (id, disabled = true) => {
    const inputs = [
      `#instruction_date_${id}`,
      `#flight_no_${id}`,
      `#delivery_no_${id}`,
      `#product_code_${id}`,
      `#arrival_quantity_${id}`,
    ];
    const names = [`#product_name_${id}`];
    const buttons = [
      `button[data-target=instruction_date_${id}]`,
      `button[data-target=searchProductModal_${id}]`,
    ];
    if (disabled) {
      [...inputs, ...names].forEach((selector) => {
        const element = $(selector);
        const old = element.attr("old");
        element.val(old);
        element.text(old);
      });
    }
    [...inputs, ...buttons].forEach((selector) =>
      $(selector).prop("disabled", disabled)
    );
  };

  $(".edit").on("click", (event) => {
    const id = $(event.target).attr("material");
    disableEditableColumns(id, false);
    $(`#EditDelete${id}`).hide();
    $(`#UdpateUndo${id}`).show();
  });

  $(".undo").on("click", (event) => {
    const id = $(event.target).attr("material");
    if (!confirm("キャンセルしますか？")) return;
    disableEditableColumns(id);
    $(`#EditDelete${id}`).show();
    $(`#UdpateUndo${id}`).hide();
  });
});
