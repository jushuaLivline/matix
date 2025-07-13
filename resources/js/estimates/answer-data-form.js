$(function () {
    $(document).on('click', '.removeFile', function () {
        $(this).closest('figure').remove();
    })

    $('#message-with-limiter').on("keyup change", function(){
        var fieldLength = $(this).val().length;
        var limit = 1000;
        if (fieldLength <= limit){
            $("#limit-indicator").html(fieldLength);
        } else {
            var str = $(this).val();
            str = str.substring(0, str.length - (fieldLength - limit));
            $(this).val(str);
            $("#limit-indicator").html(fieldLength);
        }
    })


    $(document).on('click', '.remove-btn', function () {
        if ($('.tableAddElement').length > 1) {
            $(this).closest('.tableAddElement').remove();
        }
    })
    

    $('#answerDataForm').validate({
        rules: {
            reply_estimate_d: {
                required: true
            },
            quotation_date: {
                required: true
            },
            request_content: {
                required: true,
                maxlength: 1000
            },
        },
        messages: {
            reply_estimate_d: {
                required: '入力してください'
            },
            quotation_date: {
                required: '入力してください'
            },
            request_content: {
                required: '入力してください',
                maxlength: '担当者回答内容の入力は1000文字以内にしてください'
            },
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            $(element).closest('dd').find('.error_msg').html(error);
        },
    })
})
