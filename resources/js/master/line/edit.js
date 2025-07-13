$(function () {
  const lineId = $("#line_id").val();

  $("#delete_line").on("click", function () {
    const token = document
      .querySelector('meta[name="csrf-token"]')
      .getAttribute("content");
    if (confirm("ラインマスタを削除します、よろしいでしょうか？")) {
      $.ajax({
        url: "/master/line/delete/" + lineId,
        type: "POST",
        headers: {
          "X-CSRF-TOKEN": token,
        },
        success: function () {
          window.location.href = '/master/line';
        },
      });
    }
  });
  
  $.validator.addMethod(
    "departmentCheck",
    function (value, element) {
      const code = $("#department_code").val().trim();
      const name = $("#department_name").val().trim();

      if (code !== "" && name === "") {
        return false;
      }
      return true;
    },
    "部門コードが存在しません"
  );
  
  $('#lineMasterForm').validate({
    rules: {
      line_code: {
        required: true
      },
      line_name: {
        required: true,
        maxlength: 40
      },
      line_name_abbreviation: {
        required: true,
        maxlength: 20
      },
    },
    messages: {
      line_code: {
        required: '入力してください',
      },
      line_name: {
        required: '入力してください',
        maxlength: "40文字以内で入力してください"
      },
      line_name_abbreviation: {
        required: '入力してください',
        maxlength: "20文字以内で入力してください"
      },
    },
    errorElement: 'div',
    errorPlacement: function (error, element) {
      $(element).closest('div').find('.err_msg').html(error);
    },
    invalidHandler: function (event, validator) {
      $('.submit-overlay').css('display', "none");
    }
  })
});