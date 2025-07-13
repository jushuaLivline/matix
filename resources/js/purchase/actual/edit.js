document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');
    const amountInput = document.getElementById('amount_of_money');

    // 金額を計算する関数
    function calculateAmount() {
        const quantity = parseInt(quantityInput.value) || 0;
        const unitPrice = parseInt(unitPriceInput.value) || 0;
        const amount = quantity * unitPrice;
        amountInput.value = amount;
    }

    // 数量と単価の入力時に計算を実行
    quantityInput.addEventListener('input', calculateAmount);
    unitPriceInput.addEventListener('input', calculateAmount);
});

$(function () {
    $("#clear_form").on("click", function (event) {
        if (!confirm("生産品購入実績情報をクリアします、よろしいでしょうか？")) {
            event.preventDefault(); 
        } else {
            clearAll();
            hideErrorMessages();
        }
    });

    function clearAll(){
        $("#form_request input[type='text'], #form_request input[type='number']").val("");
        
        $('input[name="voucher_class"]:first').prop("checked", true);
        $('input[name="slip_type"]:first').prop("checked", true);
        $('input[name="tax_classification"]:first').prop("checked", true);
        $("#remarks").val("");

        $("#form_request select").each(function () {
            $(this).prop("selectedIndex", 0);
        });
    }

    function hideErrorMessages(){
        // Hide the error message div
        $('#request_error_message').hide();
        $('div[data-error-container]').hide();
        $('div.validation-error-message').hide();
    }
})

