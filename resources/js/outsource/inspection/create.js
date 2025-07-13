$(function () {
    var barcodeInput = document.getElementById("barcode-input");
    var selectElement = document.querySelector(".customScrollbarSelect");
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

  
    // Set value for hidden fields outside form
    $("#form").on("input change", ".updateHidden", function() {
      let hiddenFieldId = $(this).data("hidden"); // Get the corresponding hidden field ID
      $("#" + hiddenFieldId).val($(this).val());  // Update the hidden field value
    });

    // Set value for supplier_name_hidden
    const observer = new MutationObserver(() => {
      $("#supplier_name_hidden").val($("#supplier_name").val());  
    });
    
    observer.observe(document.getElementById('supplier_code_hidden'), {
        attributes: true,
        attributeFilter: ['value']
    });
  
  });