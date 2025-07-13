$(function () {
    // Initialize the form by resetting it and setting the default date
    resetForm();
    // setRequestDefaultFirstDate();
    // setRequestDefaultLastDate();

    /**
     * Sets the default date of the input field to the first day of the current month.
     */
    function setRequestDefaultFirstDate() {
        let input = document.getElementById("request_date_from");

        if (input && !input.value) {
            let today = new Date();
            let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            let formattedDate = firstDay.getFullYear().toString() +
                                String(firstDay.getMonth() + 1).padStart(2, '0') +
                                String(firstDay.getDate()).padStart(2, '0');

            input.value = formattedDate;
        }
    }

    function setRequestDefaultLastDate() {
        let input = document.getElementById("request_date_to");

        if (input && !input.value) {
            let today = new Date();
            let lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

            let formattedDate = lastDay.getFullYear().toString() +
                                String(lastDay.getMonth() + 1).padStart(2, '0') +
                                String(lastDay.getDate()).padStart(2, '0');

            input.value = formattedDate;
        }
    }

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
            
            // setRequestDefaultFirstDate();
            // setRequestDefaultLastDate();
        });
    }

    $('#checkAll').click(function(){
        if (this.checked) {
            $(".checkboxes").prop("checked", true);
        } else {
            $(".checkboxes").prop("checked", false);
        }	
    });

    $("#unapprove-button").on('click', function () {
        let confirmation = confirm("現在選択されている購買依頼申請の承認を取り消します、よろしいでしょうか？");
        if (!confirmation) {
            return;
        }
        $("input[name=approval_type]").val("unapprove");
        $("#approval-form").submit();
    });

    $("#approve-button").on('click', function () {
        var confirmation = confirm("現在選択されている購買依頼申請を承認します、よろしいでしょうか？");
        if (!confirmation) {
            return;
        }
        console.log('clicked')

        $("input[name=approval_type]").val("approve");
        $("#approval-form").submit();
    });
})

// チェックボックスの状態をチェックしてボタンの有効/無効を切り替える関数
function updateButtonStates() {
    const checkedBoxes = document.querySelectorAll('.checkboxes:checked');
    const approveButton = document.getElementById('approve-button');
    const unapproveButton = document.getElementById('unapprove-button');
    
    // チェックされたボックスが0の場合、ボタンを無効化
    if (checkedBoxes.length === 0) {
        approveButton.disabled = true;
        approveButton.classList.add('btn-disabled');
        if (unapproveButton) {
            unapproveButton.disabled = true;
            unapproveButton.classList.add('btn-disabled');
        }
    } else {
        approveButton.disabled = false;
        approveButton.classList.remove('btn-disabled');
        if (unapproveButton) {
            unapproveButton.disabled = false;
            unapproveButton.classList.remove('btn-disabled');
        }
    }
}

// ページ読み込み時に実行
document.addEventListener('DOMContentLoaded', function() {
    // 初期状態のチェック
    updateButtonStates();

    // 個別のチェックボックスの変更を監視
    document.querySelectorAll('.checkboxes').forEach(checkbox => {
        checkbox.addEventListener('change', updateButtonStates);
    });

    // 全選択チェックボックスの変更を監視
    document.getElementById('checkAll').addEventListener('change', updateButtonStates);
});
