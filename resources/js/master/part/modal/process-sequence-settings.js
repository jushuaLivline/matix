var okcallback = function ($modal) {
    return true;
};
var scrollPosition;
var count = 0;

$(document).on('click', '.js-modal-close-search,.js-modal-close-search.bColor-cancel', function () {
    $('body').removeClass('fixed').css({'top': 0});
    window.scrollTo(0, scrollPosition);
    //callback
    var res = true;
    if (typeof okcallback === 'function') {
        if ($(this).hasClass('bColor-ok'))
            res = okcallback($(this).closest('.modal.js-modal-search'));
    }
    if (res) {
        $(this).closest('.modal.js-modal-search').fadeOut();
        $(this).closest('.modal.js-modal-search').find('.modal__content').hide();
    }
});
$(document).on('click', '.searchResultProcess li:not(.disabled)', function () {
    const value = $(this).data('value');
    const resultValueEle = $(this).closest('.searchResultProcess').attr('data-result-value-element');
    const resultNameEle = $(this).closest('.searchResultProcess').attr('data-result-name-element');
    const text = $(this).text();

    $('#' + resultValueEle).val(value);
    $('#' + resultNameEle).html(text);

    $('.js-modal-close-search').trigger('click');
})
$('input[name="keyword"]').keyup(function() {
    var model = $(this).closest('.searchModal').find('#model').val();
    var searchLabel = $(this).closest('.searchModal').find('#searchLabel').val();
    var searchQuery = $(this).val();
    $.ajax({
        url: '/search',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            query: searchQuery,
            model: model
        },
        success: function(response) {
            $('.searchResultProcess').empty();

            // Iterate over the response and append list items
            $.each(response, function(index, value) {
                var listItem = $('<li>').attr('data-value', value.code).text(value.name);
                $('.searchResultProcess').append(listItem);
            });
            // Add the disabled list item
            var disabledListItem = $('<li>').addClass('disabled').text(searchLabel);
            $('.searchResultProcess').prepend(disabledListItem);
        },
        error: function(xhr, status, error) {
          // Handle errors
            console.log(error);
        }
    });
});

$(document).on('click', '.clear-button', function () {
    const resultValueEle = $(this).attr('data-result-value-element');
    const resultNameEle = $(this).attr('data-result-name-element');

    $('#' + resultValueEle).val('');
    $('#' + resultNameEle).val('');

    $('.js-modal-close-search').trigger('click');
})
$(document).on('click', '#sequenceSetting', function () {

    $('#table-process tbody').empty();
    $('#table-process tbody tr:not(#inputs_process)').remove();

    $.ajax({
        url: '/master/process-sequence-setting/get-data',
        method: 'GET',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            part_number: $('#part_number').val()
        },
        success: function(response) {
            buttonPageBottom(response)
            var html = prependHtml(response);
            $('#table-process tbody').prepend(html)
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    })
});

$('.save-btn-process').on('click',function () {
    var elements = $('#inputs_process').find('input[name]');
    var input = {}
    elements.each(function() {
        var name = $(this).attr('name');
        var value = $(this).val();
        input[name] = value
    });

    if(!validateRequiredFields($('#table-process tfoot tr'))) return;

    if(!confirm('工程順序設定を追加します、よろしいでしょうか？')) return;

    $.ajax({
        url: '/master/process-sequence-setting/save-session',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            data: input,
            part_number: $('#part_number').val()
        },
        success: function(response) {
            buttonPageBottom(response)
            prependHtml(response);
            clearInputs();
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    });
})

$('.clear-btn-process').on('click', function() {
    clearInputs()
})

$('.btn-done-process').click(function () {
    if(!confirm('工程順序設定を登録します、よろしいでしょうか？')) return false;
    $('#closeSequenceSettingModal').trigger('click');
    $('#saveProcessOrderForm').submit();
});

$("#table-process").on("click", ".edit-btn", function() {
    handleEditProcessButtonClick($(this).closest("tr"));
});

$("#table-process").on("click", ".cancel-btn", function() {
    handleCancelProcessButtonClick($(this).closest("tr"));
});

$("#table-process").on("click", ".update-btn", function() {
    var row = $(this).closest('tr');
    var rowId = $(this).closest('tr').data('row-id');
    var process_code = $(this).closest('tr').find('input:eq(0)').val();
    var process_details = $(this).closest('tr').find('input:eq(1)').val();
    var packing = $(this).closest('tr').find('input:eq(2)').val();

    if(!validateRequiredFields(row)) return;

    updateProcessOrderRow(rowId, process_code, process_details, packing, row);
});

