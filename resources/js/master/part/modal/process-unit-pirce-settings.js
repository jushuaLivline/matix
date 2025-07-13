
var okcallback = function ($modal) {
    return true;
};
var scrollPosition;
$(document).on('click', '.js-modal-close-setting,.js-modal-close-setting.bColor-cancel', function () {
    $('body').removeClass('fixed').css({'top': 0});
    window.scrollTo(0, scrollPosition);
    //callback
    var res = true;
    if (typeof okcallback === 'function') {
        if ($(this).hasClass('bColor-ok'))
            res = okcallback($(this).closest('.modal.js-modal-setting'));
    }
    if (res) {
        $(this).closest('.modal.js-modal').fadeOut();
        $(this).closest('.modal.js-modal').find('.modal__content').hide();
    }
});
$('[id^="sequenceSettingModal"]').on('click', '.process-modal-btn', function() {
    var dataCode = $(this).data('code');
    var dataName = $(this).data('name');
    var modal = $('#processSettingModal')
    modal.find('input[name="process_code"]').val(dataCode)
    modal.find('#process_name').text(dataName)
    var part_number = modal.find('#part_number').val()

    $('#table-setting tbody tr:not(#inputs_setting)').remove();
    
    $.ajax({
        url: '/master/process-unit-pirce-setting/get-data',
        method: 'GET',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            part_number: part_number,
            process_code: dataCode
        },
        success: function(response) {
            buttonPageBottom(response[dataCode]);
            var html = prependHtml(response[dataCode]);
            console.log(response)
            $('#table-setting tbody').append(html)
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
    })
    var $buttonPickerJS = modal.find('.buttonPickerJS');
    const createDatePickerSetting = function ($input, $format = "yyyymmdd") {
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
        const pickerDate = createDatePickerSetting($input, format.toLowerCase());
        pickerDate.pickadate('picker').open();
        event.stopPropagation();
    }).on('mousedown', function (event) {
        event.preventDefault();
    });
})

$(document).on('click', '.save-btn-setting',function () {
    var modal = $(this).closest('[id^="processSettingModal"]');
    var elements = $('#inputs_setting').find('input[name]');
    var input = {}

    if(!validateRequiredFields($('#processSettingModal tfoot tr'))) return;

    if(!confirm('工程単価設定を追加します、よろしいでしょうか？')) return;

    elements.each(function() {
        var name = $(this).attr('name');
        var value = $(this).val();
        input[name] = value
    });
    if (input['effective_date_setting'] != '') {
        $.ajax({
            url: '/master/process-unit-pirce-setting/save-session',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                data: input,
                part_number: $('#part_number').val(),
                process_code: modal.find('input[name="process_code"]').val()
            },
            success: function(response) {
                buttonPageBottom(response[modal.find('input[name="process_code"]').val()]);
                prependHtml(response[modal.find('input[name="process_code"]').val()]);
                clearInputs();
                $(this).off('click');
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.log(error);
            }
        });
    }
})

$(document).on('click', '.clear-btn-setting', function() {
    clearInputs()
})

$(document).on('click','.btn-done-setting', function () {
    if(!confirm('工程単価設定を登録します、よろしいでしょうか？')) return false;
    $('.modalCloseBtn').trigger('click');
    $('#saveProcessUnitPriceForm').submit();
});

$(document).on('click','.btn-delete-setting', function () {
    if(!confirm('工程単価設定を削除します、よろしいでしょうか？')) return false;
    $('.modalCloseBtn').trigger('click');
    $('#deleteProcessUnitPriceForm').submit();
});
$('#table-setting').on("click", ".delete-btn", function() {
    var process_code = $(this).closest('[id^="processSettingModal"]').find('input[name="process_code"]').val()
    handleDeleteButtonClick($(this).closest("tr"), process_code);
});
$('#table-setting').on("click", ".edit-btn", function() {
    handleEditProcessButtonClick($(this).closest("tr"));
});
$('#table-setting').on("click", ".cancel-btn", function() {
    handleCancelProcessButtonClick($(this).closest("tr"));
});

$('#table-setting').on("click", ".update-btn", function() {
    var modal = $(this).closest('[id^="processSettingModal"]');
    var part_number = modal.find('#part_number').val()
    var process_code = modal.find('#process_code').val()
    var row = $(this).closest('tr');
    var rowId = $(this).closest('tr').data('row-id');
    var effective_date = $(this).closest('tr').find('input:eq(0)').val();
    var processing_unit_price = $(this).closest('tr').find('input:eq(1)').val();

    if(!validateRequiredFields(row)) return;

    updateProcessSettingRow(rowId, effective_date, processing_unit_price, row, part_number, process_code);
});
$('#table-setting').on("click", "#settingClose", function() {
    var modal = $('#processSettingModal')
    var part_number = modal.find('#part_number').val()
    var process_code = modal.find('input[name="process_code"]').val()
});


