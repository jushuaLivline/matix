$(function () {
    (function (app) {
        app.init = function() {
            app.getNameValue();
            app.submit();
        };


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

        app.submit = function(){
            $("[type='submit']").on("click",function(e){
                e.preventDefault();
                const pt = $(this).attr('data-post-type');
                const message = (pt == 'update') ? "従業員を更新したいのですが、よろしいでしょうか?" : "社員を登録します、よろしいですか?";
                let confirmation = window.confirm(message);
                if (!confirmation) {
                    return;
                }
                form();
            });
        }

        app.init();

        function form($action=null)
        {
            let form = $("#submit-employee-form");
                let action = form.data('action');
                let url = action == "store"
                    ? "/master/employee"
                    : `/master/employee/${action}`;

                let method = action == "store" ? "POST" : "PATCH"; 

                $.ajax({
                    url: url,
                    method: method,
                    data: form.serialize(),
                    dataType: "JSON",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") 
                    },
                    beforeSend: function(){
                        $(".error-message, #flash-message").remove();
                    },
                    success: function(response){
                        form.before("<div id='flash-message' style='background-color: #fff;'>" + response.message + "</div>");

                         // scroll to top
                         $('html, body').animate({
                            scrollTop: 0
                        }, 500);

                        // Refresh a specific container 
                        $("form.overlayedSubmitForm").load(location.href + " form.overlayedSubmitForm > *");
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
        
                            Object.keys(errors).forEach(function(field) {
                                let errorMessage = errors[field][0]; 
                                let inputField = $("[name='" + field + "']");
        
                                // Check if the error-message div already exists within the parent div
                                let errorDiv = inputField.closest(".col-10").find(".display-error");
        
                                // If the error-message div does not exist, append the error message div
                                if (errorDiv.length === 0) {
                                    inputField.closest(".col-10").append("<div class='error-message'>" + errorMessage + "</div>");
                                } else {
                                    errorDiv.append("<div class='error-message'>" + errorMessage + "</div>");
                                }
                            });

                            // scroll to top page
                            $('html, body').animate({
                                scrollTop: 0 
                            }, 300)
                        }
                    }
                });
            
        }
    })(window.App || (window.App = {}));
});
