$(document).ready(function () {
    $('input[name="quantity"]').on('input change', function () {
        let quantity = parseFloat($(this).val()) || 0;
        let unitPrice = parseFloat($('#process-unit-price').val()) || 0;
        let totalPrice = unitPrice * quantity;

        // Update the total price field
        $('#total-price').val(totalPrice);
    });
});