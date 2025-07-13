$(function () {
  $('#departmentDownloadCSV').click(function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var elements = $('input[name], select[name]');
    var input = {}
    elements.each(function () {
      var name = $(this).attr('name');
      var value = $(this).val();
      input[name] = value
    });
    $.ajax({
      url: '/master/departments/export',
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
        a.download = '部門マスタ一覧.xlsx';
        a.click();
        window.URL.revokeObjectURL(url);
      },
      error: function (xhr, status, error) {
        console.error('Error downloading product:', error);
      }
    });
  })

  const departmentId = $('#department_id').val();

  $('#delete_department').click(function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (confirm('Are you sure you want to delete this Department?')) {
      $.ajax({
        url: '/master/department/' + departmentId + '/delete',
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': token
        },
        success: function (response) {
          console.log('Department deleted successfully!');
          // Handle the response or perform any additional tasks
          window.location.href = '/master/departments';
        },
        error: function (xhr, status, error) {
          console.error('Error deleting product:', error);
          // Handle the error
        }
      });
    }
  });

  $('#btn-copy-department').click(function () {
    $.get("departments/duplicate", function (data) {
      // Assuming the session data is returned as JSON, and the keys match the input names
      $('input[name="name"]').val(data.name);
      $('input[name="name_abbreviation"]').val(data.name_abbreviation);
      $('input[name="department_name"]').val(data.department_name);
      $('input[name="section_name"]').val(data.section_name);
      $('input[name="group_name"]').val(data.group_name);
    });
  });

  $('#departmentMasterForm').validate({
    rules: {
      code: {
          required: true
      },
      name: {
          required: true
      },
      name_abbreviation: {
          required: true
      },
    },
    messages: {
      code: {
        required: '入力してください',
      },
      name: {
        required: '入力してください',
      },
      name_abbreviation: {
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