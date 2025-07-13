$(function () {
  let debounceTimer;
  const buttonActions = {
    "data-delete-button": confirmDelete,
    "data-save-button": saveData,
  };

  // Loop through the button actions and add event listeners for all matching elements
  Object.entries(buttonActions).forEach(([selector, handler]) => {
    document.querySelectorAll(`[${selector}]`).forEach((button) => {
      button.addEventListener("click", function () {
        handler(this);
      });
    });
  });

  setTimeout(function () {
    const messageElement = document.querySelector(".message");
    if (messageElement) {
      messageElement.style.display = "none";
    }
  }, 3500);

  // $("#supplyMaterialReturnedForm1").validate({
  //   rules: {
  //     product_code: {
  //       required: true,
  //     },
  //   },
  //   messages: {
  //     product_code: {
  //       required: "項目が必須です。",
  //     },
  //   },
  //   errorElement: "div",
  //   errorPlacement: function (error, element) {
  //     $(element).closest("form").find(".error_msg").html(error);
  //   },
  // });

  // Add event listener to remove error border and hide message when typing
  document.addEventListener("input", function (event) {
    if (event.target.classList.contains("detail-number-input")) {
      event.target.classList.remove("error-border");
    }
  });

  document.addEventListener("DOMContentLoaded", function () {
    let debounceTimer;

    document
      .getElementById("product_number")
      .addEventListener("input", function () {
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
          let partNumber = document.getElementById("product_number").value;
          let partName = document.getElementById("product_name").value;

          document.querySelectorAll(".js-modal-open").forEach((element) => {
            element.setAttribute("data-part-number", partNumber);
            element.setAttribute("data-part-name", partName);
          });
        }, 400);
      });

    // Add class to disable auto-close of modal when selecting a product from the lookup
    document.addEventListener("click", function (event) {
      if (event.target.matches("[data-open-material-hierarchy-modal]")) {
        let inputPartNumber = document.querySelector(
          "[input-part-number]"
        )?.value;
        let inputProductNumber = document.querySelector(
          "[input-product-number]"
        )?.value;

        let modal = document.querySelector(".product-material-hierarchy-modal");
        if (modal) {
          modal.classList.add("open-material-hierarchy-modal");
        }

        if (inputPartNumber && inputProductNumber) {
          let targetPartNumber = document.querySelector(
            ".open-material-hierarchy-modal #part_number"
          );
          let targetProductNumber = document.querySelector(
            ".open-material-hierarchy-modal #part_name"
          );

          if (targetPartNumber) targetPartNumber.value = inputPartNumber;
          if (targetProductNumber)
            targetProductNumber.value = inputProductNumber;
        }
      }
    });
  });

  // FUNCTIONS
  function saveData() {

    var arrivalQuantity = document.querySelectorAll(".arrivalQuantity");
    var processingRate = document.querySelectorAll(".deliveryNo");
    var deliveryNo = document.getElementsByName("delivery_no[]");

    var arrivalDate = document.getElementById("arrival_day");
    var partNumber = document.querySelector(".ProductMaterial");
    var invalidInputsMessage = document.getElementById("invalidInputs");

    var isValid = true;

    // Define the fields to validate in an array
    var fieldsToValidate = [
      {
        elements: arrivalQuantity,
        relatedElements: deliveryNo,
        message: "数量が必須です。",
      },
      {
        elements: deliveryNo,
        relatedElements: "",
        message: "伝票No.が必須です。",
      },
      {
        elements: [arrivalDate],
        relatedElements: [],
        message: "返却日は必須です",
      },
      {
        elements: [partNumber],
        relatedElements: [],
        message: "製品品番は必須です",
      },
    
    ];
    // Function to handle the validation logic
    fieldsToValidate.forEach(function (field) {
      // Check all elements in the 'elements' list
      field.elements.forEach(function (input, index) {
        // Validate the element data-error-messsage-container
        if (!input.value) {
          input?.classList?.add(field.class); // Add the error class
          isValid = false; // Set isValid to false
          $(`${input?.dataset?.errorMesssageContainer}`).text(field.message);
        } else {
          input.classList.remove(field.class); // Remove the error class if valid
          $(`${input?.dataset?.errorMesssageContainer}`).text("");
        }

        // If there are related elements (like deliveryNo), validate them as well
        if (field.relatedElements.length > 0) {
          var relatedElement = field.relatedElements[index]; // Corresponding related element
          if (!relatedElement.value) {
            relatedElement?.classList?.add(field.class); // Add error class
            isValid = false; // Set isValid to false
          } else {
            relatedElement?.classList?.remove(field.class); // Remove error class if valid
          }
        }
      });
    });
    return isValid;
  }
  function confirmDelete(button) {
    let materialsId = button.getAttribute("data-supply-material-order-id"),
        redirectURL = button.getAttribute("data-redirect-url");
    if (confirm("返品実績情報を削除します、よろしいでしょうか？")) {
      fetch(`/api/supply-material-arival/${materialsId}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      })
        .then((response) => response.json())
        .then((responseData) => {

          // Redirect to material 28
          window.location.href = redirectURL;
        })
        .catch((error) => {
          // Handle the error
          alert("Error deleting SupplyMaterialArrival: " + error);
        });
    }
  }
});
