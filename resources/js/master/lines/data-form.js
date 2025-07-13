$(function () {
  $('#lineDownloadCSV').click(function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var elements = $('input[name], select[name]');
    var input = {}
    elements.each(function () {
      var name = $(this).attr('name');
      var value = $(this).val();
      input[name] = value
    });
    $.ajax({
      url: '/master/lines/export',
      type: 'POST',
      data: input,
      headers: {
        'X-CSRF-TOKEN': token
      },
      xhrFields: {
        responseType: 'blob'
      },
      success: function (response) {
        var a = document.createElement('a');
        var url = window.URL.createObjectURL(response);
        a.href = url;
        a.download = 'ラインマスタ一覧.xlsx';
        a.click();
        window.URL.revokeObjectURL(url);
      },
      error: function (xhr, status, error) {
        console.error('Error downloading product:', error);
      }
    });
  })

  const lineId = $('#line_id').val();

  $('#delete_line').click(function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (confirm('Are you sure you want to delete this Customer?')) {
      $.ajax({
        url: '/master/line/' + lineId + '/delete',
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': token
        },
        success: function (response) {
          console.log('Customer deleted successfully!');
          // Handle the response or perform any additional tasks
          window.location.href = '/master/lines';
        },
        error: function (xhr, status, error) {
          console.error('Error deleting product:', error);
          // Handle the error
        }
      });
    }
  });

  $('#btn-copy-line').click(function () {
    $.get("lines/duplicate", function (data) {
      // Assuming the session data is returned as JSON, and the keys match the input names
      $('input[name="line_name"]').val(data.line_name);
      $('input[name="line_name_abbreviation"]').val(data.line_name_abbreviation);
      $('input[name="department_code"]').val(data.department_code);
      $('input[name="department_name"]').val(data.department_name);
    });
  });

  $('#lineMasterForm').validate({
    rules: {
      line_code: {
        required: true
      },
      line_name: {
        required: true
      },
      line_name_abbreviation: {
        required: true
      },
    },
    messages: {
      line_code: {
        required: '入力してください',
      },
      line_name: {
        required: '入力してください',
      },
      line_name_abbreviation: {
        required: '入力してください',
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

})