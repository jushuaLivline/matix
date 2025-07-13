$(function () {
  var barcodeInput = document.getElementById("barcode-input");
  var selectElement = document.querySelector(".customScrollbarSelect");
  var deleteButton = document.querySelector("[data-delete-button]");
  var doneTypingInterval = 500; // Delay in milliseconds
  var barcodeErrorContainer = document.querySelector(".error_msg_barcode");
  const clearButton = document.querySelector("[data-clear-inputs]");

  const form = document.getElementById("barcodeDataForm");
  const submitButton = document.querySelector("[data-register-button]");

  barcodeInput.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
      event.preventDefault(); // Prevent default form submission
      var barcodeValue = barcodeInput.value.trim();
      barcodeErrorContainer.innerHTML = "";

      var length = barcodeValue.length;
      if (length !== 7 && length !== 9) {
        alert("7桁あるいは9桁で入力してください。");
        return;
    }

      // Check if the barcode value already exists in the select element
      var existingOptions = Array.from(selectElement.options);
      var isDuplicate = existingOptions.some(function (option) {
        return option.value === barcodeValue;
      });

      if (isDuplicate) {
        alert("このバーコードは既に追加されています。");
        barcodeInput.value = ""; // Clear the input field
        return;
      }

      try {
        $.ajax({
          url: "/outsource/ajax-check-kanban-management-no",
          type: "GET",
          data: { barcode: barcodeValue },
          success: function (response) {
            if (response.exists) {
              // Create a new option element
              var newOption = document.createElement("option");
              newOption.value = barcodeValue;
              newOption.text = barcodeValue;

              // Add the new option to the select element
              selectElement.appendChild(newOption);

              // Clear the barcode input field
              barcodeInput.value = "";

              // Auto-select the newly added option
              newOption.selected = true;

              // Apply transparent background to the selected option
              newOption.style.backgroundColor = "transparent";

              // reset error
              selectElement.classList
                .remove('validation-error-message');
              selectElement.nextElementSibling.innerHTML=""
              
            } else {
              alert("入力された管理No.が存在しません。");
            }
          },
          error: function (error) {
            alert("Error fetching barcode data.");
          },
        });
      } catch (error) {
        console.log(error);
      }
    }
  });

  // Remove options from the select element &
  // remove query params from the URL
  clearButton?.addEventListener("click", () => {
    let url = window.location.origin + window.location.pathname;
    window.history.replaceState({}, document.title, url);
    selectElement.innerHTML = "";
  });

  submitButton.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default form submission
    let isValid = true;
    let inputWarings = document.getElementById("warningInputs");
    inputWarings.innerHTML = "";
    // Loop through all required inputs
    document.querySelectorAll("[data-input-required]").forEach((input) => {
      if (
        input.value.trim() === "" ||
        input.classList.contains("validation-error-message")
      ) {
        isValid = false;
        input.classList.add("input-error"); // Optional: Add error class for styling
        inputWarings.innerHTML =
          "登録に必要ないくつかの情報が入力されていません";
      } else {
        input.classList.remove("input-error"); // Remove error class if filled
      }
    });

    // Submit form if all fields are valid
    if (isValid) {
      if (!confirm('かんばん情報を登録します、よろしいでしょうか？')) return false;
      form.submit();
    }
  });

  // Select all delete buttons dynamically
  document.querySelectorAll("[data-delete-button]").forEach((button) => {
    button.addEventListener("click", function () {
      confirmDelete(this); // Pass the clicked button to the function
    });
  });

  function confirmDelete(button) {
    if (
      confirm("支給材かんばん情報を削除します、よろしいでしょうか？")
    ) {
      const row = $(button).closest("tr");
      const kanbanId = button.getAttribute("data-management-no");
      const form = $("#barcodeDataForm table tbody");
      const submitButton = $('#barcodeDataForm button[type="submit"]');

      // Get CSRF token from meta tag
      const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

      fetch(`/outsource/kanban/${kanbanId}`, {
        method: "PUT",
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
        // Remove the table row from the DOM
        row.remove();
        // only append if there are no rows left
        if ($(form).find("tr").length == 0) {
          $(form).append(
            '<tr><td colspan="12" class="text-center">検索結果はありません</td></tr>'
          );
          $(submitButton)
            .prop("disabled", true)
            .addClass("btn-disabled")
            .removeClass("btn-success");
        }
      })
      .catch((error) => {
        // Handle the error
        alert("予期せぬエラーが発生しました");
      });
    }
  }

  function updateProcessCodes() {
    let dateValues = {}; // To store date and their occurrence count

    document.querySelectorAll("tr[data-row]").forEach((row) => {
      let index = row.getAttribute("data-row");
      let creationDateInput = document.getElementById(
        "instruction_date_" + index
      );
      let processCodeInput = document.getElementById("instruction_no_" + index);

      if (creationDateInput && processCodeInput) {
        let dateValue = creationDateInput.value.trim();

        // If the input has a value, process it
        if (dateValue !== "") {
          if (dateValues[dateValue]) {
            // If date is already in the object, increment the count
            dateValues[dateValue]++;
          } else {
            // If date is not in the object, initialize it with count 1
            dateValues[dateValue] = 1;
          }
          // Set the corresponding process code value based on the count
          processCodeInput.value = dateValues[dateValue];
        } else {
          // Clear the process code if no date is entered
          processCodeInput.value = "";
        }
      }
    });
  }
});
