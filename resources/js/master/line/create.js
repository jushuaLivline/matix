$(function () {
  $("#lineMasterForm").validate({
    rules: {
      line_code: {
        required: true,
        remote: {
            url: "/master/line/check_line_code",
            type: "GET",
            data: {
              line_code: function () {
                return $("#line_code").val();
              }
            }
          }
      },
      line_name: {
        required: true,
        maxlength: 40
      },
      line_name_abbreviation: {
        required: true,
        maxlength: 20
      },
      department_code: {
        remote: {
          url: "/api/lookup-autosearch",
          type: "POST",
          dataFilter: function(response) {
            var response = JSON.parse(response);
            // Check if the response is empty
            if (response.value.trim() === "") {
              // Return false to indicate no department found (validation should fail)
              return false;
            }
            // If the response contains any text, it's considered valid
            return true;
          },
          data: {
            name: function () {
              return "name_abbreviation"; 
            },
            model: function () {
              return "department";
            },
            column: function () {
              return "code";
            },
            searchValue: function () {
              return $("#department_code").val(); 
            }
          },
        }
      },
    },
    messages: {
      line_code: {
        required: "ラインコードは必須です",
        remote: "同じラインコードは登録できません"
      },
      line_name: {
        required: "ライン名は必須です",
        maxlength: "40文字以内で入力してください"
      },
      line_name_abbreviation: {
        required: "ライン名略は必須です",
        maxlength: "20文字以内で入力してください"
      },
      department_code: {
        remote: "部門コードが存在しません"
      }
    },
    errorElement: "div",
    errorPlacement: function (error, element) {
      const errorContainer = $(element).data("error-messsage-container");
      
      if (errorContainer) {
        $(errorContainer).html(error);
      } else {
        // Fallback to standard behavior
        $(element).closest("div").find(".err_msg").html(error);
      }
    },
    invalidHandler: function (event, validator) {
      $(".submit-overlay").css("display", "none");
    },
  });
});
