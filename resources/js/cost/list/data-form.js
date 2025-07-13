$(function () {
    $('#createReqFrm').validate({
        rules: {
            year_month: {
                required: true
            },
        },
        messages: {
            year_month: {
                required: '入力してください'
            },
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            $(element).closest('.row-content').find('.error_msg').html(error);
        },
        invalidHandler: function(event, validator) {
            $('.submit-overlay').css('display', "none");
        }
    })
    $('#export_csv').click(function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var elements = $('input[name]');
        var input = {}
        elements.each(function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            input[name] = value
        });
        input['breakdown'] = $('input[type="radio"]:checked').val();
        $.ajax({
            url: '/cost/list/export',
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
                    a.download = '原価表.xlsx';
                    a.click();
                    window.URL.revokeObjectURL(url);
            },
            error: function(xhr, status, error) {
                console.error('Error downloading product:', error);
            }
        });
    })
    function showLoader() {
        $('.submit-overlay').css('display', 'flex');
    }
    $('.pagerBtn').on('click', function() {
        showLoader();
    });
    $('#search').on('click', function() {
        showLoader();
    });
})