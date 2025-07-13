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
        url: '/stock-inventory/list',
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
        url: '/stock-inventory/export-csv',
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
            a.download = '製品在庫検索・一覧.xlsx';
            a.click();
            window.URL.revokeObjectURL(url);
        },
        error: function(xhr, status, error) {
            console.error('Error downloading stock inventory:', error);
        }
    });
})