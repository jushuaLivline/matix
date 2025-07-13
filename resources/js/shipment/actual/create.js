import retainer from '../../search/retainer'
import lodash from 'lodash'

const pathname = window.location.pathname
const customerCode = $('#customer_code')
const customerName = $('#customer_name')
const slipNumber = $('#_slip-no')
const instructionDate = $('#instruction_date')
const deliveryNumber = $('#_delivery-no')
const plant = $('#_plant')
const acceptance = $('#_acceptance')
const supplierCode = $('#supplier_code')
const supplierName = $('#supplier_name')

const customerNameKey = [pathname, customerName.attr('id')].join(':')

const reloadWithRequiredFilterParams = () => {
  const inputs = [
    customerCode, slipNumber, instructionDate, deliveryNumber,
    plant, acceptance, supplierCode, supplierName
  ]
  const params = new URLSearchParams
  lodash.each(inputs, input => params.set(input.prop('name'), input.val()))
  location.href = `${location.pathname}?${params.toString()}`
}

retainer(customerCode, customerName, customerNameKey)

$('#cache-shipment-data').on('click', () => {
  const _token = $('meta[name="csrf-token"]').attr('content')
  const productNumber = $('#_product-number')
  const productName = $('#_product-name')
  const quantity = $('#_quantity')
  const remarks = $('#_remarks')

  $.ajax({
    url: '/shipment-inspections/temp-data',
    type: 'post',
    data: {
      _token,
      part_no: productNumber.val(),
      part_name: productName.val(),
      quantity: quantity.val(),
      remarks: remarks.val()
    },
    success: () => {
      $('#warningInputs').hide()
      $('#successInputs').fadeIn(1000, reloadWithRequiredFilterParams)
    },
    error: ({ responseJSON }) => {
      const { errors } = responseJSON
      // Remove existing error classes
      $('#_product-number, #_product-name, #_quantity, #customer_code, #_slip-no, #instruction_date, #_delivery-no, #_plant, #_acceptance').removeClass('input-error')

      // Check for product-related input errors
      if (errors.part_no) productNumber.addClass('input-error')
      // if (errors.part_name) productName.addClass('input-error')
      if (errors.quantity) quantity.addClass('input-error')

      // Check if required fields are empty and apply 'input-error' class
      if (!customerCode.val()) customerCode.addClass('input-error')
      if (!slipNumber.val()) slipNumber.addClass('input-error')
      if (!instructionDate.val()) instructionDate.addClass('input-error')
      if (!deliveryNumber.val()) deliveryNumber.addClass('input-error')
      // if (!plant.val()) plant.addClass('input-error')
      // if (!acceptance.val()) acceptance.addClass('input-error')

      $('#warningInputs').show()
    },
  })
})

