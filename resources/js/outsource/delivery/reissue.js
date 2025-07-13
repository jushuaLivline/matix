$('#selectAll').click(function(e){
    let table= $(e.target).closest('table');
    $('td input:checkbox',table).prop('checked',this.checked);
});

const orderSlipButton = document.getElementById("orderSlip");
const invoiceIdsInput = document.getElementById("invoiceIdsInput");

// Function to handle the form submission with confirmation
function submitFormWithConfirmation(event) {
    event.preventDefault(); // Prevent the default form submission

    const confirmationMessage = "発注明細書を出力します、よろしいでしょうか？";
    if (confirm(confirmationMessage)) {
        const selectedInvoiceIds = getSelectedInvoiceIds();
        if (selectedInvoiceIds.length > 0) {
            // Set the selected invoice ids in the hidden input field
            invoiceIdsInput.value = JSON.stringify(selectedInvoiceIds);

            // Submit the form
            const form = document.getElementById("reissueForm");
            form.action = window.location.origin + "/outsource/delivery/reissue-invoice-pdf";
            form.submit();
        } else {
            alert("少なくとも 1 つの請求書を選択してください。");
        }
    }
}

// Function to get the selected invoice ids
function getSelectedInvoiceIds() {
    const checkboxes = document.querySelectorAll("input[type='checkbox']");
    const selectedInvoiceIds = [];
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedInvoiceIds.push(checkbox.id);
        }
    });
    return selectedInvoiceIds;
}

// Attach event listener to the "orderSlip" button
orderSlipButton.addEventListener("click", submitFormWithConfirmation);

$(document).ready(function(){
    function fetchCustomerName(customerCode) {
        if (customerCode) {
            $.ajax({
                url: '/outsource/delivery/get-customer-name',
                type: 'POST',
                data: {
                    customer_code: customerCode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === "success") {
                        $('#supplier_name').val(response.customer_name);
                    } else {
                        alert(response.message);
                        $('#supplier_name').val("");
                    }
                }
            });
        } else {
            $('#supplier_name').val('');
        }
    }

    $('#supplier_code').on('input', function () {
        fetchCustomerName($(this).val());
    });

    var initialProcessCode = $('#supplier_code').val();
    if (initialProcessCode) {
        fetchCustomerName(initialProcessCode);
    }

});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("[type='reset']").addEventListener("click", function (event) {
        event.preventDefault();
        let form = document.getElementById("search-form");
        form.querySelectorAll("input[type='text']").forEach(input => input.value = "");
    });
});