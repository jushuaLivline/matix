//attendance_dayresults.js

//カレンダー Calendar
$(function () {
    var $buttonPickerJS = $('.buttonPickerJS');
    var $getdisablePreviousDates = $buttonPickerJS.attr('data-disable-previous-dates') || false
    var $disablePreviousDates = ($getdisablePreviousDates == 'true') ? true : false   
    const SESSION_STORAGE_KEY = 'selectedDate';
    const SESSION_COUNT_KEY = 'selectedCount';
    const SESSION_URL_KEY = 'pageURL';
    var $disablePreviousDates = ($getdisablePreviousDates == 'true') ? true : false  
    var $disableDates =  ($buttonPickerJS.attr('data-disable-dates') == 'true') ? true :false;
    

    // Initialize the date picker
    const createDatePicker = function ($input, $format = "yyyy/mm/dd") {
        const picker = $input.pickadate({
            min: $disablePreviousDates,
            format: $format,
            firstDay: 0,
            clear: false,
            selectYears: $disableDates, // Enable year selection dropdown
            selectMonths: $disableDates, // Enable month selection dropdown
            // closeOnSelect: $disableDates, // Close picker on selection
            onSet: function (context) {
                // Get the selected year and month
                const selcetedYear = $(".picker__select--year").val(); // Get selected year
                const selcetedMonth = parseInt($(".picker__select--month").val()); // Get selected month
                if (selcetedYear && selcetedMonth  !== null ) {
                    // Format year and month (add leading zero to month)
                    const yearMonth = `${selcetedYear}${(selcetedMonth + 1)
                        .toString()
                        .padStart(2, '0')}`;
                    $input.val(yearMonth); // Set the formatted value in the input
                }
            },
            onClose: function () {
                $input.prop('readonly', false);
                picker.pickadate('picker').stop();

                const selectedDate = $input.val();
                const currentCount = parseInt(sessionStorage.getItem(SESSION_COUNT_KEY)) || 0;
                sessionStorage.setItem(SESSION_STORAGE_KEY, selectedDate);
                sessionStorage.setItem(SESSION_COUNT_KEY, currentCount + 1);
                updateProcessCodes(); 
            }
        });

        return picker;
    }

    // Update the number input fields
    const updateNumberInput = function (count) {
        $('#number_instruction').val(count);
        $('#flight_no').val(count);
    }

    // Update the process codes based on the date values
    const updateProcessCodes = function () {
        let dateValues = {}; 

        $('input[name="creation_date[]"]').each(function() {
            let $input = $(this);
            let index = $input.closest('tr').data('row');
            let dateValue = $input.val().trim();
            let $processCodeInput = $('#process_code_' + index);

            if (dateValue !== "") {
                if (dateValues[dateValue]) {
                    dateValues[dateValue]++;
                } else {
                    dateValues[dateValue] = 1;
                }
                $processCodeInput.val(dateValues[dateValue]);
            } else {
                $processCodeInput.val("");
            }
        });
    }

    // Bind click event to date picker button
    $buttonPickerJS.on('click', function (event) {
        const $input = $('#' + $(this).data('target'));
        const format = $(this).data('format') || "yyyy/mm/dd";
        const pickerDate = createDatePicker($input, format.toLowerCase());
        pickerDate.pickadate('picker').open();
        event.stopPropagation();
    }).on('mousedown', function (event) {
        event.preventDefault();
    });

    // Check if the stored URL matches the current URL
    const storedURL = sessionStorage.getItem(SESSION_URL_KEY);
    const currentURL = window.location.href;
    if (storedURL != currentURL) {
        // If the URL has changed, remove session data
        sessionStorage.removeItem(SESSION_STORAGE_KEY);
        sessionStorage.removeItem(SESSION_COUNT_KEY);
        sessionStorage.setItem(SESSION_URL_KEY, currentURL);
    }
});


