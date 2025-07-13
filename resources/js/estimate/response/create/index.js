$(function () {
  const container = document.querySelector(".per-month-inputs-container");
  const addButton = document.getElementById("createElement");
  let counter = 0;
  dropzoneInt();

  addButton.addEventListener("click", function () {
    const originalElement = document.querySelector(".tableAddElement");
    if (!originalElement) return;

    // Clone the element
    const clonedElement = originalElement.cloneNode(true);
    counter = counter + 1;


    // Reset input values
    clonedElement.querySelectorAll("input").forEach((input, index) => {
   
      input.value = "";
      input.setAttribute("data-error-messsage-container", `#monthly_error_message_${counter}`); // Clear the data-file-name attribute
      input.setAttribute("name", `monthly_standard_amount[${counter}]`); // Clear the data-file-name attribute
    });

    clonedElement.querySelectorAll("#monthly_error_message").forEach((elem ,index) => {
      elem.innerHTML = ""; // Clear error messages
      elem.setAttribute("id", `monthly_error_message_${counter}`); // Update the ID to be unique
    });


    // Ensure the dropzone is properly re-initialized
    let clonedDropzone = clonedElement.querySelector(".dropzone");
    if (clonedDropzone.dropzone) {
      clonedDropzone.dropzone.destroy(); // Destroy only the specific Dropzone
    }
    clonedDropzone.classList.remove("dz-started"); // Reset Dropzone UI
    clonedDropzone.innerHTML = ""; // Clear old previews

    // Ensure a unique identifier for cloned Dropzone elements
    let uniqueId = "dropzone-" + Date.now();
    clonedDropzone.setAttribute("id", uniqueId);

    // Append to container
    container.appendChild(clonedElement);

    // Reinitialize functions
    dropzoneInt(`#${uniqueId}`, 1);
    inputFormValiation();

    // Attach event listener to remove button
    clonedElement
      .querySelector(".remove-btn")
      .addEventListener("click", function () {
        clonedElement.remove();
      });

    formValidationMessages();
  });

  // Handle remove button for initial element
  document.querySelectorAll(".remove-btn").forEach((button) => {
    button.addEventListener("click", function () {
      this.closest(".tableAddElement").remove();
    });
  });

  function checkRequiredFields() {
    let isValid = true;

    $("input[required], select[required], textarea[required]").each(
      function () {
        if ($(this).val().trim() === "") {
          isValid = false;
          $(this).addClass("input-error"); // Highlight the empty fields
        } else {
          $(this).removeClass("input-error");
        }
      }
    );

    return isValid;
  }
});
