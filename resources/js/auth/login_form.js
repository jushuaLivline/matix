// check validate form
$(document).ready(function () {
    $("#formLogin").validate({
        rules: {
            "name": {
                required: true,
            },
            // "password": {
            //     required: true,
            // },
        },
        messages: {
            "name": {
                required: "入力してください",
            },
            // "password": {
            //     required: "入力してください",
            // }
        },
        submitHandler: function (form) {
            form.submit();
        },
        invalidHandler: function(event, validator) {
            $('.submit-overlay').css('display', "none");
        }
    });

    if (localStorage.getItem('saved_user_id') != null) {
        $('input[name=name]').val(localStorage.getItem('saved_user_id'));
        $('input[name=rememberId]').prop('checked', true);
    }
});
