$(function () {
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const buttonActions = {
    "data-input-enable": enableInputs,
    "data-input-delete": confirmDelete,
    "data-input-update": updateData,
    "data-input-cancel": cancelEdit,
  };

  // Loop through the button actions and add event listeners for all matching elements
  Object.entries(buttonActions).forEach(([selector, handler]) => {
    document.querySelectorAll(`[${selector}]`).forEach((button) => {
      button.addEventListener("click", function () {
        handler(this);
      });
    });
  });

  // -------------------
  // FUNCTIONS

  function enableInputs(button) {
    // Enable inputs in the same row
    const row = button.closest("tr");
    const inputs = row.querySelectorAll("input");
    

    // Get the order classification value   
    const orderClassification =  $(row).attr("data-order-classification");

    // order_classification = 1: 通常
      if (orderClassification === "1") {
      // Enable only instruction date and instruction number inputs
      const instructionDateInput = row.querySelector(
        `input[id^="instruction_date_"]`
      );
      const incomingFlightNumber = row.nextElementSibling.querySelector(
        `input[id^="incoming_flight_number_"]`
      );

      if (instructionDateInput) {
        instructionDateInput.disabled = false;

        instructionDateInput.nextElementSibling.classList.remove("btn-disabled");
        instructionDateInput.nextElementSibling.removeAttribute("disabled");
      }

      if (incomingFlightNumber) {
        incomingFlightNumber.disabled = false;
      }

    // order_classification = 2: 臨時
    } else if (orderClassification === "2") {
      // Enable all inputs
      inputs.forEach((input) => {
        input.disabled = false;
      });
      

    
    // order_classification = 3: 端数指示
    } else if (orderClassification === "3") {
      // Enable instruction date, instruction number, supplier code, product code, and uniform number inputs
      const instructionDateInput = row.querySelector(
        `input[id^="instruction_date_"]`
      );
      const incomingFlightNumber = row.querySelector(
        `input[name^="incoming_flight_number"]`
      );
      const supplierCodeInput = row.querySelector(
        `input[id^="supplier_code_"]`
      );
      const productCodeInput = row.querySelector(`input[id^="product_code_"]`);
      const uniformNumberInput = row.querySelector(
        `input[name^="uniform_number"]`
      );

      if (instructionDateInput) {
        instructionDateInput.disabled = false;
      }
      if (incomingFlightNumber) {
        incomingFlightNumber.disabled = false;
      }
      if (supplierCodeInput) {
        supplierCodeInput.disabled = false;
      }
      if (productCodeInput) {
        productCodeInput.disabled = false;
      }
    }

    // Hide "EditDelete" div and show "UpdateUndo" div
    const editDeleteDiv = row.querySelector("#EditDelete");
    const updateUndoDiv = row.querySelector("#UdpateUndo");
    editDeleteDiv.style.display = "none";
    updateUndoDiv.style.display = "flex";

    
    if (orderClassification != "1") {
      const modalButtons = row.querySelectorAll(".btnSubmitCustom");
      modalButtons.forEach((button) => {
        button.disabled = false;
        button.classList.remove("btn-disabled");
      });
    }
  }

  function cancelEdit(button) {
    if (confirm("キャンセルしますか？")) {
      const row = button.closest("tr");
      const inputs = row.querySelectorAll("input");
      const inputs2 = row.nextElementSibling.querySelectorAll("input");

      inputs.forEach((input) => {
        input.disabled = true;
        input.value = input.getAttribute("old");
      });
      inputs2.forEach((input) => {
        input.disabled = true;
        input.value = input.getAttribute("old");
      });

      // Hide "UdpateUndo" div and show "EditDelete" div
      const editDeleteDiv = row.querySelector("#EditDelete");
      const updateUndoDiv = row.querySelector("#UdpateUndo");
      editDeleteDiv.style.display = "flex";
      updateUndoDiv.style.display = "none";

      const modalButtons = row.querySelectorAll(".btnSubmitCustom");
      modalButtons.forEach((button) => {
        button.disabled = true;
        button.classList.add("btn-disabled");
      });
    }
  }

  function updateData(button) {
    // Get the row and kanban data ID
    const row = button.closest("tr");
    const instructionDataId = row.getAttribute("data-id");

    // Get the input values to update
    const inputs = row.querySelectorAll("input");
    const inputs2 = row.nextElementSibling.querySelectorAll("input");
    const mergedData = { columns: {} };

    // Function to extract input values and merge them
    const extractInputs = (inputElements, targetObject) => {
      inputElements.forEach((input) => {
        const name = input.getAttribute("name");
        const value = input.value;

        targetObject.columns[name] = value;
      });
    };

    extractInputs(inputs, mergedData);
    extractInputs(inputs2, mergedData);

    // Set the order classification value

    const orderClassification =  $(row).attr("data-order-classification")

    if (orderClassification === "1") {
      mergedData.columns.order_classification = 1;
    } else if (orderClassification === "2") {
      mergedData.columns.order_classification = 2;
    } else if (orderClassification === "3") {
      mergedData.columns.order_classification = 3;
    }

    if(!confirm('発注データを更新します、よろしいでしょうか？')) return;

    // Send an AJAX request to the API endpoint
    fetch("/api/outsourced-processing/" + instructionDataId, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": token,
      },
      body: JSON.stringify(mergedData),
    })
      .then((response) => response.json())
      .then((responseData) => {
        // Handle the response outsourced instruction data UPDATING
        $('#successUpdate').fadeIn(500, function() {
          $(this).delay(500);
      });

        // Disable inputs in the same row
        inputs.forEach((input) => {
          input.disabled = true;
        });
        inputs2.forEach((input) => {
          input.disabled = true;
        });

        // Hide "EditDelete" div and show "UpdateUndo" div
        const editDeleteDiv = row.querySelector("#EditDelete");
        const updateUndoDiv = row.querySelector("#UdpateUndo");
        editDeleteDiv.style.display = "flex";
        updateUndoDiv.style.display = "none";


        const modalButtons = row.querySelectorAll(".btnSubmitCustom");
        modalButtons.forEach((button) => {
          button.disabled = true;
          button.classList.add("btn-disabled");
        });
       
      })
      .catch((error) => {
        console.error(error);
        // Handle error if needed
        alert("Some required information is missing!");
      });
  }

  function confirmDelete(button) {
    if (confirm("発注データを削除します、よろしいでしょうか？")) {
      const row = button.closest("tr");
      const instructionDataId = row.getAttribute("data-id");

      fetch(`/api/outsourced-processing/${instructionDataId}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": token,
        },
      })
        .then((response) => response.json())
        .then((responseData) => {
          // Handle the response data
          alert("発注データの削除が完了しました");

          // Remove the table row from the DOM
          location.reload();
        })
        .catch((error) => {
          // Handle the error
          alert("Error deleting OutsourcedProcessing: " + error);
        });
    }
  }
});