$("#table-process").on("click", ".delete-btn", function() {
    handleDeleteButtonClick($(this).closest("tr"));
});

$("#table-process").on("click", ".up-btn", function() {
    handleOrderButtonClick($(this).closest("tr"), 'up');
});

$("#table-process").on("click", ".down-btn", function() {
    handleOrderButtonClick($(this).closest("tr"), 'down');
});


function handleOrderButtonClick(row, direction) {
    var rowId = row.data('row-id');
    var processOrderId = row.data('process-order-id') ?? '';
    $.ajax({
        url: '/master/process-sequence-setting/order',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: rowId,
            direction: direction,
            processOrderId: processOrderId,
        },
        success: function(response) {
            prependHtml(response)
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    });
}

function handleDeleteButtonClick(row) {
    if(!confirm('工程順序設定をテーブルから削除します、よろしいでしょうか？')) return false;
    var rowId = row.data('row-id');
    var processOrderId = row.data('process-order-id');
    $.ajax({
        url: '/master/process-sequence-setting/delete-row',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: rowId,
            process_order_id: processOrderId,
        },
        success: function(response) {
            buttonPageBottom(response);
            prependHtml(response)
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    });
}

function clearInputs() {
    var elements = $('#inputs_process').find('input[name]');
    var input = {}
    elements.each(function() {
        $(this).val('');
    });
    $('#process_name').html('')
}

function updateProcessOrderRow(rowId, process_code, process_details, packing, row) {
    var processOrderId = row.data('process-order-id');
    $.ajax({
        url: '/master/process-sequence-setting/'+processOrderId,
        method: 'PUT',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: rowId,
            process_code: process_code,
            process_details: process_details,
            packing: packing,
            process_order_id: processOrderId,
            part_number: $('input[name="part_number"]').val(),
        },
        success: function(response) {
            prependHtml(response);

            row.find('.span-process-order').toggle();
            row.find('.button-order').toggle();
            row.find('.div-process-code').toggle();
            row.find('.process-code-number').toggle();
            row.find('.div-details').toggle();
            row.find('.details-display').toggle();
            row.find('.div-packing').toggle();
            row.find('.packing-display').toggle();

            row.find('.div-button').toggle();
            row.find('.edit-button').toggle();

        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    });
}

