$(function () {
  // Select all input elements with the class 'dailyValue'
  const inputs = document.querySelectorAll(".dailyValue");
  const totalCurrentMonth = document.querySelector(".totalCurrentMonth");
  const totalCurrentMonthHidden = document.querySelector(".totalCurrentMonthHidden");
  const totalNextMonth = document.querySelector(".totalNextMonth");
  const totalNextTwoMonth = document.querySelector(".totalNextTwoMonth");
  const grandTotal = document.querySelector(".grandTotal");
  const daysInThreeMonths = grandTotal.dataset.daysInThreeMonths ?? 0;

  // Function to calculate and update the total
  const updateTotal = () => {
    let total = 0;
    let hasValue = false; // Flag to check if at least one input has a value

    inputs.forEach((input) => {
      // Remove commas and parse the value
      let rawValue = input.value.replace(/,/g, "");
      let value = rawValue !== "" ? parseFloat(rawValue) : NaN;

      // Only add valid numbers (ignore empty values)
      if (!isNaN(value)) {
        total += value;
        hasValue = true;
      }

      // Keep empty fields blank
      input.value = !isNaN(value) ? value : "";
    });

    // Convert empty total to 0
    let formattedTotal = hasValue ? total : $("#orig-curr-month").val();
    totalCurrentMonth.value = formattedTotal.toLocaleString(undefined, { maximumFractionDigits: 0 }); // No commas
    totalCurrentMonthHidden.value = formattedTotal;

    // Ensure other fields are parsed properly (default to 0 if empty)
    let currentMonthValue = parseFloat(totalCurrentMonthHidden.value) || 0;
    let nextMonthValue = parseFloat(totalNextMonth.value.replace(/,/g, "")) || 0;
    let nextTwoMonthValue = parseFloat(totalNextTwoMonth.value.replace(/,/g, "")) || 0;

    let daysInThreeMonths = parseFloat(grandTotal.dataset.daysInThreeMonths) || 1; // Avoid division by zero

    let sumFields = currentMonthValue + nextMonthValue + nextTwoMonthValue;
    let finalValue =  Math.round(sumFields / daysInThreeMonths);



    grandTotal.value = finalValue.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });

  };

  // Add event listeners to each input
  inputs.forEach((input) => {
    input.addEventListener("input", updateTotal);
  });

  // Initial calculation on page load
  updateTotal();
});
