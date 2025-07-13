$(function () {
    $('#processDownloadCSV').click(function () {
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      var elements = $('input[name], select[name]');
      var input = {}
      elements.each(function() {
        var name = $(this).attr('name');
        var value = $(this).val();
        input[name] = value
      });
      $.ajax({
        url: '/master/processes/export',
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
              a.download = '工程マスタ一覧.xlsx';
              a.click();
              window.URL.revokeObjectURL(url);
        },
        error: function(xhr, status, error) {
          console.error('Error downloading product:', error);
        }
      });
    })

  const processId = $('#process_id').val();

  $('#delete_process').click(function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (confirm('Are you sure you want to delete this Process?')) {
      $.ajax({
        url: '/master/process/' + processId + '/delete',
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': token
        },
        success: function (response) {
          console.log('Process deleted successfully!');
          // Handle the response or perform any additional tasks
          window.location.href = '/master/processes';
        },
        error: function (xhr, status, error) {
          console.error('Error deleting product:', error);
          // Handle the error
        }
      });
    }
  });

  $('#btn-copy-process').click(function () {
    $.get("processes/duplicate", function (data) {
      // Assuming the session data is returned as JSON, and the keys match the input names
      $('input[name="process_name"]').val(data.process_name);
      $('input[name="abbreviation_process_name"]').val(data.abbreviation_process_name);

      // Check the inside_and_outside_division radio button
      $('input[name="inside_and_outside_division"]').prop('checked', false); // Clear previous selection
      $('input[name="inside_and_outside_division"][value="' + data.inside_and_outside_division + '"]').prop('checked', true); // Set new selection

      $('input[name="customer_code"]').val(data.customer_code);
      
      $('input[name="backorder_days"]').val(data.backorder_days);
    });
  });


  $('#processMasterForm').validate({
    rules: {
      process_code: {
          required: true
      },
      process_name: {
          required: true
      },
      abbreviation_process_name: {
          required: true
      },
      inside_and_outside_division: {
          required: true
      },
    },
    messages: {
      process_code: {
        required: '入力してください',
      },
      process_name: {
        required: '入力してください',
      },
      abbreviation_process_name: {
        required: '入力してください',
      },
      inside_and_outside_division: {
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