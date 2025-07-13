$(function () {
    console.log("JS file loaded successfully!");

    // This event listener runs when the page is about to be unloaded (refreshed or closed).
    // It removes the saved session storage value for the current domain.
    window.addEventListener("beforeunload", function () {
        const key = `${window.location.origin}/outsources/process-defect-list-modal-search-value`;
        sessionStorage.removeItem(key);
    });

    /**
     * Enables input fields and buttons for editing a row.
     * Hides the "Edit/Delete" button and displays the "Update/Undo" button.
     * 
     * @param {HTMLElement} button - The button element that was clicked.
     */
    window.enableInputs = function (button) {
        const row = button.closest("tr"); // Get the closest row to the clicked button
        const inputs = row.querySelectorAll("input"); // Get all input fields in the row
        const buttonProduct = row.querySelector("#buttonProduct"); // Get the related product button

        buttonProduct.disabled = false; // Enable the product button
        inputs.forEach(input => input.disabled = false); // Enable all input fields

        row.querySelector("#EditDelete").style.display = "none"; // Hide Edit/Delete button
        row.querySelector("#UdpateUndo").style.display = "flex"; // Show Update/Undo button
    };

    /**
     * Sends an update request for a specific row's data.
     * After a successful update, disables inputs and switches button visibility.
     *
     * @param {HTMLElement} button - The button element that was clicked.
     */
    window.updateData = function (button) {
        const row = button.closest("tr"); // Get the row where the button was clicked
        const itemId = row.getAttribute("data-id"); // Get the row's data-id attribute (record ID)
        const csrfToken = document.querySelector("meta[name='csrf-token']").getAttribute("content"); // Get CSRF token

        // Select input fields for part number, quantity, and slip number
        const partNumber = row.querySelector("#product_code" + itemId);
        const quantityInput = row.querySelector("#quantity");
        const slipNo = row.querySelector("#slip_no");

        // Prepare data to send in the request
        const updatedData = {
            id: itemId,
            part_number: partNumber.value,
            quantity: quantityInput.value,
            slip_no: slipNo.value,
        };

        // Send a PUT request to update the defect item
        fetch("/outsource/defect/process/update-defect-item", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify(updatedData),
        })
            .then(response => {
                if (response.ok) {
                    alert("データは正常に更新されました"); // Show success message (Japanese: "Data was successfully updated")
                    partNumber.disabled = true; // Disable the part number field
                    quantityInput.disabled = true; // Disable the quantity field
                    slipNo.disabled = true; // Disable the slip number field
                    row.querySelector("#EditDelete").style.display = "flex"; // Show Edit/Delete button
                    row.querySelector("#UdpateUndo").style.display = "none"; // Hide Update/Undo button
                } else {
                    console.error("Failed to update data"); // Log error if the update fails
                }
            })
            .catch(error => console.error("Error:", error)); // Catch and log any request errors
    };

    /**
     * Calculates and updates the subtotal for an item in a row.
     * The subtotal is calculated as: processing unit price * quantity.
     *
     * @param {string} rowId - The ID of the table row.
     */
    window.calculateItemSubtotal = function (rowId) {
        const row = document.getElementById(rowId); // Get the row element by its ID
        if (!row) return; // If the row does not exist, exit function

        // Get values from the processing unit price and quantity fields, defaulting to 0 if empty
        const processingUnitPrice = parseFloat(row.querySelector("#processing_unit_price")?.value) || 0;
        const itemQuantity = parseFloat(row.querySelector("#quantity")?.value) || 0;

        // Get the subtotal field and update its value
        const subTotalField = row.querySelector("#subTotal");
        if (subTotalField) {
            subTotalField.value = (processingUnitPrice * itemQuantity).toFixed(0); // Round to 0 decimal places
        }
    };

    // Attach input event listeners to all quantity fields to automatically recalculate subtotal when changed.
    document.querySelectorAll("#quantity").forEach(input => {
        input.addEventListener("input", function () {
            calculateItemSubtotal(this.closest("tr").id); // Call subtotal calculation when quantity changes
        });
    });


    /**
     * Adds an event listener to the reset button to clear the form
     */
    window.resetForm = function () {
        console.log('resetForm');
        let resetButton = document.getElementById("resetForm");
        let form = document.getElementById("form_request");

        resetButton.addEventListener("click", function () {
            if (!form) return;

            // Reset all text inputs except disabled ones
            form.querySelectorAll("input[type='text']:not(:disabled)").forEach(input => {
                input.value = "";
            });

        });
    };

    resetForm();
});
