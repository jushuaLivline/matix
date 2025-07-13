$(function () {
  $('#downloadCSV').click(function () {
    // alert('asdas');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var elements = $('input[name], select[name]');
    var input = {}
    elements.each(function () {
      var name = $(this).attr('name');
      var value = $(this).val();
      input[name] = value
    });
    $.ajax({
      url: '/master/customers/export',
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
        a.download = '取引先マスタ一覧.xlsx';
        a.click();
        window.URL.revokeObjectURL(url);
      },
      error: function (xhr, status, error) {
        console.error('Error downloading product:', error);
      }
    });
  })

  const customerId = $('#customer_id').val();

  $('#delete_customer').click(function () {
    // console.log(customerId);
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (confirm('Are you sure you want to delete this Customer?')) {
      $.ajax({
        url: '/master/customer/' + customerId + '/delete',
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': token
        },
        success: function (response) {
          console.log('Customer deleted successfully!');
          // Handle the response or perform any additional tasks
          window.location.href = '/master/customers';
        },
        error: function (xhr, status, error) {
          console.error('Error deleting product:', error);
          // Handle the error
        }
      });
    }
  });

  $('#btn-copy-customer').click(function () {
    $.get("customers/duplicate", function (data) {
      // Assuming the session data is returned as JSON, and the keys match the input names
      $('input[name="customer_name"]').val(data.customer_name);
      $('input[name="supplier_name_abbreviation"]').val(data.supplier_name_abbreviation);
      $('input[name="business_partner_kana_name"]').val(data.business_partner_kana_name);
      $('input[name="branch_factory_name"]').val(data.branch_factory_name);
      $('input[name="business_partner_kana_name"]').val(data.business_partner_kana_name);
      $('input[name="post_code"]').val(data.post_code);
      $('input[name="address_1"]').val(data.address_1);
      $('input[name="address_2"]').val(data.address_2);
      $('input[name="telephone_number"]').val(data.telephone_number);
      $('input[name="fax_number"]').val(data.fax_number);
      $('input[name="representative_name"]').val(data.representative_name);
      $('input[name="capital"]').val(data.capital);

      $('input[name="customer_flag"]').prop('checked', data.customer_flag === '1'); // Set the checkbox state
      $('input[name="supplier_tag"]').prop('checked', data.supplier_tag === '1'); // Set the checkbox state

      // Check the supplier_classication radio button
      $('input[name="supplier_classication"]').prop('checked', false); // Clear previous selection
      $('input[name="supplier_classication"][value="' + data.supplier_classication + '"]').prop('checked', true); // Set new selection

      $('input[name="purchase_report_apply_flag"]').prop('checked', data.purchase_report_apply_flag === '1'); // Set the checkbox state

      // Check the sales_amount_rounding_indicator radio button
      $('input[name="sales_amount_rounding_indicator"]').prop('checked', false); // Clear previous selection
      $('input[name="sales_amount_rounding_indicator"][value="' + data.sales_amount_rounding_indicator + '"]').prop('checked', true); // Set new selection

      // Check the purchase_amount_rounding_indicator radio button
      $('input[name="purchase_amount_rounding_indicator"]').prop('checked', false); // Clear previous selection
      $('input[name="purchase_amount_rounding_indicator"][value="' + data.purchase_amount_rounding_indicator + '"]').prop('checked', true); // Set new selection

      $('input[name="transfer_source_bank_code"]').val(data.transfer_source_bank_code);
      $('input[name="transfer_source_bank_branch_code"]').val(data.transfer_source_bank_branch_code);
      $('input[name="transfer_source_account_number"]').val(data.transfer_source_account_number);

      // Check the transfer_source_account_clarification radio button
      $('input[name="transfer_source_account_clarification"]').prop('checked', false); // Clear previous selection
      $('input[name="transfer_source_account_clarification"][value="' + data.transfer_source_account_clarification + '"]').prop('checked', true); // Set new selection

      $('input[name="payee_bank_code"]').val(data.payee_bank_code);
      $('input[name="transfer_destination_bank_branch_code"]').val(data.transfer_destination_bank_branch_code);
      $('input[name="transfer_account_number"]').val(data.transfer_account_number);

      // Check the transfer_account_clasification radio button
      $('input[name="transfer_account_clasification"]').prop('checked', false); // Clear previous selection
      $('input[name="transfer_account_clasification"][value="' + data.transfer_account_clasification + '"]').prop('checked', true); // Set new selection

      // Check the transfer_account_clasification radio button
      $('input[name="transfer_fee_burden_category"]').prop('checked', false); // Clear previous selection
      $('input[name="transfer_fee_burden_category"][value="' + data.transfer_fee_burden_category + '"]').prop('checked', true); // Set new selection

      $('input[name="bill_ratio"]').val(data.bill_ratio);

      $('input[name="transfer_fee_condition_amount"]').val(data.transfer_fee_condition_amount);
      $('input[name="amount_less_than_transfer_fee_conditions"]').val(data.amount_less_than_transfer_fee_conditions);
      $('input[name="transfer_fee_condition_or_more_amount"]').val(data.transfer_fee_condition_or_more_amount);
    });
  });

  $('#customerMasterForm').validate({
    rules: {
      customer_code: {
          required: true
      },
      customer_name: {
          required: true
      },
      supplier_name_abbreviation: {
          required: true
      },
      bill_ratio: {
          required: true
      },
    },
    messages: {
      customer_code: {
        required: '入力してください',
      },
      customer_name: {
        required: '入力してください',
      },
      supplier_name_abbreviation: {
        required: '入力してください',
      },
      bill_ratio: {
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
  
});