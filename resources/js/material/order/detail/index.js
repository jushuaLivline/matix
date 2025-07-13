$(function () {

  $('input:radio[name="issue_classification"]').change(function(){
    if ($(this).is(':checked') && $(this).val() == 2) {
        $(".others-frame").css("display", "inline-block");
    } else {
        $(".others-frame").css("display", "none");
    }
});

  // Get the radio buttons and input fields
  const reissueRadio = document.querySelector('input[value="reissue"]');
  const noIssueRadio = document.querySelector('input[value="no-issue"]');
  const inputFields = document.querySelectorAll('input[type="text"]');
  const submitButtons = document.querySelectorAll('.btnSubmitCustom'); // Select your submit button class or ID

  // Function to enable or disable input fields
  function toggleInputFields(disabled) {
      inputFields.forEach((input) => {
          input.disabled = disabled;
      });
  }

  // Function to enable or disable submit buttons
  function toggleSubmitButtons(disabled) {
      submitButtons.forEach((button) => {
          button.disabled = disabled;
      });
  }

  // Function to clear input field values
  function clearInputValues() {
      inputFields.forEach((input) => {
          input.value = '';
      });
  }

  // Event listener for radio button change
  reissueRadio.addEventListener('change', () => {
      toggleInputFields(false); // Enable input fields
      toggleSubmitButtons(false); // Enable submit buttons
      clearInputValues(); // Clear input field values
  });

  noIssueRadio.addEventListener('change', () => {
      toggleInputFields(true); // Disable input fields
      toggleSubmitButtons(true); // Disable submit buttons
      clearInputValues(); // Clear input field values
  });

  // Disable input fields and submit buttons initially if "未発行分" is selected by default
  if (noIssueRadio.checked) {
      toggleInputFields(true);
      toggleSubmitButtons(true);
      clearInputValues();
  }
});
