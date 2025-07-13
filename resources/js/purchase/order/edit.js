// resources/js/purchase/order/edit.js
document.addEventListener('DOMContentLoaded', function() {
    // 必要な要素を取得
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.querySelector('input[name="unit_price"]');
    const amountInput = document.getElementById('amount_of_money');

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

// グローバルスコープに関数を追加
window.confirmClearInputContent = function() {
    if (confirm('本当にクリアしますか？')) {
        clearInputContent();
    }
}

window.confirmCancelRequisition = function() {
    let form = document.getElementById('cancelForm');
    let isConfirmed = confirm("発注取消を実施します、よろしいでしょうか？");
    if(isConfirmed) {
        console.log('confirmed');
        form.submit();
    }
    return false;
}

window.clearInputContent = function() {
    const specificFields = ['line_name', 'department_name', 'item_name', 'amount_of_money'];
    document.querySelectorAll('input:not([readonly]), textarea:not([readonly])')
        .forEach(element => element.value = '');

    specificFields.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.value = '';
    });
}

window.orderButton = function() {
    var form = document.getElementById('orderForm');
    var inputs = form.getElementsByTagName('input');
    var errors = form.getElementsByClassName('validation-error-message');
    var hasChanges = false;
    var hasErrors = false;
    var hasEmptyRequiredField = false;

    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].type === 'text' || inputs[i].type === 'textarea') {
            if (inputs[i].value !== inputs[i].defaultValue) {
                hasChanges = true;
                break;
            }
        }

        if(inputs[i].hasAttribute('required')) {
            if(inputs[i].value == '') {
                hasErrors = true;
                break;
            }
        }
    }

    for (var i = 0; i < errors.length; i++) {
        if(errors[i].innerHTML != '') {
            hasErrors = true;
            break;
        }
    }

    if(hasErrors) {
        return false;
    }
    if(confirm('発注入力情報を更新します、よろしいでしょうか?')){
        form.submit();
    }else{
        location.reload();
    }
}