//全選択チェック - Select all check
var checkedpos = {};
$(function () {
    //全選択・解除処理 All selection/cancel processing
    function toggleCheckAll($checkAll) {

        //対象チェックのコード - Code for target check
        var datacode = $checkAll.attr('data-code');
        if (!datacode)
            return false;

        //monthOutクラスをもつthのcheckboxはdisabledに設定する - th checkbox with monthOut class set to disabled
        $('th.monthOut').find('input:checkbox').prop('disabled', true);

        //対象チェック群 - Target check group
        var $checks = $("input.boxes" + datacode + ":not(:disabled)");
        if (!$checks.length)
            return false;

        // 「全選択」チェック - "Select all" check
        $checkAll.on('click', function () {
            $checks.prop('checked', this.checked).trigger('change');
        });

        // 「全選択」以外のチェックボックスがクリックされたら、 - When a check box other than "Select all" is clicked,
        $checks.on('change', function () {
            var $checked = $checks.filter(':checked');
            if ($checked.length == $checks.length) {
                // 全てのチェックボックスにチェックが入っていたら、「全選択」 = checked - If all checkboxes are checked, "select all" = checked
                $checkAll.prop('checked', true);
            } else if ($checked.length == 0) {
                // 1つでもチェックが入っていたら、「全選択」 = checked
                $checkAll.prop('checked', false);
            }
            //チェック位置情報リセット
            checkedpos[$(this).attr('class')] = [];
            //チェック位置情報更新
            $checked.each(function (key, val) {
                checkedpos[$(this).attr('class')].push($(val).val());
            });
            //一括ボタンのdata-postを更新
            var postdata = [];
            $.each(checkedpos['boxes01'], function (k1, v1) {
                postdata.push(Object.assign({"user_id": v1}, $('.btn-approve-all').data('postadd')));
            });
            // console.log(JSON.stringify(postdata));
            if (postdata.length > 0)
                $('.btn-approve-all').attr('data-post', JSON.stringify(postdata)).toggleClass('btn-disabled', false);
            else
                $('.btn-approve-all').attr('data-post', '').toggleClass('btn-disabled', true);

        });
    }

    //全選択・解除の要素指定
    toggleCheckAll($('#checkAll1'));

});

//不要なhidden -- unnecessary hidden
var unnessesary = ['building_name', 'atwork'];
//勤怠情報の更新  --Update attendance information
//モーダルの変更ボタン押下時の送信処理 --Send processing when modal change button is pressed
var okcallback = function ($modal) {
    var $form = $modal.find('form');

    //url
    var url = createurl($modal);

    //FormData
    var fd = new FormData($form.get(0));
    var datas = {};
    for (item of fd) {
        if ($.inArray(item[0], unnessesary) == -1) {
            // console.log(item);
            var end = item[0].slice(-2);
            if (end == '[]') {
                var nm = item[0].substring(0, item[0].length - 2);
                if (!datas[nm])
                    datas[nm] = [];
                datas[nm].push(item[1]);
            } else {
                datas[item[0]] = item[1];
            }
        }
    }
    // console.log(datas);
    datas = [datas];
    datas = JSON.stringify(datas);

//	var datas = $form.serialize();
    var result;
    //ajax
    $.ajax({
        type: 'POST',
        cache: false,
        url: url,
        data: datas,
        // contentType: 'application/json',
        // dataType: 'json'
    })
        .done(function (res) {
            // console.log(res);
            result = res;
        })
        .always(function () {
            if (result == 'OK')
                location.reload();
            else {
                alert('エラーがあります');
                $modal.find('.modalInner').html(result);
                $modal.find('.js-modal-close').on('click', function () {
                    modal_close($(this));
                });
            }
        });
};

//変更ボタンのURL生成
function createurl($modal) {
    var url = $modal.find('form').data('url');
    return url;
}

