$(function () {
     // Get the input fields and submit buttons
     const inputFields = document.querySelectorAll('input[type="text"]');
     const submitButtons = document.querySelectorAll('.btnSubmitCustom');
     const form = document.querySelector('#orderSlipIssuanceForm');
     const formSubmitButton = document.querySelector('#orderSlip');
     const dateStart = document.querySelector('#instruction_date_from');
     const dateEnd = document.querySelector('#instruction_date_to');
     const radioButton = document.querySelectorAll('.issue-option-radio');
    

     radioButton.forEach((radioButton) => {
        radioButton.addEventListener('change', function () {
            toggleInputFields(true); // Enable input fields
            toggleSubmitButtons(true); // Enable submit buttons
            clearInputValues(); // Clear input field values
            toggleFormSubmitButton(true);
            $(".others-frame").hide(); // Hide by default
           
    
            if (this.checked && this.value == "2") {
                $(".others-frame").css("display", "inline-flex");
                toggleInputFields(false); // Disable input fields
                toggleSubmitButtons(false); // Disable submit buttons
                toggleFormSubmitButton(false); // Disable submit buttons
            }
        });
    });
   
   
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

    function toggleFormSubmitButton(disabled) {
        formSubmitButton.textContent = disabled ? '発注伝票発行' : '発注明細書再発行';  
        form.dataset.confirmationMessage = disabled ? '発注伝票を出力します、よろしいでしょうか？' : '発注明細書を再出力します、よろしいでしょうか？';  

        dateStart.value = '';
        dateEnd.value = '';
        if(!disabled){
            dateStart.value = dateStart.dataset.dateStart;
            dateEnd.value = dateEnd.dataset.dateEnd;
        }
    }
    // Function to clear input field values
    function clearInputValues() {
        inputFields.forEach((input) => {
            input.value = '';
            input.classList.remove('input-error');
        });
    }

});
