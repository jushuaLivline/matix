document.addEventListener("DOMContentLoaded", function () {
  let debounceTimer;
  const sessionsessionDefectProcessData = document.getElementById("sessionDefectProcessData") || {};
  const getProductCode = document.querySelector('input[name="product_code"]');
  const quantity = document.querySelector('input[name="quantity"]');
  const buttonActions = {
    "data-input-update": updateData,
  };

  getProductCode.addEventListener("input", function () {
    getProductUnitePrice()
  });``
  quantity.addEventListener("input", function () {
    getProductUnitePrice()
  });``


  // Loop through the button actions and add event listeners for all matching elements
  Object.entries(buttonActions).forEach(([selector, handler]) => {
    document.querySelectorAll(`[${selector}]`).forEach((button) => {
      button.addEventListener("click", function () {
        handler(this);
      });
    });
  });

  function updateData(button) {
    button.addEventListener("click", function () {
      if(!validateInputs()) return;
    });
  }

  function getProductUnitePrice(obj)
  {
    clearTimeout(debounceTimer); // Clear previous timer
  
    debounceTimer = setTimeout(() => { // Wait 500ms before executing
     
      const getProductCode = document.querySelector('input[name="product_code"]');
      const processingUnitPrice = document.querySelector('input[name="processing_unit_price"]');
      const quantity = document.querySelector('input[name="quantity"]');
      const subTotalInput = document.querySelectorAll('.totalAmount');
      
      if ( getProductCode.value.trim() == ''){
        processingUnitPrice.value = 0;
        subTotalInput.value = 0;
        return;
      } 

      const productCode = getProductCode.value.trim();
      if (!productCode || !quantity || !processingUnitPrice || !subTotalInput) return;


      // Perform AJAX request here (only after the user stops typing)
      $.ajax({
        url: `/outsource/defect/process/get-product-unit-price/${productCode}`, // Your route
        method: 'GET',
        success: function(response) {
          const subTotal = quantity.value * response.unit_price;
          processingUnitPrice.value = response.unit_price; // Removed `.value` to avoid syntax error

          subTotalInput.forEach((input) => {
              const formattedSubTotal = subTotal // .toFixed(2);
              input.value = formattedSubTotal;
              input.textContent = formattedSubTotal;
          });
        },
        error: function() {
          console.error("Failed to fetch product unit price");
        }
      });
    }, 100); // Adjust delay as needed
  }

  function validateInputs() {
    const fields = [
        { selector: "#return_date", value: $("#return_date").val() },
        { selector: "#process_code", value: $("#process_code").val() },
        { selector: "#product_number", value: $("#product_number").val() },
        { selector: "#slip_no", value: $("#slip_no").val() },
        { selector: "#quantity", value: $("#quantity").val() },
        { selector: "#reason_code", value: $("#reason_code").val() },
        { selector: "#processing_rate", value: $("#processing_rate").val() },
    ];

    let hasError = false;
    $("#warningInputs").hide();

    // First, remove all previous error classes
    fields.forEach(field => $(field.selector).removeClass("input-error"));

    // Then, validate inputs and add error class if needed
    fields.forEach(field => {
        if (!field.value.trim()) {
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