function clearInputs() {
    var elements = $('#inputs_setting').find('input[name]');
    var input = {}
    elements.each(function() {
        $(this).val('');
    });
}

function updateProcessSettingRow(rowId, effective_date, processing_unit_price, row, part_number, process_code) {
    var process_unit_price_id = row.data('process-unit-price-id')
    $.ajax({
        url: '/master/process-unit-pirce-setting/' + process_unit_price_id,
        method: 'PUT',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: rowId,
            effective_date: effective_date,
            processing_unit_price: processing_unit_price,
            part_number: part_number,
            process_code: process_code,
            process_unit_price_id: process_unit_price_id
        },
        success: function(response) {
            prependHtml(response[process_code]);

            row.find('.div-calendar').toggle();
            row.find('.calendar-display').toggle();
            row.find('#sp-display').toggle();
            row.find('.pup-input').toggle();

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
    $('#table-setting tbody tr:not(#inputs_setting)').remove();

    if (typeof response !== 'undefined') {
        for (let i = 0; i < response.length; i++) {
            var id = (response[i].process_unit_price_id) ? response[i].process_unit_price_id : 0;
            var html = '<tr class="modal-tr" data-row-id="' + i + '" data-process-unit-price-id="'+ id +'">' +
                '<td class="modal-td text-center" style="width:150px">'+
                    '<span class="calendar-display">' +
                        response[i]['effective_date'] +
                    '</span>' +
                    '<div class="div-calendar" style="display: none;">' +
                        response[i]['datepicker'] +
                    '</div>' +
                '</td>' +
                '<td class="modal-td text-right p-right-5" style="width:150px  !important">'+
                    '<span id="sp-display">' +
                        response[i]['processing_unit_price'] +
                    '</span>' +
                    '<div class="pup-input" style="display: none;">' +
                        '<input ' +
                            'type="text"' +
                            'pattern="\\d*" ' +
                            'oninput="this.value = this.value.replace(/[^0-9]/g, \'\')" ' +
                            'id="processing_unit_price" ' +
                            'name="processing_unit_price" ' +
                            'class="input-required acceptNumericOnly" ' +
                            'style="width:155px !important; margin:5px;" ' +
                            'value="'+ response[i]['processing_unit_price'] +'" ' +
                            'maxlength="10">' +
                        '</input>' +
                    '</div>' +
                '</td>' +
                '<td class="modal-td text-center">' +
                    '<div class="div-button">' +
                        '<button class="btn edit-btn">編集</button>' +
                        '<button class="btn delete-btn">削除</button>' +
                    '</div>' +
                    '<div class="edit-button" style="display:none;">' +
                        '<button class="btn update-btn">更新</button>' +
                        '<button class="btn cancel-btn">取消</button>' +
                    '</div>' +
                '</td>' +
            '</tr>'
            $('#table-setting tbody').append(html);
        }
    }
}

function handleCancelProcessButtonClick(row) {
    row.find('.div-calendar').toggle();
    row.find('.calendar-display').toggle();
    row.find('#sp-display').toggle();
    row.find('.pup-input').toggle();

    row.find('.div-button').toggle();
    row.find('.edit-button').toggle();
}

function handleEditProcessButtonClick(row) {
    row.find('.div-calendar').toggle();
    row.find('.calendar-display').toggle();
    row.find('#sp-display').toggle();
    row.find('.pup-input').toggle();

    var $buttonPickerJS = row.find('.buttonPickerJS');

    const createDatePicker = function ($input, $format = "yyyymmdd") {
        const picker = $input.pickadate({
            format: $format,
            firstDay: 0,
            clear: false,
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

    row.find('.div-button').toggle();
    row.find('.edit-button').toggle();
}

function handleDeleteButtonClick(row, process_code) {
    var rowId = row.data('row-id');
    var process_unit_price_id = row.data('process-unit-price-id')
    if(!confirm('工程単価設定をテーブルから削除します、よろしいでしょうか？')) return;
    $.ajax({
        url: '/master/process-unit-pirce-setting/delete-row',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            index: rowId,
            process_code: process_code,
            process_unit_price_id: process_unit_price_id
        },
        success: function(response) {
            buttonPageBottom(response[process_code])
            prependHtml(response[process_code])
            $(this).off('click');
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
        }
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
    var modal = $('#processSettingModal');
    if(response.length > 0) {
        modal.find('.btn-orange').removeAttr('disabled').removeClass('btn-disabled');
        modal.find('.btn-done-setting').removeAttr('disabled').removeClass('btn-disabled');
    }else{
        modal.find('.btn-orange').attr('disabled', true).addClass('btn-disabled');
        modal.find('.btn-done-setting').attr('disabled', true).addClass('btn-disabled');
    }
}
