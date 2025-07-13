$(function () {
    // Initialize the form by resetting it and setting the default date
    resetForm();

    /**
     * Adds an event listener to the reset button to clear the form
     */
    function resetForm() {
        let resetButton = document.getElementById("resetForm");
        let form = document.getElementById("form_request");

        resetButton.addEventListener("click", function () {
            form.reset();
            form.querySelectorAll("input[type='text']").forEach(input => {
                input.value = "";
            });
        });

    }
})

document.getElementById('machine_name1').addEventListener('input', function () {
    if (this.value.trim() === '') {
        document.getElementById('machine_number_start').value = '';
    }
});

document.getElementById('machine_name2').addEventListener('input', function () {
    if (this.value.trim() === '') {
        document.getElementById('machine_number_end').value = '';
    }
});