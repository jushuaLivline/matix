// import retainer from '../../search/retainer'

// const pathname = window.location.pathname
// const lineName = $('#line_name')
// const productName = $('#product_name')
// const departmentName = $('#department_name')

// const lineKey = [
//     pathname,
//     lineName.attr('id'),
// ].join(':')

// const productKey = [
//     pathname,
//     productName.attr('id'),
// ].join(':')

// const departmentKey = [
//     pathname,
//     departmentName.attr('id'),
// ].join(':')

// retainer($('#line_code'), lineName, lineKey)
// retainer($('#part_number'), productName, productKey)
// retainer($('#department_code'), departmentName, departmentKey)

$(document).ready(function() {
    let debounceTimer;

    $('#product_number').on('input', function() {
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            var partNumber =  $('#product_number').val();
            var partName = $('#product_name').val();

            $('.js-modal-open').attr('data-part-number', partNumber);
            $('.js-modal-open').attr('data-part-name', partName);
        }, 400);
    });
});

$('.overlayedSubmitForm').submit(function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var elements = $('input[name], select[name]');
    var input = {}
    elements.each(function() {
        var name = $(this).attr('name');
        var value = $(this).val();
        input[name] = value
    });
    $.ajax({
        url: '/master/products',
        type: 'GET',
        data: input,
        headers: {
            'X-CSRF-TOKEN': token
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(response) {
            $('.submit-overlay').css('display', "none");
        },
        error: function(xhr, status, error) {
            console.error('Error searching products:', error);
        }
    });
});

$('#export_csv').click(function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var elements = $('input[name], select[name]');
    var input = {}
    elements.each(function() {
        var name = $(this).attr('name');
        var value = $(this).val();
        input[name] = value
    });
    $.ajax({
        url: '/master/products/export',
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
                a.download = '品番マスタ一覧.xlsx';
                a.click();
                window.URL.revokeObjectURL(url);
        },
        error: function(xhr, status, error) {
            console.error('Error downloading product:', error);
        }
    });
})