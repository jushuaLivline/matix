$(function () {

  const btnClear = document.querySelector("[data-button-clear]"),
    btnDelete = document.querySelector("[data-button-delete]"),
    btnReturn = document.querySelector("[data-button-return]"),
    reasonForDenial = document.getElementById("reason_for_denial");

  // Calculate Amount based on Order Quantity and Unit Price
  const quantityInput = document.querySelector('input[name="quantity"]');
  const unitPriceInput = document.querySelector('input[name="unit_price"]');
  const amountInput = document.querySelector('input[name="amount_of_money"]');

  // CLICK EVENTS
  btnDelete.addEventListener("click", function () {
    const itemId = this.getAttribute("data-item-id");
    confirmDelete(itemId);
  });
  reasonForDenial.addEventListener('keyup', toggleButtonState);

  quantityInput.addEventListener('input', calculateAmount);
  unitPriceInput.addEventListener('input', calculateAmount);

  // FUNCTIONS
  function calculateAmount() {
    const quantity = parseInt(quantityInput.value) || 0;
    const unitPrice = parseInt(unitPriceInput.value) || 0;
    const amount = quantity * unitPrice;

    amountInput.value = amount;
  }

  function toggleButtonState() {
    if (reasonForDenial.value.trim() === "") {
      btnReturn.classList.add("btn-disabled");
      btnReturn.disabled = true;
    } else {
      btnReturn.classList.remove("btn-disabled");
      btnReturn.disabled = false;
    }
  }
  function confirmDelete(itemId) {
    const form = document.getElementById("delete-form-" + itemId);

    if (form) {
      if (confirm("購買依頼情報を削除します、よろしいでしょうか？")) {
        form.submit();
      }
    } else {
      console.error("Form element not found for itemId:", itemId);
    }
  }
});

function fetchQueryName(query, get, model, value, name) {
  if (value) {
      $.ajax({
          url: '/outsource/defect/material/fetch-query-name',
          type: 'POST',
          data: {
              query: query,
              get: get,
              model: model,
              value: value,
              _token: $('meta[name="csrf-token"]').attr('content')
          },
          dataType: 'json',
          success: function (response) {
              if (response.status == "success") {
                  $("#"+name).val(response.result);
                  console.log(response.message);
              } else {
                  console.log(response.message);
                  $("#"+name).val('');
              }
          },
          error: function (xhr, status, error) {
              console.log("Error:", xhr.status, error);
              if (xhr.status === 404) {
                  console.log("Resource not found.");
                  $("#" + name).val('');
              }
          }
      });
  } else {
      $("[name='"+name+"']").val('');
  }
}

$('.fetchQueryName').on('input', function () {
  let query = $(this).data('query');
  let get = $(this).data('query-get');
  let model = $(this).data('model');
  let value = $(this).val();
  let name = $(this).data('reference');
  fetchQueryName(query, get, model, value, name);
});

$("#form-submit").on("click", function(e){
  e.preventDefault();
  
  if (!confirm("発注情報を更新します、よろしいでしょうか？")) {
    return;
  }
  var form = $("#approvDetailsForm");
  var id = form.data('id');

  $.ajax({
    url: "/purchase/order/process/detail/"+id,
    method: "PUT",
    data: form.serialize(),
    dataType: "JSON",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") 
    },
    beforeSend: function(){
      $(".error-message, #flash-message").remove();
    },
    success: function(response){
      $(".accordion").after("<div id='flash-message'>" + response.message + "</div>");
      // scroll to top
      $('html, body').animate({
        scrollTop: 0
    }, 500);
    },
    error: function(xhr, status, error) {
      if (xhr.responseJSON && xhr.responseJSON.errors) {
          let errors = xhr.responseJSON.errors;

          Object.keys(errors).forEach(function(field) {
              let errorMessage = errors[field][0]; 
              let inputField = $("[name='" + field + "']");
              inputField.next(".error-message").remove();
              inputField.closest("dd").append("<div class='error-message'>" + errorMessage + "</div>");
          });
      }
    }
  });
});