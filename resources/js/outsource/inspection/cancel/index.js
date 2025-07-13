$(function () {
  window.toggleCheckboxes = function () {
    var checkboxes = $(".rowCheckbox");
    var selectAllCheckbox = $("#selectAll");
    var submitButton = $("#submitButton");

    function updateSubmitButton() {
        var anyChecked = checkboxes.is(":checked");
        submitButton.toggleClass("btn-disabled", !anyChecked).prop("disabled", !anyChecked);
    }

    // Toggle all checkboxes when "Select All" is clicked
    selectAllCheckbox.on("change", function () {
        checkboxes.prop("checked", this.checked);
        updateSubmitButton();
    });

    // Individual checkbox change event
    checkboxes.on("change", updateSubmitButton);
  };
});
