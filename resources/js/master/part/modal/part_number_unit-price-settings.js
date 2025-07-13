// alert('modals/row-form.js');
// return false;



$('#unitPriceSetting').click(function() {
    $.ajax({
        url: '/master/part-number-unit-pirce-setting/get-data',
        method: 'GET',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            part_number: $('#part_number').val()
        },
        success: function(response) {
            
            buttonPageBottom(response);
            var html = prependHtml(response);
            $('#table-container tbody').prepend(html);

         
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    })
})

$('.save-btn').click(function () {
    if(!validateRequiredFields($('#unitPriceSettingModal tfoot tr'))) return;

    if(!confirm('品番単価設定を追加します、よろしいでしょうか？')) return;

    var elements = $('#inputs').find('input[name]');
    var input = {}
    elements.each(function() {
        var name = $(this).attr('name');
        var value = $(this).val();
        input[name] = value
    });
    $.ajax({
        url: '/master/part-number-unit-pirce-setting/save-session',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            data: input,
            part_number: $('#part_number').val()
        },
        success: function(response) {
            buttonPageBottom(response);
            var html  = prependHtml(response);
            $('#table-container tbody').prepend(html)
            clearInputs();
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    });
})
$("#table-container").on("click", ".edit-btn", function() {
    handleEditButtonClick($(this).closest("tr"));
});
$("#table-container").on("click", ".cancel-btn", function() {
    handleCancelButtonClick($(this).closest("tr"));
});
$("#table-container").on("click", ".delete-btn", function() {
    handleDeleteButtonClick($(this).closest("tr"));
});

$("#table-container").on("click", ".update-btn", function() {
    var row = $(this).closest('tr');
    var rowId = $(this).closest('tr').data('row-id');
    var effective_date = $(this).closest('tr').find('input:eq(0)').val();
    var sell_price = $(this).closest('tr').find('input:eq(1)').val();
    var unit_price = $(this).closest('tr').find('input:eq(2)').val();

    if(!validateRequiredFields(row)) return;

    updateRow(rowId, effective_date, sell_price, unit_price, row);
});

$('.btn-done').click(function () {
    if(!confirm('品番単価設定を登録します、よろしいでしょうか？')) return false;
    $('#unitPriceSettingModal .modalCloseBtn').trigger('click');
    $('#savePartNumberUnitPriceForm').submit();
})
$('.btn-del').click(function () {
    if(!confirm('品番単価設定を削除します、よろしいでしょうか？')) return;
    $('#unitPriceSettingModal .modalCloseBtn').trigger('click');
    $('#deletePartNumberUnitPriceForm').submit();
})



function handleDeleteButtonClick(row) {
    var rowId = row.data('row-id');
    var productPricesId = row.data('product-prices-id');

    if(!confirm('品番単価設定をテーブルから削除します、よろしいでしょうか？')) return;

    $.ajax({
        url: '/master/part-number-unit-pirce-setting/delete-row',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: rowId,
            product_prices_id: productPricesId,
        },
        success: function(response) {
            buttonPageBottom(response);
            var html = prependHtml(response);
            $('#table-container tbody').prepend(html)
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    });
}

function updateRow(rowId, effective_date, sell_price, unit_price, row) {
    var productPricesId = row.data('product-prices-id') ?? 0;
    $.ajax({
        url: '/master/part-number-unit-pirce-setting/'+ productPricesId,
        method: 'PUT',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: rowId,
            effective_date: effective_date,
            sell_price: sell_price,
            unit_price: unit_price,
            product_prices_id: productPricesId,
            part_number: $('input[name="part_number"]').val(),
        },
        success: function(response) {
            var html = prependHtml(response);
            $('#table-container tbody').prepend(html)

            row.find('.div-calendar').toggle();
            row.find('.calendar-display').toggle();
            row.find('#sprice-display').toggle();
            row.find('#up-display').toggle();
            row.find('#sell_price').toggle();
            row.find('#unit_price').toggle();
            row.find('.div-button').toggle();
            row.find('.edit-button').toggle();

        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    });
}