function prependHtml(response)
{
    $('#table-process tbody tr:not(#inputs_process)').remove();
    // empty first before appending the modal
    $('.process-search-modal').empty();
    validationMessage();

    for (let i = 0; i < response.length; i++) {
        var disabled = response[i]['process_code'] == '' ? 'disabled' : '';
        var id = (response[i].process_order_id) ? response[i].process_order_id : 0;

        var html = '<tr class="modal-tr" data-row-id="' + i + '" data-process-order-id="'+id+'" >' +
            '<td class="modal-td text-left">'+
                '<span class="span-process-order">' +
                    response[i]['process_order'] +
                '</span>' +
                '<button class="button-order up-btn">' +
                    '<svg xmlns="http://www.w3.org/2000/svg"' +
                        'height="15px"' +
                        'viewBox="0 0 384 512">' +
                        '<style>svg{fill:#ffffff}</style>' +
                        '<path ' +
                            'd="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"/>' +
                    '</svg>' +
                '</button>' +
                '<button class="button-order down-btn">' +
                    '<svg xmlns="http://www.w3.org/2000/svg"' +
                        'height="15px"' +
                        'viewBox="0 0 384 512">' +
                        '<style>svg{fill:#ffffff}</style>' +
                        '<path ' +
                            'd="M169.4 470.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 370.8 224 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 306.7L54.6 265.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/>' +
                    '</svg>' +
                '</button>' +
            '</td>' +
            '<td class="modal-td text-center" style="padding-left: 7px; padding-right: 7px;">' +
                '<span class="process-code-number">' +
                    response[i]['process_code'] +
                '</span>' +
                '<div class="d-flex-inline div-process-code ml-1" style="display:none !important;">  <p class="formPack mr-2">' +
                    '<input type="text" '+
                    ' data-validate-exist-model="Process"  ' +
                    ' data-validate-exist-column="process_code" '+
                    ' data-inputautosearch-model="Process" ' +
                    ' data-inputautosearch-column="process_code" '+
                    ' data-inputautosearch-return="process_name" '+
                    ' data-inputautosearch-reference="process_name_'+response[i]['process_order']+'" maxlength="4" ' +
                    'id="process_code_'+ response[i]['process_order'] +'" '+
                    'name="process_code_'+ response[i]['process_order'] +'" style="width: 100%; padding: 5px;" '+ 
                    ' value="'+ response[i]['process_code'] +'" data-modal-autosearch class="input-required">' +
                    '</p> <div class="formPack ">' +
                    ' <button type="button" class="btnSubmitCustom js-modal-open"' +
                                'data-target="searchProcessModal-'+ response[i]['process_order'] +'">' +
                        '<img src="'+assetUrl+'"' +
                            'alt="magnifying_glass.svg">' +
                    '</button> </div>' +
                '</div> ' +
            '</td>' +
            '<td class="modal-td text-right p-right-5" style="padding: 5px;">'+
                response[i]['processing_unit_price'] +
            '</td>' +
            '<td class="modal-td text-center" id="process_name_'+response[i]['process_order']+'" style="padding: 5px;">' +
                response[i]['process_name'] +
            '</td>' +
            '<td class="modal-td" style="width:150px">' +
                '<span class="details-display" style="padding: 5px;">' +
                    response[i]['process_details'] +
                '</span>' +
                '<div class="div-details" style="display: none;">' +
                    '<input type="text" id="process_details" class="input-required" name="process_details" maxlength="4" style="width:95% !important; margin:5px;" value="'+ response[i]['process_details'] +'">' +
                '</div>' +
            '</td>'+
            '<td class="modal-td" style="width:150px">' +
                '<span class="packing-display" style="padding: 5px;">' +
                    response[i]['packing'] +
                '</span>' +
                '<div class="div-packing" style="display: none;">' +
                    '<input type="text" id="packing" class="input-required" name="packing" maxlength="20" style="width:95% !important; margin:5px;" value="'+ response[i]['packing'] +'">' +
                '</div>' +
            '</td>'+
            '<td class="modal-td text-center">' +
                '<span id="io_division">' +
                    response[i]['inside_and_outside_division'] +
                '</span>' +
            '</td>' +
            '<td class="modal-td text-center">' +
                '<div class="div-button" style="justify-content: start; padding-left: 7px; padding-right: 7px;">' +
                    '<button class=" btn edit-btn">編集</button>' +
                    '<button class=" btn delete-btn">削除</button>' +
                    '<button class=" btn process-modal-btn js-modal-open" data-target="processSettingModal" data-name="'+response[i]['process_name']+'" data-code="'+response[i]['process_code']+'" '+disabled+'>工程単価設定</button>' +
                '</div>' +
                '<div class="edit-button" style="justify-content: start; padding-left: 7px; display:none;">' +
                    '<button class="btn update-btn">更新</button>' +
                    '<button class="btn cancel-btn">取消</button>' +
                '</div>' +
            '</td>' +
        '</tr>'
        $('#table-process tbody').append(html);

        $('.process-search-modal').append(response[i]['process_modal'])
        $('.process-setting-modal').append(response[i]['process_setting_modal'])

        
        
    }
}

function handleCancelProcessButtonClick(row) {
    row.find('.span-process-order').toggle();
    row.find('.button-order').toggle();
    row.find('.div-process-code').toggle();
    row.find('.process-code-number').toggle();
    row.find('.div-details').toggle();
    row.find('.details-display').toggle();
    row.find('.div-packing').toggle();
    row.find('.packing-display').toggle();
    row.find('.div-button').toggle();
    row.find('.edit-button').toggle();
}

