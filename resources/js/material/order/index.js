$(function () {
  window.enableInputs = function (button) {
    // Enable inputs in the same row
    const row = button.closest("tr");
    const inputs = row.querySelectorAll("input");

    // Get the order classification value
    const orderClassification = row
      .querySelector('td[rowspan="2"]')
      .textContent.trim();
    const enableButtons = (enabled) => {
      const buttons = row.querySelectorAll(".btnSubmitCustom");
      buttons.forEach((btn) => {
        btn.disabled = !enabled;
      });
    };

    // Enable all inputs
    inputs.forEach((input) => {
      if (input.name !== "arrival_quantity") {
        input.disabled = false;
      }
      enableButtons(true);
    });

    // Hide "EditDelete" div and show "UpdateUndo" div
    const editDeleteDiv = row.querySelector("#EditDelete");
    const updateUndoDiv = row.querySelector("#UdpateUndo");
    editDeleteDiv.style.display = "none";
    updateUndoDiv.style.display = "flex";
  };

  window.updateData = function updateData(button) {
    const row = button.closest("tr");
    const token = document
      .querySelector('meta[name="csrf-token"]')
      .getAttribute("content");
    const supplyMaterialOrderId = row.getAttribute(
      "data-supply-material-order-id"
    );

    const instructionDateInput = row.querySelector(
      'input[name="instruction_date"]'
    );
    const instructionNoInput = row.querySelector(
      'input[name="instruction_no"]'
    );

    // Valitate the form
    formValidaton(row);
    const isValid = $("#temporaryValidationForm").valid();
    if (!isValid) {
      return;
    }

    // Proceed with the rest of your update logic...
    const data = {
      columns: {
        instruction_date: instructionDateInput.value,
        instruction_no: instructionNoInput.value,
      },
    };

    fetch("/api/supply-material-order/" + supplyMaterialOrderId, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": token,
      },
      body: JSON.stringify(data),
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then((errorData) => {
            throw errorData;
          });
        }
        return response.json();
      })
      .then((responseData) => {
        alert("支給材情報の更新が完了しました");
        const inputs = row.querySelectorAll("input");
        inputs.forEach((input) => (input.disabled = true));

        const editDeleteDiv = row.querySelector("#EditDelete");
        const updateUndoDiv = row.querySelector("#UdpateUndo");
        editDeleteDiv.style.display = "flex";
        updateUndoDiv.style.display = "none";

        location.reload();
      })
      .catch((error) => {
        console.error(error);
        alert("登録に必要ないくつかの情報が入力されていません！");
      });
  };

  window.cancelEdit = function cancelEdit(button) {
    if (confirm("キャンセルしますか？")) {
      // Get the row index from the button's data attribute
      var rowIndex = button.getAttribute("data-row-index");

      // Select all inputs within the same row
      var rowInputs = document.querySelectorAll(`input[id*="_${rowIndex}"]`);

      // Restore the original values from the data-original-value attribute
      rowInputs.forEach(function (input) {
        var originalValue = input.getAttribute("data-original-value");
        input.value = originalValue; // Set the value back to the original
      });

      // Disable inputs in the same row
      const row = button.closest("tr");
      const inputs = row.querySelectorAll("input");
      inputs.forEach((input) => {
        input.disabled = true;
        input.classList.remove("error");
      });

      const enableButtons = (enabled) => {
        const buttons = row.querySelectorAll(".btnSubmitCustom");
        buttons.forEach((btn) => {
          btn.disabled = !enabled;
        });
      };

      document.querySelectorAll('.error_message').forEach(function(el){ el.innerHTML = '' })

      // Disable buttons
      enableButtons(false);

      // Hide "UdpateUndo" div and show "EditDelete" div
      const editDeleteDiv = row.querySelector("#EditDelete");
      const updateUndoDiv = row.querySelector("#UdpateUndo");
      editDeleteDiv.style.display = "flex";
      updateUndoDiv.style.display = "none";
    }
  };

  window.confirmDelete = function confirmDelete(button) {
    if (confirm("発注情報を削除します、よろしいでしょうか？")) {
      const row = $(button).closest("tr");
      const supplyMaterialOrderId = row.data("supply-material-order-id");

      fetch(`/api/supply-material-order/${supplyMaterialOrderId}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      })
        .then((response) => response.json())
        .then((responseData) => {
          // Handle the response data
          alert("発注データの削除が完了しました");

          // Remove the table row from the DOM
          row.remove();

          location.reload();
        })
        .catch((error) => {
          // Handle the error
          alert("Error deleting SupplyMaterialOrder: " + error);
        });
    }
  };

  $(document).ready(function () {
    $("input[name='management_no']").on("input", function () {
      let managementInput = $(this);
      let managementValue = managementInput.val().trim();
      let originalValue = managementInput.data("original-value");

      // Only process if there's a change from the original value
      if (managementValue !== originalValue && managementValue.length === 5) {
        // Perform an AJAX request to check if management_no exists
        $.ajax({
          url: `/material/kanban/temporary/fetch-kanban-details?management_no=${managementValue}`,
          type: "GET",
          dataType: "json",
          success: function (data) {
            if ($.isEmptyObject(data)) {
              alert("この管理No.は登録されていません.");
            }
          },
          error: function (xhr, status, error) {
            console.error("Error fetching kanban details:", error);
          },
        });
      }
    });
  });

  $(document).ready(function () {
    $(
      "input[name='instruction_kanban_quantity'], input[name='number_of_accomodated']"
    ).on("input", function () {
      let id = $(this).attr("id").split("_").pop(); // Extract the unique ID

      let instructionQty =
        parseInt($(`#instruction_kanban_quantity_${id}`).val()) || 0;
      let numberAccommodated =
        parseInt($(`#number_of_accomodated_${id}`).val()) || 0;

      let arrivalQty = instructionQty * numberAccommodated;

      $(`#arrival_quantity_${id}`).val(arrivalQty);
    });
  });
});

$(document).ready(function () {
  function resetForm() {
    let resetButton = document.getElementById("resetForm");
    let form = document.getElementById("form_request");

    if (resetButton && form) {
      resetButton.addEventListener("click", function (event) {
        event.preventDefault();

        form.reset();
        form.querySelectorAll("input[type='text']").forEach((input) => {
          input.value = "";
        });
      });
    } else {
      console.error("resetButton or form_request not found!");
    }
  }

  resetForm();
});

function formValidaton(row) {
  $(".error_message").each((elem) => {
    $(elem).html("");
  });

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

  $("#temporaryValidationForm").validate({
    rules: {
      instruction_date: {
        required: true,
        digits: true,
        maxlength: 8,
        dateFormat: true,
      },
      instruction_no: {
        required: true,
        digits: true,
        maxlength: 2,
      },
    },
    messages: {
      instruction_date: {
        required: "指示日は必須です。",
        digits: "指示日は整数で入力してください",
        maxlength: "指示日8max文字以内で入力してください。",
        dateFormat: "正しい形式で入力してください",
      },
      instruction_no: {
        required: "便No.は必須です。",
        digits: "便No.は整数で入力してください",
        maxlength: "便No.2max文字以内で入力してください",
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
