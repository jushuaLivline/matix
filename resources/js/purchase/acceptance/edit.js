document.addEventListener("DOMContentLoaded", function () {
  const checkboxes = document.querySelectorAll(".date_checkbox");
  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("click", function () {
      const dataId = this.closest("tr").getAttribute("data-id");
      const dateInput = document.getElementById(`dateInput_${dataId}`);
      const hidden_dateInput = document.getElementById(
        `hidden_dateInput_${dataId}`
      );

      if (this.checked) {
        // Get the current date in YYYY-MM-DD format
        const currentDate = new Date().toISOString().slice(0, 10);
        dateInput.value = currentDate;
        dateInput.disabled = false;

        hidden_dateInput.value = currentDate;
      } else {
        dateInput.value = "";
        dateInput.disabled = true;

        hidden_dateInput.value = "";
      }
    });
  });

  document.addEventListener("DOMContentLoaded", function () {
    const scrollPosition = sessionStorage.getItem("lastScrollPosition");

    if (scrollPosition) {
      window.scrollTo(0, parseInt(scrollPosition));
      sessionStorage.removeItem("lastScrollPosition");
    }

    // Calculate Amount based on Order Quantity and Unit Price
    const quantityInput = document.querySelector('input[name="quantity"]');
    const unitPriceInput = document.querySelector('input[name="unit_price"]');
    const amountInput = document.querySelector('input[name="amount_of_money"]');

    function calculateAmount() {
      const quantity = parseInt(quantityInput.value) || 0;
      const unitPrice = parseInt(unitPriceInput.value) || 0;
      const amount = quantity * unitPrice;

      amountInput.value = amount;
    }

    quantityInput.addEventListener("input", calculateAmount);
    unitPriceInput.addEventListener("input", calculateAmount);
  });

  window.submitStoreForm = function() {
    sessionStorage.setItem("lastScrollPosition", window.pageYOffset);
    document.getElementById("storeDataForm").submit();
  }

  window.submitStoreForm = function() {
    if(!confirm('ご注文情報を登録させていただきます。よろしいでしょうか？')) return;
    document.getElementById("storeDataForm").submit();
  }

  window.confirmDelete = function(button) {
    if (confirm("選択された入荷・受入情報を削除します。よろしいでしょうか？")) {
      const row = button.closest("tr");
      const dataId = row.getAttribute("data-id");

      fetch(`/purchase/acceptance/${dataId}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      })
      .then((responseData) => {
          console.log(responseData);
          alert("データは正常に削除されました");
          location.reload();
        })
        .catch((error) => {
          // Handle the error
          alert("Error deleting data: " + error);
        });
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

$("#form-submit").on("click",function(e){
  e.preventDefault();

  let confirmation = window.confirm("発注情報を更新します。よろしいですか?");
  if (!confirmation) {
      return;
  }

  let form = $("#acceptance-form");
  let id = form.data('id');

  $.ajax({
      url: `/purchase/acceptance/${id}`,
      method: "PATCH",
      data: form.serialize(),
      dataType: "JSON",
      headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") 
      },
      beforeSend: function(){
          $(".error-message").remove(); 
      },
      success: function(response){
          form.before("<div id='flash-message' style='margin-bottom:30px; padding: 20px; border-radius: 5px;  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); color:#0d9c38;'>" + response.message + "</div>");
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

                  let closestDiv = inputField.closest("div");
                  let nextDiv = closestDiv.next("div");

                  if (nextDiv.hasClass("error-field")) {
                    nextDiv.append("<div class='mt-1 ml-1 error-message' style='color:red'>" + errorMessage + "</div>");
                  } else {
                    closestDiv.append("<div class='mt-1 ml-1 error-message' style='color:red'>" + errorMessage + "</div>");
                  }
              });
          }
      }
  });
});