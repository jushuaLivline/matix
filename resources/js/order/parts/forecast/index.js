$(document).ready(function () {
    $('#clear-daily-inputs').on('click', function (e) {
        e.preventDefault();
        if (confirm('内示情報を削除します、よろしいでしょうか？')) {
            $('#daily-inputs input[type="text"]').val('0');
        }
    });

    $("[type='reset']").on("click", function(e) {
        e.preventDefault();
        let form = $("#forecast-form");
        form.find("input[type='text']").val('');
    });

    $("#previous-month, #next-month").on('click',function(e){
        e.preventDefault();
        var action = $(this).attr('id') === "previous-month" ? "previous" : "next";
        $.ajax({
            url: window.location.origin + "/order/parts/forecast/search-month",
            type: "POST",
            data: {
                // pass the fetched year and month and not the one on the form
                year_and_month: $("#year-and-month").val(),
                delivery_destination_code: $("[name='delivery_destination_code']").val(),
                acceptance: $("[name='acceptance']").val(),
                product_number: $("[name='product_number']").val(),
                action: action,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType:"JSON",
            beforeSend: function(){
                $('.submit-overlay').css('display', "flex");
            },
            success: function(response){
                $('.submit-overlay').css('display', "none");

                //Update the value of year-and-month input
                $("#year-and-month").val(response.new_year_and_month);
                // Extract year and month
                var year = response.new_year_and_month.substring(0, 4);
                var month = response.new_year_and_month.substring(4, 6);

                // Update the elements
                $(".year").text(year)
                $(".month").text(month);
                
                if(response.status == "success"){
                    for(var num=1; num<=31; num++){
                        var dayValue = response.notice["day_" + num] || '';
                        $("#day_"+num).val(dayValue);
                    }
                }else{
                    for(var num=1; num<=31; num++){
                        $("#day_"+num).val('');
                    }
                }
            }
        })
    });
    
    function fetchQueryName(query, get, model, value, name) {
        if (value) {
            $.ajax({
                url: '/outsource/defect/material/fetch-query-name',
                type: 'POST',
                data: {
                    query: query,
                    get: get,
                    model: model,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status == "success") {
                        $("#"+name).val(response.result);
                        console.log(response.message);
                    } else {
                        console.log(response.message);
                        $("#"+name).val('');
                    }
                },
                error: function (xhr, status, error) {
                    console.log("Error:", xhr.status, error);
                    if (xhr.status === 404) {
                        console.log("Resource not found.");
                        $("#" + name).val('');
                    }
                }
            });
        } else {
            $("[name='"+name+"']").val('');
        }
    }

    $('.fetchQueryName').on('input', function () {
        let query = $(this).data('query');
        let get = $(this).data('query-get');
        let model = $(this).data('model');
        let value = $(this).val();
        let name = $(this).data('reference');
        fetchQueryName(query, get, model, value, name);
    });

    $('#register-daily-form').on("click", function (e) {
        e.preventDefault();
    
        // Select the form
        let form = $("#forecast-form");
    
        // Check if the form is valid
        if (!form[0].checkValidity()) {
            form[0].reportValidity(); // Show built-in validation messages
            return; // Stop execution if validation fails
        }
    
        // Get the year_and_month from the hidden field and not on the form
        var year_and_month = $("#year-and-month").val();
        var delivery_destination_code = $("[name='supplier_code']").val();
        var acceptance = $("[name='acceptance']").val();
        var product_number = $("[name='product_code']").val();
        var _token = $('meta[name="csrf-token"]').attr('content');
    
        var days = {};
        for (var i = 1; i <= 31; i++) {
            var value = $("[name='day_" + i + "']").val();
            days['day_' + i] = value ? value : 0;
        }
    
        var formData = {
            year_and_month: year_and_month,
            delivery_destination_code: delivery_destination_code,
            acceptance: acceptance,
            product_number: product_number,
            days: days,
            _token: _token
        };

        if (!confirm('指示部品内示情報を登録します、よろしいでしょうか？')) return false;
    
        $.ajax({
            url: window.location.origin + "/order/parts/forecast/add-update",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(formData),
            dataType: "JSON",
            beforeSend: function () {
                $('.submit-overlay').css('display', "flex");
            },
            success: function (response) {
                $('.submit-overlay').css('display', "none");
                if (response) {
                    var messageClass = response.status ? "success-message" : "error-message";
                    var flashMessage = $('<div class="flash-message bt-white ' + messageClass + '" id="flash-message">' + response.message + '</div>');
    
                    $("#head-label").before(flashMessage);
    
                    // Ensure all empty inputs are set to 0
                    for (var i = 1; i <= 31; i++) {
                        var element = $("[name='day_" + i + "']");
                        var value = element.val();
    
                        if (value === null || value.trim() === "") {
                            element.val(0);
                        }
                    }
                    
                    // scoll to the top of the page
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                } else {
                    console.log("An error has occurred");
                }
            },
            error: function (xhr, status, error) {
                $('.submit-overlay').css('display', "none");
                console.error("Error:", error);
            }
        });
    });
    
});