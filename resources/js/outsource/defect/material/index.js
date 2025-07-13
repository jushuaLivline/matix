$(function () {
  const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
  const buttonActions = {
    "data-input-enable": enableInputs,
    "data-input-update": updateData,
    "data-cancel-button": cancelEdit,
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
    const selects = row.querySelectorAll("select");
    const inputs = row.querySelectorAll("input");

    selects.forEach((select) => {
      select.disabled = false;
    });

    inputs.forEach((input) => {
      input.disabled = false;
    });

    // Hide "EditDelete" div and show "UdpateUndo" div
    const editDeleteDiv = row.querySelector("#EditDelete");
    const updateUndoDiv = row.querySelector("#UdpateUndo");
    editDeleteDiv.style.display = "none";
    updateUndoDiv.style.display = "flex";
  }

  function updateData(button) {
    const row = button.closest("tr");
    const itemId = row.getAttribute("data-id");
    var csrfToken = token;

    const selectReason = row.querySelector("#reason_code");
    const quantityInput = row.querySelector("#quantity");
    const processRateSelect = row.querySelector("#processRate");

    const updatedData = {
      // id: itemId,
      reason_code: selectReason.value,
      quantity: quantityInput.value,
      processing_rate: processRateSelect.value,
    };

    console.log("itemId:", itemId);
    console.log("reason_code:", selectReason.value);
    console.log("quantity:", quantityInput.value);
    console.log("processing_rate:", processRateSelect.value);
    console.log("csrfToken:", csrfToken);

    // Send the updated data to the server for processing
    fetch("/outsource/defect/material/" + itemId, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": csrfToken,
      },
      body: JSON.stringify(updatedData),
    })
      .then((response) => {
        // Handle the response from the server (e.g., show a success message)
        if (response.ok) {
          alert("データは正常に更新されました");

          // Disable inputs in the same row
          selectReason.disabled = true;
          quantityInput.disabled = true;
          processRateSelect.disabled = true;

          // Hide "UpdateUndo" div and show "EditDelete" div
          const editDeleteDiv = row.querySelector("#EditDelete");
          const updateUndoDiv = row.querySelector("#UdpateUndo");
          editDeleteDiv.style.display = "flex";
          updateUndoDiv.style.display = "none";
          // location.reload();
        } else {
          console.error("Failed to update data");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  function cancelEdit(button) {
    if (confirm("キャンセルしますか？")) {
      // Disable inputs in the same row
      const row = button.closest("tr");
      const inputs = row.querySelectorAll("input");
      const select = row.querySelectorAll("select");
      inputs.forEach((input) => {
        input.disabled = true;
        input.value = input.getAttribute("data-old-value");
      });

      select.forEach((select) => {
        select.disabled = true;
        const oldValue = select.getAttribute("data-old-value"); // Get previous value
        // Set the option with the matching value as selected
        if (oldValue) {
          select.querySelectorAll("option").forEach((option) => {
            option.selected = option.value === oldValue;
          });
        }
      });
      // Hide "UdpateUndo" div and show "EditDelete" div
      const editDeleteDiv = row.querySelector("#EditDelete");
      const updateUndoDiv = row.querySelector("#UdpateUndo");
      editDeleteDiv.style.display = "flex";
      updateUndoDiv.style.display = "none";
    }
  }
});
