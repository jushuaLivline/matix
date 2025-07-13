// check validate form
$(document).ready(function () {
    $("#forgotPassword").validate({
        rules: {
            "email": {
                required: true,
            },
            "name": {
                required: true,
            },
        },
        messages: {
            "email": {
                required: "登録されているメは必須です",
                email: "有効なメールアドレスを入力してください。"
            },
            "name": {
                required: "ユーザーIDは必須です",
            }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});