function handleEditProcessButtonClick(row) {

    row.find('.span-process-order').toggle();
    row.find('.button-order').toggle();
    row.find('.div-process-code').toggle();
    row.find('.process-code-number').toggle();
    row.find('.div-details').toggle();
    row.find('.details-display').toggle();
    row.find('.div-packing').toggle();
    row.find('.packing-display').toggle();

    var okcallback = function ($modal) {
        return true;
    };

    var scrollPosition;
    $(document).on('click', '.js-modal-close-search,.js-modal-close-search.bColor-cancel', function () {
        $('body').removeClass('fixed').css({'top': 0});
        window.scrollTo(0, scrollPosition);
        //callback
        var res = true;
        if (typeof okcallback === 'function') {
            if ($(this).hasClass('bColor-ok'))
                res = okcallback($(this).closest('.modal.js-modal-search'));
        }
        if (res) {
            $(this).closest('.modal.js-modal-search').fadeOut();
            $(this).closest('.modal.js-modal-search').find('.modal__content').hide();
        }
    });
    $(document).on('click', '.searchResultProcess li:not(.disabled)', function () {
        const value = $(this).data('value');
        const resultValueEle = $(this).closest('.searchResultProcess').attr('data-result-value-element');
        const resultNameEle = $(this).closest('.searchResultProcess').attr('data-result-name-element');
        const text = $(this).text();

        $('#' + resultValueEle).val(value);
        $('#' + resultNameEle).html(text);

        $('.js-modal-close-search').trigger('click');
    })
    

    $(document).on('click', '.clear-button', function () {
        const resultValueEle = $(this).attr('data-result-value-element');
        const resultNameEle = $(this).attr('data-result-name-element');

        $('#' + resultValueEle).val('');
        $('#' + resultNameEle).val('');

        $('.js-modal-close-search').trigger('click');
    })

    row.find('.div-button').toggle();
    row.find('.edit-button').toggle();
}


validationMessage();
function validationMessage() {
  $.ajax({
    url: "/api/validation-messages",
    type: "GET",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
    },
    success: function (response) {
      const validationMessages = response;
      $(".with-js-validation-modal").each(function () {
        var form = $(this);

        $(this)
          .find("input[data-modal-autosearch]")
          .each(function () {
            const model = $(this).data("inputautosearch-model");
            const column = $(this).data("inputautosearch-column");
            const columnReturn = $(this).data("inputautosearch-return");
            const reference = $(this).data("inputautosearch-reference");
            const counter = $(this).data("inputautosearch-counter");
            let debounceTimeout;

            $(this).keyup(function () {
              const errorMessageElement = $(
                `[data-error-container="employee_codes-${counter}"]`
              );
              var inputElement = $(this);
              var inputValue = inputElement.val();

              // Clear the previous debounce timer
              clearTimeout(debounceTimeout);

              // Set a new debounce timer
              debounceTimeout = setTimeout(function () {
                if (inputValue.trim() == "") {
                  inputElement.addClass("validation-error-message");
                  errorMessageElement
                    .addClass("validation-error-message")
                    .text(validationMessages.required);
                } else {
                  $.ajax({
                    type: "POST",
                    url: "/api/lookup-autosearch",
                    data: {
                      name: columnReturn,
                      model: model,
                      column: column,
                      searchValue: inputValue,
                    },
                    headers: {
                      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (response) {
                      errorMessageElement
                        .removeClass("validation-error-message")
                        .text("");
                      inputElement.removeClass("validation-error-message");
                      if (response.value == "") {
                        inputElement.addClass("validation-error-message");
                        errorMessageElement
                          .addClass("validation-error-message")
                          .text(validationMessages.remote);
                        form.find(`#${reference}`).val("");
                      } else {
                        form.find(`#${reference}`).val(response.value);
                        form.find(`#${reference}`).text(response.value);
                      }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                      console.log(errorThrown);
                    },
                  });
                }
              }, 300);
            });
          });
      });
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
  });
}

function validateRequiredFields(row) {
    let hasError = false;
    let elements = $(row).find(".input-required");
   
    elements.each(function () {
        if ($(this).val().trim() === "") {
            $(this).addClass("input-error");
            hasError = true;
        } else {
            $(this).removeClass("input-error");
        }

        $(this).one("input", function () {
            if ($(this).val().trim() !== "") {
                $(this).removeClass("input-error");
            } else {
                $(this).addClass("input-error");
            }
        });
    });
    return !hasError;
}

function buttonPageBottom(response)
{   
    var modal = $('#sequenceSettingModal');
    if(response.length > 0) {
        modal.find('.btn-orange').removeAttr('disabled').removeClass('btn-disabled');
        modal.find('.btn-done-process').removeAttr('disabled').removeClass('btn-disabled');
    }else{
        modal.find('.btn-orange').attr('disabled', true).addClass('btn-disabled');
        modal.find('.btn-done-process').attr('disabled', true).addClass('btn-disabled');
    }
}
