$(function() {
    $('#arrivalForm').validate({
        rules: {
            arrival_quantity: {
                required: true
            },
            arrival_day: {
                required: true
            }
        },
        messages: {
            arrival_quantity: {
                required: '入荷数は必須です'
            },
            arrival_day: {
                required: '入荷数は必須です'
            }
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            $(element).closest('dd').find('.error_msg').html(error);
        },
        invalidHandler: function(event, validator) {
            $('.submit-overlay').css('display', "none");
        }
    })
})

document.addEventListener('DOMContentLoaded', function () {
    const arrivalDayInput = document.getElementById('arrival_day');
    const errorMsg = arrivalDayInput.closest('dd').querySelector('.error_msg');

    function validateArrivalDay() {
        const value = arrivalDayInput.value.trim();

        if (value.length === 0) {
            errorMsg.textContent = ''; // No error when empty
            arrivalDayInput.setCustomValidity(''); // Reset validation
        } else if (value.length !== 8 || isNaN(value)) {
            const customMessage = '正しい形式で入力してください'; // Custom Japanese error
            errorMsg.textContent = customMessage;
            arrivalDayInput.setCustomValidity(customMessage); // Set custom error
        } else {
            errorMsg.textContent = '';
            arrivalDayInput.setCustomValidity(''); // Clear error
        }
    }

    function clearValidationOnInput() {
        errorMsg.textContent = ''; // Hide error message while typing
        arrivalDayInput.setCustomValidity(''); // Reset browser validation
    }

    arrivalDayInput.addEventListener('input', clearValidationOnInput); // Clear message while typing
    arrivalDayInput.addEventListener('blur', validateArrivalDay); // Validate on blur

    // Prevent default browser validation tooltip
    arrivalDayInput.addEventListener('invalid', function (event) {
        event.preventDefault(); // Stops browser tooltip
    });
});

window.addEventListener('beforeunload', function() {
    localStorage.setItem('scrollPosition', window.scrollY);
});
window.addEventListener('load', function() {
    const scrollPosition = localStorage.getItem('scrollPosition');
    if (scrollPosition !== null) {
        //window.scrollTo(0, scrollPosition);
    }
});