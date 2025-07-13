$(function () {
    $(document).on('click', '.removeFile', function () {
        $(this).closest('figure').remove();
    })

    $('#message-with-limiter').on("keyup change", function(){
        var fieldLength = $(this).val().length;
        var limit = 1000;
        if (fieldLength <= limit){
            $("#limit-indicator").html(fieldLength);
            $(this).closest('dd').find('.error_msg').html("");            
        } else {
            var str = $(this).val();
            str = str.substring(0, str.length - (fieldLength - limit));
            $(this).val(str);
            $("#limit-indicator").html(fieldLength);
            $(this).closest('dd').find('.error_msg').html("1000 文字以内で入力してください");            
        }
    })

})
