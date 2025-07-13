$(function () {
    (function (app) {
        app.init = function () {
            app.clear_and_submit();
            app.radio();
            app.getNameValue();
        };

        app.clear_and_submit = function () {
            $(document).ready(function(){
                $("[type='reset']").on('click',function(e){
                    e.preventDefault();
                    var form = $(this).closest("form");
                    form.find("[type='text']").val("");
                    form.find("[type='radio']").prop("checked", false);

                    form.submit();
                });
            });
        };

        app.radio = function() {
            $(document).ready(function(){
                $("input[type='radio'][name='voucher_class']").on("click", function () {
                    if ($(this).prop("checked")) {
                        if ($(this).data("wasChecked")) {
                            $(this).prop("checked", false);
                            $(this).data("wasChecked", false);
                        } else {
                            $("input[type='radio'][name='voucher_class']").data("wasChecked", false);
                            $(this).data("wasChecked", true);
                        }
                    }
                });
            })
        }

        app.getNameValue = function() {
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
        }

        app.init();
    })(window.App || (window.App = {}));
});