//フォーム内のチェックボックス・ラジオボタンの初期処理
$(function () {
    $(':checkbox:checked, :radio:checked').trigger('change');
    //和暦セレクトボックス以外のセレクトボックス
    $('select:not([class*=-select])').trigger('change');
});

$(function () {
    var unnessesary = [];
    //承認ボタンイベント
    $('input:checkbox[name=authc]').on('click', function () {
        var datas = {};
        //params
        params = $(this).data('post');
        for (key in params) {
            if ($.inArray(key, unnessesary) == -1)
                datas[key] = params[key];
        }
        //承認チェック状態を追加
        datas['admin_lock_flag'] = $(this).prop('checked') ? "1" : "0";
        //打刻時間を追加
        var $inputtime = $(this).closest('tr').find('.timecard.listformsetBox');
        datas['timecard_start_time'] = $inputtime.find('[name=timecard_start_time]').val();
        datas['timecard_end_time'] = $inputtime.find('[name=timecard_end_time]').val();
        //url
        var url = $(this).data('url');
        //送信
        $tr = $(this).closest('tr');
        flag = $(this).prop('checked');
        // console.log([datas]);
        approve(url, [datas], function (datas) {
            /*toggleInputTimecard($tr, flag, datas)*/
            var data = JSON.parse(datas)[0];
            $tr.find('.js-attendance-add').toggle(data['admin_lock_flag'] == '0');
        });
    });
    //一括承認処理
    $('.btn-approve-all').on('click', function () {
        var datas = [];
        //params
        params = $(this).data('post');
        $.each(params, function (k, v) {
            var data = {};
            for (key in v) {
                if ($.inArray(key, unnessesary) == -1)
                    data[key] = v[key];
            }
            //承認チェック状態を追加
            data['admin_lock_flag'] = "1";
            //現在の打刻時間
            var $checkbox = $('input:checkbox[class=boxes01][value="' + data['user_id'] + '"]');
            var $tr = $checkbox.closest('tr');
            var $timecardinput = $tr.find('.timecard.listformsetBox');
            data['timecard_start_time'] = $timecardinput.find('[name=timecard_start_time]').val()
            data['timecard_end_time'] = $timecardinput.find('[name=timecard_end_time]').val()
            // console.log(data)
            datas.push(data);
        });
        //url
        var url = $(this).data('url');
        //送信
        approve(url, datas, function () {
            location.reload();
        });
    });
});

//打刻時間の入力切替 --Input switching of stamping time
function toggleInputTimecard(tr, flag, datas) {
    console.log(JSON.parse(datas));
    var data = JSON.parse(datas)[0];
    var $td = $(tr).find('.timecard:not(.listformsetBox)');
    var starttime = data['timecard_start_time'] == '00:00' ? '' : data['timecard_start_time'];
    var endtime = data['timecard_end_time'] == '00:00' ? '' : data['timecard_end_time'];
    $td.text(starttime + (endtime ? '-' + endtime : ''));
    $(tr).find('.timecard:not(.listformsetBox)').toggle(flag);
    $(tr).find('.timecard.listformsetBox').toggle(!flag);
}

// //現時刻ボタン処理
// $(function(){
// 	$('.listInnertimeImp').on('click', function(){
// 		var inputtime = $(this).prev('input');
// 		var now = new Date();
// 		var nowtime = ("0" + now.getHours()).slice(-2) + ':' + ("0" + now.getMinutes()).slice(-2);
// 		inputtime.val(nowtime);
// 	});
// });
//承認処理
function approve(url, datas, cb) {
    datas = JSON.stringify(datas);
    // console.log(datas);
    //ajax
    $.ajax({
        type: 'POST',
        cache: false,
        url: url,
        data: datas,
        contentType: 'application/json',
        dataType: 'json'
    })
        .done(function (res) {
            console.log(res);
        })
        .always(function () {
            if (typeof cb === 'function')
                cb(datas);
            // location.reload();
        });
}
