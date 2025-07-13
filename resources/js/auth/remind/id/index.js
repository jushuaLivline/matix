// check validate form
$(document).ready(function () {
    $("#forgotUserId").validate({
        rules: {
            "email": {
                required: true,
            },
            "password": {
                required: true,
            },
        },
        messages: {
            "email": {
                required: "登録されているメは必須です",
                email: "有効なメールアドレスを入力してください。"
            },
            "password": {
                required: "パスワードは必須です",
            }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});
