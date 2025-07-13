$(function () {
    var form = $('#createReqFrm');
    var isCreateMode = form.data('mode') === 'create';

    // Custom Lookup function
    lookupAutoSearch(form);

    // Add custom date format validation (for local client-side validation)
    $.validator.addMethod("dateFormat", function(value, element) {
        if (this.optional(element)) return true;
    
        const regex = /^\d{8}$/;
        if (!regex.test(value)) {
            console.log("Failed regex check");
            return false;
        }
    
        const year = parseInt(value.substring(0, 4), 10);
        const month = parseInt(value.substring(4, 6), 10);
        const day = parseInt(value.substring(6, 8), 10);
        const date = new Date(year, month - 1, day);
    
        const valid =
            date.getFullYear() === year &&
            date.getMonth() === month - 1 &&
            date.getDate() === day;
    
        if (!valid) console.log("Failed date validity check");
    
        return valid;
    }, "日付はYYYYMMDDの形式で入力してください");
    

    form.validate({
        rules: {
            sign: {
                required: true,
                maxlength: 2,
                digits: true
            },
            branch_number: {
                required: true,
                maxlength: 1,
                digits: true
            },
            machine_number: {
                required: true,
                maxlength: 6,
                remote: isCreateMode ? {
                    url: '/master/machine/check-machine-number',  // Update the URL to your route
                    type: 'get',
                    data: {
                        machine_number: function() {
                            return $('#machine_number').val(); // Get the value from the input
                        }
                    }
                } : false
            },
            machine_number_name: {
                required: true,
                maxlength: 50
            },
            machine_division: {
                required: true
            },
            line_name: {
                maxlength: 50
            },
            created_at: {
                dateFormat: true,
            },
            drawing_date: {
                dateFormat: true,
            },
            completion_date: {
                dateFormat: true,
            },
            manager: {
                maxlength: 50
            },
            remarks: {
                maxlength: 50
            },
        },
        messages: {
            sign: {
                required: 'プロジェクトNo.は必須です',
                maxlength: '得意先依頼内容の入力は2文字以内にしてください',
            },
            branch_number: {
                required: 'プロジェクトNo.は必須です ',
                maxlength: '得意先依頼内容の入力は1文字以内にしてください',
            },
            machine_number: {
                required: 'プロジェクトNo.は必須です',
                maxlength: 'プロジェクトNo.は6桁以内で入力してください',
                remote: '同じプロジェクトNo.は登録できません'
            },
            machine_number_name: {
                required: '機械名は必須です',
                maxlength: '機械名は50文字以内で入力してください'
            },
            machine_division: {
                required: '入力してください'
            },
            line_name: {
                maxlength: 'ライン名は50文字以内で入力してください'
            },
            created_at: {
                dateFormat: '正しい形式で登録日を入力してください',
                maxlength: '正しい形式で登録日を入力してください',
                minlength: '正しい形式で登録日を入力してください',
            },
            drawing_date: {
                dateFormat: '正しい形式で出図日を入力してください',
                maxlength: '正しい形式で出図日を入力してください',
                minlength: '正しい形式で出図日を入力してください',
            },
            completion_date: {
                dateFormat: '正しい形式で完成日を入力してください',
                maxlength: '正しい形式で完成日を入力してください',
                minlength: '正しい形式で完成日を入力してください',
            },
            manager: {
                maxlength: '担当者は50文字以内で入力してください'
            },
            remarks: {
                maxlength: '備考は50文字以内で入力してください'
            },
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            $(element).closest('.row-field').find('.error_msg').html(error);
            // scroll to top page
            $('html, body').animate({
                scrollTop: 0 
            }, 300)
        },
        invalidHandler: function(event, validator) {
            $('.submit-overlay').css('display', "none");
        }
    })
    $('#btn-copy-mn').click(function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const source = $(this).data("source");
        $.ajax({
            url: '/master/machine/duplicate',
            type: 'GET',
            headers: {
              'X-CSRF-TOKEN': token,
            },
            success: function(response) {
                populateInputFields(response);
                populateSelectOptions(response);
                $('#card').hide()
            },
            error: function(xhr, status, error) {
              console.error('Error downloading product:', error);
            }
        });
    })

    $('#export_csv').click(function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var elements = $('input[name], select[name]');
        var input = {}
        elements.each(function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            input[name] = value
        });
        $.ajax({
          url: '/master/machine-numbers/export',
          type: 'POST',
          data: input,
          headers: {
            'X-CSRF-TOKEN': token
          },
          xhrFields: {
            responseType: 'blob'
          },
          success: function(response) {
            var a = document.createElement('a');
                var url = window.URL.createObjectURL(response);
                a.href = url;
                a.download = '機番マスタ一覧.xlsx';
                a.click();
                window.URL.revokeObjectURL(url);
          },
          error: function(xhr, status, error) {
            console.error('Error downloading product:', error);
          }
        });
    })

    $(document).on('click', '.btn-register', function () {
        var confirmation = form.attr('data-confirmation-message');
        if(!confirm(confirmation))  return false;
        form.submit();

    });
    const machineNumberId = $('#machine_number_id').val();
    $('#hard_delete_machine_number').click(function () {
        if(!confirm('機番マスタを削除します、よろしいですか')) return false;
        $('#deleteReqFrm').submit();
    });

    function populateInputFields(response) {
        var responseData = response;

        // delete responseData.machine_number;
        delete responseData.edited_part_number;
        for (var key in responseData) {
            if (responseData.hasOwnProperty(key)) {
                var value = responseData[key];
                var inputField = document.getElementById(key);
                if (inputField) {
                    inputField.value = value;
                }
            }
        }
    }

    function populateSelectOptions(response) {
        // Assuming the response is already a JavaScript object (not JSON)
        var responseData = response;

        // Get the select element
        var selectOptions = document.getElementById("machine_division");

        var existingOptionIDs = Array.from(selectOptions.options).map(option => option.value);

        // Loop through the response data and select the matching option, if it exists
        for (var key in responseData) {
            if (responseData.hasOwnProperty(key)) {
                var value = responseData[key];
                if (existingOptionIDs.includes(key)) {
                    selectOptions.value = key; // Set the select element value to the matched ID
                }
            }
        }
    }

    $('.overlayedSubmitForm').submit(function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var elements = $('input[name], select[name]');
        var input = {}
        elements.each(function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            input[name] = value
        });
        $.ajax({
            url: '/master/machine-numbers',
            type: 'GET',
            data: input,
            headers: {
                'X-CSRF-TOKEN': token
            },
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response) {
                $('.submit-overlay').css('display', "none");
            },
            error: function(xhr, status, error) {
                console.error('Error searching machine numbers:', error);
            }
        });
    });
})