$(function(){
  var allProductNumberIsValid = true;

  $("#table-body").on( 'click', '.addRow', function (e) {
      addRow($(this));
  });

  $('.btn-sumbmit').on('click', function(e){
      e.preventDefault();
      $('#form').submit();
  });
  
  $(document).on( 'change, keyup', '.productNumberValidation', function (e) {
      var productNumber = $(this).val();
      if(productNumber){
          $(this).parents("tr").find("td").each( function (index){
              $(this).find("input[name='product_name[]']").val('')
          })
          $("#productNumberWarning").hide();
          var response = fetch("/api/part-number/check-exists", {
                  method: 'POST',
                  body: JSON.stringify({ product_number: productNumber }),
                  headers: {
                      'Content-Type': 'application/json',
                      'Accept': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include the CSRF token in the request headers
                  },
              })
              .then(response => response.json())
              .then( data => {
                  if(data.status == 'success'){
                      $(this).removeClass("input-error")
                      
                      $(this).parents("tr").find("td").each( function (index){
                          $(this).find("input[name='product_name[]']").val(data.data.product_name)
                      })
                  }else{
                      $("#productNumberWarning").show();
                      $(this).addClass("input-error")
                  }
              })
      }else{

      }
  });

  window.editRow = function(btn) {
      const $row = $(btn).closest("tr");

      $row.find("input:not([id*='product-name'])").each(function () {
          $(this).data("original-value", $(this).val());
          $(this).prop("readonly", false);
      });
      $(btn).removeClass("btn-primary edit-button").addClass("btn-success update-button").text("更新");
      $row.find(".removeRow")
          .removeClass("btn-orange removeRow")
          .addClass("btn-secondary cancel-button")
          .text("取消");
      $row.find(".update-button").off("click").on("click", function () {
          if (confirm('出荷実績情報を更新します、よろしいでしょうか？')) {
              $row.find("input").prop("readonly", true);
              $(this).removeClass("btn-success update-button").addClass("btn-primary edit-button").text("編集");
              $row.find(".cancel-button")
                  .removeClass("btn-secondary cancel-button")
                  .addClass("btn-orange removeRow")
                  .text("削除");
              $row.find(".edit-button").off("click").on("click", function () {
                  editRow(this);
              });
              $row.find(".removeRow").off("click").on("click", function () {
                  deleteRow(this);
              });
          }
      });
      $row.find(".cancel-button").off("click").on("click", function () {
          $row.find("input").each(function () {
              const originalValue = $(this).data("original-value");
              if (originalValue !== undefined) {
                  $(this).val(originalValue);
              }
              $(this).prop("readonly", true);
          });
          $row.find(".update-button")
              .removeClass("btn-success update-button")
              .addClass("btn-primary edit-button")
              .text("編集");
          $(this).removeClass("btn-secondary cancel-button")
              .addClass("btn-orange removeRow")
              .text("削除");
          $row.find(".edit-button").off("click").on("click", function () {
              editRow(this);
          });
      });
  }

  window.deleteRow = function(btn) {
      if (confirm('出荷実績情報を削除します、よろしいでしょうか？')) {
          const $row = $(btn).closest("tr");
          $row.remove();
      }
  }

  window.addRow = function(btn) {
      let current = btn ? $(btn).attr("data-count") : $("#table-body tr").length;
      let count = parseInt(current) + 1;

      if(count != 1){
          if(!validatedRow(btn)){
              return;
          }
      }

      $("#table-body").append(
              '<tr>' +
                  '<td class="d">' + 
                    '<div class="d-flex">' +
                      '<input required name="product_number[]" type="text" id="_product-number-'+ count +'" ' +
                          'class="numberCharacter searchOnInput ProductNumber productNumberValidation mr-25" style="width: 100%"' +
                          'data-field-name="製品品番"' +
                          'data-error-messsage-container="#part_number_error_"'+ count +'' +

                          'data-inputautosearch-model="ProductNumber"' +
                          'data-inputautosearch-reference="product_name-"'+ count +'' +
                          'data-inputautosearch-column="part_number"' +
                          'data-inputautosearch-return="name_abbreviation">' +                      
                      '<p class="formPack fixedWidth fpfw25p">' +
                          '<button type="button" class="btnSubmitCustom js-modal-open"' +
                                  'data-target="searchPartNumberModal-'+ count +'">' +
                              '<img src="/images/icons/magnifying_glass.svg"' +
                                      'alt="magnifying_glass.svg">' +
                          '</button>' +
                      '</p></div>' +
                      '<div id="part_number_error_'+ count +'"></div>' + 
                  '</td>' +
                  '<td>' +
                      '<input type="text" disabled style="width:100%" name="product_name[]" class="textCharacter" value="" id="_product-name-'+ count +'">' +
                  '</td>' +
                  '<td>' +
                      '<input type="text" required class="numberCharacter" name="quantity[]" data-field-name="納入計" data-error-messsage-container="#quantity_error_"'+ count +'" id="_quantity-'+ count +'">' +
                      '<div id="quantity_error_'+ count +'"></div>' + 
                  '</td>' +
                  '<td>' +
                      '<input type="text" class="textCharacter" name="remarks[]" value="" id="_remarks-'+ count +'">' +
                  '</td>' +
                  '<td class="center">' +
                      '<button type="button" class="btn btn-block btn-success addRow" data-count="'+ count +'">追加</button>' +
                      '<button type="button" class="btn btn-block btn-secondary clearRow" style="margin-left: 1px">クリア</button>' +
                  '</td>' +
              '</tr>'
      ) 

      addModal(count)
      formValidationMessages();
      
      $(btn).removeClass("btn-success addRow").addClass("btn-primary edit-button").text("編集");
      $(btn).closest("tr").find(".clearRow").removeClass("btn-secondary clearRow").addClass("btn-orange removeRow").text("削除");
      
      $(".edit-button").on("click", function () {
          editRow(this);
      });

      $(".removeRow").on("click", function () {
          deleteRow(this);
      });

      $(".clearRow").on("click", function() {
          $("#_product-number-"+count).val("");
          $("#_product-name-"+count).val("");
          $("#_quantity-"+count).val("");
          $("#_remarks-"+count).val("");
      });

      if (current > 0) {
          // Disable previous row fields
          $("#_product-number-" + current).prop("readonly", true);
          $("#_product-name-" + current).prop("readonly", true);
          $("#_quantity-" + current).prop("readonly", true);
          $("#_remarks-" + current).prop("readonly", true);
      }

  }

  window.validatedRow = function(btn){
      $(this).find("input").removeClass("input-error")
      var valid = true;
      var fetchStatus = "no";
      $(btn).parents("tr").find("td").each( function (index){
          if($(this).find("input[required]").val() === ''){
              valid = false
              // $(this).find('input[required]').addClass("input-error")
          }else{
              var productNumberValue = $(this).find("input[name='product_number[]']").val()
              if(productNumberValue){
              
              }
          }
      })

      if(valid){
          $(btn).parents("tr").find("td").each( function (index){
              $(this).find("input").removeClass("input-error")
          })
          $('#warningInputs').hide();
      } else {
          $('#warningInputs').show();
      }

      return valid;
  }

  window.validateProductNumber = function(productNumber){
      let valid = false;
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      var response = fetch("/api/part-number/check-exists", {
                  method: 'POST',
                  body: JSON.stringify({ product_number: productNumber }),
                  headers: {
                      'Content-Type': 'application/json',
                      'Accept': 'application/json',
                      'X-CSRF-TOKEN': token // Include the CSRF token in the request headers
                  },
              })
      return response
  }

  window.addModal = function(count){
      var current = $("#modalContainer").html();
      $('#modalContainer').html(current + 
          '<div id="searchPartNumberModal-'+ count +'" class="modal js-modal modal__bg modalSs">'+
                  '<div class="modal__content modal_fix_width">'+
                      '<button type="button" class="modalCloseBtn js-modal-close">x</button>'+
                      '<div class="modalInner">'+
                          '<form action="#" accept-charset="utf-8">'+
                              '<div class="section">'+
                                  '<div class="boxModal mb-1">'+
                                      '<div class="mr-0">'+
                                          '<label class="form-label dotted indented label_for">製品品番選択</label>'+
                                          '<div class="flex searchModal">'+
                                              '<input type="hidden" id="model" value="ProductNumber">'+
                                              '<input type="hidden" id="searchLabel" value="製品品番一覧">'+
                                              '<input type="hidden" id="query" value="">'+
                                              '<input type="hidden" id="reference" value="">'+
                                              '<input type="text" class="w-100 mr-half"'+
                                                  'placeholder="検索キーワードを入力"'+
                                                  'name="keyword">'+
                                              '<ul class="searchResult"'+
                                                  'id="search-result"'+
                                                  'data-result-value-element="_product-number-'+ count +'"'+
                                                  'data-result-name-element="_product-name-'+ count +'">'+
                                              '</ul>'+
                                              '<div class="clear">'+
                                                  '<button '+
                                                      ' type="button"'+
                                                      ' id="clear"'+
                                                      ' class="clear-button"'+
                                                      ' data-result-value-element="_product-number-'+ count +'"'+
                                                      ' data-result-name-element="_product-name-'+ count +'">'+
                                                      '選択した値をクリアする'+
                                                  '</button>'+
                                              '</div>'+
                                          '</div>'+
                                      '</div>'+
                                  '</div>'+
                              '</div>'+
                          '</form>'+
                      '</div>'+
                  '</div>'+
              '</div>'
          )
  }



  addRow();
});