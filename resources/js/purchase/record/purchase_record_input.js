$(function () {
    $("#calculate_amount").on('click', function(e) {
        let unitPrice = $("#unit_price").val();
        let quantity = $("#quantity").val();

        if (unitPrice && quantity) {
            let amount = unitPrice * quantity;
            $("#amount").val(amount);
        }
    });

    $('#btn-populate-input-from-session').on('click', function() {
        $('#form_request input[type="text"]').css({
            'color': 'black',
            'font-weight': 'normal'
        });

        hideErrorMessages();
    });

    $('#line_code').on('input', function() {
        if ($(this).val().length == 3) {
            $.ajax({
                url: '/purchase/search-department-by-line-code/' + $(this).val(),
                type: 'GET',
                success: function(response) {
                    $('#department_code').val(response.code);
                    $('#department_name').val(response.name);
                }
            }); 
        }
    });

    $("#submit_button").on("click", function (event) {
        if (!confirm("生産品購入実績情報を登録します、よろしいでしょうか？")) {
            event.preventDefault(); 
        } else {
            $("#form_request").submit();
        }
    });

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

document.addEventListener('DOMContentLoaded', function() {
    // 必要な要素を取得
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.querySelector('input[name="unit_price"]');
    const amountInput = document.getElementById('amount');

    // 金額を計算する関数
    function calculateAmount() {
        const quantity = parseInt(quantityInput.value) || 0;
        const unitPrice = parseInt(unitPriceInput.value) || 0;
        const amount = quantity * unitPrice;
        
        // 計算結果を金額フィールドに設定
        amountInput.value = amount;
    }

    // 発注数と単価の入力イベントにリスナーを追加
    quantityInput.addEventListener('input', calculateAmount);
    unitPriceInput.addEventListener('input', calculateAmount);
});