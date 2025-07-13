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

    $('#createReqFrm').validate({
        rules: {
            customer_code: {
                required: true
            },
            customer_person: {
                required: true
            },
            estimate_d: {
                required: true
            },
            base_product_code: {
                required: true
            },
            product_name: {
                required: true
            },
            per_month_reference_amount: {
                required: true
            },
            sop_d: {
                required: true
            },
            answer_due_d: {
                required: true
            },
            request_date: {
                required: true
            },
            response_due_date: {
                required: true
            },
            part_number: {
                required: true
            },
            model_type: {
                required: true
            },
            request_date: {
                required: true
            },
            response_due_date: {
                required: true
            },
            part_number: {
                required: true
            },
            model_code: {
                required: true
            },
            monthly_production_volume: {
                required: true
            },
            mass_production_period: {
                required: true
            },
            request_content: {
                required: true,
                maxlength: 1000
            },
            message: {
                required: true,
                maxlength: 1000
            }, 
        },
        messages: {
            customer_code: {
                required: '入力してください'
            },
            customer_person: {
                required: '入力してください'
            },
            estimate_d: {
                required: '入力してください'
            },
            base_product_code: {
                required: '入力してください'
            },
            product_name: {
                required: '入力してください'
            },
            per_month_reference_amount: {
                required: '入力してください'
            },
            sop_d: {
                required: '入力してください'
            },
            answer_due_d: {
                required: '入力してください'
            },
            request_date: {
                required: '入力してください',
            },
            response_due_date: {
                required: '入力してください',
            },
            part_number: {
                required: '入力してください'
            },
            model_type: {
                required: '入力してください'
            },
            model_code: {
                required: '入力してください'
            },
            message: {
                required: '入力してください'
            },
            monthly_production_volume: {
                required: '入力してください'
            },
            mass_production_period: {
                required: '入力してください'
            },  
            request_content: {
                required: '入力してください',
                maxlength: '得意先依頼内容の入力は1000文字以内にしてください'
            },
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            $(element).closest('dd').find('.error_msg').html(error);
        },
        invalidHandler: function(event, validator) {
            $('.submit-overlay').css('display', "none");
        }
    })
})
