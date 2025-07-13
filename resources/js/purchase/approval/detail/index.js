$(function () {
    const checkboxes = document.querySelectorAll("input[name='approval_ids[]']");
    const approveButton = document.getElementById("delete_approval_button");
    const addNewApproverButton = document.getElementById("addNewApproverButton");
    const employeeNameInput = document.getElementById("employee_name");
    const denialButton = document.getElementById("denial-submit");
    const denialTextarea = document.querySelector("textarea[name='reason_for_denial']");
    
    // 承認依頼ボタンの状態を制御する関数
    function toggleAddNewApproverButton() {
      if (!addNewApproverButton || !employeeNameInput) return; // Ensure elements exist
    
      const employeeName = employeeNameInput.value.trim();
      if (employeeName === '') {
        addNewApproverButton.disabled = true;
        addNewApproverButton.style.pointerEvents = 'none';
        addNewApproverButton.classList.add('btn-secondary');
        addNewApproverButton.classList.remove('btn-primary', 'btn-blue');
      } else {
        addNewApproverButton.disabled = false;
        addNewApproverButton.style.pointerEvents = 'auto';
        addNewApproverButton.classList.remove('btn-secondary');
        addNewApproverButton.classList.add('btn-primary', 'btn-blue');
      }
    }
    
    // 否認ボタンの状態を制御する関数
    function toggleDenialButton() {
      const denialReason = denialTextarea.value.trim();
      if (denialReason === '') {
        denialButton.disabled = true;
        denialButton.style.pointerEvents = 'none';
        denialButton.classList.add('btn-secondary');
        denialButton.classList.remove('btn-primary', 'btn-blue');
      } else {
        denialButton.disabled = false;
        denialButton.style.pointerEvents = 'auto';
        denialButton.classList.remove('btn-secondary');
        denialButton.classList.add('btn-primary', 'btn-blue');
      }
    }
    
    // 初期状態の設定
    toggleAddNewApproverButton();
    toggleDenialButton();
    
    // employee_nameの監視
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' || mutation.attributeName === 'value') {
          toggleAddNewApproverButton();
        }
      });
    });
    
    observer.observe(employeeNameInput, {
      attributes: true,
      characterData: true,
      subtree: true
    });
    
    // 定期的なチェック
    setInterval(toggleAddNewApproverButton, 500);
    
    // 否認理由の入力監視
    denialTextarea.addEventListener('input', toggleDenialButton);
    denialTextarea.addEventListener('change', toggleDenialButton);
    
    function toggleButtonExcludeApprovarRoute() {
      const isChecked = Array.from(checkboxes).some(
        (checkbox) => checkbox.checked
      );
      approveButton.classList.toggle("d-none", !isChecked);
    }
    
    $("#denial-submit").on("click", function () {
      const $confirmMessageButton = $(this).attr("data-confirm-message");
      const $confirmMessageTextInput = $(this).attr("data-confirm-message");
    
      if (denialTextarea.value.trim() === "") {
        $("#reason_for_denial_error").html($confirmMessageTextInput);
      } else {
        if (confirm($confirmMessageButton)) {
          $("#deny-form").submit();
        }
      }
    });
    
    $(".approval-row").on("click", function () {
      const checkbox = $(this).children().find("input[type=checkbox]");
      checkbox.prop("checked", !checkbox.is(":checked"));
      toggleButtonExcludeApprovarRoute();
    });
    
    $("#delete_approval_button").on("click", function () {
      const $confirmMessage = $(this).attr("data-confirm-message");
      if (confirm($confirmMessage)) {
        $("#remove_approval_form").submit();
      }
    });
    
    $("#addNewApproverButton").on("click", function () {
      const $confirmMessage = $(this).attr("data-confirm-message");
      if (confirm($confirmMessage)) {
        $("#addNewApproverForm").submit();
      }
    });
    
    // employee_nameの監視をjQueryでも行う
    $(employeeNameInput).on('change input propertychange', function() {
      toggleAddNewApproverButton();
    });
    
    // 否認理由の監視をjQueryでも行う
    $(denialTextarea).on('change input propertychange', function() {
      toggleDenialButton();
    });
    
    // Approved Search/List
    $('#selectAll').click(function(e) {
      let table = $(e.target).closest('table');
      $('td input:checkbox', table).prop('checked', this.checked);
    });
});