function handleEditButtonClick(row) {
    row.find('.div-calendar').toggle();
    row.find('.calendar-display').toggle();
    row.find('#sprice-display').toggle();
    row.find('#up-display').toggle();
    row.find('#sell_price').toggle();
    row.find('#unit_price').toggle();
    row.find('.div-button').toggle();
    row.find('.edit-button').toggle();

    var $buttonPickerJS = row.find('.buttonPickerJS');

    const createDatePicker = function ($input, $format = "yyyymmdd") {
        const picker = $input.pickadate({
            format: $format,
            firstDay: 0,
            clear: false,
            closeOnSelect: true,
            onClose: function () {
                $input.prop('readonly', false); // Set the input field as editable
                picker.pickadate('picker').stop(); // Destroy the pickadate instance
            }
        });
        return picker;
    }

    $buttonPickerJS.on('click', function (event) {
        const $input = $('#' + $(this).data('target'));
        const format = $(this).data('format') || "yyyymmdd";
        const pickerDate = createDatePicker($input, format.toLowerCase());
        pickerDate.pickadate('picker').open();
        event.stopPropagation();
    }).on('mousedown', function (event) {
        event.preventDefault();
    });
}

function handleCancelButtonClick(row) {
    row.find('.div-calendar').toggle();
    row.find('.calendar-display').toggle();
    row.find('#sprice-display').toggle();
    row.find('#up-display').toggle();
    row.find('#sell_price').toggle();
    row.find('#unit_price').toggle();
    row.find('.div-button').toggle();
    row.find('.edit-button').toggle();
}

$('.clear-btn').on('click', function() {
    clearInputs()
})

function clearInputs() {
    var elements = $('#inputs').find('input[name]');
    var input = {}
    elements.each(function() {
        $(this).val('');
    });
}

function prependHtml(response)
{
    $('#table-container tbody tr:not(.modal-tr#inputs)').remove();
    if (typeof response !== 'undefined') {
        console.log("prependHtml  ", response)
        for (let i = 0; i < response.length; i++) {
            var id = (response[i]['id']) ? response[i]['id'] : 0;

            var html = '<tr class="modal-tr" data-row-id="' + i + '" data-product-prices-id="'+ id +'">' +
                '<td class="modal-td text-center" style="width:150px">'+
                    '<span class="calendar-display">' +
                        response[i]['effective_date'] +
                    '</span>' +
                    '<div class="div-calendar" style="display: none;">' +
                        response[i]['datepicker'] +
                    '</div>' +
                '</td>' +
                '<td class="modal-td text-right p-right-5" style="width:150px">'+
                    '<span id="sprice-display">' +
                        response[i]['sell_price'] +
                    '</span>' +
                    '<input type="text" name="sell_price" class="input-required" id="sell_price" value="'+ response[i]['sell_price'] +'" style="width:90%; margin:5px; display: none;">' +
                '</td>' +
                '<td class="modal-td text-right p-right-5" style="width:150px">'+
                    '<span id="up-display">' +
                        response[i]['unit_price'] +
                    '</span>' +
                    '<input type="text" name="unit_price" class="input-required" id="unit_price" value="'+ response[i]['unit_price'] +'" style="width:90%; margin:5px; display: none;">' +
                '</td>' +
                '<td class="modal-td text-right p-right-5" style="width:150px">'+
                    '<span id="pup-display">' +
                        response[i]['inside_process'] +
                    '</span>' +
                '</td>' +
                '<td class="modal-td text-right p-right-5" style="width:150px">'+ response[i]['material_component_unit_price'] +'</td>' +
                '<td class="modal-td text-right p-right-5" style="width:150px">'+ response[i]['outside_process'] +'</td>' +
                '<td class="modal-td text-right p-right-5" style="width:150px">'+ response[i]['processing_unit_price'] +'</td>' +
                '<td class="text-center modal-td">' +
                    '<div class="div-button">' +
                        '<button class="btn edit-btn">編集</button>' +
                        '<button class="btn delete-btn">削除</button>' +
                    '</div>' +
                    '<div class="edit-button" style="display:none;">' +
                        '<button class="btn update-btn">更新</button>' +
                        '<button class="btn cancel-btn">取消</button>' +
                    '</div>' +
                '</td>' +
            '</tr>';
            $('#table-container tbody').prepend(html)
        }
    }
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
    var modal = $('#unitPriceSettingModal');
    if(response.length > 0) {
        modal.find('.btn-del').removeAttr('disabled').removeClass('btn-disabled');
        modal.find('.btn-done').removeAttr('disabled').removeClass('btn-disabled');
    }else{
        modal.find('.btn-del').attr('disabled', true).addClass('btn-disabled');
        modal.find('.btn-done').attr('disabled', true).addClass('btn-disabled');
    }
}