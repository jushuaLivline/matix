$(document).ready(function () {
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  var currentUserLoggedIn = $('#purchaseRequisitionInputForm').attr('data-current-user');
  var count = 0;
  
  $('input[name="approval_method_category"]').click(function(){
      var inputValue = $(this).attr("value");
      if(inputValue == 2){
          $("#approval-form-container").hide()
      }else{
          $("#approval-form-container").show()
      }
  });

  $(function() {
      getApprovalRoute();
  })

  $(".close-modal-purchase").on("click", function () {
      $(this).parents('.modal__content').find('.js-modal-close').trigger('click');
  });
  $("#calculate_amount").on('click', function () {
      let amount = $("#unit_price").val() * $("#quantity").val();
      $("#amount_of_money").val(amount);
  });

  $("#open_approval_modal").on('click', function () {
      getApprovalRoute();
  });

  $("#createApprovalRouteButton").on('click', function () {
      clearCreateApprovalRouteRow();
      addCreateApprovalRouteRow();
      validationMessage();
  });

  $("#create-approval-route-body").on("click", ".row-down", function () {
      var tableRow = $(this).parents("tr");

      if (! tableRow.next().children('.hidden-part').hasClass('d-none')) {
          tableRow.insertAfter(tableRow.next());
          createApprovalRouteBodyOrdering()
      }
  });

  $("#create-approval-route-body").on("click", ".row-up", function () {
      var tableRow = $(this).parents("tr");
      tableRow.insertBefore(tableRow.prev());
      createApprovalRouteBodyOrdering()
  });

  $("#update-approval-route-body").on("click", ".row-down", function () {
      var tableRow = $(this).parents("tr");
      if (! tableRow.next().children('.hidden-part').hasClass('d-none')) {
          tableRow.insertAfter(tableRow.next());
          updateApprovalRouteBodyOrdering()
      }
  });

  $("#update-approval-route-body").on("click", ".row-up", function () {
      var tableRow = $(this).parents("tr");
      tableRow.insertBefore(tableRow.prev());
      updateApprovalRouteBodyOrdering()
  });

  $("#approval-route-body").on("click", ".row-down", function () {
      var tableRow = $(this).parents("tr");
      if (! tableRow.next().children('.hidden-part').hasClass('d-none')) {
          tableRow.insertAfter(tableRow.next());
          $.ajax({
              type: 'POST',
              url: '/purchase/reorder-approval-route/',
              data : {id: $(this).attr('data-id'), type: 'down'},
              headers: {
                  'X-CSRF-TOKEN': token
              },
              success: function(data) {
                  getApprovalRoute();
              },
              error: function (jqXHR, textStatus, errorThrown) {
                  // window.location.reload(true);
              }
          });
      }
  });

  $("#approval-route-body").on("click", ".row-up", function () {
      var tableRow = $(this).parents("tr");
      tableRow.insertBefore(tableRow.prev());
      $.ajax({
          type: 'POST',
          url: '/purchase/reorder-approval-route/',
          data : {id: $(this).attr('data-id'), type: 'up'},
          headers: {
              'X-CSRF-TOKEN': token
          },
          success: function(data) {
              getApprovalRoute();
          },
          error: function (jqXHR, textStatus, errorThrown) {
              // window.location.reload(true);
          }
      });
  });
  $("#approval-route-body").on("click", ".approval-route-update-row", function () {
      $("#update-id").val($(this).attr('data-id'));
      // $("#update-approval_route_name").val($(this).parent("td").siblings("#route_name").html());
      fetch('/purchase/approval-route-details' + "/" + $(this).attr('data-id'), {
          method: 'GET',
          headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
          },
      })
      .then(response => response.json())
      .then(data => {
          $("#update-approval_route_name").val(data.data['approval_route_name']);
          $("#update-approval-route-body").html("");
          var html = "";
          $.each(data.details.data, function (index, value) {
              count+=1;
              html += '<tr>' +
                          '<td>' +
                          '<span class="pt-2 display_number">' + value['order_of_approval'] + '</span>'+
                          '</td>' +
                          '<td>' +
                          '    <div class="d-flex">' +
                          '        <input type="text" name="update_employee_codes"' +
                          '            id="update-employee_codes-'+value['order_of_approval']+'"' +
                          '            class="text-left mr-5c" maxlength="10"' +
                          '            value="' + value['employee'].data['code'] + '">' +
                          '        <input type="text" readonly' +
                          '            name="employee_name"' +
                          '            id="employee_name-'+value['order_of_approval']+'"' +
                          '            value="' + value['employee'].data['name'] + '"' +
                          '            class="middle-name text-left mr-5c">' +
                          '        <button type="button" class="btnSubmitCustom js-modal-open"' +
                          '                data-target="searchEmployeeModal-'+value['order_of_approval']+'" data-modal-employee>' +
                          '            <img src="/images/icons/magnifying_glass.svg"' +
                          '                alt="magnifying_glass.svg">' +
                          '        </button>' +
                          '    </div>' +
                          '</td>' +
                          '<td id="button-cell-'+value['order_of_approval']+'" class="d-none">' +
                          '    <button type="button" class="btn btn-green update-approval-route-save-row" style="width: 47%" data-hold="'+value['order_of_approval']+'">' +
                          '        追加' +
                          '    </button>' +
                          '<button type="button" class="btn btn-gray ml-5c" style="width: 47%;">クリア</button>'+
                          '</td>' +
                          '<td id="hidden-button-cell-'+value['order_of_approval']+'" class="hidden-part">'+
                          '    <button type="button" class="btn btn-orange update-approval-route-delete-row" data-id="' + value['id'] +'" style="width: 47%">'+
                          '        削除'+ 
                          '    </button>'+
                          '    <button type="button" class="btn btn-blue row-down" style="width: 23%" data-order="'+value['approval_route_no']+'">↓</button>'+
                          '    <button type="button" class="btn btn-blue row-up" style="width: 23%" data-order="'+value['approval_route_no']+'">↑</button>'+
                          '</td>'+
                      '</tr>';
                      addNewModal();
          });

          count++

          html += '<tr>' +
                  '<td>' +
                      '<span class="pt-2 display_number"></span>'+
                  '</td>' +
                  '<td>' +
                  '    <div class="d-flex">' +
                  '        <input type="text" name="update_employee_codes"' +
                  '            id="employee_codes-'+count+'" style="margin-right: 5px;"' +
                  '            class="text-left" maxlength="10"' +
                  '            data-validate-exist-model="employee"' +
                  '           data-validate-exist-column="employee_code"' +
                  '           data-inputautosearch-model="employee"' +
                  '           data-inputautosearch-column="employee_code"' +
                  '           data-inputautosearch-return="employee_name"' +
                  '           data-inputautosearch-reference="employee_name-'+count+'"' +
                  '           data-inputautosearch-counter="'+count+'"' +
                  '            data-modal-autosearch>' +
                  '        <input type="text" readonly' +
                  '            name="employee_name1"' +
                  '            id="employee_name-'+count+'" style="margin-right: 5px;"' +
                  '            value=""' +
                  '            class="middle-name text-left mr-5c">' +
                  '        <button type="button" class="btnSubmitCustom js-modal-open"' +
                  '                data-target="searchEmployeeModal-'+count+'">' +
                  '            <img src="/images/icons/magnifying_glass.svg"' +
                  '                alt="magnifying_glass.svg">' +
                  '        </button>' +
                  '    </div>' +
                  '    <div data-error-container="employee_codes-'+count+'" class="text-left employee_error_message" data-required-message="項目が必須です。"></div>' +
                  '</td>' +
                  '<td id="button-cell-'+count+'" class="">' +
                  '    <button type="button" class="btn btn-green update-approval-route-save-row" style="width: 47%" data-hold="'+count+'">' +
                  '        追加' +
                  '    </button>' +
                      '<button type="button" class="btn btn-gray update-ar-clear-button create-ar-clear-button" style="width: 47%; margin-left: 5px">クリア</button>'+
                  '</td>' +
                  '<td id="hidden-button-cell-'+count+'" class="d-none hidden-part">'+
                  '    <button type="button" class="btn btn-orange update-approval-route-delete-row" style="width: 47%">'+
                  '        削除'+
                  '    </button>'+
                  '    <button type="button" class="btn btn-blue row-down" style="width: 23%" data-order="'+count+'">↓</button>'+
                  '    <button type="button" class="btn btn-blue row-up" style="width: 23%" data-order="'+count+'">↑</button>'+
                  '</td>'+
                  '</tr>';
              addNewModal();
              
          $("#update-approval-route-body").html(html);
      })
      .catch(error => console.error('Error:', error));
      
  });


  $("#create-approval-route-body").on("click", ".create-approval-route-delete-row", function () {
      var tableRow = $(this).parents("tr");
      tableRow.remove();
  });

  $("#update-approval-route-body").on("click", ".update-approval-route-delete-row", function () {
      var tableRow = $(this).parents("tr");
      var confirmationMessage = confirm("削除しますか。");
      if(confirmationMessage){
          var id = $(this).data("id");
          if(id){
              fetch('/purchase/approval-route-detail/' + id, {
                  method: 'DELETE',
                  headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': token
                  },
              })
              .then(response => {
                  // tableRow.remove();
              })
          }
          tableRow.remove();
          updateApprovalRouteBodyOrdering();
          validationMessage();
      }
  });

  $("#approval-route-body").on("click", ".delete-route", function () {
      var tableRow = $(this).parents("tr");
      var id = $(this).data("id")
      var confirmMessage = confirm("削除しますか。")
      if(confirmMessage){
          fetch('/purchase/approval-route-list/' + id, {
                  method: 'DELETE',
                  headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': token
                  },
              })
              .then(response => {
                  tableRow.remove();
                  getApprovalRoute();
              })
      }
  });

  $("#update-approval-route-body").on( 'click', '.update-approval-route-save-row', function (e) {
      var hasInputWithNoValue = false;
      $("#update-approval-route-body tr").each( function (index){
          if($(this).find("input").val().trim() === ''){
              hasInputWithNoValue = true;
          }
      })
      const errorMessages = $('.employee_error_message.validation-error-message');
      const lastErrorMessageElement = errorMessages.last(); // Gets the last element directly
      if(lastErrorMessageElement.text().trim() !== "" ) {
          hasInputWithNoValue = true;;
      }

      if(hasInputWithNoValue){
          $("#update-approval-route-body tr").each( function (index){
              $(this).find("td").each(function(){
                  $(this).find("input").each(function(){
                      if($(this).val().trim() === ''){
                          $(this).addClass("border-danger")
                      }
                  })
              })
          })
          return;
      }else{
          $("#update-approval-route-body tr").each( function (index){
              $(this).find("td").each(function(){
                  $(this).find("input").each(function(){
                      if($(this).val().trim() != ''){
                          $(this).removeClass("border-danger")
                      }
                  })
              })
          })
      }

      addUpdateApprovalRouteRow();
      $("#button-cell-" + $(this).attr("data-hold")).addClass("d-none");
      $("#employee_codes-" + $(this).attr("data-hold")).attr("name", "update_employee_codes");
      $("#hidden-button-cell-" + $(this).attr("data-hold")).removeClass("d-none");
      updateApprovalRouteBodyOrdering();
  })

  $("#create-approval-route-body").on( 'click', '.create-approval-route-save-row', function (e) {
      var hasInputWithNoValue = false;
      $("#create-approval-route-body tr").each( function (index){
          if($(this).find("input").val().trim() === ''){
              hasInputWithNoValue = true;
          }
      })
      
      const errorMessages = $('.employee_error_message.validation-error-message');
      const lastErrorMessageElement = errorMessages.last(); // Gets the last element directly
      if(lastErrorMessageElement.text().trim() !== "" ) {
          hasInputWithNoValue = true;;
      }

      if(hasInputWithNoValue){
          $("#create-approval-route-body tr").each( function (index){
              $(this).find("td").each(function(){
                  $(this).find("input").each(function(){
                      if($(this).val().trim() === ''){
                          $(this).addClass("border-danger")
                      }
                  })
              })
          })
          return;
      }else{
          $("#create-approval-route-body tr").each( function (index){
              $(this).find("td").each(function(){
                  $(this).find("input").each(function(){
                      if($(this).val().trim() != ''){
                          $(this).removeClass("border-danger")
                      }
                  })
              })
          })
      }
      addCreateApprovalRouteRow();
      $("#button-cell-" + $(this).attr("data-hold")).addClass("d-none");
      $("#employee_codes-" + $(this).attr("data-hold")).attr("name", "employee_codes[]");
      $("#hidden-button-cell-" + $(this).attr("data-hold")).removeClass("d-none");
      createApprovalRouteBodyOrdering()
  });

  function clearCreateApprovalRouteRow() {
      var current = $("#create-approval-route-body").html("");
      count = 0;
  }
  function addCreateApprovalRouteRow() {
      var current = $("#create-approval-route-body").html();
      count += 1;
      $("#create-approval-route-body").append('<tr>' +
                              '<td>' +
                              '<span class="pt-2 display_number"></span>'+
                              '</td>' +
                              '<td>' +
                                '    <div class="d-flex">' +
                              '        <input type="text" name="employee_codes"' +
                              '            id="employee_codes-'+count+'" style="margin-right: 5px;"' +
                              '            class="text-left"' +
                              '            data-validate-exist-model="employee"' +
                              '           data-validate-exist-column="employee_code"' +
                              '           data-inputautosearch-model="employee"' +
                              '           data-inputautosearch-column="employee_code"' +
                              '           data-inputautosearch-return="employee_name"' +
                              '           data-inputautosearch-reference="employee_name-'+count+'"' +
                              '           data-inputautosearch-counter="'+count+'"' +
                              '            value="" data-modal-autosearch>' +

                              '        <input type="text" readonly' +
                              '            name="employee_name1"' +
                              '            id="employee_name-'+count+'" style="margin-right: 5px;"' +
                              '            value=""' +
                              '            class="middle-name text-left mr-5c">' +
                              '        <button type="button" class="btnSubmitCustom js-modal-open"' +
                              '                data-target="searchEmployeeModal-'+count+'">' +
                              '            <img src="/images/icons/magnifying_glass.svg"' +
                              '                alt="magnifying_glass.svg">' +
                              '        </button>' +
                              '    </div>' +
                              '       <div data-error-container="employee_codes-'+count+'" class="text-left employee_error_message" data-required-message="項目が必須です。"></div>' +
                              '</td>' +
                              '<td id="button-cell-'+count+'" class="">' +
                              '    <button type="button" class="btn btn-green create-approval-route-save-row" style="width: 47%" data-hold="'+count+'">' +
                              '        追加' +
                              '    </button>' +
                              '<button type="button" class="btn btn-gray create-ar-clear-button" style="width: 47%; margin-left: 5px">クリア</button>'+
                              '</td>' +
                              '<td id="hidden-button-cell-'+count+'" class="d-none hidden-part">'+
                              '    <button type="button" class="btn btn-orange create-approval-route-delete-row" style="width: 47%">'+
                              '        削除'+
                              '    </button>'+
                              '    <button type="button" class="btn btn-blue row-down" style="width: 23%" data-order="'+count+'">↓</button>'+
                              '    <button type="button" class="btn btn-blue row-up" style="width: 23%" data-order="'+count+'">↑</button>'+
                              '</td>'+
                              '</tr>');
          addNewModal();
          
  }

  function updateApprovalRouteBodyOrdering(){
      $("#update-approval-route-body tr").not(':last').each( function (index){
          $(this).find(".display_number").text(index + 1)
      })
  }

  function createApprovalRouteBodyOrdering(){
      $("#create-approval-route-body tr").not(':last').each( function (index){
          $(this).find(".display_number").text(index + 1)
      })
  }

  function addUpdateApprovalRouteRow() {
      var current = $("#update-approval-route-body").html();
      count += 2;
      $("#update-approval-route-body").append('<tr>' +
                              '<td>' +
                              '<span class="pt-2 display_number"></span>'+
                              '</td>' +
                              '<td>' +
                              '    <div class="d-flex">' +
                              '        <input type="text" name="update_employee_codes"' +
                              '            id="employee_codes-'+count+'" style="margin-right: 5px;"' +
                              '            class="text-left"' +
                              '            data-validate-exist-model="employee"' +
                              '           data-validate-exist-column="employee_code"' +
                              '           data-inputautosearch-model="employee"' +
                              '           data-inputautosearch-column="employee_code"' +
                              '           data-inputautosearch-return="employee_name"' +
                              '           data-inputautosearch-reference="employee_name-'+count+'"' +
                              '           data-inputautosearch-counter="'+count+'"' +
                              '            value="" data-modal-autosearch>' +
                              '        <input type="text" readonly' +
                              '            name="employee_name"' +
                              '            id="employee_name-'+count+'"' +
                              '            value=""' +
                              '            class="middle-name text-left mr-5c">' +
                              '        <button type="button" class="btnSubmitCustom js-modal-open"' +
                              '                data-target="searchEmployeeModal-'+count+'">' +
                              '            <img src="/images/icons/magnifying_glass.svg"' +
                              '                alt="magnifying_glass.svg">' +
                              '        </button>' +
                              '    </div>' +
                              '    <div data-error-container="employee_codes-'+count+'" class="text-left employee_error_message" data-required-message="項目が必須です。"></div>' +
                              '</td>' +
                              '<td id="button-cell-'+count+'" class="">' +
                              '    <button type="button" class="btn btn-green update-approval-route-save-row" style="width: 47%" data-hold="'+count+'">' +
                              '        追加' +
                              '    </button>' +
                              '<button type="button" class="btn btn-gray update-ar-clear-button create-ar-clear-button" style="width: 47%; margin-left: 5px">クリア</button>'+
                              '</td>' +
                              '<td id="hidden-button-cell-'+count+'" class="d-none hidden-part">'+
                              '    <button type="button" class="btn btn-orange update-approval-route-delete-row" style="width: 47%">'+
                              '        削除'+
                              '    </button>'+
                              '    <button type="button" class="btn btn-blue row-down" style="width: 23%" data-order="'+count+'">↓</button>'+
                              '    <button type="button" class="btn btn-blue row-up" style="width: 23%" data-order="'+count+'">↑</button>'+
                              '</td>'+
                              '</tr>');
          addNewModal();
  }

  function addNewModal () {
      var current = $("#approvalRouteModalStorage").html();
      $("#approvalRouteModalStorage").html(current + '<div id="searchEmployeeModal-'+count+'" class="modal js-modal modal__bg modalSs searchEmployeeModal-wrapper">'+
      '    <div class="modal__content modal_fix_width">'+
      '        <button type="button" class="modalCloseBtn js-modal-close">x</button>'+
      '        <div class="modalInner">'+
      '            <form action="#" accept-charset="utf-8">'+
      '                <div class="section">'+
      '                    <div class="boxModal mb-1">'+
      '                        <div class="mr-0">'+
      '                            <label class="form-label dotted indented label_for">社員選択</label>'+
      '                            <div class="flex searchModal">'+
      '                                <input type="hidden" id="model" value="Employee">'+
      '                                <input type="hidden" id="searchLabel" value="社員一覧">'+
      '                                <input type="hidden" id="query" value="">'+
      '                                <input type="hidden" id="reference" value="">'+
      '                                <input type="text" class="w-100 mr-half" placeholder="検索キーワードを入力" name="keyword">'+
      '                                <ul class="searchResult search--result-modal-employee" id="search-result" data-result-value-element="employee_codes-'+count+'" data-result-name-element="employee_name-'+count+'">'+
      '                                </ul>'+
      '                                <div class="clear">'+
      '                                    <button type="button" id="clear" class="clear-button" data-result-value-element="employee_codes-'+count+'" data-result-name-element="employee_name-'+count+'">'+
      '                                        選択した値をクリアする'+
      '                                    </button>'+
      '                                </div>'+
      '                            </div>'+
      '                        </div>'+
      '                    </div>'+
      '                </div>'+
      '            </form>'+
      '        </div>'+
      '    </div>'+
      '</div>');


      
      clearButton();
      validationMessage();
  }
  function getApprovalRoute() {
      fetch('/purchase/approval-route-list?employee_code='+currentUserLoggedIn+'', {
          method: 'GET',
          headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
          },
      })
          .then(response => response.json())
          .then(data => {
              $("#approval-route-body").html("");
              $("#approval_route_number").html("");
              var html = "";
              var html_dropdown = "";
              const isSingleItem = data?.data?.length === 1;
              var dataCounter = data?.data.length - 1;
              var disabledButtonUp = isSingleItem ? "disabled" : "";
              var disabledButtonDown = isSingleItem ? "" : "disabled";
              var approvalRouteNumber = $("#approval_route_number").attr('data-approval-route_number');
              
              if(data?.data?.length == 0) {
                  html = '<tr><td colspan="4">該当データがありません</td></tr>';
              }else{
                  $.each(data.data, function (index, value) {
                      if(data?.data.length > 1) {
                          disabledButtonUp = (index === 0) ? 'disabled' : "";
                          disabledButtonDown = (index === dataCounter) ? "disabled" : "";
                      }

                      html += "<tr>"+
                                  "<td>"+value['display_order']+"</td>"+
                                  "<td id='route_name'>"+value['approval_route_name']+"</td>"+
                                  "<td>"+value['details_count']+"</td>"+
                                  "<td><button type='button' data-id='"+value['id']+"' class='btn btn-blue approval-route-update-row js-modal-open' data-target='updateApprovalModal' style='width: 30%; margin-right: 5px;'>"+
                                  '編集'+
                                  '</button>'+
                                  '<button type="button" class="btn btn-blue row-down" style="width: 15%; margin-right: 5px;" data-id="'+value['id']+'" '+disabledButtonDown+'>↓</button>'+
                                  '<button type="button" class="btn btn-blue row-up" style="width: 15%; margin-right: 5px;" data-id="'+value['id']+'" '+disabledButtonUp+'>↑</button>'+
                                  '<button type="button" class="btn btn-orange delete-route" style="width: 30%" data-id="'+value['id']+'">削除</button></td>'+
                              "</tr>";
                      // Check if the approval_route_no is equal to approvalRouteNumber
                    if (value['approval_route_no'] == approvalRouteNumber) {
                        // Add the option with the selected attribute
                        html_dropdown += "<option value='"+value['approval_route_no']+"' selected>"+value['approval_route_name']+"</option>";
                    } else {
                        // Add the option without the selected attribute
                        html_dropdown += "<option value='"+value['approval_route_no']+"'>"+value['approval_route_name']+"</option>";
                    }
                  });
              }

              $("#approval-route-body").html(html);
              $("#approval_route_number").html(html_dropdown);
          })
          .catch(error => console.error('Error:', error));
  }

  function clearButton() {
      setTimeout(function(){
          $('.create-ar-clear-button').each(function() {
              $(this).on('click', function() {
                  $(this).parent().parent().find('.d-flex input[type="text"]').each(function(){
                      $(this).val("");
                      $(this).removeClass("validation-error-message");
                  })
                  
                  $(this).parent().parent().find('.employee_error_message').text("")
              });
          });
      }, 300);
  }

  function validationMessage() {
      $.ajax({
          url: "/api/validation-messages",
          type: 'GET',
          headers: {
          'X-CSRF-TOKEN': token
          },
          success: function(response) {
              const validationMessages = response 
              $('.with-js-validation-modal').each(function(){
                  var form = $(this);
                  
                  $(this).find("input[data-modal-autosearch]").each( function () {
                      const model = $(this).data('inputautosearch-model');
                      const column = $(this).data('inputautosearch-column');
                      const columnReturn = $(this).data('inputautosearch-return');
                      const reference = $(this).data('inputautosearch-reference');
                      const counter = $(this).data('inputautosearch-counter');
                      let debounceTimeout; 
                      
                      $(this).keyup(function(){
                          const errorMessageElement = $(`[data-error-container="employee_codes-${counter}"]`);
                          var inputElement = $(this);
                          var inputValue = inputElement.val();
                          

                          // Clear the previous debounce timer
                          clearTimeout(debounceTimeout);
                          
                          // Set a new debounce timer
                          debounceTimeout = setTimeout(function () {

                              if(inputValue.trim() == '') {
                                  inputElement.addClass("validation-error-message")
                                  errorMessageElement.addClass("validation-error-message").text(validationMessages.required)
                              }else{
                                  $.ajax({
                                      type: 'POST',
                                      url: '/api/lookup-autosearch',
                                      data : {
                                          name: columnReturn,
                                          model: model,
                                          column: column,
                                          searchValue: inputValue
                                      },
                                      headers: {
                                          'X-CSRF-TOKEN': token
                                      },
                                      success: function(response) {
                                          errorMessageElement.removeClass("validation-error-message").text("")
                                          inputElement.removeClass("validation-error-message")
                                          if(response.value == '') {
                                              inputElement.addClass("validation-error-message")
                                              errorMessageElement.addClass("validation-error-message").text(validationMessages.remote)
                                              form.find(`#${reference}`).val("")
                                          }else{
                                              form.find(`#${reference}`).val(response.value)
                                          }
                                      },
                                      error: function (jqXHR, textStatus, errorThrown) {
                                          console.log(error)
                                      }
                                  });
                              }
                          }, 300); 
                          
                      });
                  })

              });
          },
          error: function(xhr, status, error) {
              console.error('Error:', error);
          }
      });

  }
  $('#createApprovalRouteForm').validate({
      rules: {
          approval_route_name: {
              required: true
          }
      },
      messages: {
          approval_route_name: {
              required: '入力してください'
          }
      },
      errorElement : 'div',
      errorPlacement: function(error, element) {
          $(element).parents(".formBody").find('.error_msg').html(error)
      },
      invalidHandler: function(event, validator) {
          $('.submit-overlay').css('display', "none");
      },
      submitHandler: function(form) {
          var values = $("input[name^='employee_codes']").map(function (idx, ele) {
              return $(ele).val();
          }).get();

          const errorMessages = $('.employee_error_message.validation-error-message');
          const lastErrorMessageElement = errorMessages.last(); // Gets the last element directly
          // Disabled the form submission when there are error
          if(lastErrorMessageElement.text().trim() !== "" ) {
              return;
          }

          const errorMessageElement = $(`[data-error-container="employee_codes-1"]`);
          const employeeCodeInput = $('#employee_codes-1');
          // Disabled the form submission when there are error
          if (values.length === 1 && values[0] === "") {
              const requiredMessage = errorMessageElement.data('required-message');
              employeeCodeInput.addClass('validation-error-message');
              errorMessageElement.addClass('validation-error-message').text(requiredMessage);
              return;
          }


          // Reset error message styles
          errorMessageElement.removeClass('validation-error-message').text("");
          employeeCodeInput.removeClass('validation-error-message');

          var name = $("#approval_route_name").val();

          $.ajax({
              type: 'POST',
              url: '/purchase/save-approval-route',
              data : {
                      values: values, 
                      name: name, 
                      employee_code: currentUserLoggedIn },
              headers: {
                  'X-CSRF-TOKEN': token
              },
              success: function(data) {
                  getApprovalRoute();
                  $("#createApprovalModal .js-modal-close").trigger('click');
                  $("#approval_route_name").val("");
                  // $(this).parents('.modal__content').find('.js-modal-close').trigger('click');
              },
              error: function (jqXHR, textStatus, errorThrown) {
                  // window.location.reload(true);
              }
          });
      }
  });
  $('#updateApprovalRouteForm').validate({
      rules: {
          approval_route_name: {
              required: true
          },
      },
      messages: {
          approval_route_name: {
              required: '入力してください'
          },
      },
      errorElement : 'div',
      errorPlacement: function(error, element) {
          $(element).parents(".formBody").find('.error_msg').html(error)
      },
      invalidHandler: function(event, validator) {
          $('.submit-overlay').css('display', "none");
      },
      submitHandler: function(form) {
          var values = $("input[name^='update_employee_codes']").map(function (idx, ele) {
              var value = $(ele).val();
              if(value){
                  return $(ele).val();
              }
          }).get();
          
          const errorMessages = $('.employee_error_message.validation-error-message');
          const lastErrorMessageElement = errorMessages.last(); // Gets the last element directly
          if(lastErrorMessageElement.text().trim() !== "" ) {
              return;
          }

          var name = $("#update-approval_route_name").val();

          $.ajax({
              type: 'POST',
              url: '/purchase/update-approval-route',
              data : {update_id: $("#update-id").val(), values: values, name: name},
              headers: {
                  'X-CSRF-TOKEN': token
              },
              success: function(data) {
                  getApprovalRoute();
                  $("#updateApprovalModal .js-modal-close").trigger('click');
              },
              error: function (jqXHR, textStatus, errorThrown) {
                  // window.location.reload(true);
              }
          });
      }
  });



  $(document).on('click', '.btnSubmitCustom', function () {
    const targetModalForm = $(this).attr('data-target');

    // Toggle class based on data-modal-employee attribute
    $('.search--result-modal-employee').toggleClass('isEditForm', $(this).is('[data-modal-employee]'));

    // Bind debounced keyup event to search input inside the correct modal
    attachSearchEvent(`#${targetModalForm}`);
  });

  /**
  * Attaches keyup event with debounce for search functionality inside the specified modal.
  * @param {string} modalSelector - The selector for the modal where the search input resides.
  */
  function attachSearchEvent(modalSelector) {
    const $input = $(`${modalSelector} input[name="keyword"]`);

    if (!$input.data('debounced')) {
        $input.data('debounced', true);

        $input.on('keyup', _.debounce(function () {
            const $modal = $(this).closest('.searchModal');
            const searchQuery = $(this).val();
            const searchLabel = $modal.find('#searchLabel').val();
            const model = $modal.find('#model').val();
            const queryData = $modal.find('#query').val();
            const referenceData = $modal.find('#reference').val();

            let additionalData = getSessionStorageData(this);

            performSearch(searchQuery, model, additionalData, modalSelector, searchLabel, queryData, referenceData);
        }, 300)); // Debounce time: 300ms
    }
  }

  /**
  * Retrieves session storage data if available.
  * @param {HTMLElement} element - The input element triggering the search.
  * @returns {string} - Additional data string for the request.
  */
  function getSessionStorageData(element) {
    if (typeof Storage === "undefined") return "";

    var currentURL = window.location.href.split('?')[0]
    const modalSearchItem = sessionStorage.getItem(currentURL + '-modal-search-item');
    const modalSearchReference = sessionStorage.getItem(currentURL + '-modal-search-reference');
    const modalSearchValue = sessionStorage.getItem(currentURL + '-modal-search-value');

    if (modalSearchItem === $(element).attr('data-target')) {
        return ` ${modalSearchReference}=${modalSearchValue}`;
    }
    return "";
  }

  /**
  * Performs an AJAX search request and updates the search result list.
  * @param {string} searchQuery - The search query string.
  * @param {string} model - The model name for search.
  * @param {string} additionalData - Additional search parameters.
  * @param {string} modalSelector - The selector for the target modal.
  * @param {string} searchLabel - The label for the search field.
  * @param {string} queryData - Query data attribute.
  * @param {string} referenceData - Reference data attribute.
  */
  function performSearch(searchQuery, model, additionalData, modalSelector, searchLabel, queryData, referenceData) {
    $.ajax({
        url: '/search',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            query: searchQuery,
            model: model,
            'additional-data': additionalData
        },
        success: function (response) {
            const $searchResultContainer = $(modalSelector).find('.searchResult');
            $searchResultContainer.empty();

            $.each(response, function (index, value) {
                let listItem = $('<li>')
                    .attr({
                        'data-value': value.code,
                        'data-name': value.name,
                        'data-query': queryData,
                        'data-reference': referenceData
                    })
                    .text(`[${value.code}] ${value.name}`);

                $searchResultContainer.append(listItem);
            });

            $searchResultContainer.prepend($('<li>').addClass('disabled').text(searchLabel));
        },
        error: function (xhr, status, error) {
            console.error("Search Error:", error);
        }
    });
  }
});

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