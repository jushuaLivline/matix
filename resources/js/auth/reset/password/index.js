// check validate form
$(document).ready(function () {
    $("#passwordSetting").validate({
        rules: {
            "password": {
                required: true,
                maxlength: 10,
                minlength: 4
            },
            "reEnterPassword": {
                equalTo: "#password"
            },
        },
        messages: {
            "password": {
                required: "入力してください",
                minlength: "4 文字以上で入力してください",
                maxlength: "10文字以内で入力してください",
            },
            "reEnterPassword": {
                equalTo: "確認パスワードが正しくありません"
            }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});
