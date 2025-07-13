$(function () {
    // Initialize the form by resetting it and setting the default date
    resetForm();

    /**
     * Adds an event listener to the reset button to clear the form
     * and reset the date input to the first day of the current month.
     */
    function resetForm() {
        console.log('resetForm');
        let resetButton = document.getElementById("resetForm");
        let form = document.getElementById("form_request");

        resetButton.addEventListener("click", function () {
            form.reset();
            form.querySelectorAll("input[type='text']").forEach(input => {
                input.value = "";
            });

            form.querySelectorAll("input[type='checkbox']").forEach(checkbox => {
                checkbox.checked = false;
            });
            
            setRequestDefaultFirstDate();
            setRequestDefaultLastDate();
        });
    }
})
